<?php
/** Store/vendor contact mail delivery. */
// Keep the store-contact mail wording aligned with the current site's backup.
// CF7 reproduces the current site's recipients, senders and mail content.
add_filter('wpcf7_contact_form_properties', 'foods_contact_mail_wording', 20, 2);
function foods_contact_mail_wording($properties, $form) {
    $ids = get_option('foods_cf7_forms', []);
    if ((int) $form->id() !== (int) ($ids['contact'] ?? 0) && $form->name() !== 'foods-form-contact') {
        return $properties;
    }
    $details = "[ お名前 ]\n[your_name]\n\n[ メールアドレス ]\n[your_email]\n\n[ お問合せ内容 ]\n[message]";
    $line = '------------------------------------------------------------------';
    $divider = '==================================================================';
    $properties['mail']['active'] = true;
    $properties['mail']['recipient'] = '[_foods_contact_store_email]';
    // Explicit empty display names prevent WordPress's default name being added.
    $properties['mail']['sender'] = '"" <[your_email]>';
    $properties['mail']['additional_headers'] = '';
    $properties['mail']['use_html'] = false;
    $properties['mail_2']['active'] = true;
    $properties['mail_2']['recipient'] = '[your_email]';
    $properties['mail_2']['sender'] = '"" <[_foods_contact_store_email]>';
    $properties['mail_2']['additional_headers'] = '';
    $properties['mail_2']['use_html'] = false;
    $properties['mail']['subject'] = 'お問合せ　（[contact_store]）';
    $properties['mail']['body'] = $line . "\nこのメールは、メールフォームから送信されたメッセージです\n" . $line
        . "\n[ 送信日時 ] [_foods_contact_datetime]\n[ 送信IPアドレス ] [_remote_ip]\n" . $divider . "\n\n" . $details . "\n";
    $properties['mail_2']['subject'] = 'お問合せありがとうございます';
    $properties['mail_2']['body'] = $line . "\n本メールは、お問い合わせいただいた方に自動で送られる確認メールです。\n" . $line
        . "\n\n[your_name] 様\n\nこのたびは、お問い合わせいただきありがとうございます。\nお問い合わせいただいた内容につきましては、\n確認させていただきます。\n\n下記が、ご送信いただいた内容となります。\n尚、３営業日以内に返信がない場合は、\n直接対象店舗にお電話でご連絡ください。\n" . $divider . "\n\n" . $details . "\n";
    return $properties;
}

function foods_contact_store_email($store) {
    // CF7 normalizes selectable fields, including radios, to arrays.
    if (is_array($store)) {
        if (count($store) !== 1) { return ''; }
        $store = reset($store);
    }
    $stores = [
        '全店舗' => '01', '三郷店' => '02', '八潮店' => '03', '行徳店' => '04',
        '西原店' => '05', '西船橋店' => '06', '花野井店' => '07', 'しいの木台店' => '08',
        '青葉台店' => '11', '松戸店' => '12', '西新井店' => '13', '業者様' => '09',
    ];
    return is_string($store) && isset($stores[$store]) ? 'noreply' . $stores[$store] . '@foods-selection.co.jp' : '';
}

add_filter('wpcf7_special_mail_tags', 'foods_contact_mail_store_email', 20, 4);
function foods_contact_mail_store_email($output, $name, $html, $mail_tag = null) {
    if ($name !== '_foods_contact_store_email') { return $output; }
    $submission = WPCF7_Submission::get_instance();
    return $submission ? foods_contact_store_email($submission->get_posted_data('contact_store')) : '';
}

add_filter('wpcf7_special_mail_tags', 'foods_contact_mail_datetime', 20, 4);
function foods_contact_mail_datetime($output, $name, $html, $mail_tag = null) {
    if ($name !== '_foods_contact_datetime') { return $output; }
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) { return ''; }
    $timestamp = $submission->get_meta('timestamp');
    // The current FormMails implementation uses UTC+9 and English weekday names.
    return $timestamp ? gmdate('Y/m/d(D) H:i', (int) $timestamp + 9 * 60 * 60) : '';
}

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
    if (!is_string($nonce) || !wp_verify_nonce($nonce, 'foods_contact')) {
        wp_die('入力内容を確認し、お問い合わせ画面から再度お進みください。', '入力内容の確認', ['response' => 400, 'back_link' => true]);
    }
    if (!foods_cf7_preview_validate('contact', $errors)) {
        wp_die(esc_html(implode(' ', $errors)), '入力内容の確認', ['response' => 400, 'back_link' => true]);
    }
}

function foods_contact_send() {
    foods_contact_check_request();
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
