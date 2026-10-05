<?php
// No database or WordPress bootstrap: check routes, permalink isolation and rendered pages.
define('OBJECT', 'OBJECT');
$pages = [];
$rules = [];
$filters = [];
function add_action($name, $callback) {}
function add_filter($name, $callback, $priority = 10, $count = 1) { global $filters; $filters[$name] = $callback; }
function add_rewrite_rule($regex, $query, $priority) { global $rules; $rules[$regex] = $query; }
function get_post($post) { global $pages; return is_object($post) ? $post : ($pages[$post] ?? null); }
function get_page_template_slug($id) { return get_post($id)->template ?? ''; }
function get_page_by_path($slug, $output, $type) { global $pages; foreach ($pages as $p) { if ($p->post_name === $slug && $p->post_type === $type) return $p; } return null; }
function home_url($path = '') { return 'https://example.test' . $path; }
function user_trailingslashit($path) { return rtrim($path, '/') . '/'; }
function get_permalink($post) { $post = get_post($post); return foods_recruit_interview_permalink(home_url('/' . $post->post_name . '/'), $post->ID, false); }
function get_queried_object_id() { global $current_id; return $current_id; }
function get_template_directory_uri() { return '/theme'; }
function get_header($name) {}
function get_footer($name) {}
function get_template_part($slug, $name = null, $args = []) { include dirname(__DIR__) . '/' . $slug . '.php'; }
function esc_attr($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function esc_html($v) { return esc_attr($v); }
function esc_url($v) { return esc_attr($v); }
function wp_kses($v, $allowed) { return strip_tags($v, '<br>'); }
function check($test, $message) { if (!$test) throw new RuntimeException($message); }

require dirname(__DIR__) . '/inc/recruit-interview.php';
foods_register_recruit_interview_routes();
check(count($rules) === 3, 'Register exactly three routes');
foreach (['01', '02', '03'] as $i => $number) {
    $current_id = $i + 1;
    $pages[$current_id] = (object) ['ID' => $current_id, 'post_type' => 'page', 'post_name' => 'recruit-interview-' . $number, 'template' => 'page-recruit-interview.php'];
    $url = home_url('/recruit/interview/' . $number . '/');
    check(get_permalink($current_id) === $url, 'Canonical permalink for ' . $number);
    check(foods_get_recruit_interview_url($number) === $url, 'Card link for ' . $number);
    check($rules['^recruit/interview/' . $number . '/?$'] === 'index.php?pagename=recruit-interview-' . $number, 'Route resolves to matching page');
    ob_start();
    include dirname(__DIR__) . '/page-recruit-interview.php';
    $html = ob_get_clean();
    check(substr_count($html, '<h1>') === 1, 'One document heading');
    check(substr_count($html, '<h2 id="interview-question-') === 6, 'Six interview questions');
    check(substr_count($html, 'class="p-interview__photo ') === 3, 'Three story photos');
    check(substr_count($html, 'class="p-page-recruit__voice-link"') === 3, 'Three linked cards');
    check(strpos($html, 'https://www.figma.com/api/') === false, 'No temporary asset URLs');
    preg_match_all('~/theme/([^"\\s,?]+\\.(?:png|svg))~', $html, $assets);
    foreach ($assets[1] as $asset) {
        $path = dirname(__DIR__) . '/' . $asset;
        check(is_file($path) && filesize($path) > 0, 'Missing image ' . $asset);
    }
    if ($number === '01') check(strpos($html, '現場の気持ちがわかる') !== false && strpos($html, '地域の食卓を支える仕事。') !== false, 'Distinct SP/PC heading');
    if ($number === '02') check(strpos($html, '2年前から本格的に採用業務') !== false && strpos($html, '相手が安心して楽しく働ける環境づくりを大切にしながら、教育や採用業務などにも') !== false, 'Distinct SP/PC relationship answer');
    if ($number === '03') check(strpos($html, '部下との関係性はどうですか？') !== false && strpos($html, '上司や先輩との関係性はどうですか？') !== false, 'Distinct SP/PC relationship question');
}
foreach (['recruit/interview/', 'recruit/interview/04/', 'recruit/interview/1/', 'recruit/interview/01/extra/'] as $path) {
    foreach ($rules as $regex => $query) check(!preg_match('~' . $regex . '~', $path), 'Do not match unconfigured route ' . $path);
}
$pages[9] = (object) ['ID' => 9, 'post_type' => 'page', 'post_name' => 'recruit-interview-01', 'template' => 'default'];
check(foods_recruit_interview_permalink('/original/', 9, false) === '/original/', 'Do not alter other page templates');
check(foods_get_recruit_interview_url('99') === '', 'Reject unsupported article number');
echo "PASS: three pages, six Q&As each, local assets, SP/PC copy, routes and isolated permalinks.\n";
