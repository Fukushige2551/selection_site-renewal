<?php
/** Store/vendor contact mail delivery. */
function foods_contact_values() {
    return foods_cf7_values('contact');
}

function foods_contact_validate($values) {
    $stores = ['全店舗', '行徳店', '西船橋店', '花野井店', 'しいの木台店', '青葉台店', '西原店', '松戸店', '西新井店', '三郷店', '八潮店', '業者様'];
    return in_array($values['contact_store'], $stores, true)
        && $values['your_name'] !== '' && $values['message'] !== ''
        && is_email($values['your_email'])
        && $values['your_email'] === $values['your_email_confirm']
        && $values['privacy_agree'] === '1';
}

function foods_contact_check_request() {
    $nonce = $_POST['foods_contact_nonce'] ?? '';
    if (!is_string($nonce) || !wp_verify_nonce($nonce, 'foods_contact') || !foods_cf7_preview_validate('contact')) {
        wp_die('入力内容を確認し、お問い合わせ画面から再度お進みください。', '入力内容の確認', ['response' => 400, 'back_link' => true]);
    }
}

function foods_contact_send() {
    foods_contact_check_request();
    // This delivery configuration is restricted to the user's LocalWP development site.
    if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'foods-selectioncojp.local') {
        wp_die('この環境のメール送信先は設定されていません。', '送信設定の確認', ['response' => 503, 'back_link' => true]);
    }
    $values = foods_contact_values();
    $token = $_POST['foods_contact_token'] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{32}$/', $token) || !get_transient('foods_contact_' . $token)) {
        wp_die('確認画面の有効期限が切れています。お問い合わせ画面から再度お進みください。', '再入力のお願い', ['response' => 400, 'back_link' => true]);
    }
    delete_transient('foods_contact_' . $token);
    foods_cf7_deliver('contact', '/contact/thanks/');

}
add_action('admin_post_foods_contact_send', 'foods_contact_send');
add_action('admin_post_nopriv_foods_contact_send', 'foods_contact_send');
