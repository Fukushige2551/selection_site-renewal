<?php
/**
 * Import 10 flyer test posts for local development.
 *
 * CLI usage:
 *   php tools/import-test-flyers.php
 *   php tools/import-test-flyers.php --dry-run
 *   php tools/import-test-flyers.php --force
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-flyers.php?token=foods-selection-test-flyers&dry-run=1
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-flyers.php?token=foods-selection-test-flyers&force=1
 */

const IMPORT_TEST_FLYERS_TOKEN = 'foods-selection-test-flyers';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_TEST_FLYERS_TOKEN, $token)) {
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
$force = $is_cli
    ? in_array('--force', $argv, true)
    : isset($_GET['force']);

$stores = [
    ['name' => '行徳店', 'slug' => 'gyoutoku'],
    ['name' => '西船橋店', 'slug' => 'nishifunabashi'],
    ['name' => '西原店', 'slug' => 'nishihara'],
    ['name' => '花野井店', 'slug' => 'hananoi'],
    ['name' => 'しいの木台店', 'slug' => 'shiinokidai'],
    ['name' => '八潮店', 'slug' => 'yashio'],
    ['name' => '青葉台店', 'slug' => 'aobadai'],
    ['name' => '松戸店', 'slug' => 'matsudo'],
    ['name' => '西新井店', 'slug' => 'nishiarai'],
    ['name' => '三郷店', 'slug' => 'misato'],
];

$base_date = new DateTimeImmutable(current_time('Y-m-d'));
$sample_items = [
    [
        'item_name' => '国産若鶏もも肉',
        'item_origin' => '国産',
        'item_quantity' => '100gあたり',
        'label_1' => 'おすすめ',
        'label_2' => '精肉',
        'base_price' => 128,
        'tax_price' => 138,
        'before_discount_price' => 158,
        'note' => '数量限定のテスト商品です。',
    ],
    [
        'item_name' => 'キャベツ',
        'item_origin' => '千葉県産',
        'item_quantity' => '1玉',
        'label_1' => '特価',
        'label_2' => '青果',
        'base_price' => 98,
        'tax_price' => 106,
        'before_discount_price' => 128,
        'note' => '天候により産地が変更になる場合があります。',
    ],
    [
        'item_name' => '銀鮭切身',
        'item_origin' => 'チリ産',
        'item_quantity' => '3切',
        'label_1' => '広告の品',
        'label_2' => '鮮魚',
        'base_price' => 398,
        'tax_price' => 430,
        'before_discount_price' => 498,
        'note' => '解凍品を含みます。',
    ],
    [
        'item_name' => 'たまご',
        'item_origin' => '国内産',
        'item_quantity' => '10個入',
        'label_1' => '日替り',
        'label_2' => '食品',
        'base_price' => 198,
        'tax_price' => 214,
        'before_discount_price' => 238,
        'note' => 'お一人様1点限りのテスト条件です。',
    ],
];
$created = 0;
$updated = 0;
$skipped = 0;

foreach ($stores as $index => $store) {
    for ($flyer_index = 1; $flyer_index <= 10; $flyer_index++) {
        $global_number = ($index * 10) + $flyer_index;
        $slug = sprintf('test-flyer-%s-%02d', $store['slug'], $flyer_index);
        $title = sprintf('【テスト】%s チラシ %02d', $store['name'], $flyer_index);
        if ($global_number === 1) {
            $start_date = $base_date;
            $end_date = $start_date->modify('+1 month');
        } else {
            $start_date = $base_date->modify('+1 month +' . (($global_number - 2) * 7 + 1) . ' days');
            $end_date = $start_date->modify('+6 days');
        }

        $publish_start_date = $start_date;
        $publish_end_date = $end_date;

        $existing = get_page_by_path($slug, OBJECT, 'flyer');

        if ($existing && !$force) {
            $skipped++;
            echo "Skipped existing flyer: {$title}\n";
            continue;
        }

        $post_data = [
            'post_type' => 'flyer',
            'post_status' => 'publish',
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => sprintf(
                "%s向けのテスト用チラシです。表示確認・絞り込み確認・並び順確認に使用します。",
                $store['name']
            ),
        ];

        if ($existing) {
            $post_data['ID'] = $existing->ID;
        }

        if ($dry_run) {
            echo ($existing ? 'Would update' : 'Would create') . ": {$title}\n";
            continue;
        }

        $post_id = $existing
            ? wp_update_post($post_data, true)
            : wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            echo "Failed: {$title} - " . $post_id->get_error_message() . "\n";
            continue;
        }

        $term = term_exists($store['name'], 'flyer_store');
        if (!$term) {
            $term = wp_insert_term($store['name'], 'flyer_store', ['slug' => $store['slug']]);
        }

        if (!is_wp_error($term)) {
            wp_set_object_terms($post_id, [(int) $term['term_id']], 'flyer_store', false);
        }

        $bargain_items = [];
        foreach ($sample_items as $item_index => $sample_item) {
            $item_start_date = $start_date->modify('+' . $item_index . ' days');
            $item_end_date = $item_start_date->modify('+1 day');

            $bargain_items[] = array_merge(
                $sample_item,
                [
                    'item_name' => $sample_item['item_name'] . ' ' . $store['name'],
                    'item_image' => '',
                    'item_start_date' => $item_start_date->format('Ymd'),
                    'item_end_date' => $item_end_date->format('Ymd'),
                ]
            );
        }

        $meta = [
            'flyer_image' => '',
            'flyer_images' => [],
            'publish_start_date' => $publish_start_date->format('Ymd'),
            'publish_end_date' => $publish_end_date->format('Ymd'),
            'flyer_start_date' => $start_date->format('Ymd'),
            'flyer_end_date' => $end_date->format('Ymd'),
            'bargain_items' => $bargain_items,
        ];

        foreach ($meta as $key => $value) {
            if (function_exists('update_field')) {
                update_field($key, $value, $post_id);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        if ($existing) {
            $updated++;
            echo "Updated flyer: {$title} (#{$post_id})\n";
        } else {
            $created++;
            echo "Created flyer: {$title} (#{$post_id})\n";
        }
    }
}

echo "Done. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}\n";
