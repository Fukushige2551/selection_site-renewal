<?php
// Isolated checks: a newly selected ordinary image must never inherit a store's fixed variants.
$media = [1 => 'image/png', 2 => 'image/png', 3 => 'image/webp', 4 => 'image/webp', 5 => 'image/jpeg'];
$mapping = [1 => ['pc_png' => 1, 'sp_png' => 2, 'pc_webp' => 3, 'sp_webp' => 4]];
function add_filter(...$args) {}
function get_post_type($id) { global $media; return isset($media[$id]) ? 'attachment' : false; }
function get_post_mime_type($id) { global $media; return $media[$id] ?? false; }
function get_post_meta($id, $key, $single) { global $mapping; return $mapping[$id] ?? ''; }
function wp_get_attachment_url($id) { return "https://example.test/uploads/$id"; }
require dirname(__DIR__) . '/inc/manager-images.php';
function verify($condition, $message) { if (!$condition) { throw new RuntimeException($message); } }
verify(foods_get_manager_responsive_assets(1) === ['png' => 'https://example.test/uploads/1', 'webp' => 'https://example.test/uploads/3'], 'Shared larger variants failed');
verify(foods_get_manager_responsive_assets(5) === null, 'Ordinary selected image inherited variants');
verify(foods_get_manager_responsive_assets(0) === null, 'Empty field inherited variants');
unset($media[3]);
verify(foods_get_manager_responsive_assets(1) === null, 'Deleted variant did not fall back');
$media[3] = 'image/png';
verify(foods_get_manager_responsive_assets(1) === null, 'Invalid MIME did not fall back');
echo "5 manager image checks passed\n";
