<?php
/** Remaining SEO: consolidate registered requirements pages and exclude utility URLs. */
function foods_seo_legacy_routes() {
    return ['recruit/entry' => 'entry', 'recruit/career-entry' => 'career-entry', 'recruit-part-time' => 'job'];
}

add_action('template_redirect', static function () {
    if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
    global $wp;
    $path = trim($wp->request, '/');
    $routes = foods_seo_legacy_routes();
    if (isset($routes[$path])) {
        $target = home_url('/' . $routes[$path] . '/');
        if ($_GET) { $target = add_query_arg(wp_unslash($_GET), $target); }
        wp_safe_redirect($target, 301, 'Foods SEO');
        exit;
    }
}, -10);

function foods_seo_sitemap_excluded_ids() {
    static $ids;
    if (isset($ids)) { return $ids; }
    $ids = get_posts(['post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1,
        'fields' => 'ids', 'meta_key' => '_wp_page_template', 'meta_value' => [
            'page-job-entry.php', 'page-job-search.php', 'page-search.php',
            'page-contact-confirm.php', 'page-contact-thanks.php',
            'page-application-result.php', 'page-company-business-contact-result.php',
        ]]);
    $ids = array_merge($ids, get_posts(['post_type' => 'flyer', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids']));
    foreach (array_keys(foods_seo_legacy_routes()) as $path) {
        $page = get_page_by_path($path);
        if ($page) { $ids[] = $page->ID; }
    }
    return array_values(array_unique(array_map('intval', $ids)));
}

add_filter('aioseo_sitemap_exclude_posts', static function ($ids) {
    return array_values(array_unique(array_merge($ids, foods_seo_sitemap_excluded_ids())));
});

// Cover archive entries and RSS in addition to the XML post query.
function foods_seo_filter_sitemap_entries($entries) {
    $excluded = array_map('get_permalink', foods_seo_sitemap_excluded_ids());
    $excluded = array_merge($excluded, array_map(static fn($path) => home_url('/' . $path . '/'), array_keys(foods_seo_legacy_routes())));
    return array_values(array_filter($entries, static function ($entry) use ($excluded) {
        $url = $entry['loc'] ?? $entry['guid'] ?? '';
        $path = wp_parse_url($url, PHP_URL_PATH) ?: '';
        $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
        if (str_starts_with($path, $home_path)) { $path = substr($path, strlen($home_path)); }
        if (preg_match('~^(?:flyer|flyer-store)(?:/|$)~', ltrim($path, '/'))) { return false; }
        return !in_array($url, $excluded, true);
    }));
}
add_filter('aioseo_sitemap_posts', 'foods_seo_filter_sitemap_entries');
add_filter('aioseo_sitemap_rss', 'foods_seo_filter_sitemap_entries');
add_filter('aioseo_sitemap_post_archives', 'foods_seo_filter_sitemap_entries');
add_filter('aioseo_sitemap_terms', 'foods_seo_filter_sitemap_entries');
add_filter('aioseo_sitemap_indexes', static function ($entries) {
    return array_values(array_filter($entries, static function ($entry) {
        $path = wp_parse_url($entry['loc'] ?? '', PHP_URL_PATH) ?: '';
        return !preg_match('~/(?:flyer|flyer_store)-sitemap(?:[0-9]+)?\.xml$~', $path);
    }));
});

add_action('customize_register', static function ($customizer) {
    $customizer->add_section('foods_seo_social', ['title' => 'SEO・SNS共有画像']);
    $customizer->add_setting('foods_seo_default_share_image', ['default' => 0, 'sanitize_callback' => 'absint']);
    $customizer->add_control(new WP_Customize_Media_Control($customizer, 'foods_seo_default_share_image', [
        'section' => 'foods_seo_social', 'label' => '共通のSNS共有画像', 'mime_type' => 'image',
        'description' => '承認済みの画像を選択してください。ページに登録済みの画像がない場合だけ使用します。未選択の間は画像を補完しません。',
    ]));
});

function foods_seo_share_image() {
    $id = get_queried_object_id();
    if (is_singular('recipe') && function_exists('foods_recipe_detail_get_recipe_image_data')) {
        $photo = foods_recipe_detail_get_recipe_image_data($id, 'full');
        if (!empty($photo['url'])) { return $photo['url']; }
    }
    if (is_singular('news') && function_exists('foods_news_detail_get_news_image_data')) {
        $photo = foods_news_detail_get_news_image_data($id, 'full');
        if (!empty($photo['url'])) { return $photo['url']; }
    }
    if (is_singular('shop') && function_exists('foods_single_shop_get_first_field_value')) {
        $field = foods_single_shop_get_first_field_value($id, ['shop_image', 'shop_images', 'main_image', 'main_photo']);
        $photo = foods_single_shop_get_image_data($field, 'full');
        if (!empty($photo['url'])) { return $photo['url']; }
    }
    $featured = is_singular() ? get_the_post_thumbnail_url($id, 'full') : '';
    return $featured ?: wp_get_attachment_image_url((int) get_theme_mod('foods_seo_default_share_image', 0), 'full');
}

/** Share only existing public copy and an actual registered page image. */
add_action('wp_head', static function () {
    if (foods_seo_noindex()) { return; }
    $url = foods_seo_canonical_url();
    if ($url === '') { return; }
    $copy = foods_seo_current_copy();
    $description = $copy[1] ?? '';
    $tags = ['og:locale' => 'ja_JP', 'og:type' => is_singular('news') ? 'article' : 'website',
        'og:site_name' => 'フーズマーケットセレクション', 'og:title' => wp_get_document_title(), 'og:url' => $url];
    if ($description !== '') { $tags['og:description'] = $description; }
    $image = foods_seo_share_image();
    if ($image) { $tags['og:image'] = $image; }
    foreach ($tags as $key => $value) { echo '<meta property="' . esc_attr($key) . '" content="' . esc_attr($value) . '">' . "\n"; }
    echo '<meta name="twitter:card" content="' . ($image ? 'summary_large_image' : 'summary') . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr(wp_get_document_title()) . '">' . "\n";
    if ($description !== '') { echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n"; }
    if ($image) { echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n"; }
}, 20);
