<?php
/**
 * Set the common recipe eyecatch and detail images.
 *
 * Dry run:
 *   php tools/set-recipe-images.php
 *
 * Apply:
 *   php tools/set-recipe-images.php --apply
 */

if (PHP_SAPI !== 'cli') {
    exit("This script can only be run from the command line.\n");
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';
if (!file_exists($wp_load)) {
    exit("wp-load.php was not found: {$wp_load}\n");
}

require_once $wp_load;

$apply = in_array('--apply', $argv, true);
$recipe_posts = get_posts([
    'post_type' => 'recipe',
    'post_status' => ['publish', 'draft', 'pending', 'private', 'future'],
    'posts_per_page' => -1,
    'orderby' => 'ID',
    'order' => 'ASC',
]);

if (empty($recipe_posts)) {
    exit("No recipe posts were found.\n");
}

$parent_bowl_posts = array_values(array_filter($recipe_posts, static function ($post) {
    return str_contains(trim(wp_strip_all_tags(get_the_title($post))), '親子丼');
}));

if (count($parent_bowl_posts) !== 1) {
    exit(sprintf("Expected exactly one 親子丼 recipe, found %d.\n", count($parent_bowl_posts)));
}

$normalize_image_id = static function ($value) {
    if (is_array($value)) {
        $value = $value['ID'] ?? $value['id'] ?? 0;
    }

    return is_numeric($value) ? (int) $value : 0;
};

$get_image_id = static function ($post_id, $field_name) use ($normalize_image_id) {
    $value = function_exists('get_field') ? get_field($field_name, $post_id) : null;
    if (empty($value)) {
        $value = get_post_meta($post_id, $field_name, true);
    }

    return $normalize_image_id($value);
};

$parent_bowl = $parent_bowl_posts[0];
$parent_bowl_photo_id = $get_image_id($parent_bowl->ID, 'recipe_photo');
if ($parent_bowl_photo_id <= 0 || get_post_type($parent_bowl_photo_id) !== 'attachment') {
    exit("The 親子丼 recipe does not have a valid detail image.\n");
}

$mask_attachments = get_posts([
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'meta_query' => [
        [
            'key' => '_wp_attached_file',
            'value' => 'Mask-レシピ背景_画像.png',
            'compare' => 'LIKE',
        ],
    ],
]);
$mask_image_ids = array_values(array_filter(array_map('intval', $mask_attachments), static function ($attachment_id) {
    return wp_basename((string) get_post_meta($attachment_id, '_wp_attached_file', true)) === 'Mask-レシピ背景_画像.png';
}));
if (count($mask_image_ids) !== 1) {
    exit(sprintf(
        "Expected exactly one Mask-レシピ背景_画像.png attachment, found %d.\n",
        count($mask_image_ids)
    ));
}

$mask_image_id = $mask_image_ids[0];
$backup_rows = [];

foreach ($recipe_posts as $recipe_post) {
    $backup_rows[] = [
        'post_id' => (int) $recipe_post->ID,
        'title' => get_the_title($recipe_post),
        'post_status' => get_post_status($recipe_post),
        'recipe_eyecatch_image' => $get_image_id($recipe_post->ID, 'recipe_eyecatch_image'),
        'recipe_photo' => $get_image_id($recipe_post->ID, 'recipe_photo'),
    ];
}

echo sprintf(
    "Mode: %s\nRecipes: %d\nEyecatch image: #%d %s\nDetail image: #%d %s\n",
    $apply ? 'APPLY' : 'DRY RUN',
    count($recipe_posts),
    $mask_image_id,
    wp_basename((string) get_post_meta($mask_image_id, '_wp_attached_file', true)),
    $parent_bowl_photo_id,
    wp_basename((string) get_post_meta($parent_bowl_photo_id, '_wp_attached_file', true))
);

foreach ($backup_rows as $row) {
    echo sprintf(
        "#%d [%s] %s: eyecatch %d -> %d, photo %d -> %d\n",
        $row['post_id'],
        $row['post_status'],
        $row['title'],
        $row['recipe_eyecatch_image'],
        $mask_image_id,
        $row['recipe_photo'],
        $parent_bowl_photo_id
    );
}

if (!$apply) {
    exit(0);
}

$backup_dir = 'C:/AI-KOS/backups/foods-selectioncojp';
if (!is_dir($backup_dir) && !wp_mkdir_p($backup_dir)) {
    exit("Could not create backup directory: {$backup_dir}\n");
}

$backup_path = $backup_dir . '/recipe-images-before-' . current_time('Ymd-His') . '.json';
$backup = [
    'created_at' => current_time(DATE_ATOM),
    'eyecatch_image_id' => $mask_image_id,
    'detail_image_id' => $parent_bowl_photo_id,
    'recipes' => $backup_rows,
];

if (file_put_contents(
    $backup_path,
    wp_json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
) === false) {
    exit("Could not write backup: {$backup_path}\n");
}

$updated = 0;
foreach ($recipe_posts as $recipe_post) {
    $eyecatch_result = function_exists('update_field')
        ? update_field('field_recipe_eyecatch_image', $mask_image_id, $recipe_post->ID)
        : update_post_meta($recipe_post->ID, 'recipe_eyecatch_image', $mask_image_id);
    $photo_result = function_exists('update_field')
        ? update_field('field_recipe_photo', $parent_bowl_photo_id, $recipe_post->ID)
        : update_post_meta($recipe_post->ID, 'recipe_photo', $parent_bowl_photo_id);

    $saved_eyecatch_id = $get_image_id($recipe_post->ID, 'recipe_eyecatch_image');
    $saved_photo_id = $get_image_id($recipe_post->ID, 'recipe_photo');
    if ($saved_eyecatch_id !== $mask_image_id || $saved_photo_id !== $parent_bowl_photo_id) {
        exit(sprintf("Verification failed for recipe #%d.\n", $recipe_post->ID));
    }

    if ($eyecatch_result !== false || $photo_result !== false) {
        $updated++;
    }
}

echo sprintf("Updated: %d\nBackup: %s\n", $updated, $backup_path);
