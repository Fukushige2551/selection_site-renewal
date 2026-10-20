<?php
/** Resolve image variants from the media selected in the existing manager_image field. */
function foods_get_manager_responsive_assets($attachment_id) {
    $attachment_id = (int) $attachment_id;
    if (!$attachment_id || get_post_type($attachment_id) !== 'attachment') {
        return null;
    }

    $variants = get_post_meta($attachment_id, '_foods_manager_image_variants', true);
    if (!is_array($variants)) {
        return null;
    }

    $assets = [];
    foreach (['png' => 'image/png', 'webp' => 'image/webp'] as $key => $mime) {
        // Reuse the larger approved image at every viewport width.
        $id = (int) ($variants['pc_' . $key] ?? 0);
        if (!$id || get_post_type($id) !== 'attachment' || get_post_mime_type($id) !== $mime) {
            return null;
        }
        $url = wp_get_attachment_url($id);
        if (!$url) {
            return null;
        }
        $assets[$key] = $url;
    }

    return $assets;
}

function foods_manager_image_field_instructions($field) {
    $field['instructions'] = '選択した画像をPC・SP共通で表示します。WebPの対応画像が登録された画像は、WebPを優先して表示します。';
    return $field;
}
add_filter('acf/load_field/name=manager_image', 'foods_manager_image_field_instructions');
