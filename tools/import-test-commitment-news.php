<?php
/**
 * Ensure that each selection commitment has at least 10 published news posts.
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-commitment-news.php?token=foods-selection-commitment-news
 *
 * CLI usage:
 *   php tools/import-test-commitment-news.php
 */

const IMPORT_TEST_COMMITMENT_NEWS_TOKEN = 'foods-selection-commitment-news';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_TEST_COMMITMENT_NEWS_TOKEN, $token)) {
        http_response_code(403);
        exit("Invalid token.\n");
    }
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';

if (!file_exists($wp_load)) {
    exit("wp-load.php was not found: {$wp_load}\n");
}

require_once $wp_load;

$vegetables_term = term_exists('vegetables', 'news_commitment');
if (!$vegetables_term) {
    $vegetables_term = wp_insert_term('お野菜・果物', 'news_commitment', ['slug' => 'vegetables']);
}

if (!is_wp_error($vegetables_term)) {
    $vegetables_term_id = (int) (is_array($vegetables_term) ? $vegetables_term['term_id'] : $vegetables_term);
    wp_update_term($vegetables_term_id, 'news_commitment', ['name' => 'お野菜・果物']);

    $fruit_term = get_term_by('slug', 'fruit', 'news_commitment');
    if ($fruit_term) {
        $fruit_posts = get_objects_in_term($fruit_term->term_id, 'news_commitment');
        foreach ($fruit_posts as $fruit_post_id) {
            wp_set_object_terms($fruit_post_id, ['vegetables'], 'news_commitment', true);
        }

        $deleted = wp_delete_term($fruit_term->term_id, 'news_commitment');
        if (is_wp_error($deleted)) {
            exit('Failed to merge fruit: ' . $deleted->get_error_message() . "\n");
        }

        echo 'Merged fruit into お野菜・果物: ' . count($fruit_posts) . " posts.\n";
    }
}

$commitments = [
    'meat' => ['name' => 'お肉', 'terms' => ['meat']],
    'rice' => ['name' => 'お米', 'terms' => ['rice']],
    'snacks' => ['name' => 'お菓子', 'terms' => ['snacks']],
    'vegetables-fruit' => ['name' => 'お野菜・果物', 'terms' => ['vegetables']],
    'deli' => ['name' => 'お惣菜', 'terms' => ['deli']],
    'fish' => ['name' => 'お魚', 'terms' => ['fish']],
    'dairy-products' => ['name' => '乳製品', 'terms' => ['dairy-products']],
    'processed-foods' => ['name' => '加工食品', 'terms' => ['processed-foods']],
    'japanese-daily-foods' => ['name' => '和日配', 'terms' => ['japanese-daily-foods']],
    'alcohol' => ['name' => 'お酒', 'terms' => ['alcohol']],
];

$topics = [
    ['旬のおいしさを楽しむおすすめ商品', '季節ならではのおいしさを楽しめる商品を厳選しました。素材の持ち味やおすすめの食べ方とあわせてご紹介します。'],
    ['毎日の食卓を支える品質へのこだわり', '産地や原材料、製法を確かめながら、毎日の食卓に安心して選べる品質の商品を取りそろえています。'],
    ['素材の魅力を引き出す選び方', '味わい、香り、食感など、素材ごとの特徴を見極めて仕入れています。料理や好みに合う選び方をご案内します。'],
    ['産地とつながるおいしさの取り組み', 'つくり手とのつながりを大切にし、産地の環境や製造工程まで確認しながら、おいしさを売場へ届けています。'],
    ['家族で楽しみたい今月のおすすめ', '家族が集まる食卓にぴったりの商品をご用意しました。定番の楽しみ方から手軽なアレンジまでご紹介します。'],
    ['おいしさを保つ保存と扱いのコツ', '購入後もおいしく召し上がっていただけるよう、保存方法や扱い方のポイントを分かりやすくお伝えします。'],
    ['売場担当者が選ぶ注目の商品', '豊富な品ぞろえの中から、売場担当者が自信を持っておすすめする商品と、その魅力をご紹介します。'],
    ['手軽に楽しめるおすすめアレンジ', 'いつもの商品をひと工夫でさらに楽しめる、手軽な食べ方や組み合わせをご提案します。'],
    ['安心とおいしさを届ける売場づくり', '温度管理や鮮度確認、分かりやすいご案内を徹底し、安心して商品を選べる売場づくりに取り組んでいます。'],
    ['つくり手の想いが伝わる一品', '原材料や製法に向き合うつくり手の想いとともに、丁寧につくられた商品の味わいをご紹介します。'],
];

