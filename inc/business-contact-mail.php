<?php
// Apply the current wholesale-contact mail configuration to the CF7 business form.
add_filter('wpcf7_contact_form_properties', 'foods_business_contact_mail_properties', 20, 2);
function foods_business_contact_mail_properties($properties, $form) {
    $ids = get_option('foods_cf7_forms', []);
    $business_id = (int) ($ids['business'] ?? 0);
    if (($business_id <= 0 || (int) $form->id() !== $business_id) && $form->name() !== 'foods-form-business') {
        return $properties;
    }
    $line = '------------------------------------------------------------------';
    $divider = '==================================================================';
    $details = "[ お名前 ]\n[contact_name]\n\n[ メールアドレス ]\n[email]\n\n[ お問合せ内容 ]\n[message]\n";
    $properties['mail'] = array_merge($properties['mail'], [
        'active' => true,
        'recipient' => 'noreply09@foods-selection.co.jp',
        'sender' => '"" <[email]>',
        'subject' => 'お問合せ　（業者様）',
        'additional_headers' => '',
        'attachments' => '',
        'use_html' => false,
        'exclude_blank' => false,
        'body' => $line . "\nこのメールは、メールフォームから送信されたメッセージです\n" . $line
            . "\n[ 送信日時 ] [_foods_contact_datetime]\n[ 送信IPアドレス ] [_remote_ip]\n" . $divider . "\n\n" . $details,
    ]);
    $properties['mail_2'] = array_merge($properties['mail_2'], [
        'active' => true,
        'recipient' => '[email]',
        'sender' => '"" <noreply09@foods-selection.co.jp>',
        'subject' => 'お問合せありがとうございます',
        'additional_headers' => '',
        'attachments' => '',
        'use_html' => false,
        'exclude_blank' => false,
        'body' => $line . "\n本メールは、お問い合わせいただいた方に自動で送られる確認メールです。\n" . $line
            . "\n\n[contact_name] 様\n\nこのたびは、お問い合わせいただきありがとうございます。\nお問いあわせいただいた内容につきましては、\n再度確認してご連絡させていただきます。\n下記がご送信いただいた内容となります。\n" . $divider . "\n\n" . $details,
    ]);
    return $properties;
}

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
    if (!is_string($nonce) || !wp_verify_nonce($nonce, 'foods_business_contact')) {
        wp_die('入力画面の有効期限が切れています。お問い合わせ画面を再読み込みしてから再度お進みください。', '入力画面の再読み込み', ['response' => 400, 'back_link' => true]);
    }
    if (!foods_cf7_preview_validate('business', $errors)) {
        wp_die(esc_html(implode(' ', $errors)), '入力内容の確認', ['response' => 400, 'back_link' => true]);
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
    $token = $_POST['foods_business_token'] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{32}$/', $token) || !get_transient('foods_business_' . $token)) {
        wp_die('確認画面の有効期限が切れています。お問い合わせ画面から再度お進みください。', '再入力のお願い', ['response' => 400, 'back_link' => true]);
    }
    delete_transient('foods_business_' . $token);
    foods_cf7_deliver('business', '/company/business/contact/thanks/');

}
add_action('admin_post_foods_business_contact_send', 'foods_business_contact_send');
add_action('admin_post_nopriv_foods_business_contact_send', 'foods_business_contact_send');
