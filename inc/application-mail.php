<?php
function foods_application_config($kind) {
    $paths = ['job' => '/job/entry/', 'graduate' => '/recruit/entry/', 'career' => '/recruit/career-entry/'];
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
    if (!foods_application_config($kind) || !is_string($nonce) || !wp_verify_nonce($nonce, 'foods_application_' . $kind) || !foods_cf7_preview_validate($kind)) {
        wp_die('入力内容を確認し、応募画面から再度お進みください。', '入力内容の確認', ['response' => 400, 'back_link' => true]);
    }
}
add_action('parse_request', static function ($wp) {
    foreach (['job', 'graduate', 'career'] as $kind) {
        $config = foods_application_config($kind);
        foreach (['confirm', 'thanks'] as $step) {
            if (trim($wp->request, '/') === trim($config['path'], '/') . '/' . $step) {
                $wp->query_vars = ['pagename' => $kind === 'job' ? 'job' : trim($config['path'], '/'), 'foods_application_kind' => $kind, 'foods_application_step' => $step];
            }
        }
    }
});
add_filter('page_template', static function ($template) {
    return get_query_var('foods_application_kind') ? __DIR__ . '/../page-application-result.php' : $template;
}, 20);
add_filter('redirect_canonical', static function ($redirect) {
    return get_query_var('foods_application_kind') ? false : $redirect;
});
function foods_application_send() {
    $kind = $_POST['foods_application_kind'] ?? '';
    $kind = is_string($kind) ? $kind : '';
    foods_application_check_request($kind);
    if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'foods-selectioncojp.local') {
        wp_die('この環境のメール送信先は設定されていません。', '送信設定の確認', ['response' => 503, 'back_link' => true]);
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