$target_count = 10;
$base_datetime = (new DateTimeImmutable('now', wp_timezone()))->modify('-1 hour');
$created_total = 0;

foreach ($commitments as $key => $commitment) {
    foreach ($commitment['terms'] as $term_slug) {
        if (!term_exists($term_slug, 'news_commitment')) {
            $term_name = $term_slug === 'alcohol' ? 'お酒' : $commitment['name'];
            $result = wp_insert_term($term_name, 'news_commitment', ['slug' => $term_slug]);
            if (is_wp_error($result)) {
                echo "Failed to create term {$term_name}: " . $result->get_error_message() . "\n";
                continue 2;
            }
            echo "Created term: {$term_name} ({$term_slug})\n";
        }
    }

    for ($sequence = 1; $sequence <= $target_count; $sequence++) {
        $managed_slug = sprintf('test-commitment-%s-%02d', $key, $sequence);
        $managed_post = get_page_by_path($managed_slug, OBJECT, 'news');

        if ($managed_post && get_post_status($managed_post) !== 'publish') {
            $normalized_datetime = $base_datetime->modify('-' . $sequence . ' hours');
            wp_update_post([
                'ID' => $managed_post->ID,
                'post_status' => 'publish',
                'post_date' => $normalized_datetime->format('Y-m-d H:i:s'),
                'post_date_gmt' => get_gmt_from_date($normalized_datetime->format('Y-m-d H:i:s')),
            ]);
            echo "Published scheduled {$commitment['name']} #{$sequence} (#{$managed_post->ID})\n";
        }
    }

    $existing_query = new WP_Query([
        'post_type' => 'news',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'cache_results' => false,
        'tax_query' => [[
            'taxonomy' => 'news_commitment',
            'field' => 'slug',
            'terms' => $commitment['terms'],
        ]],
    ]);
    $existing_count = count($existing_query->posts);

    if ($existing_count >= $target_count) {
        echo "Skipped {$commitment['name']}: already {$existing_count} posts.\n";
        continue;
    }

    $needed = $target_count - $existing_count;
    $created_for_commitment = 0;

    for ($index = 0; $index < $needed; $index++) {
        $sequence = $existing_count + $index + 1;
        $slug = sprintf('test-commitment-%s-%02d', $key, $sequence);
        $topic = $topics[($sequence - 1) % count($topics)];
        $title = $commitment['name'] . '：' . $topic[0];
        $body = $commitment['name'] . 'について、' . $topic[1];
        $post_datetime = $base_datetime->modify('-' . ($created_total + $index) . ' hours');
        $post_date = $post_datetime->format('Y-m-d H:i:s');
        $assigned_term = $commitment['terms'][$sequence % count($commitment['terms'])];
        $existing = get_page_by_path($slug, OBJECT, 'news');
        $post_data = [
            'post_type' => 'news',
            'post_status' => 'publish',
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => $body,
            'post_excerpt' => $body,
            'post_date' => $post_date,
            'post_date_gmt' => get_gmt_from_date($post_date),
        ];

        if ($existing) {
            $post_data['ID'] = $existing->ID;
        }

        $post_id = $existing
            ? wp_update_post($post_data, true)
            : wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            echo "Failed {$commitment['name']} #{$sequence}: " . $post_id->get_error_message() . "\n";
            continue;
        }

        wp_set_object_terms($post_id, ['commitment'], 'news_category', false);
        wp_set_object_terms($post_id, [$assigned_term], 'news_commitment', false);

        $meta = [
            'news_publish_date' => $post_datetime->format('Ymd'),
            'news_body' => $body,
            'show_first_view' => 0,
            'first_view_order' => '',
        ];

        foreach ($meta as $meta_key => $value) {
            if (function_exists('update_field')) {
                update_field($meta_key, $value, $post_id);
            } else {
                update_post_meta($post_id, $meta_key, $value);
            }
        }

        $created_for_commitment++;
        $action = $existing ? 'Published existing' : 'Created';
        $assigned_slugs = wp_get_object_terms($post_id, 'news_commitment', ['fields' => 'slugs']);
        echo "{$action} {$commitment['name']} #{$sequence}: {$title} (#{$post_id}, " . get_post_status($post_id) . ', ' . implode(',', $assigned_slugs) . ")\n";
    }

    $created_total += $created_for_commitment;
    echo "Completed {$commitment['name']}: before {$existing_count}, created {$created_for_commitment}.\n";
}

echo "Done. Created total: {$created_total}\n";
