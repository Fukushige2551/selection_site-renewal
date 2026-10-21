<?php
/** FV-only promotions have no public article or archive. */
add_action('init', static function () {
    register_post_type('fv_banner', [
        'labels' => [
            'name' => 'FVバナー', 'singular_name' => 'FVバナー',
            'add_new_item' => 'FVバナーを追加', 'edit_item' => 'FVバナーを編集',
        ],
        'public' => false, 'publicly_queryable' => false,
        'show_ui' => true, 'show_in_rest' => true,
        'exclude_from_search' => true, 'has_archive' => false, 'rewrite' => false,
        'menu_icon' => 'dashicons-images-alt2', 'menu_position' => 8,
        'supports' => ['title'],
    ]);
});

function foods_get_fv_banner_field_group() {
    $app_condition = [[['field' => 'field_fv_link_type', 'operator' => '==', 'value' => 'app']]];
    return [
        'key' => 'group_fv_banner', 'title' => 'FVバナー設定',
        'fields' => [
            ['key' => 'field_fv_image', 'name' => 'fv_image', 'label' => 'PC・共通画像', 'type' => 'image', 'return_format' => 'id', 'required' => 1],
            ['key' => 'field_fv_image_sp', 'name' => 'fv_image_sp', 'label' => 'SP画像（任意）', 'type' => 'image', 'return_format' => 'id', 'instructions' => '空欄の場合はPC・共通画像を使用します。'],
            ['key' => 'field_fv_link_type', 'name' => 'fv_link_type', 'label' => 'リンク方式', 'type' => 'select', 'choices' => ['url' => '指定URLへ移動', 'app' => 'アプリダウンロード'], 'default_value' => 'url', 'required' => 1],
            ['key' => 'field_fv_url', 'name' => 'fv_url', 'label' => '遷移先URL', 'type' => 'url', 'required' => 1, 'conditional_logic' => [[['field' => 'field_fv_link_type', 'operator' => '==', 'value' => 'url']]]],
            ['key' => 'field_fv_ios_url', 'name' => 'fv_ios_url', 'label' => 'App Store URL', 'type' => 'url', 'required' => 1, 'conditional_logic' => $app_condition],
            ['key' => 'field_fv_android_url', 'name' => 'fv_android_url', 'label' => 'Google Play URL', 'type' => 'url', 'required' => 1, 'conditional_logic' => $app_condition],
            ['key' => 'field_fv_order', 'name' => 'fv_order', 'label' => '表示順', 'type' => 'number', 'default_value' => 0, 'min' => 0, 'instructions' => '小さい番号から表示します。同じ番号の新着情報は新しい順です。下書きに戻すとFVから非表示になります。'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'fv_banner']]],
        'active' => true,
    ];
}

add_action('acf/init', static function () {
    if (is_admin() && get_option('foods_fv_banner_fields_version') !== '2') {
        $result = foods_sync_scf_field_group(foods_get_fv_banner_field_group());
        if (!empty($result['field_group_id']) && $result['field_count'] === 7) {
            update_option('foods_fv_banner_fields_version', '2');
        }
    }
});

function foods_get_fv_banner_items() {
    $posts = get_posts([
        'post_type' => 'fv_banner', 'post_status' => 'publish', 'numberposts' => 5,
        'meta_key' => 'fv_order', 'orderby' => ['meta_value_num' => 'ASC', 'date' => 'DESC', 'ID' => 'DESC'],
    ]);
    $items = [];
    foreach ($posts as $post) {
        $id = $post->ID;
        $image_id = (int) get_post_meta($id, 'fv_image', true) ?: get_post_thumbnail_id($id);
        $app = get_post_meta($id, 'fv_link_type', true) === 'app';
        $url = esc_url_raw(get_post_meta($id, 'fv_url', true), ['http', 'https']);
        $ios = esc_url_raw(get_post_meta($id, 'fv_ios_url', true), ['http', 'https']);
        $android = esc_url_raw(get_post_meta($id, 'fv_android_url', true), ['http', 'https']);
        if (!$image_id || ($app ? (!$ios || !$android) : !$url)) {
            continue;
        }
        $items[] = [
            'id' => $id, 'url' => $app ? '#fv-app-' . $id : $url,
            'image_url' => wp_get_attachment_image_url($image_id, 'full'),
            'image_alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_the_title($id),
            'sp_url' => wp_get_attachment_image_url((int) get_post_meta($id, 'fv_image_sp', true), 'full'),
            'app' => $app, 'ios' => $ios, 'android' => $android,
            'order' => (int) get_post_meta($id, 'fv_order', true),
            'date' => str_replace('-', '', substr($post->post_date, 0, 10)) . substr($post->post_date, 10), 'banner' => true,
        ];
    }
    return $items;
}

function foods_render_fv_app_dialogs($items) {
    foreach ($items as $item) {
        if (empty($item['app'])) { continue; }
        ?>
        <dialog class="fv-app-dialog" id="fv-app-<?php echo esc_attr($item['id']); ?>" aria-labelledby="fv-app-title-<?php echo esc_attr($item['id']); ?>">
            <button type="button" class="fv-app-dialog__close" aria-label="閉じる" autofocus>×</button>
            <h2 id="fv-app-title-<?php echo esc_attr($item['id']); ?>">公式アプリをダウンロード</h2>
            <div class="fv-app-dialog__stores">
                <?php foreach (['ios' => 'App Store', 'android' => 'Google Play'] as $key => $label) : ?>
                    <div class="fv-app-dialog__store">
                        <a href="<?php echo esc_url($item[$key]); ?>" class="fv-app-dialog__store-link"><?php echo esc_html($label); ?>でダウンロード</a>
                        <canvas class="fv-app-dialog__qr" data-qr-url="<?php echo esc_url($item[$key]); ?>" role="img" aria-label="<?php echo esc_attr($label . 'のダウンロード用QRコード'); ?>"></canvas>
                    </div>
                <?php endforeach; ?>
            </div>
            <noscript><p>上のリンクから各ストアへ移動できます。</p></noscript>
        </dialog>
        <?php
    }
}

/** Both common footers use the same configured stores as the official app FV. */
function foods_get_footer_app_item() {
    static $item;
    if (isset($item)) { return $item; }
    $post = get_page_by_path('app-renewal', OBJECT, 'fv_banner');
    $item = [
        'id' => 'footer', 'app' => true,
        'ios' => 'https://itunes.apple.com/jp/app/serekushonapuri/id662779931?mt=8',
        'android' => 'https://play.google.com/store/apps/details?id=com.misbit.selection&hl=ja',
    ];
    if ($post) {
        foreach (['ios', 'android'] as $platform) {
            $url = esc_url_raw(get_post_meta($post->ID, 'fv_' . $platform . '_url', true), ['http', 'https']);
            if ($url) { $item[$platform] = $url; }
        }
    }
    return $item;
}

function foods_footer_app_link_attributes() {
    $item = foods_get_footer_app_item();
    echo 'href="#fv-app-footer" data-fv-app data-ios-url="' . esc_url($item['ios']) . '" data-android-url="' . esc_url($item['android']) . '" aria-haspopup="dialog" aria-controls="fv-app-footer"';
    add_action('wp_footer', 'foods_render_footer_app_dialog', 5);
}

function foods_render_footer_app_dialog() {
    foods_render_fv_app_dialogs([foods_get_footer_app_item()]);
}
