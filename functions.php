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
        'foods-single-shop-js',
        'foods-archive-news-js',
        'foods-single-news-js',
        'foods-archive-recipe-js',
    ];

    if (in_array($handle, $module_handles, true)) {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    return $tag;
}
add_filter('script_loader_tag', 'foods_add_module_type', 10, 3);

/**
 * WordPress同梱のjQuery Migrate案内ログを非表示にする
 */
function foods_mute_jquery_migrate_notice() {
    $mute_script = 'window.jQuery && (window.jQuery.migrateMute = true);';

    wp_add_inline_script(
        'jquery-core',
        $mute_script,
        'after'
    );
    wp_add_inline_script(
        'jquery-migrate',
        $mute_script,
        'before'
    );
}
add_action('wp_enqueue_scripts', 'foods_mute_jquery_migrate_notice', 20);

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
    $single_shop_entry = 'src/js/single-shop.js';
    $archive_news_entry = 'src/js/archive-news.js';
    $single_news_entry = 'src/js/single-news.js';
    $archive_recipe_entry = 'src/js/archive-recipe.js';

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
    if ((is_page() && basename((string) get_page_template()) === 'page-shop.php') || is_post_type_archive('shop')) {
        foods_enqueue_vite_entry(
            'foods-page-shop',
            $page_shop_entry,
            $dev_server,
            $manifest,
            $is_local
        );
    }

    // single-shop.php 専用アセット
    if (is_singular('shop')) {
        foods_enqueue_vite_entry(
            'foods-single-shop',
            $single_shop_entry,
            $dev_server,
            $manifest,
            $is_local
        );
    }
    if (is_post_type_archive('news')) {
        foods_enqueue_vite_entry(
            'foods-archive-news',
            $archive_news_entry,
            $dev_server,
            $manifest,
            $is_local
        );
    }

    if (is_singular('news')) {
        foods_enqueue_vite_entry(
            'foods-single-news',
            $single_news_entry,
            $dev_server,
            $manifest,
            $is_local
        );
    }

    if (is_post_type_archive('recipe')) {
        foods_enqueue_vite_entry(
            'foods-archive-recipe',
            $archive_recipe_entry,
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

function foods_get_news_permalink_category_slug($post_id) {
    $terms = get_the_terms($post_id, 'news_category');

    if (is_wp_error($terms) || empty($terms)) {
        return 'news';
    }

    $term = reset($terms);

    return $term && !empty($term->slug) ? $term->slug : 'news';
}

function foods_news_post_type_link($post_link, $post) {
    if ($post->post_type !== 'news') {
        return $post_link;
    }

    $category_slug = foods_get_news_permalink_category_slug($post->ID);

    return home_url(user_trailingslashit('news/' . $category_slug . '/' . $post->ID));
}
add_filter('post_type_link', 'foods_news_post_type_link', 10, 2);

function foods_add_news_rewrite_rules() {
    add_rewrite_rule(
        '^news/([^/]+)/([0-9]+)/?$',
        'index.php?post_type=news&p=$matches[2]',
        'top'
    );
}
add_action('init', 'foods_add_news_rewrite_rules');

function foods_flush_news_rewrite_rules_once() {
    $rewrite_version = '20260707-news-detail-id-url';

    if (get_option('foods_news_rewrite_rules_version') === $rewrite_version) {
        return;
    }

    foods_add_news_rewrite_rules();
    flush_rewrite_rules(false);
    update_option('foods_news_rewrite_rules_version', $rewrite_version);
}
add_action('init', 'foods_flush_news_rewrite_rules_once', 20);

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

function foods_get_scf_field_id($field_group_id, $field_key, $field_name) {
    $field_posts = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'post_parent' => $field_group_id,
        'posts_per_page' => -1,
        'orderby' => 'ID',
        'order' => 'ASC',
    ]);

    foreach ($field_posts as $field_post) {
        if ($field_post->post_name === $field_key || $field_post->post_excerpt === $field_name) {
            return (int) $field_post->ID;
        }
    }

    return 0;
}

function foods_trash_duplicate_scf_fields($field_group_id, $allowed_names) {
    $seen_names = [];
    $trashed_fields = 0;

    $field_posts = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'post_parent' => $field_group_id,
        'posts_per_page' => -1,
        'orderby' => 'menu_order ID',
        'order' => 'ASC',
    ]);

    foreach ($field_posts as $field_post) {
        $field_name = $field_post->post_excerpt;

        if (!in_array($field_name, $allowed_names, true)) {
            continue;
        }

        if (!isset($seen_names[$field_name])) {
            $seen_names[$field_name] = (int) $field_post->ID;
            continue;
        }

        if (function_exists('acf_trash_field')) {
            acf_trash_field($field_post->ID);
        } else {
            wp_trash_post($field_post->ID);
        }
        $trashed_fields++;
    }

    return $trashed_fields;
}

