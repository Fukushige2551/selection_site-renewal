<?php
function foods_business_contact_fields() {
    return ['inquiry_type' => 'お問い合わせ内容（種別）', 'company_name' => '企業名', 'department' => '部署名', 'postal_code' => '郵便番号', 'address_1' => '住所1', 'address_2' => '住所2（建物名）', 'phone' => '電話番号', 'fax' => 'FAX番号', 'contact_name' => '担当者名', 'contact_name_kana' => '担当者名（フリガナ）', 'email' => 'メールアドレス', 'main_products' => '主力商品', 'site_url' => 'サイトURL', 'message' => 'お問い合わせ内容'];
}
function foods_business_contact_values() {
    return foods_cf7_values('business');
}
function foods_business_contact_validate($values) {
    foreach (['company_name', 'postal_code', 'address_1', 'phone', 'contact_name', 'message'] as $key) {
        if ($values[$key] === '') { return false; }
    }
    return in_array($values['inquiry_type'], ['お取引について', '商品について', 'その他'], true)
        && is_email($values['email']) && $values['email'] === $values['email_confirm'] && $values['privacy_agree'] === '1';
}
function foods_business_contact_check_request() {
    $nonce = $_POST['foods_business_nonce'] ?? '';
    if (!is_string($nonce) || !wp_verify_nonce($nonce, 'foods_business_contact') || !foods_cf7_preview_validate('business')) {
        wp_die('入力内容を確認し、お問い合わせ画面から再度お進みください。', '入力内容の確認', ['response' => 400, 'back_link' => true]);
    }
}
add_action('parse_request', static function ($wp) {
    foreach (['confirm', 'thanks'] as $step) {
        if (trim($wp->request, '/') === 'company/business/contact/' . $step) {
            $wp->query_vars = ['pagename' => 'company/business/contact', 'foods_business_step' => $step];
        }
    }
});
add_filter('page_template', static function ($template) {
    return get_query_var('foods_business_step') ? __DIR__ . '/../page-company-business-contact-result.php' : $template;
});
add_filter('redirect_canonical', static function ($redirect) {
    return get_query_var('foods_business_step') ? false : $redirect;
});
function foods_business_contact_send() {
    foods_business_contact_check_request();
    if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'foods-selectioncojp.local') {
        wp_die('この環境のメール送信先は設定されていません。', '送信設定の確認', ['response' => 503, 'back_link' => true]);
    }
    $token = $_POST['foods_business_token'] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{32}$/', $token) || !get_transient('foods_business_' . $token)) {
        wp_die('確認画面の有効期限が切れています。お問い合わせ画面から再度お進みください。', '再入力のお願い', ['response' => 400, 'back_link' => true]);
    }
    delete_transient('foods_business_' . $token);
    foods_cf7_deliver('business', '/company/business/contact/thanks/');

}
add_action('admin_post_foods_business_contact_send', 'foods_business_contact_send');
add_action('admin_post_nopriv_foods_business_contact_send', 'foods_business_contact_send');
