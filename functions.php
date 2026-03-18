<?php

/**
 *テーマのセットアップ
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
 * アセット読み込み
 */
function foods_theme_scripts() {
    $env = function_exists('wp_get_environment_type')
        ? wp_get_environment_type()
        : 'production';

    $dev_server = 'http://localhost:5173';
    $entry_key  = 'src/js/main.js';

    // local 環境では Vite dev server を読む
    if ($env === 'local') {
        wp_enqueue_script(
            'foods-vite-client',
            $dev_server . '/@vite/client',
            [],
            null,
            true
        );

        wp_enqueue_script(
            'foods-main-js',
            $dev_server . '/' . $entry_key,
            [],
            null,
            true
        );

        return;
    }

    // staging / production は build 済み dist を読む
    $manifest = foods_get_vite_manifest();

    if (!$manifest || empty($manifest[$entry_key])) {
        return;
    }

    $entry = $manifest[$entry_key];

    if (!empty($entry['file'])) {
        wp_enqueue_script(
            'foods-main-js',
            get_template_directory_uri() . '/dist/' . $entry['file'],
            [],
            null,
            true
        );
    }

    if (!empty($entry['css']) && is_array($entry['css'])) {
        foreach ($entry['css'] as $index => $css_file) {
            wp_enqueue_style(
                'foods-main-css-' . $index,
                get_template_directory_uri() . '/dist/' . $css_file,
                [],
                null
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'foods_theme_scripts');