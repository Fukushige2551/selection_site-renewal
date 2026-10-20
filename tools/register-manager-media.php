<?php
/** One-time local migration of approved manager images into the existing media field. */
if (PHP_SAPI !== 'cli') { exit(1); }
define('DISABLE_WP_CRON', true);
$_SERVER['HTTP_HOST'] = 'foods-selectioncojp.local';
require dirname(__DIR__, 4) . '/wp-load.php';
if (parse_url(home_url(), PHP_URL_HOST) !== 'foods-selectioncojp.local') { exit("Local site required\n"); }
$apply = in_array('--apply', $argv, true);
$slugs = ['gyoutoku', 'nishifunabashi', 'nishihara', 'hananoi', 'aobadai', 'matsudo', 'misato', 'yashio', 'nishiarai'];
$plan = [];
foreach ($slugs as $slug) {
    $shop = get_page_by_path($slug, OBJECT, 'shop');
    if (!$shop) { throw new RuntimeException("Missing shop: $slug"); }
    $field = get_field_object('manager_image', $shop->ID);
    if (!$field || $field['type'] !== 'image') { throw new RuntimeException("Missing image field: $slug"); }
    $files = [];
    foreach (['pc_png', 'sp_png', 'pc_webp', 'sp_webp'] as $variant) {
        [$device, $ext] = explode('_', $variant);
        $file = dirname(__DIR__) . "/img/page/single-shop/managers/$slug-$device@2x.$ext";
        $size = getimagesize($file);
        if (!$size || $size[0] !== ($device === 'pc' ? 960 : 621) || $size[1] !== ($device === 'pc' ? 736 : 476)) { throw new RuntimeException("Invalid source: $file"); }
        $files[$variant] = $file;
    }
    $plan[$slug] = ['id' => $shop->ID, 'title' => $shop->post_title, 'field_key' => $field['key'], 'previous' => get_post_meta($shop->ID, 'manager_image', true), 'files' => $files];
}
echo count($plan) . " shops, 36 original files; " . ($apply ? 'APPLY' : 'DRY RUN') . "\n";
if (!$apply) { exit; }
$backup = 'C:/AI-KOS/backups/foods-selectioncojp/manager-admin-20261020/media-migration.json';
if (file_exists($backup)) { throw new RuntimeException('Migration backup already exists; inspect before rerunning.'); }
$state = ['shops' => $plan, 'created' => []];
file_put_contents($backup, wp_json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
try {
    foreach ($plan as $slug => $item) {
        $variants = [];
        foreach ($item['files'] as $variant => $source) {
            $upload = wp_upload_bits('manager-' . basename($source), null, file_get_contents($source));
            if ($upload['error']) { throw new RuntimeException($upload['error']); }
            if (hash_file('sha256', $source) !== hash_file('sha256', $upload['file'])) { throw new RuntimeException('Original image bytes changed'); }
            $size = getimagesize($upload['file']);
            $id = wp_insert_attachment(['post_title' => $item['title'] . ' 店長 ' . $variant, 'post_mime_type' => $size['mime'], 'post_status' => 'inherit'], $upload['file'], $item['id'], true);
            if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
            wp_update_attachment_metadata($id, ['width' => $size[0], 'height' => $size[1], 'file' => _wp_relative_upload_path($upload['file']), 'filesize' => filesize($upload['file']), 'sizes' => []]);
            update_post_meta($id, '_wp_attachment_image_alt', $item['title'] . ' 店長');
            $variants[$variant] = $id;
            $state['created'][] = ['id' => $id, 'file' => $upload['file']];
            file_put_contents($backup, wp_json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        foreach ($variants as $id) { update_post_meta($id, '_foods_manager_image_variants', $variants); }
        update_field($item['field_key'], $variants['pc_png'], $item['id']);
        if ((int) get_post_meta($item['id'], 'manager_image', true) !== $variants['pc_png'] || !foods_get_manager_responsive_assets($variants['pc_png'])) { throw new RuntimeException("Verification failed: $slug"); }
        echo "$slug: registered and verified\n";
    }
} catch (Throwable $error) {
    foreach ($plan as $item) { update_field($item['field_key'], $item['previous'], $item['id']); }
    throw $error;
}
echo "Complete; backup: $backup\n";
