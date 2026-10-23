<?php
function foods_job_mail_addresses() {
    return ['行徳店' => '04', '八潮店' => '03', '西原店' => '05', '三郷店' => '02',
        '西船橋店' => '06', '花野井店' => '07', 'しいの木台店' => '08',
        '水産加工センター' => '010', '青葉台店' => '011', '松戸店' => '12', '西新井店' => '13'];
}
function foods_job_mail_address($store) {
    if (is_array($store)) { $store = count($store) === 1 ? reset($store) : ''; }
    if ($store === '船橋水産加工センター') { $store = '水産加工センター'; }
    $addresses = foods_job_mail_addresses();
    return is_string($store) && isset($addresses[$store]) ? 'noreply' . $addresses[$store] . '@foods-selection.co.jp' : '';
}
add_filter('wpcf7_special_mail_tags', static function ($output, $name) {
    if (!in_array($name, ['_foods_job_address', '_foods_job_store'], true)) { return $output; }
    $submission = WPCF7_Submission::get_instance();
    $store = $submission ? $submission->get_posted_data('store') : '';
    if ($name === '_foods_job_address') { return foods_job_mail_address($store); }
    if (is_array($store)) { $store = count($store) === 1 ? reset($store) : ''; }
    return is_string($store) ? $store : '';
}, 20, 2);
add_filter('wpcf7_contact_form_properties', static function ($properties, $form) {
    $ids = get_option('foods_cf7_forms', []);
    if ((int) $form->id() !== (int) ($ids['job'] ?? 0) && $form->name() !== 'foods-form-job') { return $properties; }
    $subject = 'パートナー採用応募申込＜[_foods_job_store]＞';
    $prefix = '差出人: [name] <[email]>' . "\n題名: " . $subject . "\n\n";
    $properties['form'] = preg_replace_callback('/\[select\*?\s+(store|job|contact_time)\b([^\]]*)\]/', static function ($match) {
        $options = preg_replace('/\s+(?:include_blank|first_as_label|default:[^\s\]]+)/', '', $match[2]);
        $type = $match[1] === 'contact_time' ? 'select' : 'select*';
        return '[' . $type . ' ' . $match[1] . ' default:1' . $options . ']';
    }, $properties['form']);
    $properties['form'] = preg_replace_callback('/\[number\*?\s+age\b([^\]]*)\]/', static function ($match) {
        $options = preg_replace('/\s+(?:min|step):[^\s\]]+/', '', $match[1]);
        return '[number* age min:0 step:1' . $options . ']';
    }, $properties['form']);
    $details = "[ お名前 ]\n[name]\n\n[ お名前（カナ） ]\n[name_kana]\n\n[ 年齢 ]\n[age]歳\n\n[ 電話番号 ]\n[phone]\n\n[ メールアドレス ]\n[email]\n\n[ 希望職種 ]\n[job]\n\n[ 都合の良い連絡時間帯 ]\n[contact_time]\n\n[ その他 ]\n[message]";
    $signature = 'このメールは 株式会社セレクション採用サイト (http://foods-selection.co.jp/job) の';
    $properties['mail'] = array_merge($properties['mail'], [
        'active' => true, 'recipient' => '[_foods_job_address]', 'sender' => '[name] <[email]>',
        'subject' => $subject, 'additional_headers' => '', 'attachments' => '',
        'use_html' => false, 'exclude_blank' => false,
        'body' => $prefix . $details . "\n\n--\n" . $signature . 'お問い合わせフォームから送信されました',
    ]);
    $properties['mail_2'] = array_merge($properties['mail_2'], [
        'active' => true, 'recipient' => '[email]',
        'sender' => '株式会社セレクション採用サイト <[_foods_job_address]>',
        'subject' => '株式会社セレクション・パートナー採用応募申込ありがとうございます',
        'additional_headers' => '', 'attachments' => '', 'use_html' => false, 'exclude_blank' => false,
        'body' => $prefix . "この度は、株式会社セレクション採用情報WEBサイトからご応募いただきありがとうございます。\n後ほど、担当者からご連絡いたします。\nいましばらくお待ちください。\n\nご応募いただきました内容は下記の通りです。\n\n"
            . $details . "\n\n--\n" . $signature . '採用情報応募フォームから送信されました',
    ]);
    return $properties;
}, 20, 2);
add_filter('wpcf7_contact_form_properties', static function ($properties, $form) {
    $ids = get_option('foods_cf7_forms', []);
    foreach (['graduate', 'career'] as $kind) {
        if ((int) $form->id() !== (int) ($ids[$kind] ?? 0) && $form->name() !== 'foods-form-' . $kind) { continue; }
        $address = 'noreply01@foods-selection.co.jp';
        $title = $kind === 'graduate' ? '新卒採用' : 'キャリア採用';
        $subject = $title . '応募申込＜' . $title . '＞';
        $prefix = '差出人: [name] <[email]>' . "\n題名: " . $subject . "\n\n";
        $details = [];
        foreach (foods_application_config($kind)['fields'] as $name => $label) {
            $details[] = '[ ' . $label . " ]\n[" . $name . ']';
        }
        $details = implode("\n\n", $details);
        $signature = "\n\n--\nこのメールは 株式会社セレクション採用サイト (http://foods-selection.co.jp/job) の";
        $properties['mail'] = array_merge($properties['mail'], [
            'active' => true, 'recipient' => $address,
            'sender' => '株式会社セレクション採用サイト <' . ($kind === 'graduate' ? $address : '[email]') . '>',
            'subject' => $subject,
            'body' => $prefix . $details . $signature . 'お問い合わせフォームから送信されました',
            'additional_headers' => '', 'attachments' => '', 'use_html' => false, 'exclude_blank' => false,
        ]);
        $properties['mail_2'] = array_merge($properties['mail_2'], [
            'active' => true, 'recipient' => '[email]',
            'sender' => '株式会社セレクション採用サイト <' . $address . '>',
            'subject' => '株式会社セレクション・' . $title . '応募申込ありがとうございます',
            'body' => $prefix . "この度は、株式会社セレクション採用情報WEBサイトからご応募いただきありがとうございます。\n後ほど、担当者からご連絡いたします。\nいましばらくお待ちください。\n\nご応募いただきました内容は下記の通りです。\n\n"
                . $details . $signature . '採用情報応募フォームから送信されました',
            'additional_headers' => '', 'attachments' => '', 'use_html' => false, 'exclude_blank' => false,
        ]);
        break;
    }
    return $properties;
}, 20, 2);
function foods_application_config($kind) {
    $paths = ['job' => '/job/entry/', 'graduate' => '/entry/', 'career' => '/career-entry/'];
    if (!isset($paths[$kind])) { return null; }
    $fields = $kind === 'job'
        ? ['store' => '応募店舗', 'job' => '応募職種', 'name' => 'お名前', 'name_kana' => 'お名前（カナ）', 'phone' => '電話番号', 'age' => '年齢', 'email' => 'メールアドレス', 'contact_time' => '都合の良い連絡時間帯', 'message' => 'その他']
        : ['application_type' => '応募区分', 'name' => 'お名前', 'name_kana' => 'お名前（カナ）', 'postal_code' => '郵便番号', 'address_1' => '住所1', 'address_2' => '住所2（建物名）', 'phone' => '電話番号', 'birth_date' => '生年月日', 'education' => '最終学歴', 'email' => 'メールアドレス', 'message' => 'その他内容'];
    return ['path' => $paths[$kind], 'title' => $kind === 'job' ? 'パートアルバイト応募' : ($kind === 'career' ? 'キャリア採用応募' : '新卒採用応募'), 'fields' => $fields];
}
function foods_application_values($kind) {
    return foods_cf7_values($kind);
}
function foods_application_validate($kind, $values) {
    $config = foods_application_config($kind);
    if (!$config) { return false; }
    $required = $kind === 'job' ? ['job', 'name', 'phone', 'age', 'email'] : array_keys($config['fields']);
    foreach ($required as $key) { if (($values[$key] ?? '') === '') { return false; } }
    if (!is_email($values['email']) || $values['email'] !== $values['email_confirm'] || $values['privacy_agree'] !== '1') { return false; }
    if ($kind !== 'job') { return in_array($values['application_type'], ['新卒', 'インターン', 'キャリア'], true); }
    return in_array($values['job'], array_values(foods_get_job_occupation_choices()), true)
        && ($values['store'] === '' || in_array($values['store'], array_values(foods_get_job_shop_choices()), true))
        && preg_match('/^[0-9]+$/D', $values['age'])
        && in_array($values['contact_time'], ['', 'いつでも', '午前', '午後', '夕方以降'], true);
}
function foods_application_check_request($kind) {
    $nonce = $_POST['foods_application_nonce'] ?? '';
    if (!foods_application_config($kind) || !is_string($nonce) || !wp_verify_nonce($nonce, 'foods_application_' . $kind)) {
        if (in_array($kind, ['graduate', 'career'], true)) {
            wp_die('入力画面の有効期限が切れています。応募画面を再読み込みしてから再度お進みください。', '入力画面の再読み込み', ['response' => 400, 'back_link' => true]);
        }
        wp_die('入力内容を確認し、応募画面から再度お進みください。', '入力内容の確認', ['response' => 400, 'back_link' => true]);
    }
    if ($kind === 'job' && ($error = foods_job_application_error($_POST)) !== '') {
        wp_die(esc_html($error), '募集受付の確認', ['response' => 400, 'back_link' => true]);
    }
    if (!foods_cf7_preview_validate($kind, $errors)) {
        $message = in_array($kind, ['graduate', 'career'], true)
            ? '<ul><li>' . implode('</li><li>', array_map('esc_html', $errors)) . '</li></ul>'
            : '入力内容を確認し、応募画面から再度お進みください。';
        wp_die($message, '入力内容の確認', ['response' => 400, 'back_link' => true]);
    }
}
function foods_recruit_route_query($path) {
    foreach ([$path, 'recurit', 'recruit'] as $candidate) {
        $page = get_page_by_path($candidate, OBJECT, 'page');
        if ($page && $page->post_status === 'publish') { return ['page_id' => $page->ID]; }
    }
    return ['pagename' => $path];
}
add_action('parse_request', static function ($wp) {
    if (trim($wp->request, '/') === 'recruit/entry') {
        $wp->query_vars = array_merge(foods_recruit_route_query('recruit/entry'), ['foods_graduate_entry' => 1]);
    }
    foreach (['job', 'graduate', 'career'] as $kind) {
        $config = foods_application_config($kind);
        foreach (['confirm', 'thanks'] as $step) {
            $legacy_path = $kind === 'graduate' ? 'recruit/entry' : ($kind === 'career' ? 'recruit/career-entry' : 'job/entry');
            if (in_array(trim($wp->request, '/'), [trim($config['path'], '/') . '/' . $step, $legacy_path . '/' . $step], true)) {
                $page_query = $kind === 'job' ? ['pagename' => 'job'] : foods_recruit_route_query(trim($config['path'], '/'));
                $wp->query_vars = array_merge($page_query, ['foods_application_kind' => $kind, 'foods_application_step' => $step]);
            }
        }
    }
});
add_filter('page_template', static function ($template) {
    if (get_query_var('foods_graduate_entry')) { return __DIR__ . '/../page-recruit-entry.php'; }
    return get_query_var('foods_application_kind') ? __DIR__ . '/../page-application-result.php' : $template;
}, 20);
add_filter('redirect_canonical', static function ($redirect) {
    return get_query_var('foods_application_kind') || get_query_var('foods_graduate_entry') ? false : $redirect;
});
function foods_application_send() {
    $kind = $_POST['foods_application_kind'] ?? '';
    $kind = is_string($kind) ? $kind : '';
    foods_application_check_request($kind);
    if ($kind === 'job' && !foods_job_mail_address(foods_application_values($kind)['store'] ?? '')) {
        wp_die('応募店舗の採用メール送信先が未設定です。送信先の設定をご確認ください。', '送信設定の確認', ['response' => 503, 'back_link' => true]);
    }
    $token = $_POST['foods_application_token'] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{32}$/D', $token) || get_transient('foods_application_' . $token) !== $kind) {
        wp_die('確認画面の有効期限が切れています。応募画面から再度お進みください。', '再入力のお願い', ['response' => 400, 'back_link' => true]);
    }
    delete_transient('foods_application_' . $token);
    foods_cf7_deliver($kind, foods_application_config($kind)['path'] . 'thanks/');

}
add_action('admin_post_foods_application_send', 'foods_application_send');
add_action('admin_post_nopriv_foods_application_send', 'foods_application_send');
