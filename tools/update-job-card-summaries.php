<?php
/** LocalWP Site Shell: wp eval-file wp-content/themes/foods-selectioncojp-theme/tools/update-job-card-summaries.php [apply] */
if (!defined('WP_CLI') || !WP_CLI) { exit('Run through WP-CLI.'); }
if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'foods-selectioncojp.local') { WP_CLI::error('Local site only.'); }
$data = json_decode(file_get_contents(__DIR__ . '/job-card-summaries.json'), true, 512, JSON_THROW_ON_ERROR);
$posts = get_posts(['post_type' => 'recruit_part_time', 'post_status' => 'publish', 'numberposts' => -1]);
$by_source = [];
foreach ($posts as $post) {
    $source_id = (int) get_post_meta($post->ID, '_foods_legacy_job_id', true);
    if (!$source_id || isset($by_source[$source_id])) { WP_CLI::error('Unmapped or duplicate source job.'); }
    $by_source[$source_id] = $post;
}
if (count($posts) !== 34 || count($data['jobs']) !== 34) { WP_CLI::error('Expected exactly 34 jobs.'); }
$plan = [];
foreach ($data['jobs'] as $row) {
    $post = $by_source[$row['source_id']] ?? null;
    if (!$post || $post->post_title !== $row['title'] || get_post_meta($post->ID, 'shop_name', true) !== $row['shop_name']) { WP_CLI::error('Job identity mismatch.'); }
    if (!preg_match('/^時給 [0-9,]+円～?$/uD', $row['card_salary']) || substr_count($row['card_access'], "\n") !== 1) { WP_CLI::error('Invalid card summary.'); }
    $plan[] = ['id' => $post->ID, 'salary' => $row['card_salary'], 'access' => $row['card_access']];
    WP_CLI::log($post->ID . ' ' . $row['card_salary'] . ' / ' . str_replace("\n", ' / ', $row['card_access']));
}
if (!in_array('apply', $args ?? [], true)) { WP_CLI::success('Dry run: 34 jobs, no changes.'); return; }
$backup_dir = 'C:/AI-KOS/backups/foods-selectioncojp';
if (!wp_mkdir_p($backup_dir)) { WP_CLI::error('Backup directory unavailable.'); }
$backup = [];
foreach ($posts as $post) { $backup[] = ['post' => $post->to_array(), 'meta' => get_post_meta($post->ID)]; }
$backup_file = $backup_dir . '/before-job-card-summaries-' . gmdate('Ymd-His') . '.json';
if (file_put_contents($backup_file, wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) === false) { WP_CLI::error('Backup failed.'); }
// Register the two editorial fields in the existing SCF field group.
$result = foods_sync_recruit_part_time_scf_fields();
if (empty($result['field_group_id']) || $result['field_count'] !== count(foods_get_recruit_part_time_field_group()['fields'])) { WP_CLI::error('SCF field registration failed.'); }
update_option('foods_recruit_part_time_scf_fields_version', '20261020-job-card-summary-1');
foreach ($plan as $row) {
    foreach (['card_salary' => $row['salary'], 'card_access' => $row['access']] as $name => $value) {
        update_field('field_recruit_part_time_' . $name, $value, $row['id']);
        if (get_post_meta($row['id'], $name, true) !== $value) { WP_CLI::error('Summary verification failed: ' . $row['id']); }
    }
}
foreach ($backup as $before) {
    $id = $before['post']['ID'];
    $after = get_post_meta($id);
    foreach (['card_salary', '_card_salary', 'card_access', '_card_access'] as $key) { unset($after[$key], $before['meta'][$key]); }
    ksort($after); ksort($before['meta']);
    if ($after !== $before['meta'] || get_post_field('post_content', $id, 'raw') !== $before['post']['post_content']) { WP_CLI::error('Original job data changed: ' . $id); }
}
WP_CLI::success('Updated 34 cards; original job data unchanged. Backup: ' . $backup_file);
