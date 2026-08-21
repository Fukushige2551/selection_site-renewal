<?php
/**
 * Import news test posts for local development.
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-news.php?token=foods-selection-test-news
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-news.php?token=foods-selection-test-news&force=1
 *
 * CLI usage:
 *   php tools/import-test-news.php
 *   php tools/import-test-news.php --force
 */

const IMPORT_TEST_NEWS_TOKEN = 'foods-selection-test-news';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_TEST_NEWS_TOKEN, $token)) {
        http_response_code(403);
        exit("Invalid token.\n");
    }
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';

if (!file_exists($wp_load)) {
    exit("wp-load.php was not found: {$wp_load}\n");
}

require_once $wp_load;

$force = $is_cli
    ? in_array('--force', $argv, true)
    : isset($_GET['force']);

$patterns = [
    [
        'slug' => 'test-news-information',
        'title' => '【テスト】お知らせ',
        'body' => '新着情報カテゴリ「お知らせ」の表示確認用テストデータです。',
        'category' => 'information',
    ],
    [
        'slug' => 'test-news-important',
        'title' => '【テスト】重要',
        'body' => '新着情報カテゴリ「重要」の表示確認用テストデータです。',
        'category' => 'important',
    ],
];

for ($index = 1; $index <= 10; $index++) {
    $patterns[] = [
        'slug' => sprintf('test-news-campaign-%02d', $index),
        'title' => sprintf('【テスト%02d】キャンペーン', $index),
        'body' => sprintf('新着情報カテゴリ「キャンペーン」の表示確認用テストデータ%02dです。', $index),
        'category' => 'campaign',
    ];
}

$shop_terms = [
    ['name' => '行徳店', 'slug' => 'gyoutoku'],
    ['name' => '西船橋店', 'slug' => 'nishifunabashi'],
    ['name' => '西原店', 'slug' => 'nishihara'],
    ['name' => '花野井店', 'slug' => 'hananoi'],
    ['name' => 'しいの木台店', 'slug' => 'shiinokidai'],
    ['name' => '青葉台店', 'slug' => 'aobadai'],
    ['name' => '松戸店', 'slug' => 'matsudo'],
    ['name' => '西新井店', 'slug' => 'nishiarai'],
    ['name' => '三郷店', 'slug' => 'misato'],
    ['name' => '八潮店', 'slug' => 'yashio'],
];

foreach ($shop_terms as $shop_term) {
    $patterns[] = [
        'slug' => 'test-news-shop-' . $shop_term['slug'],
        'title' => '【テスト】店舗から - ' . $shop_term['name'],
        'body' => $shop_term['name'] . 'の店舗カテゴリ表示確認用テストデータです。',
        'category' => 'shop-news',
        'shop_category' => $shop_term['slug'],
    ];
}

$commitment_terms = [
    ['name' => 'お肉', 'slug' => 'meat'],
    ['name' => 'お魚', 'slug' => 'fish'],
    ['name' => 'お米', 'slug' => 'rice'],
    ['name' => 'お野菜・果物', 'slug' => 'vegetables'],
    ['name' => 'お惣菜', 'slug' => 'deli'],
    ['name' => '乳製品', 'slug' => 'dairy-products'],
    ['name' => '和日配', 'slug' => 'japanese-daily-foods'],
    ['name' => '加工食品', 'slug' => 'processed-foods'],
    ['name' => 'お菓子', 'slug' => 'snacks'],
    ['name' => 'お酒', 'slug' => 'alcohol'],
];

foreach ($commitment_terms as $commitment_term) {
    $patterns[] = [
        'slug' => 'test-news-commitment-' . $commitment_term['slug'],
        'title' => '【テスト】こだわり - ' . $commitment_term['name'],
        'body' => $commitment_term['name'] . 'のこだわりカテゴリ表示確認用テストデータです。',
        'category' => 'commitment',
        'commitment' => $commitment_term['slug'],
    ];
}

$base_datetime = new DateTimeImmutable('2026-06-25 09:00:00', wp_timezone());
$created = 0;
$updated = 0;
$skipped = 0;

foreach ($patterns as $index => $pattern) {
    $post_datetime = $base_datetime->modify('+' . $index . ' hours');
    $post_date = $post_datetime->format('Y-m-d H:i:s');
    $post_date_gmt = get_gmt_from_date($post_date);
    $existing = get_page_by_path($pattern['slug'], OBJECT, 'news');

    if ($existing && !$force) {
        $skipped++;
        echo "Skipped existing news: {$pattern['title']}\n";
        continue;
    }

    $post_data = [
        'post_type' => 'news',
        'post_status' => 'publish',
        'post_title' => $pattern['title'],
        'post_name' => $pattern['slug'],
        'post_content' => $pattern['body'],
        'post_excerpt' => $pattern['body'],
        'post_date' => $post_date,
        'post_date_gmt' => $post_date_gmt,
    ];

    if ($existing) {
        $post_data['ID'] = $existing->ID;
    }

    $post_id = $existing
        ? wp_update_post($post_data, true)
        : wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
        echo "Failed: {$pattern['title']} - " . $post_id->get_error_message() . "\n";
        continue;
    }

    wp_set_object_terms($post_id, [$pattern['category']], 'news_category', false);

    if (!empty($pattern['shop_category'])) {
        wp_set_object_terms($post_id, [$pattern['shop_category']], 'news_shop_category', false);
    }

    if (!empty($pattern['commitment'])) {
        wp_set_object_terms($post_id, [$pattern['commitment']], 'news_commitment', false);
    }

    $meta = [
        'news_publish_date' => $post_datetime->format('Ymd'),
        'news_body' => $pattern['body'],
        'show_first_view' => 0,
        'first_view_order' => '',
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
        echo "Updated news: {$pattern['title']} (#{$post_id}) {$post_date}\n";
    } else {
        $created++;
        echo "Created news: {$pattern['title']} (#{$post_id}) {$post_date}\n";
    }
}

echo "Done. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}\n";
echo "Patterns: news categories 1 each, campaign 10, shop categories 1 each, commitments 1 each.\n";
