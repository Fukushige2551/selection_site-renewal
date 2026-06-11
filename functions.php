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
}
add_action('wp_enqueue_scripts', 'foods_theme_scripts');

function mytheme_enqueue_scripts() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');

/**
 * カスタム投稿
 */
// 店舗情報
function create_shop_post_type() {
    register_post_type('shop', [
        'labels' => [
        'name' => '店舗',
        'singular_name' => '店舗',
        'add_new' => '新規店舗を追加',
        'add_new_item' => '新規店舗を追加',
        'edit_item' => '店舗を編集',
        'new_item' => '新規店舗',
        'view_item' => '店舗を見る',
        'search_items' => '店舗を検索',
        'not_found' => '店舗が見つかりません',
        'not_found_in_trash' => 'ゴミ箱に店舗はありません',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-store',
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => [
        'slug' => 'shop',
        'with_front' => false,
        ],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'create_shop_post_type');

// チラシ
function register_flyer_post_type() {
    register_post_type('flyer', [
        'labels' => [
        'name' => 'チラシ',
        'singular_name' => 'チラシ',
        'add_new' => '新規追加',
        'add_new_item' => '新しいチラシを追加',
        'edit_item' => 'チラシを編集',
        'new_item' => '新しいチラシ',
        'view_item' => 'チラシを表示',
        'search_items' => 'チラシを検索',
        'not_found' => 'チラシが見つかりません',
        'not_found_in_trash' => 'ゴミ箱にチラシはありません',
        'menu_name' => 'チラシ',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-media-document',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
        'rewrite' => [
        'slug' => 'flyer',
        ],
    ]);
}
add_action('init', 'register_flyer_post_type');

// 新着情報
function register_news_post_type() {
    register_post_type('news', [
        'labels' => [
        'name' => '新着情報',
        'singular_name' => '新着情報',
        'add_new' => '新規追加',
        'add_new_item' => '新しい新着情報を追加',
        'edit_item' => '新着情報を編集',
        'new_item' => '新しい新着情報',
        'view_item' => '新着情報を表示',
        'search_items' => '新着情報を検索',
        'not_found' => '新着情報が見つかりません',
        'not_found_in_trash' => 'ゴミ箱に新着情報はありません',
        'menu_name' => '新着情報',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'taxonomies' => ['news_category', 'news_shop_category', 'news_commitment'],
        'show_in_rest' => true,
        'rewrite' => [
        'slug' => 'news',
        ],
    ]);
}
add_action('init', 'register_news_post_type');

// 採用情報
function register_recruit_post_type() {
    register_post_type('recruit', [
        'labels' => [
        'name' => '採用情報',
        'singular_name' => '採用情報',
        'add_new' => '新規追加',
        'add_new_item' => '新しい採用情報を追加',
        'edit_item' => '採用情報を編集',
        'new_item' => '新しい採用情報',
        'view_item' => '採用情報を表示',
        'search_items' => '採用情報を検索',
        'not_found' => '採用情報が見つかりません',
        'not_found_in_trash' => 'ゴミ箱に採用情報はありません',
        'menu_name' => '採用情報',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 8,
        'menu_icon' => 'dashicons-businessman',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
        'rewrite' => [
        'slug' => 'recruit',
        ],
    ]);
}
add_action('init', 'register_recruit_post_type');

// 採用情報（パート・アルバイト）
function register_recruit_part_time_post_type() {
    register_post_type('recruit_part_time', [
        'labels' => [
        'name' => '採用情報（パート・アルバイト）',
        'singular_name' => '採用情報（パート・アルバイト）',
        'add_new' => '新規追加',
        'add_new_item' => '新しい採用情報（パート・アルバイト）を追加',
        'edit_item' => '採用情報（パート・アルバイト）を編集',
        'new_item' => '新しい採用情報（パート・アルバイト）',
        'view_item' => '採用情報（パート・アルバイト）を表示',
        'search_items' => '採用情報（パート・アルバイト）を検索',
        'not_found' => '採用情報（パート・アルバイト）が見つかりません',
        'not_found_in_trash' => 'ゴミ箱に採用情報（パート・アルバイト）はありません',
        'menu_name' => '採用情報（パート・アルバイト）',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 9,
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
        'rewrite' => [
        'slug' => 'recruit-part-time',
        ],
    ]);
}
add_action('init', 'register_recruit_part_time_post_type');

// セレクションのこだわり
function register_selection_commitment_post_type() {
    register_post_type('selection_commitment', [
        'labels' => [
        'name' => 'セレクションのこだわり',
        'singular_name' => 'セレクションのこだわり',
        'add_new' => '新規追加',
        'add_new_item' => '新しいセレクションのこだわりを追加',
        'edit_item' => 'セレクションのこだわりを編集',
        'new_item' => '新しいセレクションのこだわり',
        'view_item' => 'セレクションのこだわりを表示',
        'search_items' => 'セレクションのこだわりを検索',
        'not_found' => 'セレクションのこだわりが見つかりません',
        'not_found_in_trash' => 'ゴミ箱にセレクションのこだわりはありません',
        'menu_name' => 'セレクションのこだわり',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-awards',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
        'rewrite' => [
        'slug' => 'selection-commitment',
        ],
    ]);
}
add_action('init', 'register_selection_commitment_post_type');

// レシピ
function register_recipe_post_type() {
    register_post_type('recipe', [
        'labels' => [
        'name' => 'レシピ',
        'singular_name' => 'レシピ',
        'add_new' => '新規追加',
        'add_new_item' => '新しいレシピを追加',
        'edit_item' => 'レシピを編集',
        'new_item' => '新しいレシピ',
        'view_item' => 'レシピを表示',
        'search_items' => 'レシピを検索',
        'not_found' => 'レシピが見つかりません',
        'not_found_in_trash' => 'ゴミ箱にレシピはありません',
        'menu_name' => 'レシピ',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 11,
        'menu_icon' => 'dashicons-carrot',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
        'rewrite' => [
        'slug' => 'recipe',
        ],
    ]);
}
add_action('init', 'register_recipe_post_type');

/**
 * カスタムタクソノミー
 */
// 店舗情報
function register_shop_taxonomies() {
    // 店舗の都道府県
    register_taxonomy(
        'shop_prefecture',
        ['shop'],
        [
        'labels' => [
            'name' => '都道府県',
            'singular_name' => '都道府県',
            'search_items' => '都道府県を検索',
            'all_items' => 'すべての都道府県',
            'parent_item' => '親都道府県',
            'parent_item_colon' => '親都道府県:',
            'edit_item' => '都道府県を編集',
            'update_item' => '都道府県を更新',
            'add_new_item' => '新しい都道府県を追加',
            'new_item_name' => '新しい都道府県名',
            'menu_name' => '都道府県',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'shop-prefecture',
        ],
        ]
    );

    // 店舗の営業状況
    register_taxonomy(
        'shop_status',
        ['shop'],
        [
        'labels' => [
            'name' => '営業状況',
            'singular_name' => '営業状況',
            'search_items' => '営業状況を検索',
            'all_items' => 'すべての営業状況',
            'parent_item' => '親営業状況',
            'parent_item_colon' => '親営業状況:',
            'edit_item' => '営業状況を編集',
            'update_item' => '営業状況を更新',
            'add_new_item' => '新しい営業状況を追加',
            'new_item_name' => '新しい営業状況名',
            'menu_name' => '営業状況',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'shop-status',
        ],
        ]
    );
}
add_action('init', 'register_shop_taxonomies');

// チラシ
function register_flyer_taxonomies() {
    register_taxonomy(
        'flyer_store',
        ['flyer'],
        [
        'labels' => [
            'name' => 'チラシ対象店舗',
            'singular_name' => 'チラシ対象店舗',
            'search_items' => 'チラシ対象店舗を検索',
            'all_items' => 'すべてのチラシ対象店舗',
            'edit_item' => 'チラシ対象店舗を編集',
            'update_item' => 'チラシ対象店舗を更新',
            'add_new_item' => '新しいチラシ対象店舗を追加',
            'new_item_name' => '新しいチラシ対象店舗名',
            'menu_name' => 'チラシ対象店舗',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'flyer-store',
        ],
        ]
    );
}
add_action('init', 'register_flyer_taxonomies');

// 新着情報
function register_news_category_taxonomy() {
    register_taxonomy('news_category', ['news'], [
        'labels' => [
            'name' => '新着情報カテゴリ',
            'singular_name' => '新着情報カテゴリ',
            'search_items' => '新着情報カテゴリを検索',
            'all_items' => 'すべての新着情報カテゴリ',
            'parent_item' => '親カテゴリ',
            'parent_item_colon' => '親カテゴリ:',
            'edit_item' => '新着情報カテゴリを編集',
            'update_item' => '新着情報カテゴリを更新',
            'add_new_item' => '新しい新着情報カテゴリを追加',
            'new_item_name' => '新しい新着情報カテゴリ名',
            'menu_name' => '新着情報カテゴリ',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'news-category',
        ],
    ]);

    register_taxonomy('news_shop_category', ['news'], [
        'labels' => [
            'name' => '店舗カテゴリ',
            'singular_name' => '店舗カテゴリ',
            'search_items' => '店舗カテゴリを検索',
            'all_items' => 'すべての店舗カテゴリ',
            'parent_item' => '親カテゴリ',
            'parent_item_colon' => '親カテゴリ:',
            'edit_item' => '店舗カテゴリを編集',
            'update_item' => '店舗カテゴリを更新',
            'add_new_item' => '新しい店舗カテゴリを追加',
            'new_item_name' => '新しい店舗カテゴリ名',
            'menu_name' => '店舗カテゴリ',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'news-shop-category',
        ],
    ]);

    register_taxonomy('news_commitment', ['news'], [
        'labels' => [
            'name' => 'こだわり',
            'singular_name' => 'こだわり',
            'search_items' => 'こだわりを検索',
            'all_items' => 'すべてのこだわり',
            'parent_item' => '親カテゴリ',
            'parent_item_colon' => '親カテゴリ:',
            'edit_item' => 'こだわりを編集',
            'update_item' => 'こだわりを更新',
            'add_new_item' => '新しいこだわりを追加',
            'new_item_name' => '新しいこだわり名',
            'menu_name' => 'こだわり',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'news-commitment',
        ],
    ]);
}
add_action('init', 'register_news_category_taxonomy');