function foods_trash_stale_scf_fields($field_group_id, $allowed_names) {
    $trashed_fields = 0;

    $field_posts = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'post_parent' => $field_group_id,
        'posts_per_page' => -1,
        'orderby' => 'menu_order ID',
        'order' => 'ASC',
    ]);

    foreach ($field_posts as $field_post) {
        if (in_array($field_post->post_excerpt, $allowed_names, true)) {
            continue;
        }

        if (function_exists('acf_trash_field')) {
            acf_trash_field($field_post->ID);
        } else {
            wp_trash_post($field_post->ID);
        }
        $trashed_fields++;
    }

    return $trashed_fields;
}

function foods_sync_scf_sub_fields($parent_field_id, $sub_fields) {
    if (!$parent_field_id || empty($sub_fields)) {
        return 0;
    }

    $saved_count = 0;
    foreach ($sub_fields as $menu_order => $sub_field) {
        $nested_sub_fields = $sub_field['sub_fields'] ?? [];
        unset($sub_field['sub_fields']);

        $existing_field_id = foods_get_scf_field_id($parent_field_id, $sub_field['key'], $sub_field['name']);

        if ($existing_field_id) {
            $sub_field['ID'] = $existing_field_id;
        }

        $sub_field['parent'] = $parent_field_id;
        $sub_field['menu_order'] = $menu_order;
        $sub_field['wrapper'] = [
            'width' => '',
            'class' => '',
            'id' => '',
        ];
        $sub_field['conditional_logic'] = 0;

        $saved_sub_field = acf_update_field($sub_field);
        if (!$saved_sub_field) {
            continue;
        }

        $saved_count++;
        $saved_sub_field_id = is_array($saved_sub_field) && !empty($saved_sub_field['ID'])
            ? (int) $saved_sub_field['ID']
            : foods_get_scf_field_id($parent_field_id, $sub_field['key'], $sub_field['name']);

        $saved_count += foods_sync_scf_sub_fields($saved_sub_field_id, $nested_sub_fields);
    }

    $allowed_names = array_column($sub_fields, 'name');
    foods_trash_stale_scf_fields($parent_field_id, $allowed_names);
    foods_trash_duplicate_scf_fields($parent_field_id, $allowed_names);

    return $saved_count;
}

function foods_sync_scf_field_group($field_group) {
    if (
        !function_exists('acf_update_field_group') ||
        !function_exists('acf_update_field') ||
        !function_exists('acf_get_field_group')
    ) {
        return [
            'field_group_id' => 0,
            'field_count' => 0,
            'trashed_fields' => 0,
            'trashed_groups' => 0,
        ];
    }

    $fields = $field_group['fields'];
    unset($field_group['fields']);

    $updated_field_group = acf_update_field_group($field_group);
    $saved_field_group = acf_get_field_group($field_group['key']);
    $field_group_id = is_array($saved_field_group) && !empty($saved_field_group['ID'])
        ? (int) $saved_field_group['ID']
        : 0;

    if (!$field_group_id && is_array($updated_field_group) && !empty($updated_field_group['ID'])) {
        $field_group_id = (int) $updated_field_group['ID'];
    }

    if (!$field_group_id) {
        return [
            'field_group_id' => 0,
            'field_count' => 0,
            'trashed_fields' => 0,
            'trashed_groups' => 0,
        ];
    }

    $field_count = 0;
    foreach ($fields as $menu_order => $field) {
        $sub_fields = $field['sub_fields'] ?? [];
        unset($field['sub_fields']);

        $existing_field_id = foods_get_scf_field_id($field_group_id, $field['key'], $field['name']);

        if ($existing_field_id) {
            $field['ID'] = $existing_field_id;
        }

        $field['parent'] = $field_group_id;
        $field['menu_order'] = $menu_order;
        $field['wrapper'] = [
            'width' => '',
            'class' => '',
            'id' => '',
        ];
        $field['conditional_logic'] = 0;

        $saved_field = acf_update_field($field);
        if ($saved_field) {
            $field_count++;
            $saved_field_id = is_array($saved_field) && !empty($saved_field['ID'])
                ? (int) $saved_field['ID']
                : foods_get_scf_field_id($field_group_id, $field['key'], $field['name']);

            foods_sync_scf_sub_fields($saved_field_id, $sub_fields);
        }
    }

    $allowed_names = array_column($fields, 'name');
    $trashed_fields = foods_trash_stale_scf_fields($field_group_id, $allowed_names);
    $trashed_fields += foods_trash_duplicate_scf_fields($field_group_id, $allowed_names);

    $trashed_groups = 0;
    $duplicate_groups = get_posts([
        'post_type' => 'acf-field-group',
        'post_status' => ['publish', 'acf-disabled'],
        'posts_per_page' => -1,
        'title' => $field_group['title'],
        'fields' => 'ids',
    ]);

    foreach ($duplicate_groups as $duplicate_group_id) {
        if ((int) $duplicate_group_id === $field_group_id) {
            continue;
        }

        if (function_exists('acf_trash_field_group')) {
            acf_trash_field_group($duplicate_group_id);
        } else {
            wp_trash_post($duplicate_group_id);
        }
        $trashed_groups++;
    }

    return [
        'field_group_id' => $field_group_id,
        'field_count' => $field_count,
        'trashed_fields' => $trashed_fields,
        'trashed_groups' => $trashed_groups,
    ];
}

/**
 * 採用情報（パート・アルバイト）のSCFフィールドをDBに登録
 */
