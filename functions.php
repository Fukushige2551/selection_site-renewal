<?php

/**
 * テーマのセットアップ
 */
function foods_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    register_nav_menus([
        'global-menu' => 'グローバルメニュー',
    ]);
}
add_action('after_setup_theme', 'foods_theme_setup');

/**
 * script タグに type="module" を付与
 */
function foods_add_module_type($tag, $handle, $src) {
    $module_handles = [
        'foods-vite-client',
        'foods-main-js',
        'foods-front-page-js',
        'foods-page-shop-js',
        'foods-page-select-js',
    ];

    if (in_array($handle, $module_handles, true)) {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    return $tag;
}
add_filter('script_loader_tag', 'foods_add_module_type', 10, 3);

/**
 * Vite の manifest を読む
 */
function foods_get_vite_manifest() {
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';

    if (!file_exists($manifest_path)) {
        return null;
    }

    $manifest = json_decode(file_get_contents($manifest_path), true);

    return is_array($manifest) ? $manifest : null;
}

/**
 * Vite のエントリを読み込む
 */
function foods_enqueue_vite_entry($handle_prefix, $entry_key, $dev_server, $manifest, $is_local) {
    if ($is_local) {
        wp_enqueue_script(
            $handle_prefix . '-js',
            $dev_server . '/' . $entry_key,
            [],
            null,
            true
        );
        return;
    }

    if (!$manifest || empty($manifest[$entry_key])) {
        return;
    }

    $entry = $manifest[$entry_key];

    if (!empty($entry['file'])) {
        wp_enqueue_script(
            $handle_prefix . '-js',
            get_template_directory_uri() . '/dist/' . $entry['file'],
            [],
            null,
            true
        );
    }

    if (!empty($entry['css']) && is_array($entry['css'])) {
        foreach ($entry['css'] as $index => $css_file) {
            wp_enqueue_style(
                $handle_prefix . '-css-' . $index,
                get_template_directory_uri() . '/dist/' . $css_file,
                [],
                null
            );
        }
    }
}

/**
 * アセット読み込み
 */
function foods_theme_scripts() {
    $env = function_exists('wp_get_environment_type')
        ? wp_get_environment_type()
        : 'production';

    $http_host  = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $is_local   = ($env === 'local') && (
        in_array(strtok($http_host, ':'), ['localhost', '127.0.0.1', '::1'], true) ||
        substr($http_host, -6) === '.local'
    );
    $dev_server = 'http://localhost:5173';

    $main_entry       = 'src/js/main.js';
    $front_page_entry = 'src/js/front-page.js';
    $page_shop_entry  = 'src/js/page-shop.js';
    $page_select_entry  = 'src/js/page-select.js';

    if ($is_local) {
        wp_enqueue_script(
            'foods-vite-client',
            $dev_server . '/@vite/client',
            [],
            null,
            true
        );
    }

    $manifest = $is_local ? null : foods_get_vite_manifest();

    // 共通アセット
    foods_enqueue_vite_entry(
        'foods-main',
        $main_entry,
        $dev_server,
        $manifest,
        $is_local
    );

    // トップページ専用アセット
    if (is_front_page()) {
        foods_enqueue_vite_entry(
            'foods-front-page',
            $front_page_entry,
            $dev_server,
            $manifest,
            $is_local
        );
    }

    // page-shop.php 専用アセット
    if (is_page() && basename((string) get_page_template()) === 'page-shop.php') {
        foods_enqueue_vite_entry(
            'foods-page-shop',
            $page_shop_entry,
            $dev_server,
            $manifest,
            $is_local
        );
    }
        // page-select.php 専用アセット
    if (is_page() && basename((string) get_page_template()) === 'page-select.php') {
        foods_enqueue_vite_entry(
            'foods-page-select',
            $page_select_entry,
            $dev_server,
            $manifest,
            $is_local
        );
    }
}
add_action('wp_enqueue_scripts', 'foods_theme_scripts');

function mytheme_enqueue_scripts() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');