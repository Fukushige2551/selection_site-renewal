<?php
// CLI rendering checks; no WordPress bootstrap, database, or HTTP requests.
function esc_attr($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function esc_html($value) { return esc_attr($value); }
function esc_url($value) { return esc_attr($value); }
function get_template_directory_uri() { return '/theme'; }
function check($condition, $message) {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$root = dirname(__DIR__);
require $root . '/inc/recipe-results-decorations.php';
$source = file_get_contents($root . '/archive-recipe.php');
$functions = substr($source, strpos($source, 'function foods_recipe_archive_get_field_value'));
$functions = substr($functions, 0, strpos($functions, '$recipe_search ='));
eval($functions);
$start = strpos($source, '    <?php if ($recipe_is_search || !empty($recipe_items)) : ?>');
$end = strpos($source, '</main>', $start);
check($start !== false && $end !== false, 'Results template boundaries missing');
$template = substr($source, $start, $end - $start);

function render_results($template, $count, $search = true, $limit = 12) {
    $recipe_is_search = $search;
    $recipe_search = '<script>alert(1)</script>';
    $recipe_archive_url = '/recipe/';
    $recipe_paged = 1;
    $recipe_per_page = $limit;
    $recipe_total_pages = $count === 12 ? 2 : 1;
    $recipe_page_url = static function ($page) { return '/recipe/?recipe_search=test&recipe_page=' . $page; };
    $recipe_items = array_fill(0, $count, [
        'title' => '<Test> recipe with a long title', 'permalink' => '/recipe/test/',
        'image' => null, 'cooking_time' => '30分', 'terms' => ['recipe_categories' => [['name' => '麺']]],
    ]);
    ob_start();
    eval('?>' . $template);
    return ob_get_clean();
}

foreach (range(0, 12) as $count) {
    $html = render_results($template, $count);
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xp = new DOMXPath($doc);
    $class = static function ($name) { return 'contains(concat(" ", normalize-space(@class), " "), " ' . $name . ' ")'; };
    $cards = $xp->query('//a[' . $class('p-recipe-archive__card-link') . ']');
    check($cards->length === $count, 'Card count changed: ' . $count);
    check(strpos($html, 'p-recipe-archive__decorations"') === false, 'Legacy floating layer in results');
    check(strpos($html, '<script>') === false && strpos($html, '<Test>') === false, 'Unescaped dynamic content');
    foreach (['mobile', 'tablet', 'tab-design', 'desktop'] as $viewport) {
        $ornaments = $xp->query('//span[' . $class('p-recipe-archive__decoration--' . $viewport) . ']');
        foreach ($ornaments as $ornament) {
            check($ornament->getAttribute('aria-hidden') === 'true', 'Decoration is exposed to assistive technology');
            check($ornament->parentNode->nodeName === 'a', 'Decoration has no rendered card anchor');
        }
        if ($count <= 1) { check($ornaments->length === 0, 'Sparse results contain list ornaments'); }
    }
    $ending = $xp->query('//*[' . $class('p-recipe-archive__results-ending') . ']');
    if ($count >= 10) {
        $desktopShapes = $xp->query('.//span[' . $class('p-recipe-archive__decoration--desktop') . ']', $cards->item(9));
        check($desktopShapes->length === 3, 'DPC card 10 requires three separate ornaments');
        foreach (['strainer', 'small-01', 'small-02'] as $index => $asset) {
            check(strpos($desktopShapes->item($index)->getAttribute('class'), 'p-recipe-archive__decoration--' . $asset . ' ') !== false, 'DPC ornament differs from Figma');
        }
    }
    $tabPlacements = [2 => ['cup'], 3 => ['glove'], 7 => ['strainer', 'small-01'], 12 => ['bowl']];
    foreach ($cards as $index => $card) {
        $tabOrnaments = $xp->query('.//span[' . $class('p-recipe-archive__decoration--tab-design') . ']', $card);
        $expected = $tabPlacements[$index + 1] ?? [];
        check($tabOrnaments->length === count($expected), 'TAB decoration count on card ' . ($index + 1));
        foreach ($tabOrnaments as $assetIndex => $ornament) {
            check(strpos($ornament->getAttribute('class'), 'p-recipe-archive__decoration--' . $expected[$assetIndex] . ' ') !== false, 'TAB decoration attached to wrong card');
        }
    }
    check(substr_count($html, 'p-recipe-archive__decoration--tab-ending') === ($count === 12 ? 1 : 0), 'TAB star must appear only at the page limit');
    check($ending->length === ($count === 12 ? 1 : 0), 'Ending must appear only at the page limit');
    check(substr_count($html, 'p-recipe-archive__decoration--ending') === ($count === 12 ? 2 : 0), 'Ending ornaments leaked below the limit');
    if ($count === 0) {
        check(substr_count($html, 'aria-hidden="true"') === 2, 'Zero results must contain only heading decorations');
    }
    if ($count === 12) {
        check(strpos($html, 'recipe_search=test&amp;recipe_page=2') !== false, 'Pagination lost its search query');
    }
    echo 'PASS results=' . $count . PHP_EOL;
}

$ordinary = render_results($template, 12, false);
check(strpos($ordinary, 'p-recipe-archive__decorations"') !== false, 'Ordinary archive decorations were removed');
check(strpos($ordinary, 'p-recipe-archive__results-heading') === false, 'Results heading leaked into ordinary archive');
check(strpos($ordinary, 'p-recipe-archive__decoration--desktop') === false, 'Anchored decorations leaked into ordinary archive');
echo 'PASS ordinary archive unchanged' . PHP_EOL;

foreach (range(0, 10) as $count) {
    $html = render_results($template, $count, true, 10);
    check(substr_count($html, 'p-recipe-archive__decoration--ending') === ($count === 10 ? 2 : 0), 'SP ending limit failed');
}
echo 'PASS SP ending 0..10; wide ending 0..12' . PHP_EOL;