function foods_get_recruit_part_time_field_group() {
    return [
        'key' => 'group_recruit_part_time_fields',
        'title' => '採用情報（パート・アルバイト）',
        'fields' => [
            [
                'key' => 'field_recruit_part_time_job_type',
                'label' => '募集職種',
                'name' => 'job_type',
                'type' => 'text',
                'required' => 0,
            ],
            [
                'key' => 'field_recruit_part_time_salary',
                'label' => '給与',
                'name' => 'salary',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 3,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recruit_part_time_work_location_access',
                'label' => '勤務地・アクセス',
                'name' => 'work_location_access',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 4,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recruit_part_time_working_hours',
                'label' => '勤務時間',
                'name' => 'working_hours',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 4,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recruit_part_time_benefits',
                'label' => '待遇/福利厚生',
                'name' => 'benefits',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 4,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recruit_part_time_qualification',
                'label' => '資格',
                'name' => 'qualification',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 3,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recruit_part_time_commuting',
                'label' => '通勤について',
                'name' => 'commuting',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 3,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recruit_part_time_application_method',
                'label' => '応募方法',
                'name' => 'application_method',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 4,
                'new_lines' => 'br',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'recruit_part_time',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ];
}

function foods_register_recruit_part_time_scf_fields() {
    if (!is_admin() || !function_exists('foods_sync_recruit_part_time_scf_fields')) {
        return;
    }

    $version = '20260623-3';
    if (get_option('foods_recruit_part_time_scf_fields_version') === $version) {
        return;
    }

    $result = foods_sync_recruit_part_time_scf_fields();

    if (!empty($result['field_group_id']) && $result['field_count'] === 8) {
        update_option('foods_recruit_part_time_scf_fields_version', $version);
    }
}
add_action('acf/init', 'foods_register_recruit_part_time_scf_fields');

function foods_sync_recruit_part_time_scf_fields() {
    return foods_sync_scf_field_group(foods_get_recruit_part_time_field_group());
}

/**
 * 新着情報のSCFフィールドをDBに登録
 */
function foods_get_news_field_group() {
    return [
        'key' => 'group_news_fields',
        'title' => '新着情報',
        'fields' => [
            [
                'key' => 'field_news_publish_date',
                'label' => '投稿日',
                'name' => 'news_publish_date',
                'type' => 'date_picker',
                'required' => 0,
                'display_format' => 'Y/m/d',
                'return_format' => 'Ymd',
                'first_day' => 0,
                'default_value' => current_time('Ymd'),
            ],
            [
                'key' => 'field_news_eyecatch_image',
                'label' => 'アイキャッチ画像',
                'name' => 'news_eyecatch_image',
                'type' => 'image',
                'required' => 0,
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],
            [
                'key' => 'field_news_summary',
                'label' => 'お知らせ概要',
                'name' => 'news_summary',
                'type' => 'wysiwyg',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'basic',
                'media_upload' => 1,
                'delay' => 0,
            ],            [
                'key' => 'field_news_body',
                'label' => '本文',
                'name' => 'news_body',
                'type' => 'wysiwyg',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ],
            [
                'key' => 'field_news_show_first_view',
                'label' => 'ファーストビューへ表示',
                'name' => 'show_first_view',
                'type' => 'true_false',
                'required' => 0,
                'message' => '',
                'default_value' => 0,
                'ui' => 1,
                'ui_on_text' => '表示',
                'ui_off_text' => '非表示',
            ],
            [
                'key' => 'field_news_first_view_order',
                'label' => 'ファーストビュー表示順',
                'name' => 'first_view_order',
                'type' => 'number',
                'required' => 0,
                'default_value' => '',
                'min' => 0,
                'step' => 1,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'news',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ];
}

function foods_register_news_scf_fields() {
    if (!is_admin() || !function_exists('foods_sync_news_scf_fields')) {
        return;
    }

    $version = '20260707-1';
    if (get_option('foods_news_scf_fields_version') === $version) {
        return;
    }

    $result = foods_sync_news_scf_fields();

    if (!empty($result['field_group_id']) && $result['field_count'] === 6) {
        update_option('foods_news_scf_fields_version', $version);
    }
}
add_action('acf/init', 'foods_register_news_scf_fields');

function foods_sync_news_scf_fields() {
    return foods_sync_scf_field_group(foods_get_news_field_group());
}

/**
 * チラシのSCFフィールドをDBに登録
 */
function foods_get_flyer_field_group() {
    return [
        'key' => 'group_flyer_fields',
        'title' => 'チラシ',
        'fields' => [
            [
                'key' => 'field_flyer_images',
                'label' => 'チラシ画像',
                'name' => 'flyer_images',
                'type' => 'repeater',
                'required' => 0,
                'layout' => 'block',
                'button_label' => 'チラシ画像を追加',
                'min' => 0,
                'max' => 2,
                'instructions' => '最大2枚まで登録できます。',
                'sub_fields' => [
                    [
                        'key' => 'field_flyer_images_image',
                        'label' => '画像',
                        'name' => 'image',
                        'type' => 'image',
                        'required' => 0,
                        'return_format' => 'id',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ],
                ],
            ],
            [
                'key' => 'field_flyer_publish_start_date',
                'label' => '公開開始日',
                'name' => 'publish_start_date',
                'type' => 'date_picker',
                'required' => 0,
                'display_format' => 'Y/m/d',
                'return_format' => 'Ymd',
                'first_day' => 0,
            ],
            [
                'key' => 'field_flyer_publish_end_date',
                'label' => '公開終了日',
                'name' => 'publish_end_date',
                'type' => 'date_picker',
                'required' => 0,
                'display_format' => 'Y/m/d',
                'return_format' => 'Ymd',
                'first_day' => 0,
            ],
            [
                'key' => 'field_flyer_start_date',
                'label' => 'チラシ開始日',
                'name' => 'flyer_start_date',
                'type' => 'date_picker',
                'required' => 0,
                'display_format' => 'Y/m/d',
                'return_format' => 'Ymd',
                'first_day' => 0,
            ],
            [
                'key' => 'field_flyer_end_date',
                'label' => 'チラシ終了日',
                'name' => 'flyer_end_date',
                'type' => 'date_picker',
                'required' => 0,
                'display_format' => 'Y/m/d',
                'return_format' => 'Ymd',
                'first_day' => 0,
            ],
            [
                'key' => 'field_flyer_bargain_items',
                'label' => 'お買い得品',
                'name' => 'bargain_items',
                'type' => 'repeater',
                'required' => 0,
                'layout' => 'block',
                'button_label' => '商品を追加',
                'sub_fields' => [
                    [
                        'key' => 'field_flyer_bargain_item_name',
                        'label' => '商品名',
                        'name' => 'item_name',
                        'type' => 'text',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_flyer_bargain_item_origin',
                        'label' => '産地・補足',
                        'name' => 'item_origin',
                        'type' => 'text',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_flyer_bargain_item_quantity',
                        'label' => '容量・単位',
                        'name' => 'item_quantity',
                        'type' => 'text',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_flyer_bargain_item_image',
                        'label' => '商品画像',
                        'name' => 'item_image',
                        'type' => 'image',
                        'required' => 0,
                        'return_format' => 'id',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ],
                    [
                        'key' => 'field_flyer_bargain_label_1',
                        'label' => 'ラベル1',
                        'name' => 'label_1',
                        'type' => 'text',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_flyer_bargain_label_2',
                        'label' => 'ラベル2',
                        'name' => 'label_2',
                        'type' => 'text',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_flyer_bargain_base_price',
                        'label' => '本体価格',
                        'name' => 'base_price',
                        'type' => 'number',
                        'required' => 0,
                        'min' => 0,
                        'step' => 1,
                    ],
                    [
                        'key' => 'field_flyer_bargain_tax_price',
                        'label' => '税込価格',
                        'name' => 'tax_price',
                        'type' => 'number',
                        'required' => 0,
                        'min' => 0,
                        'step' => 0.01,
                    ],
                    [
                        'key' => 'field_flyer_bargain_before_discount_price',
                        'label' => '値引き前価格',
                        'name' => 'before_discount_price',
                        'type' => 'number',
                        'required' => 0,
                        'min' => 0,
                        'step' => 0.01,
                    ],
                    [
                        'key' => 'field_flyer_bargain_item_start_date',
                        'label' => '商品開始日',
                        'name' => 'item_start_date',
                        'type' => 'date_picker',
                        'required' => 0,
                        'display_format' => 'Y/m/d',
                        'return_format' => 'Ymd',
                        'first_day' => 0,
                    ],
                    [
                        'key' => 'field_flyer_bargain_item_end_date',
                        'label' => '商品終了日',
                        'name' => 'item_end_date',
                        'type' => 'date_picker',
                        'required' => 0,
                        'display_format' => 'Y/m/d',
                        'return_format' => 'Ymd',
                        'first_day' => 0,
                    ],
                    [
                        'key' => 'field_flyer_bargain_note',
                        'label' => '注記',
                        'name' => 'note',
                        'type' => 'textarea',
                        'required' => 0,
                        'rows' => 3,
                        'new_lines' => 'br',
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'flyer',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ];
}

function foods_register_flyer_scf_fields() {
    if (!is_admin() || !function_exists('foods_sync_flyer_scf_fields')) {
        return;
    }

    $version = '20260630-1';
    if (get_option('foods_flyer_scf_fields_version') === $version) {
        return;
    }

    $result = foods_sync_flyer_scf_fields();

    if (!empty($result['field_group_id']) && $result['field_count'] === 6) {
        update_option('foods_flyer_scf_fields_version', $version);
    }
}
add_action('acf/init', 'foods_register_flyer_scf_fields');

function foods_sync_flyer_scf_fields() {
    $result = foods_sync_scf_field_group(foods_get_flyer_field_group());
    $result['trashed_obsolete_item_images_fields'] = 0;

    if (!empty($result['field_group_id'])) {
        $result['trashed_obsolete_item_images_fields'] = foods_remove_obsolete_flyer_item_images_field();
        foods_migrate_legacy_flyer_image_field();
    }

    return $result;
}

function foods_remove_obsolete_flyer_item_images_field() {
    $field_posts = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'posts_per_page' => -1,
        'orderby' => 'ID',
        'order' => 'ASC',
    ]);

    $target_ids = [];
    foreach ($field_posts as $field_post) {
        if (
            $field_post->post_name === 'field_flyer_bargain_item_images' ||
            $field_post->post_excerpt === 'item_images'
        ) {
            $target_ids[] = (int) $field_post->ID;
        }
    }

    if (!$target_ids) {
        return 0;
    }

    $ids_to_trash = $target_ids;
    $queue = $target_ids;

    while ($queue) {
        $parent_id = array_shift($queue);
        foreach ($field_posts as $field_post) {
            if ((int) $field_post->post_parent !== $parent_id) {
                continue;
            }

            $child_id = (int) $field_post->ID;
            if (in_array($child_id, $ids_to_trash, true)) {
                continue;
            }

            $ids_to_trash[] = $child_id;
            $queue[] = $child_id;
        }
    }

    $trashed = 0;
    foreach ($ids_to_trash as $field_id) {
        if (function_exists('acf_trash_field')) {
            acf_trash_field($field_id);
        } else {
            wp_trash_post($field_id);
        }
        $trashed++;
    }

    return $trashed;
}

function foods_migrate_legacy_flyer_image_field() {
    if (!function_exists('update_field')) {
        return 0;
    }

    $flyer_posts = get_posts([
        'post_type' => 'flyer',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'fields' => 'ids',
    ]);

    $migrated = 0;

    foreach ($flyer_posts as $post_id) {
        $current_images = get_field('flyer_images', $post_id);
        if (!empty($current_images)) {
            continue;
        }

        $legacy_image = get_post_meta($post_id, 'flyer_image', true);
        if (is_array($legacy_image) && !empty($legacy_image['ID'])) {
            $legacy_image = $legacy_image['ID'];
        }

        if (!is_numeric($legacy_image) || (int) $legacy_image <= 0) {
            continue;
        }

        update_field('flyer_images', [
            [
                'image' => (int) $legacy_image,
            ],
        ], $post_id);
        $migrated++;
    }

    return $migrated;
}

function foods_set_default_news_publish_date($post_id, $post, $update) {
    if ($post->post_type !== 'news' || wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    if (get_post_meta($post_id, 'news_publish_date', true) !== '') {
        return;
    }

    $date = $post->post_date && $post->post_date !== '0000-00-00 00:00:00'
        ? mysql2date('Ymd', $post->post_date)
        : current_time('Ymd');

    update_post_meta($post_id, 'news_publish_date', $date);
}
add_action('wp_insert_post', 'foods_set_default_news_publish_date', 10, 3);

/**
 * セレクションのこだわりのSCFフィールドをDBに登録
 */
function foods_get_selection_commitment_scene_fields($scene_number) {
    $prefix = 'commitment_scene_' . $scene_number;
    $label_prefix = 'こだわり現場' . $scene_number;

    $fields = [
        [
            'key' => 'field_' . $prefix . '_subtitle',
            'label' => $label_prefix . ' 産地などサブタイトル',
            'name' => $prefix . '_subtitle',
            'type' => 'text',
            'required' => 0,
        ],
        [
            'key' => 'field_' . $prefix . '_producer_title',
            'label' => $label_prefix . ' 生産者などタイトル',
            'name' => $prefix . '_producer_title',
            'type' => 'text',
            'required' => 0,
        ],
        [
            'key' => 'field_' . $prefix . '_producer_image',
            'label' => $label_prefix . ' 生産者キャッチ画像',
            'name' => $prefix . '_producer_image',
            'type' => 'image',
            'required' => 0,
            'return_format' => 'id',
            'preview_size' => 'medium',
            'library' => 'all',
        ],
        [
            'key' => 'field_' . $prefix . '_producer_text',
            'label' => $label_prefix . ' 生産者説明テキスト',
            'name' => $prefix . '_producer_text',
            'type' => 'textarea',
            'required' => 0,
            'rows' => 4,
            'new_lines' => 'br',
        ],
        [
            'key' => 'field_' . $prefix . '_product_title',
            'label' => $label_prefix . ' 商品説明タイトル',
            'name' => $prefix . '_product_title',
            'type' => 'text',
            'required' => 0,
        ],
        [
            'key' => 'field_' . $prefix . '_product_image',
            'label' => $label_prefix . ' 商品キャッチ画像',
            'name' => $prefix . '_product_image',
            'type' => 'image',
            'required' => 0,
            'return_format' => 'id',
            'preview_size' => 'medium',
            'library' => 'all',
        ],
        [
            'key' => 'field_' . $prefix . '_product_text',
            'label' => $label_prefix . ' 商品説明テキスト',
            'name' => $prefix . '_product_text',
            'type' => 'textarea',
            'required' => 0,
            'rows' => 4,
            'new_lines' => 'br',
        ],
    ];

    for ($image_number = 1; $image_number <= 4; $image_number++) {
        $fields[] = [
            'key' => 'field_' . $prefix . '_gallery_image_' . $image_number,
            'label' => $label_prefix . ' ギャラリー画像' . $image_number,
            'name' => $prefix . '_gallery_image_' . $image_number,
            'type' => 'image',
            'required' => 0,
            'return_format' => 'id',
            'preview_size' => 'medium',
            'library' => 'all',
        ];
    }

    return $fields;
}

function foods_get_selection_commitment_field_group() {
    $fields = [
        [
            'key' => 'field_selection_commitment_fv_image',
            'label' => 'FV画像',
            'name' => 'commitment_fv_image',
            'type' => 'image',
            'required' => 0,
            'return_format' => 'id',
            'preview_size' => 'medium',
            'library' => 'all',
        ],
        [
            'key' => 'field_selection_commitment_lead_text',
            'label' => 'リード文',
            'name' => 'commitment_lead_text',
            'type' => 'textarea',
            'required' => 0,
            'rows' => 4,
            'new_lines' => 'br',
        ],
        [
            'key' => 'field_selection_commitment_buyer_icon',
            'label' => 'バイヤーアイコン',
            'name' => 'commitment_buyer_icon',
            'type' => 'image',
            'required' => 0,
            'return_format' => 'id',
            'preview_size' => 'thumbnail',
            'library' => 'all',
        ],
        [
            'key' => 'field_selection_commitment_buyer_message_title',
            'label' => 'バイヤーメッセージタイトル',
            'name' => 'commitment_buyer_message_title',
            'type' => 'text',
            'required' => 0,
            'default_value' => 'バイヤーメッセージ',
        ],
        [
            'key' => 'field_selection_commitment_buyer_message',
            'label' => 'バイヤーメッセージ',
            'name' => 'commitment_buyer_message',
            'type' => 'wysiwyg',
            'required' => 0,
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 0,
        ],
    ];

    foreach ([1, 2, 3] as $scene_number) {
        $fields = array_merge($fields, foods_get_selection_commitment_scene_fields($scene_number));
    }

    return [
        'key' => 'group_selection_commitment_fields',
        'title' => 'セレクションのこだわり',
        'fields' => $fields,
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'selection_commitment',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ];
}

function foods_register_selection_commitment_scf_fields() {
    if (!is_admin() || !function_exists('foods_sync_selection_commitment_scf_fields')) {
        return;
    }

    $version = '20260624-1';
    if (get_option('foods_selection_commitment_scf_fields_version') === $version) {
        return;
    }

    $result = foods_sync_selection_commitment_scf_fields();

    if (!empty($result['field_group_id']) && $result['field_count'] === 38) {
        update_option('foods_selection_commitment_scf_fields_version', $version);
    }
}
add_action('acf/init', 'foods_register_selection_commitment_scf_fields');

function foods_sync_selection_commitment_scf_fields() {
    return foods_sync_scf_field_group(foods_get_selection_commitment_field_group());
}

/**
 * レシピのSCFフィールドをDBに登録
 */
function foods_get_recipe_field_group() {
    return [
        'key' => 'group_recipe_fields',
        'title' => 'レシピ',
        'fields' => [
            [
                'key' => 'field_recipe_photo',
                'label' => '写真',
                'name' => 'recipe_photo',
                'type' => 'image',
                'required' => 0,
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],
            [
                'key' => 'field_recipe_summary',
                'label' => 'レシピ概要',
                'name' => 'recipe_summary',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 4,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recipe_cooking_time',
                'label' => '調理時間',
                'name' => 'recipe_cooking_time',
                'type' => 'text',
                'required' => 0,
                'instructions' => '例: 30分',
            ],
            [
                'key' => 'field_recipe_servings',
                'label' => '何人前',
                'name' => 'recipe_servings',
                'type' => 'text',
                'required' => 0,
                'instructions' => '例: 2人前',
            ],
            [
                'key' => 'field_recipe_ingredients',
                'label' => '材料グループ',
                'name' => 'recipe_ingredients',
                'type' => 'repeater',
                'required' => 0,
                'layout' => 'block',
                'button_label' => '材料グループを追加',
                'sub_fields' => [
                    [
                        'key' => 'field_recipe_ingredients_group_name',
                        'label' => 'グループ名',
                        'name' => 'group_name',
                        'type' => 'text',
                        'required' => 0,
                        'instructions' => '例: A、トッピング。通常材料の場合は空欄にします。',
                    ],
                    [
                        'key' => 'field_recipe_ingredients_items',
                        'label' => '材料詳細',
                        'name' => 'ingredients',
                        'type' => 'repeater',
                        'required' => 0,
                        'layout' => 'table',
                        'button_label' => '材料を追加',
                        'sub_fields' => [
                            [
                                'key' => 'field_recipe_ingredients_item_name',
                                'label' => '食材名',
                                'name' => 'ingredient_name',
                                'type' => 'text',
                                'required' => 0,
                            ],
                            [
                                'key' => 'field_recipe_ingredients_item_amount',
                                'label' => '分量',
                                'name' => 'amount',
                                'type' => 'text',
                                'required' => 0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'field_recipe_preparation',
                'label' => '準備',
                'name' => 'recipe_preparation',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 5,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_recipe_steps',
                'label' => '調理手順',
                'name' => 'recipe_steps',
                'type' => 'repeater',
                'required' => 0,
                'layout' => 'block',
                'button_label' => '手順を追加',
                'sub_fields' => [
                    [
                        'key' => 'field_recipe_steps_text',
                        'label' => '手順本文',
                        'name' => 'step_text',
                        'type' => 'textarea',
                        'required' => 0,
                        'rows' => 4,
                        'new_lines' => 'br',
                    ],
                ],
            ],
            [
                'key' => 'field_recipe_tips',
                'label' => 'コツ・ポイント',
                'name' => 'recipe_tips',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 5,
                'new_lines' => 'br',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'recipe',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ];
}

function foods_register_recipe_scf_fields() {
    if (!is_admin() || !function_exists('foods_sync_recipe_scf_fields')) {
        return;
    }

    $version = '20260624-10';
    if (get_option('foods_recipe_scf_fields_version') === $version) {
        return;
    }

    $result = foods_sync_recipe_scf_fields();

    if (!empty($result['field_group_id']) && $result['field_count'] === 8) {
        update_option('foods_recipe_scf_fields_version', $version);
    }
}
add_action('acf/init', 'foods_register_recipe_scf_fields');

function foods_sync_recipe_scf_fields() {
    return foods_sync_scf_field_group(foods_get_recipe_field_group());
}

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

function foods_register_recipe_taxonomies() {
    $common_args = [
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
    ];

    register_taxonomy('recipe_category', ['recipe'], array_merge($common_args, [
        'labels' => [
            'name' => '料理ジャンル',
            'singular_name' => '料理ジャンル',
            'search_items' => '料理ジャンルを検索',
            'all_items' => 'すべての料理ジャンル',
            'edit_item' => '料理ジャンルを編集',
            'update_item' => '料理ジャンルを更新',
            'add_new_item' => '新しい料理ジャンルを追加',
            'new_item_name' => '新しい料理ジャンル名',
            'menu_name' => '料理ジャンル',
        ],
        'hierarchical' => true,
        'rewrite' => [
            'slug' => 'recipe-category',
        ],
    ]));

    register_taxonomy('recipe_main_ingredient', ['recipe'], array_merge($common_args, [
        'labels' => [
            'name' => 'メイン食材',
            'singular_name' => 'メイン食材',
            'search_items' => 'メイン食材を検索',
            'all_items' => 'すべてのメイン食材',
            'edit_item' => 'メイン食材を編集',
            'update_item' => 'メイン食材を更新',
            'add_new_item' => '新しいメイン食材を追加',
            'new_item_name' => '新しいメイン食材名',
            'menu_name' => 'メイン食材',
        ],
        'hierarchical' => false,
        'rewrite' => [
            'slug' => 'recipe-main-ingredient',
        ],
    ]));

    register_taxonomy('recipe_tag', ['recipe'], array_merge($common_args, [
        'labels' => [
            'name' => 'キーワード',
            'singular_name' => 'キーワード',
            'search_items' => 'キーワードを検索',
            'popular_items' => 'よく使われているキーワード',
            'all_items' => 'すべてのキーワード',
            'edit_item' => 'キーワードを編集',
            'update_item' => 'キーワードを更新',
            'add_new_item' => '新しいキーワードを追加',
            'new_item_name' => '新しいキーワード名',
            'separate_items_with_commas' => 'キーワードをカンマで区切って入力',
            'add_or_remove_items' => 'キーワードを追加または削除',
            'choose_from_most_used' => 'よく使われているキーワードから選択',
            'menu_name' => 'キーワード',
        ],
        'hierarchical' => false,
        'rewrite' => [
            'slug' => 'recipe-keyword',
        ],
    ]));
}
add_action('init', 'foods_register_recipe_taxonomies');

function foods_get_recipe_default_terms() {
    return [
        'recipe_category' => [
            ['name' => 'お肉', 'slug' => 'meat'],
            ['name' => 'お魚', 'slug' => 'fish'],
            ['name' => '野菜', 'slug' => 'vegetable'],
            ['name' => '主食', 'slug' => 'staple'],
            ['name' => 'その他', 'slug' => 'other'],
        ],
        'recipe_main_ingredient' => [
            ['name' => '鶏肉', 'slug' => 'chicken'],
            ['name' => '豚肉', 'slug' => 'pork'],
            ['name' => '鮭', 'slug' => 'salmon'],
            ['name' => '豆腐', 'slug' => 'tofu'],
            ['name' => 'トマト', 'slug' => 'tomato'],
            ['name' => '麺', 'slug' => 'noodles'],
            ['name' => 'ご飯', 'slug' => 'rice'],
        ],
        'recipe_tag' => [
            ['name' => '時短', 'slug' => 'quick'],
            ['name' => '簡単', 'slug' => 'easy'],
            ['name' => 'さっぱり', 'slug' => 'refreshing'],
            ['name' => 'ピリ辛', 'slug' => 'spicy'],
            ['name' => 'お弁当', 'slug' => 'bento'],
            ['name' => '夏向け', 'slug' => 'summer'],
        ],
    ];
}

function foods_sync_recipe_default_terms() {
    foreach (foods_get_recipe_default_terms() as $taxonomy => $terms) {
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        foreach ($terms as $term) {
            $existing_term = get_term_by('slug', $term['slug'], $taxonomy);

            if (!$existing_term) {
                wp_insert_term($term['name'], $taxonomy, [
                    'slug' => $term['slug'],
                ]);
                continue;
            }

            if ($existing_term->name !== $term['name']) {
                wp_update_term($existing_term->term_id, $taxonomy, [
                    'name' => $term['name'],
                ]);
            }
        }
    }
}
add_action('init', 'foods_sync_recipe_default_terms', 20);

function foods_get_recipe_term_slugs_for_post($index) {
    $genre_sets = [
        ['staple'],
        ['meat'],
        ['fish'],
        ['vegetable'],
        ['other'],
    ];
    $ingredient_sets = [
        ['noodles'],
        ['chicken'],
        ['salmon'],
        ['tomato'],
        ['tofu'],
        ['rice'],
        ['pork'],
    ];
    $keyword_sets = [
        ['quick', 'refreshing'],
        ['easy', 'spicy'],
        ['summer'],
        ['bento', 'easy'],
        ['quick'],
        ['easy'],
    ];

    return [
        'recipe_category' => $genre_sets[$index % count($genre_sets)],
        'recipe_main_ingredient' => $ingredient_sets[$index % count($ingredient_sets)],
        'recipe_tag' => $keyword_sets[$index % count($keyword_sets)],
    ];
}

function foods_assign_recipe_default_terms_to_existing_posts() {
    $version = '20260709';
    if (get_option('foods_recipe_default_terms_assigned_version') === $version) {
        return;
    }

    if (!post_type_exists('recipe')) {
        return;
    }

    $recipe_posts = get_posts([
        'post_type' => 'recipe',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'orderby' => 'date',
        'order' => 'ASC',
    ]);

    foreach ($recipe_posts as $index => $post_id) {
        foreach (foods_get_recipe_term_slugs_for_post($index) as $taxonomy => $slugs) {
            if (!taxonomy_exists($taxonomy) || !empty(wp_get_object_terms($post_id, $taxonomy, ['fields' => 'ids']))) {
                continue;
            }

            wp_set_object_terms($post_id, $slugs, $taxonomy, false);
        }
    }

    update_option('foods_recipe_default_terms_assigned_version', $version);
}
add_action('init', 'foods_assign_recipe_default_terms_to_existing_posts', 30);
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

function foods_get_news_default_terms() {
    return [
        'news_category' => [
            [
                'name' => 'お知らせ',
                'slug' => 'information',
            ],
            [
                'name' => 'キャンペーン',
                'slug' => 'campaign',
            ],
            [
                'name' => 'こだわり',
                'slug' => 'commitment',
            ],
            [
                'name' => '店舗から',
                'slug' => 'shop-news',
            ],
            [
                'name' => '重要',
                'slug' => 'important',
            ],
        ],
        'news_shop_category' => [
            [
                'name' => '行徳店',
                'slug' => 'gyoutoku',
            ],
            [
                'name' => '西船橋店',
                'slug' => 'nishifunabashi',
            ],
            [
                'name' => '西原店',
                'slug' => 'nishihara',
            ],
            [
                'name' => '花野井店',
                'slug' => 'hananoi',
            ],
            [
                'name' => 'しいの木台店',
                'slug' => 'shiinokidai',
            ],
            [
                'name' => '青葉台店',
                'slug' => 'aobadai',
            ],
            [
                'name' => '松戸店',
                'slug' => 'matsudo',
            ],
            [
                'name' => '西新井店',
                'slug' => 'nishiarai',
            ],
            [
                'name' => '三郷店',
                'slug' => 'misato',
            ],
            [
                'name' => '八潮店',
                'slug' => 'yashio',
            ],
        ],
        'news_commitment' => [
            [
                'name' => 'お肉',
                'slug' => 'meat',
            ],
            [
                'name' => 'お魚',
                'slug' => 'fish',
            ],
            [
                'name' => '果物',
                'slug' => 'fruit',
            ],
            [
                'name' => 'お米',
                'slug' => 'rice',
            ],
            [
                'name' => 'お野菜',
                'slug' => 'vegetables',
            ],
            [
                'name' => '乳製品',
                'slug' => 'dairy-products',
            ],
            [
                'name' => '和日配',
                'slug' => 'japanese-daily-foods',
            ],
            [
                'name' => '加工食品',
                'slug' => 'processed-foods',
            ],
            [
                'name' => 'お菓子',
                'slug' => 'snacks',
            ],
        ],
    ];
}

function foods_sync_news_default_terms() {
    if (!is_admin()) {
        return;
    }

    foreach (foods_get_news_default_terms() as $taxonomy => $terms) {
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        foreach ($terms as $term) {
            $existing_term = get_term_by('slug', $term['slug'], $taxonomy);

            if (!$existing_term) {
                wp_insert_term($term['name'], $taxonomy, [
                    'slug' => $term['slug'],
                ]);
                continue;
            }

            if ($existing_term->name !== $term['name']) {
                wp_update_term($existing_term->term_id, $taxonomy, [
                    'name' => $term['name'],
                ]);
            }
        }
    }
}
add_action('init', 'foods_sync_news_default_terms', 20);
