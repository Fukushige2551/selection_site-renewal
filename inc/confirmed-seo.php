<?php
// AIOSEO is installed for administration. Enable its frontend SEO output only
// when the remaining SEO specifications have been approved and configured.
add_filter('aioseo_disable', '__return_true');

/**
 * SEO values explicitly specified in the basic requirements workbook.
 * Other pages retain their existing WordPress output.
 */
function foods_confirmed_seo_title($title) {
    if (is_front_page()) {
        return 'フーズマーケットセレクション｜千葉・東京・埼玉のスーパーマーケット';
    }

    if (is_post_type_archive('shop') || (is_page() && basename((string) get_page_template()) === 'page-shop.php')) {
        return '店舗一覧｜フーズマーケットセレクション｜千葉・東京・埼玉のスーパーマーケット';
    }

    return $title;
}
add_filter('pre_get_document_title', 'foods_confirmed_seo_title');

function foods_confirmed_seo_head() {
    if (is_front_page()) {
        $description = 'フーズマーケットセレクション公式サイト。千葉・東京・埼玉で展開する地域密着型スーパーマーケットです。チラシやお買い得情報、店舗一覧、バイヤーが全国から選び抜いたこだわりの商品をご紹介しています。';
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    // Reuse the legacy image; its actual format is PNG despite the .ico name.
    // Let WordPress own icon output if a site icon is configured in the future.
    if (!has_site_icon()) {
        echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url(get_theme_file_uri('/img/favicon.png')) . '">' . "\n";
    }
}
add_action('wp_head', 'foods_confirmed_seo_head');
