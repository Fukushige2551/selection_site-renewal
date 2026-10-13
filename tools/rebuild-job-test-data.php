<?php
/**
 * LocalWP site shell:
 * wp eval-file wp-content/themes/foods-selectioncojp-theme/tools/rebuild-job-test-data.php
 * Inspect the plan first. To apply it, append: apply
 */
if (!defined('WP_CLI') || !WP_CLI) {
    exit('Run this file through WP-CLI.');
}
if (wp_parse_url(home_url('/'), PHP_URL_HOST) !== 'foods-selectioncojp.local') {
    WP_CLI::error('This command is restricted to foods-selectioncojp.local.');
}

$apply = in_array('apply', $args ?? [], true);
$shops = array_keys(foods_get_job_shop_choices());
$roles = array_keys(foods_get_job_occupation_choices());
$posts = get_posts([
    'post_type' => 'recruit_part_time',
    'post_status' => ['publish', 'draft', 'pending', 'private'],
    'posts_per_page' => -1,
    'orderby' => 'ID',
    'order' => 'ASC',
]);
$plan = [];
$backup = [];
foreach ($posts as $post) {
    if (!preg_match('/^【テスト[0-9０-９]+】/u', $post->post_title)) {
        continue;
    }
    $shop_text = get_post_meta($post->ID, 'shop_name', true) . ' ' . $post->post_title;
    $role_text = get_post_meta($post->ID, 'job_type', true) . ' ' . $post->post_title;
    $matched_shops = array_values(array_filter($shops, static fn($shop) => mb_strpos($shop_text, $shop) !== false));
    $matched_roles = [];
    foreach ($roles as $role) {
        $labels = $role === 'チェッカー（レジ）' ? ['チェッカー', 'レジ'] : [$role];
        foreach ($labels as $label) {
            if (mb_strpos($role_text, $label) !== false) {
                $matched_roles[] = $role;
                break;
            }
        }
    }
    if (count($matched_shops) !== 1 || count($matched_roles) !== 1) {
        WP_CLI::warning('Cannot uniquely identify shop/occupation for test job ' . $post->ID . '. No changes applied.');
        WP_CLI::error('Resolve ambiguous test data before applying the plan.');
    }
    $plan[] = ['ID' => $post->ID, 'title' => $post->post_title, 'shop_name' => $matched_shops[0], 'job_type' => $matched_roles[0]];
    $backup[] = ['post' => $post->to_array(), 'meta' => get_post_meta($post->ID)];
}
if (!$plan) {
    WP_CLI::error('No explicitly named test jobs were found.');
}
\WP_CLI\Utils\format_items('table', $plan, ['ID', 'title', 'shop_name', 'job_type']);
if (!$apply) {
    WP_CLI::success('Dry run only. Append apply to update these test jobs.');
    return;
}
$backup_dir = 'C:/AI-KOS/backups/foods-selectioncojp';
if (!wp_mkdir_p($backup_dir)) {
    WP_CLI::error('Cannot create the external backup directory.');
}
$backup_file = $backup_dir . '/job-test-data-' . gmdate('Ymd-His') . '-' . wp_generate_uuid4() . '.json';
$json = wp_json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
if ($json === false || file_put_contents($backup_file, $json, LOCK_EX) === false) {
    WP_CLI::error('Backup failed; test jobs were not updated.');
}
foreach ($plan as $item) {
    foreach (['shop_name', 'job_type'] as $field) {
        update_post_meta($item['ID'], $field, $item[$field]);
        if (get_post_meta($item['ID'], $field, true) !== $item[$field]) {
            WP_CLI::error('Verification failed for test job ' . $item['ID'] . '. Backup: ' . $backup_file);
        }
    }
    clean_post_cache($item['ID']);
}
WP_CLI::success('Updated ' . count($plan) . ' test jobs. Backup: ' . $backup_file);
