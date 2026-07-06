<?php
/**
 * Create the news field group in SCF.
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-news-fields.php?token=foods-selection-news-fields
 */

const IMPORT_NEWS_FIELDS_TOKEN = 'foods-selection-news-fields';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_NEWS_FIELDS_TOKEN, $token)) {
        http_response_code(403);
        exit("Invalid token.\n");
    }
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';

if (!file_exists($wp_load)) {
    exit("wp-load.php was not found: {$wp_load}\n");
}

require_once $wp_load;

if (!function_exists('foods_sync_news_scf_fields')) {
    exit("SCF sync function is unavailable.\n");
}

$field_group = foods_get_news_field_group();
$result = foods_sync_news_scf_fields();

if (empty($result['field_group_id'])) {
    exit("Failed to create or update field group.\n");
}

echo "Created or updated field group: {$field_group['title']} (#{$result['field_group_id']})\n";
echo "Target post type: news\n";
echo "Fields: {$result['field_count']}\n";
echo "Trashed duplicate fields: {$result['trashed_fields']}\n";
echo "Trashed duplicate groups: {$result['trashed_groups']}\n";
