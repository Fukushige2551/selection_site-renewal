<?php
/**
 * Import/update shop posts and normalize related shop slugs.
 *
 * Browser usage:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-shops.php?token=foods-selection-shops
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-shops.php?token=foods-selection-shops&dry-run=1
 *
 * CLI usage:
 *   php tools/import-shops.php
 *   php tools/import-shops.php --dry-run
 */

const IMPORT_SHOPS_TOKEN = 'foods-selection-shops';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_SHOPS_TOKEN, $token)) {
        http_response_code(403);
        exit("Invalid token.\n");
    }
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';

if (!file_exists($wp_load)) {
    exit("wp-load.php was not found: {$wp_load}\n");
}

require_once $wp_load;

$dry_run = $is_cli
    ? in_array('--dry-run', $argv, true)
    : isset($_GET['dry-run']);

$shops = [
    [
        'name' => '行徳店',
        'slug' => 'gyoutoku',
        'old_slugs' => ['gyotoku'],
        'prefecture' => '千葉県',
        'order' => 10,
    ],
    [
        'name' => '西船橋店',
        'slug' => 'nishifunabashi',
        'old_slugs' => ['nishi-funabashi'],
        'prefecture' => '千葉県',
        'order' => 20,
    ],
    [
        'name' => '西原店',
        'slug' => 'nishihara',
        'old_slugs' => [],
        'prefecture' => '千葉県',
        'order' => 30,
    ],
    [
        'name' => '花野井店',
        'slug' => 'hananoi',
        'old_slugs' => [],
        'prefecture' => '千葉県',
        'order' => 40,
    ],
    [
        'name' => 'しいの木台店',
        'slug' => 'shiinokidai',
        'old_slugs' => [],
        'prefecture' => '千葉県',
        'order' => 50,
    ],
    [
        'name' => '青葉台店',
        'slug' => 'aobadai',
        'old_slugs' => [],
        'prefecture' => '千葉県',
        'order' => 60,
    ],
    [
        'name' => '松戸店',
        'slug' => 'matsudo',
        'old_slugs' => [],
        'prefecture' => '千葉県',
        'order' => 70,
    ],
    [
        'name' => '西新井店',
        'slug' => 'nishiarai',
        'old_slugs' => [],
        'prefecture' => '東京都',
        'order' => 80,
    ],
    [
        'name' => '三郷店',
        'slug' => 'misato',
        'old_slugs' => [],
        'prefecture' => '埼玉県',
        'order' => 90,
    ],
    [
        'name' => '八潮店',
        'slug' => 'yashio',
        'old_slugs' => [],
        'prefecture' => '埼玉県',
        'order' => 100,
    ],
];

function foods_import_shops_find_post($shop) {
    $slugs = array_merge([$shop['slug']], $shop['old_slugs']);

    foreach ($slugs as $slug) {
        $post = get_page_by_path($slug, OBJECT, 'shop');
        if ($post) {
            return $post;
        }
    }

    $posts = get_posts([
        'post_type' => 'shop',
        'post_status' => 'any',
        'title' => $shop['name'],
        'posts_per_page' => 1,
    ]);

    return $posts ? $posts[0] : null;
}

function foods_import_shops_ensure_term($taxonomy, $name, $slug, $dry_run) {
    $term = get_term_by('slug', $slug, $taxonomy);

    if ($term) {
        return (int) $term->term_id;
    }

    $term = get_term_by('name', $name, $taxonomy);

    if ($term) {
        if (!$dry_run && $term->slug !== $slug) {
            wp_update_term((int) $term->term_id, $taxonomy, ['slug' => $slug]);
        }

        return (int) $term->term_id;
    }

    if ($dry_run) {
        echo "Would create term: {$taxonomy} {$name} ({$slug})\n";
        return 0;
    }

    $created = wp_insert_term($name, $taxonomy, ['slug' => $slug]);
    if (is_wp_error($created)) {
        echo "Failed term: {$taxonomy} {$name} - " . $created->get_error_message() . "\n";
        return 0;
    }

    return (int) $created['term_id'];
}

function foods_import_shops_normalize_term_slugs($taxonomy, $shops, $dry_run) {
    foreach ($shops as $shop) {
        $canonical_term_id = foods_import_shops_ensure_term($taxonomy, $shop['name'], $shop['slug'], $dry_run);

        foreach ($shop['old_slugs'] as $old_slug) {
            $old_term = get_term_by('slug', $old_slug, $taxonomy);

            if (!$old_term || (int) $old_term->term_id === $canonical_term_id) {
                continue;
            }

            if ($dry_run) {
                echo "Would normalize term slug: {$taxonomy} {$old_slug} -> {$shop['slug']}\n";
                continue;
            }

            if ($canonical_term_id) {
                $object_ids = get_objects_in_term((int) $old_term->term_id, $taxonomy);

                if (!is_wp_error($object_ids)) {
                    foreach ($object_ids as $object_id) {
                        wp_set_object_terms((int) $object_id, [$canonical_term_id], $taxonomy, true);
                        wp_remove_object_terms((int) $object_id, [(int) $old_term->term_id], $taxonomy);
                    }
                }

                wp_delete_term((int) $old_term->term_id, $taxonomy);
                echo "Merged term: {$taxonomy} {$old_slug} -> {$shop['slug']}\n";
                continue;
            }

            wp_update_term((int) $old_term->term_id, $taxonomy, [
                'name' => $shop['name'],
                'slug' => $shop['slug'],
            ]);
        }
    }
}

$created = 0;
$updated = 0;
$skipped = 0;

foreach ($shops as $shop) {
    $existing = foods_import_shops_find_post($shop);

    $post_data = [
        'post_type' => 'shop',
        'post_status' => 'publish',
        'post_title' => $shop['name'],
        'post_name' => $shop['slug'],
    ];

    if ($existing) {
        $post_data['ID'] = $existing->ID;
        $post_data['post_content'] = $existing->post_content;
    } else {
        $post_data['post_content'] = '';
    }

    if ($dry_run) {
        echo ($existing ? 'Would update' : 'Would create') . ": {$shop['name']} ({$shop['slug']})\n";
        $skipped++;
        continue;
    }

    $post_id = $existing
        ? wp_update_post($post_data, true)
        : wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
        echo "Failed shop: {$shop['name']} - " . $post_id->get_error_message() . "\n";
        continue;
    }

    $prefecture_slug = sanitize_title($shop['prefecture']);
    $prefecture_term_id = foods_import_shops_ensure_term(
        'shop_prefecture',
        $shop['prefecture'],
        $prefecture_slug,
        false
    );

    if ($prefecture_term_id) {
        wp_set_object_terms($post_id, [$prefecture_term_id], 'shop_prefecture', false);
    }

    if (function_exists('update_field')) {
        update_field('shop_order', $shop['order'], $post_id);
    } else {
        update_post_meta($post_id, 'shop_order', $shop['order']);
    }

    if ($existing) {
        $updated++;
        echo "Updated shop: {$shop['name']} (#{$post_id}) {$shop['slug']}\n";
    } else {
        $created++;
        echo "Created shop: {$shop['name']} (#{$post_id}) {$shop['slug']}\n";
    }
}

foods_import_shops_normalize_term_slugs('flyer_store', $shops, $dry_run);
foods_import_shops_normalize_term_slugs('news_shop_category', $shops, $dry_run);

if (!$dry_run && function_exists('flush_rewrite_rules')) {
    flush_rewrite_rules(false);
}

echo "Done. Created: {$created}, Updated: {$updated}, Dry-run skipped: {$skipped}\n";
