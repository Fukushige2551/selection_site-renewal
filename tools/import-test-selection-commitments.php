<?php
/**
 * Import selection_commitment test posts from news_commitment terms.
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-selection-commitments.php?token=foods-selection-test-commitments
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-test-selection-commitments.php?token=foods-selection-test-commitments&force=1
 *
 * CLI usage:
 *   php tools/import-test-selection-commitments.php
 *   php tools/import-test-selection-commitments.php --force
 */

const IMPORT_TEST_SELECTION_COMMITMENTS_TOKEN = 'foods-selection-test-commitments';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_TEST_SELECTION_COMMITMENTS_TOKEN, $token)) {
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

if (!taxonomy_exists('news_commitment')) {
    exit("Taxonomy news_commitment is not registered.\n");
}

$terms = get_terms([
    'taxonomy' => 'news_commitment',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
]);

if (is_wp_error($terms)) {
    exit("Failed to get news_commitment terms: " . $terms->get_error_message() . "\n");
}

if (!$terms) {
    exit("No news_commitment terms registered. Nothing to import.\n");
}

$base_datetime = new DateTimeImmutable('2026-06-25 09:00:00', wp_timezone());
$created = 0;
$updated = 0;
$skipped = 0;

foreach (array_values($terms) as $index => $term) {
    $number = $index + 1;
    $slug = 'test-selection-commitment-' . $term->slug;
    $title = '【テスト】セレクションのこだわり - ' . $term->name;
    $post_datetime = $base_datetime->modify('+' . $index . ' hours');
    $post_date = $post_datetime->format('Y-m-d H:i:s');
    $post_date_gmt = get_gmt_from_date($post_date);

    $existing = get_page_by_path($slug, OBJECT, 'selection_commitment');

    if ($existing && !$force) {
        $skipped++;
        echo "Skipped existing commitment: {$title}\n";
        continue;
    }

    $post_data = [
        'post_type' => 'selection_commitment',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => $term->name . 'のこだわり紹介ページのテストデータです。',
        'post_excerpt' => $term->name . 'のこだわりを紹介します。',
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
        'commitment_fv_image' => '',
        'commitment_lead_text' => "{$term->name}について、セレクションが大切にしている品質・鮮度・おいしさを紹介するテスト用リード文です。",
        'commitment_buyer_icon' => '',
        'commitment_buyer_message_title' => $term->name . '担当バイヤーより',
        'commitment_buyer_message' => "<p>{$term->name}の魅力が伝わるよう、産地や売場での取り組みをわかりやすくまとめています。</p>",
    ];

    for ($scene_number = 1; $scene_number <= 3; $scene_number++) {
        $prefix = 'commitment_scene_' . $scene_number;
        $meta[$prefix . '_subtitle'] = $term->name . ' こだわり現場' . $scene_number;
        $meta[$prefix . '_producer_title'] = $term->name . 'を支える現場の取り組み';
        $meta[$prefix . '_producer_image'] = '';
        $meta[$prefix . '_producer_text'] = "テストデータ{$number}-{$scene_number}です。産地や加工現場で大切にしているポイントを紹介します。";
        $meta[$prefix . '_product_title'] = $term->name . 'のおいしさの理由';
        $meta[$prefix . '_product_image'] = '';
        $meta[$prefix . '_product_text'] = "売場で手に取ったときに魅力が伝わるよう、品質・鮮度・価格のバランスを確認しています。";

        for ($image_number = 1; $image_number <= 4; $image_number++) {
            $meta[$prefix . '_gallery_image_' . $image_number] = '';
        }
    }

    foreach ($meta as $key => $value) {
        if (function_exists('update_field')) {
            update_field($key, $value, $post_id);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }

    if ($existing) {
        $updated++;
        echo "Updated commitment: {$title} (#{$post_id}) term:{$term->slug}\n";
    } else {
        $created++;
        echo "Created commitment: {$title} (#{$post_id}) term:{$term->slug}\n";
    }
}

echo "Done. Terms: " . count($terms) . ", Created: {$created}, Updated: {$updated}, Skipped: {$skipped}\n";
