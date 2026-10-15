<?php
// Exercise the production loader without booting WordPress or sending mail.
$root = dirname(__DIR__);
$source = file_get_contents($root . '/functions.php');
$start = strpos($source, 'function foods_enqueue_vite_entry(');
$end = strpos($source, 'function foods_theme_scripts()', $start);
if ($start === false || $end === false) { throw new RuntimeException('Vite loader not found'); }
$section = substr($source, $start, $end - $start);
eval(substr($section, 0, strrpos($section, '}') + 1));
$styles = [];
$scripts = [];
function get_template_directory_uri() { return 'https://example.test/theme'; }
function wp_enqueue_style($handle, $url, $deps, $version) { global $styles; $styles[$handle] = $url; }
function wp_enqueue_script(...$args) { global $scripts; $scripts[] = $args; }
function verify_assets($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
$manifest = json_decode(file_get_contents($root . '/dist/.vite/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
$count = 0;
foreach ($manifest as $key => $entry) {
    if (empty($entry['isEntry']) || !str_ends_with($key, '.js')) { continue; }
    $visited = [];
    $expected = [];
    $visit = static function ($key) use (&$visit, &$visited, &$expected, $manifest, $root) {
        verify_assets(isset($manifest[$key]), 'Missing dependency: ' . $key);
        if (isset($visited[$key])) { return; }
        $visited[$key] = true;
        foreach ($manifest[$key]['imports'] ?? [] as $dependency) { $visit($dependency); }
        foreach ($manifest[$key]['css'] ?? [] as $css) {
            verify_assets(is_file($root . '/dist/' . $css), 'Missing CSS: ' . $css);
            $expected[$css] = 'https://example.test/theme/dist/' . $css;
        }
    };
    $visit($key);
    $styles = [];
    foods_enqueue_vite_entry('test', $key, '', $manifest, false);
    verify_assets(array_values($styles) === array_values($expected), 'CSS missing or out of order: ' . $key);
    $count++;
}
$styles = [];
foreach (['src/js/page-contact.js', 'src/js/page-company-business-form.js', 'src/js/page-recruit-entry.js'] as $key) {
    foods_enqueue_vite_entry(str_replace('/', '-', $key), $key, '', $manifest, false);
}
verify_assets(count(array_filter($styles, static fn($url) => str_ends_with($url, '/form-kana-date-validation.css'))) === 1, 'Form shared CSS missing or duplicated');
$fixture = ['a' => ['imports' => ['b', 'missing'], 'css' => ['a.css']], 'b' => ['imports' => ['a'], 'css' => ['b.css']]];
$styles = [];
foods_enqueue_vite_entry('test', 'a', '', $fixture, false);
verify_assets(count($styles) === 2, 'Cycle or missing import handling failed');
$styles = []; $scripts = [];
foods_enqueue_vite_entry('test', 'a', 'http://localhost:5173', $fixture, true);
verify_assets(!$styles && $scripts[0][1] === 'http://localhost:5173/a', 'Local development behavior changed');
echo "PASS: {$count} production entries, CSS order, shared forms, cycles and local mode\n";
