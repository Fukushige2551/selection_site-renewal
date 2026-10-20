<?php
// Local-only cleanup, strictly limited to the recorded manager image migration.
if (PHP_SAPI !== 'cli') { exit(1); }
define('DISABLE_WP_CRON', true);
$_SERVER['HTTP_HOST'] = 'foods-selectioncojp.local';
require dirname(__DIR__, 4) . '/wp-load.php';
if (parse_url(home_url(), PHP_URL_HOST) !== 'foods-selectioncojp.local') { exit(1); }
$record = json_decode(file_get_contents('C:/AI-KOS/backups/foods-selectioncojp/manager-admin-20261020/media-migration.json'), true);
$root = realpath(dirname(__DIR__) . '/img/page/single-shop/managers');
$backup = 'C:/AI-KOS/backups/foods-selectioncojp/manager-cleanup-20261020';
$targets = [];
foreach ($record['created'] as $created) {
    $id = (int) $created['id'];
    $file = get_attached_file($id);
    if (!$file || !preg_match('/^manager-[a-z]+-sp@2x\.(png|webp)$/', basename($file))) { continue; }
    if (realpath($file) !== realpath($created['file'])) { throw new RuntimeException('Media path mismatch'); }
    global $wpdb;
    $used = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ('manager_image','_thumbnail_id') AND meta_value = %s LIMIT 1", (string) $id));
    if ($used) { throw new RuntimeException("Image still selected: $id"); }
    $targets[$id] = $file;
}
echo count($targets) . " unused SP media; 36 source copies\n";
if (!in_array('--apply', $argv, true)) { exit; }
if (!is_dir($backup)) { mkdir($backup, 0777, true); }
$snapshot = [];
foreach ($targets as $id => $file) {
    $snapshot[$id] = ['post' => get_post($id, ARRAY_A), 'meta' => get_post_meta($id)];
    if (!copy($file, $backup . '/' . basename($file))) { throw new RuntimeException('Backup failed'); }
}
foreach ($record['shops'] as $shop) {
    foreach ($shop['files'] as $file) {
        if (!is_file($file) || realpath(dirname($file)) !== $root) { throw new RuntimeException('Source outside expected directory or missing'); }
        if (!copy($file, $backup . '/' . basename($file))) { throw new RuntimeException('Source backup failed'); }
    }
}
file_put_contents($backup . '/media.json', wp_json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
foreach ($targets as $id => $file) {
    if (!wp_delete_attachment($id, true)) { throw new RuntimeException("Delete failed: $id"); }
}
foreach ($record['created'] as $created) {
    $id = (int) $created['id'];
    if (get_post_type($id) !== 'attachment') { continue; }
    $variants = get_post_meta($id, '_foods_manager_image_variants', true);
    if (is_array($variants)) {
        unset($variants['sp_png'], $variants['sp_webp']);
        update_post_meta($id, '_foods_manager_image_variants', $variants);
    }
}
foreach ($record['shops'] as $shop) {
    foreach ($shop['files'] as $file) { if (!unlink($file)) { throw new RuntimeException('Source delete failed'); } }
    $selected = get_post_meta($shop['id'], 'manager_image', true);
    if (!foods_get_manager_responsive_assets($selected)) { throw new RuntimeException('Active images unavailable'); }
}
echo "Deleted 18 media attachments and 36 theme copies; 9 selected image pairs verified. Backup: $backup\n";
