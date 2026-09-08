<?php
/** Create local preview news for the washoku-daily detail page. */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}
require_once dirname(__DIR__, 4) . '/wp-load.php';
if (wp_get_environment_type() === 'production') {
    exit("Local/development environments only.\n");
}
$term = term_exists('washoku-daily', 'news_commitment');
if (!$term) {
    $term = wp_insert_term('和日配', 'news_commitment', ['slug' => 'washoku-daily']);
}
if (is_wp_error($term)) {
    throw new RuntimeException($term->get_error_message());
}
$topics = [
    '梅干しの味わいを楽しむ',
    '朝ごはんに合わせたい納豆',
    '豆腐で広がる毎日の献立',
    '油揚げを使った一品',
    'こんにゃくの食感を楽しむ',
    'ごはんに合わせたい漬物',
    '素材と製法に注目した商品選び',
    '季節の食卓に和日配を',
    '忙しい日の食卓づくり',
    '和日配売場のおすすめ紹介',
];
$created = 0;
foreach ($topics as $index => $topic) {
    $slug = sprintf('test-washoku-daily-news-%02d', $index + 1);
    $existing = get_page_by_path($slug, OBJECT, 'news');
    if ($existing) {
        echo "Existing: {$existing->ID}\n";
        continue;
    }
    $date = current_datetime()->modify('-' . ($index + 1) . ' days');
    $body = '【表示確認用テストデータ】' . $topic . 'をテーマにした和日配のこだわり記事です。商品紹介、素材へのこだわり、食卓での楽しみ方を掲載する想定で、一覧と詳細ページの表示を確認するためのサンプルです。実際の商品や販売情報ではありません。';
    $id = wp_insert_post([
        'post_type' => 'news',
        'post_status' => 'publish',
        'post_name' => $slug,
        'post_title' => '【テスト】和日配：' . $topic,
        'post_content' => $body,
        'post_excerpt' => $body,
        'post_date' => $date->format('Y-m-d H:i:s'),
        'post_date_gmt' => get_gmt_from_date($date->format('Y-m-d H:i:s')),
    ], true);
    if (is_wp_error($id)) {
        throw new RuntimeException($id->get_error_message());
    }
    foreach (['news_category' => ['commitment'], 'news_commitment' => ['washoku-daily']] as $taxonomy => $terms) {
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
