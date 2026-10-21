<?php
/** Instagram source is connected by the account owner in Smash Balloon. */
defined('ABSPATH') || exit;

add_action('customize_register', function ($customizer) {
    $customizer->add_section('foods_instagram', [
        'title' => 'SNS / Instagram',
        'priority' => 160,
    ]);
    $customizer->add_setting('foods_instagram_feed_id', [
        'default' => 0,
        'sanitize_callback' => 'absint',
    ]);
    $customizer->add_control('foods_instagram_feed_id', [
        'section' => 'foods_instagram',
        'label' => 'InstagramフィードID',
        'description' => 'Instagram Feedで接続・保存したフィードのIDを入力してください。0は未接続（現在の固定画像を表示）です。',
        'type' => 'number',
        'input_attrs' => ['min' => 0, 'step' => 1],
    ]);
});

function foods_instagram_feed_html() {
    $feed_id = absint(get_theme_mod('foods_instagram_feed_id', 0));
    if (!$feed_id || !shortcode_exists('instagram-feed')) {
        return '';
    }

    return do_shortcode(sprintf(
        '[instagram-feed feed="%d" num="12" showheader="false" showbio="false" showfollow="false" showbutton="false"]',
        $feed_id
    ));
}
