<?php
/**
 * Import 12 meat commitment news posts for local development.
 *
 * CLI usage:
 *   php tools/import-test-meat-news.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';

if (!file_exists($wp_load)) {
    exit("wp-load.php was not found: {$wp_load}\n");
}

require_once $wp_load;

$posts = [
    ['title' => '美味北総豚のおいしさを支える飼育環境', 'category' => 'commitment'],
    ['title' => '毎日の食卓におすすめしたい豚肉の選び方', 'category' => 'information'],
    ['title' => 'かみむら牛のおいしさと安心への取り組み', 'category' => 'commitment'],
    ['title' => '部位ごとの特徴を生かした牛肉の楽しみ方', 'category' => 'information'],
    ['title' => '素材を生かした無添加ハム・ウインナー', 'category' => 'commitment'],
    ['title' => '週末のごちそうにおすすめのお肉特集', 'category' => 'campaign'],
    ['title' => '鮮度にこだわったお肉売場づくり', 'category' => 'shop-news'],
    ['title' => '料理に合わせて選べる便利なカット肉', 'category' => 'information'],
    ['title' => '生産者とつながる産地直結の取り組み', 'category' => 'commitment'],
    ['title' => '平日の時短調理を助ける味付け肉', 'category' => 'campaign'],
    ['title' => 'お肉をもっとおいしく味わう保存のコツ', 'category' => 'information'],
    ['title' => '家族で楽しむおすすめ肉料理', 'category' => 'shop-news'],
];

$bodies = [
    '豊かな自然環境の中で、健康管理や飼料に気を配りながら大切に育てられています。きめ細かくやわらかな肉質と、脂の自然な甘みをお楽しみください。',
    '色やつや、脂の状態など、おいしい豚肉を選ぶときに確認したいポイントをご紹介します。料理に合った部位選びにもぜひお役立てください。',
    '飼料づくりから製造・加工まで一貫して管理し、安全とおいしさの両立を目指しています。口どけのよい脂と豊かな旨味が特長です。',
    '焼く、煮る、炒めるなど、調理方法に合った牛肉の部位をご案内します。それぞれの特徴を知ることで、いつもの料理がさらにおいしく仕上がります。',
    '豚肉、塩、砂糖、香辛料など、シンプルな素材を大切にしています。余計なものに頼らず、肉本来の旨味と香りを引き出しました。',
    '家族が集まる週末の食卓にぴったりな、焼肉やステーキ、しゃぶしゃぶ用のお肉を取りそろえました。特別な一皿をお楽しみください。',
    '産地から売場までの温度管理と、店内での丁寧な商品管理を徹底しています。毎日の食卓へ、新鮮で安心なお肉をお届けします。',
    '用途がひと目で分かるカットと分量で、献立づくりをお手伝いします。下ごしらえの手間を減らし、忙しい日にも使いやすい商品です。',
    '生産者の顔が見える関係を大切にし、飼育環境や品質を確認しながら仕入れています。産地と売場が協力して安定したおいしさを届けます。',
    '焼くだけ、炒めるだけで一品が完成する味付け肉をご用意しました。野菜と合わせた簡単なアレンジもおすすめです。',
    '購入後のお肉をおいしく保つ冷蔵・冷凍保存のポイントをご紹介します。小分けや解凍方法を工夫して、最後までおいしく召し上がれます。',
    'ハンバーグや生姜焼き、焼肉など、家族みんなで楽しめる定番メニューをご提案します。毎日の献立選びにぜひお役立てください。',
];

$base_datetime = new DateTimeImmutable('2026-08-04 12:00:00', wp_timezone());
$created = 0;
$skipped = 0;

foreach ($posts as $index => $post) {
    $number = $index + 1;
    $slug = sprintf('test-meat-commitment-%02d', $number);
    $existing = get_page_by_path($slug, OBJECT, 'news');

    if ($existing) {
        $skipped++;
        echo "Skipped existing news: {$post['title']} (#{$existing->ID})\n";
        continue;
    }

    $post_datetime = $base_datetime->modify('-' . $index . ' days');
    $post_date = $post_datetime->format('Y-m-d H:i:s');
    $body = $bodies[$index];
    $post_id = wp_insert_post([
        'post_type' => 'news',
        'post_status' => 'publish',
        'post_title' => $post['title'],
        'post_name' => $slug,
        'post_content' => $body,
        'post_excerpt' => $body,
        'post_date' => $post_date,
        'post_date_gmt' => get_gmt_from_date($post_date),
    ], true);

    if (is_wp_error($post_id)) {
        echo "Failed: {$post['title']} - " . $post_id->get_error_message() . "\n";
        continue;
    }

    wp_set_object_terms($post_id, [$post['category']], 'news_category', false);
    wp_set_object_terms($post_id, ['meat'], 'news_commitment', false);

    $meta = [
        'news_publish_date' => $post_datetime->format('Ymd'),
        'news_body' => $body,
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

    $created++;
    echo "Created news: {$post['title']} (#{$post_id}) {$post_date}\n";
}

echo "Done. Created: {$created}, Skipped: {$skipped}\n";
