<?php
/**
 * Import 15 recipe test posts for local development.
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-recipes.php?token=foods-selection-test-recipes
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-recipes.php?token=foods-selection-test-recipes&force=1
 *
 * CLI usage:
 *   php tools/import-test-recipes.php
 *   php tools/import-test-recipes.php --force
 */

const IMPORT_TEST_RECIPES_TOKEN = 'foods-selection-test-recipes';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_TEST_RECIPES_TOKEN, $token)) {
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

$recipes = [
    ['title' => 'さっぱり冷やし中華', 'category' => '麺', 'time' => '20分', 'main' => '中華麺'],
    ['title' => 'ピリ辛麻婆豆腐', 'category' => 'お豆腐', 'time' => '25分', 'main' => '木綿豆腐'],
    ['title' => '夏野菜カレー', 'category' => 'ご飯', 'time' => '40分', 'main' => 'なす'],
    ['title' => '鮭ときのこのホイル焼き', 'category' => '魚', 'time' => '30分', 'main' => '鮭切身'],
    ['title' => '鶏むね肉の照り焼き', 'category' => '肉', 'time' => '25分', 'main' => '鶏むね肉'],
    ['title' => 'トマトと卵の中華炒め', 'category' => '卵', 'time' => '15分', 'main' => 'トマト'],
    ['title' => '豚しゃぶサラダ', 'category' => 'サラダ', 'time' => '20分', 'main' => '豚ロース'],
    ['title' => '野菜たっぷり焼きそば', 'category' => '麺', 'time' => '20分', 'main' => '焼きそば麺'],
    ['title' => 'じゃがいものそぼろ煮', 'category' => '煮物', 'time' => '35分', 'main' => 'じゃがいも'],
    ['title' => 'きゅうりとわかめの酢の物', 'category' => '副菜', 'time' => '10分', 'main' => 'きゅうり'],
    ['title' => '牛肉とピーマンの炒め物', 'category' => '肉', 'time' => '20分', 'main' => '牛こま切れ肉'],
    ['title' => 'あさりの炊き込みご飯', 'category' => 'ご飯', 'time' => '45分', 'main' => 'あさり'],
    ['title' => 'かぼちゃのポタージュ', 'category' => 'スープ', 'time' => '30分', 'main' => 'かぼちゃ'],
    ['title' => '豆腐ハンバーグ', 'category' => 'お豆腐', 'time' => '35分', 'main' => '豆腐'],
    ['title' => 'フルーツヨーグルト', 'category' => 'デザート', 'time' => '10分', 'main' => 'ヨーグルト'],
];

$base_datetime = new DateTimeImmutable('2026-06-25 09:00:00', wp_timezone());
$created = 0;
$updated = 0;
$skipped = 0;

foreach ($recipes as $index => $recipe) {
    $number = $index + 1;
    $slug = sprintf('test-recipe-%02d', $number);
    $title = sprintf('【テスト%02d】%s', $number, $recipe['title']);
    $post_datetime = $base_datetime->modify('+' . $index . ' hours');
    $post_date = $post_datetime->format('Y-m-d H:i:s');
    $post_date_gmt = get_gmt_from_date($post_date);

    $existing = get_page_by_path($slug, OBJECT, 'recipe');

    if ($existing && !$force) {
        $skipped++;
        echo "Skipped existing recipe: {$title}\n";
        continue;
    }

    $post_data = [
        'post_type' => 'recipe',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => sprintf(
            "%sを使った%sカテゴリのテスト用レシピです。",
            $recipe['main'],
            $recipe['category']
        ),
        'post_excerpt' => sprintf('%sで作る、かんたんテストレシピです。', $recipe['main']),
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
        echo "Failed: {$title} - " . $post_id->get_error_message() . "\n";
        continue;
    }

    $meta = [
        'recipe_photo' => '',
        'recipe_summary' => sprintf('%sを主役にした、毎日の食卓で使いやすいテストレシピです。', $recipe['main']),
        'recipe_cooking_time' => $recipe['time'],
        'recipe_servings' => '2人前',
        'recipe_ingredients' => [
            [
                'group_name' => '',
                'ingredients' => [
                    ['ingredient_name' => $recipe['main'], 'amount' => '適量'],
                    ['ingredient_name' => '塩', 'amount' => '少々'],
                    ['ingredient_name' => 'こしょう', 'amount' => '少々'],
                ],
            ],
            [
                'group_name' => 'A',
                'ingredients' => [
                    ['ingredient_name' => 'しょうゆ', 'amount' => '大さじ1'],
                    ['ingredient_name' => 'みりん', 'amount' => '大さじ1'],
                    ['ingredient_name' => '砂糖', 'amount' => '小さじ1'],
                ],
            ],
        ],
        'recipe_preparation' => "材料は食べやすい大きさに切っておきます。\n調味料Aはあらかじめ混ぜ合わせます。",
        'recipe_steps' => [
            ['step_text' => sprintf('%sを下ごしらえします。', $recipe['main'])],
            ['step_text' => 'フライパンまたは鍋で材料を加熱します。'],
            ['step_text' => '調味料を加えて全体になじませます。'],
            ['step_text' => '器に盛り付けて完成です。'],
        ],
        'recipe_tips' => '火加減を調整しながら、焦げ付かないように仕上げるのがポイントです。',
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
        echo "Updated recipe: {$title} (#{$post_id}) {$post_date}\n";
    } else {
        $created++;
        echo "Created recipe: {$title} (#{$post_id}) {$post_date}\n";
    }
}

echo "Done. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}\n";
