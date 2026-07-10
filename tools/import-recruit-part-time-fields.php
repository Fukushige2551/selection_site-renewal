<?php
/**
 * Create the recruit_part_time field group in SCF/ACF.
 *
 * Browser usage on local development:
 *   /wp-content/themes/foods-selectioncojp-theme/tools/import-recruit-part-time-fields.php?token=foods-selection-recruit-fields
 *
 * CLI usage:
 *   php tools/import-recruit-part-time-fields.php
 */

const IMPORT_RECRUIT_PART_TIME_FIELDS_TOKEN = 'foods-selection-recruit-fields';

$is_cli = PHP_SAPI === 'cli';

if (!$is_cli) {
    header('Content-Type: text/plain; charset=UTF-8');

    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals(IMPORT_RECRUIT_PART_TIME_FIELDS_TOKEN, $token)) {
        http_response_code(403);
        exit("Invalid token.\n");
    }
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';

if (!file_exists($wp_load)) {
    exit("wp-load.php was not found: {$wp_load}\n");
}

require_once $wp_load;

if (!function_exists('foods_sync_recruit_part_time_scf_fields')) {
    exit("SCF sync function is unavailable.\n");
}

$field_group = foods_get_recruit_part_time_field_group();
$result = foods_sync_recruit_part_time_scf_fields();

if (empty($result['field_group_id'])) {
    exit("Failed to create or update field group.\n");
}

echo "Created or updated field group: {$field_group['title']} (#{$result['field_group_id']})\n";
echo "Target post type: recruit_part_time\n";
echo "Fields: {$result['field_count']}\n";
echo "Trashed duplicate fields: {$result['trashed_fields']}\n";
echo "Trashed duplicate groups: {$result['trashed_groups']}\n";
