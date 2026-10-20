<?php
/** Run in LocalWP Site Shell: wp eval-file wp-content/themes/foods-selectioncojp-theme/tools/import-current-jobs.php [apply] [publish] */
if (!defined('WP_CLI') || !WP_CLI) { exit('Run through WP-CLI.'); }
if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'foods-selectioncojp.local') { WP_CLI::error('Local site only.'); }
$snapshot = json_decode(file_get_contents(__DIR__ . '/current-jobs-snapshot.json'), true, 512, JSON_THROW_ON_ERROR);
$jobs = $snapshot['jobs'];
if (count($jobs) !== 34) { WP_CLI::error('Expected 34 source jobs.'); }
$apply = in_array('apply', $args ?? [], true);
$status = in_array('publish', $args ?? [], true) ? 'publish' : 'draft';
$existing = get_posts(['post_type' => 'recruit_part_time', 'post_status' => ['publish', 'draft', 'pending', 'private', 'future'], 'numberposts' => -1]);
if ($existing) { WP_CLI::error('Active jobs exist. Refusing to duplicate or overwrite them.'); }
foreach ($jobs as $job) {
    if (empty($job['source_id']) || empty($job['title']) || empty($job['conditions'])) { WP_CLI::error('Incomplete source.'); }
    WP_CLI::log($job['source_id'] . ' / ' . $job['title'] . ' / ' . $status);
}
if (!$apply) { WP_CLI::success('Dry run: no database changes.'); return; }
$backup_dir = 'C:/AI-KOS/backups/foods-selectioncojp';
if (!wp_mkdir_p($backup_dir)) { WP_CLI::error('Cannot create backup directory.'); }
$backup = $backup_dir . '/before-current-jobs-' . gmdate('Ymd-His') . '.json';
$all = get_posts(['post_type' => 'recruit_part_time', 'post_status' => 'any', 'numberposts' => -1]);
$all = array_merge($all, get_posts(['post_type' => 'recruit_part_time', 'post_status' => 'trash', 'numberposts' => -1]));
$data = array_map(static fn($post) => ['post' => $post->to_array(), 'meta' => get_post_meta($post->ID)], $all);
if (file_put_contents($backup, wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX) === false) { WP_CLI::error('Backup failed.'); }
$created = [];
foreach ($jobs as $job) {
    $wage_lines = array_filter(explode("\n", $job['conditions']), static fn($line) => preg_match('/円|時給|基本給|研修|昇給|アップ/u', $line));
    $fields = [
        'shop_name' => $job['shop_name'], 'job_type' => $job['occupation'],
        'salary' => implode("\n", $wage_lines), 'working_hours' => $job['conditions'],
        'work_location_access' => $job['work_location_access'], 'benefits' => $job['benefits'],
        'application_method' => $job['application_method'], 'job_lead' => $job['occupation_description'],
        '_foods_legacy_job_conditions' => $job['conditions'],
        '_foods_legacy_job_occupation' => $job['occupation_description'],
        '_foods_legacy_job_id' => (string) $job['source_id'], '_foods_legacy_job_url' => $job['source_url'],
    ];
    $id = wp_insert_post(wp_slash(['post_type' => 'recruit_part_time', 'post_status' => $status,
        'post_title' => $job['title'], 'post_content' => $job['source_html'], 'meta_input' => $fields]), true);
    if (is_wp_error($id)) { WP_CLI::error('Import stopped: ' . $id->get_error_message()); }
    if (get_post_field('post_content', $id, 'raw') !== $job['source_html']) { WP_CLI::error('Original HTML verification failed: ' . $id); }
    foreach ($fields as $name => $value) {
        if (get_post_meta($id, $name, true) !== $value) { WP_CLI::error('Verification failed for ' . $id . '/' . $name); }
    }
    $created[] = ['source_id' => $job['source_id'], 'id' => $id, 'url' => get_permalink($id)];
    WP_CLI::log('Created ' . $id . ': ' . $job['title']);
}
file_put_contents($backup_dir . '/current-jobs-created-' . gmdate('Ymd-His') . '.json', wp_json_encode($created, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
WP_CLI::success(count($created) . ' jobs created. Backup: ' . $backup);
