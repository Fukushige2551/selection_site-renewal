<?php
/**
 * Import 20 recruit_part_time test posts for local development.
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-recruit-part-time.php?token=foods-selection-test-recruit
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-recruit-part-time.php?token=foods-selection-test-recruit&force=1
 *
 * CLI usage:
 *   php tools/import-test-recruit-part-time.php
 *   php tools/import-test-recruit-part-time.php --force
 */

const IMPORT_TEST_RECRUIT_PART_TIME_TOKEN = 'foods-selection-test-recruit';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_TEST_RECRUIT_PART_TIME_TOKEN, $token)) {
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

$stores = [
    '行徳店',
    '西船橋店',
    '西原店',
    '花野井店',
    'しいの木台店',
    '八潮店',
    '青葉台店',
    '松戸店',
    '西新井店',
    '三郷店',
];

$jobs = [
    'レジスタッフ',
    '青果スタッフ',
    '精肉スタッフ',
    '鮮魚スタッフ',
    '惣菜スタッフ',
];

$base_datetime = new DateTimeImmutable('2026-06-23 09:00:00', wp_timezone());
$created = 0;
$updated = 0;
$skipped = 0;

for ($index = 1; $index <= 20; $index++) {
    $store = $stores[($index - 1) % count($stores)];
    $job = $jobs[($index - 1) % count($jobs)];
    $slug = sprintf('test-recruit-part-time-%02d', $index);
    $title = sprintf('【テスト%02d】%s %s', $index, $store, $job);
    $post_datetime = $base_datetime->modify('+' . ($index - 1) . ' hours');
    $post_date = $post_datetime->format('Y-m-d H:i:s');
    $post_date_gmt = get_gmt_from_date($post_date);

    $existing = get_page_by_path($slug, OBJECT, 'recruit_part_time');

    if ($existing && !$force) {
        $skipped++;
        echo "Skipped existing recruit: {$title}\n";
        continue;
    }

    $post_data = [
        'post_type' => 'recruit_part_time',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => sprintf(
            "%sの%s募集テストデータです。最新10件表示の確認に使用します。",
            $store,
            $job
        ),
        'post_excerpt' => sprintf('%sで%sを募集しています。', $store, $job),
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
        'job_type' => $job,
        'salary' => '時給 ' . (1100 + ($index * 10)) . '円から',
        'work_location_access' => $store . "\n駅から徒歩圏内",
        'working_hours' => "週2日からOK\n9:00-18:00のうち4時間から",
        'benefits' => "交通費規定支給\n制服貸与\n社員割引あり",
        'qualification' => "未経験歓迎\n学生・主婦（夫）歓迎",
        'commuting' => "自転車通勤可\n店舗により車通勤応相談",
        'application_method' => "お電話または応募フォームよりご応募ください。\nテストデータ{$index}番です。",
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
        echo "Updated recruit: {$title} (#{$post_id}) {$post_date}\n";
    } else {
        $created++;
        echo "Created recruit: {$title} (#{$post_id}) {$post_date}\n";
    }
}

echo "Done. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}\n";
echo "Newest expected top 10: 【テスト20】 through 【テスト11】\n";
