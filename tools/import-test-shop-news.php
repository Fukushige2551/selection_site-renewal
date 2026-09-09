<?php
/** Create local preview news for the shop detail page. */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}
require_once dirname(__DIR__, 4) . '/wp-load.php';
if (wp_get_environment_type() === 'production') {
    exit("Local/development environments only.\n");
}
$term = term_exists('shop', 'news_commitment');
if (!$term) {
    $term = wp_insert_term('お店', 'news_commitment', ['slug' => 'shop']);
}
if (is_wp_error($term)) {
    throw new RuntimeException($term->get_error_message());
}
$topics = [
    '地域に根差したお店づくり',
    '旬の食材を楽しむ売場',
    '毎日の食卓を支える商品選び',
    'スタッフとの会話から広がる献立',
    '体と家計にやさしいお買い物',
    '地元の味を見つける楽しさ',
    '手軽に楽しむお惣菜',
    '季節を感じるおすすめ商品',
    '新しい味との出会い',
    '地域の台所としての取り組み',
];
$created = 0;
foreach ($topics as $index => $topic) {
    $slug = sprintf('test-shop-news-%02d', $index + 1);
    $existing = get_page_by_path($slug, OBJECT, 'news');
    if ($existing) {
        echo "Existing: {$existing->ID}\n";
        continue;
    }
    $date = current_datetime()->modify('-' . ($index + 1) . ' days');
    $body = '【表示確認用テストデータ】' . $topic . 'をテーマにしたお店のこだわり記事です。商品紹介、素材へのこだわり、食卓での楽しみ方を掲載する想定で、一覧と詳細ページの表示を確認するためのサンプルです。実際の商品や販売情報ではありません。';
    $id = wp_insert_post([
        'post_type' => 'news',
        'post_status' => 'publish',
        'post_name' => $slug,
        'post_title' => '【テスト】お店：' . $topic,
        'post_content' => $body,
        'post_excerpt' => $body,
        'post_date' => $date->format('Y-m-d H:i:s'),
        'post_date_gmt' => get_gmt_from_date($date->format('Y-m-d H:i:s')),
    ], true);
    if (is_wp_error($id)) {
        throw new RuntimeException($id->get_error_message());
    }
    foreach (['news_category' => ['commitment'], 'news_commitment' => ['shop']] as $taxonomy => $terms) {
        $result = wp_set_object_terms($id, $terms, $taxonomy);
        if (is_wp_error($result)) {
            throw new RuntimeException($result->get_error_message());
        }
    }
    foreach (['news_publish_date' => $date->format('Ymd'), 'news_body' => $body, 'show_first_view' => 0, 'first_view_order' => ''] as $key => $value) {
        if (function_exists('update_field')) {
            update_field($key, $value, $id);
        } else {
            update_post_meta($id, $key, $value);
        }
    }
    $created++;
    echo "Created: {$id} {$topic}\n";
}
echo "Created total: {$created}\n";
