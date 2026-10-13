<?php
/** CF7 manages fields and mail; the theme retains the separate confirmation screens. */
function foods_cf7_form($kind) {
    $ids = get_option('foods_cf7_forms', []);
    if (!class_exists('WPCF7_ContactForm')) { return null; }
    $form = WPCF7_ContactForm::get_instance($ids[$kind] ?? 0);
    if (!$form) {
        $post = get_page_by_path('foods-form-' . $kind, OBJECT, 'wpcf7_contact_form');
        $form = $post ? WPCF7_ContactForm::get_instance($post->ID) : null;
    }
    return $form;
}
function foods_cf7_render($kind, $path, $class) {
    $form = foods_cf7_form($kind);
    if (!$form) { echo '<p role="alert">フォームの設定を確認してください。</p>'; return; }
    $disable_autop = static function () { return false; };
    add_filter('wpcf7_autop_or_not', $disable_autop);
    $html = do_shortcode('[contact-form-7 id="' . $form->id() . '" html_class="' . esc_attr($class) . '"]');
    remove_filter('wpcf7_autop_or_not', $disable_autop);
    // Native posting to our confirmation screen must not trigger CF7's Ajax or non-Ajax mail controller.
    $html = preg_replace('/class="wpcf7(?=\s|")/', 'class="foods-cf7', $html, 1);
    $html = preg_replace('/(<form\b[^>]*\baction=")[^"]*/', '$1' . esc_url(home_url($path)), $html, 1);
    $html = str_replace('name="_wpcf7"', 'name="foods_cf7_id"', $html);
    $html = str_replace('name="privacy_agree[]"', 'name="privacy_agree"', $html);
    $html = str_replace('aria-required="true"', 'aria-required="true" required', $html);
    if ($kind === 'contact') {
        $html = str_replace('<label><input type="radio"', '<label class="p-contact-store__option"><input type="radio"', $html);
        $html = str_replace('<input type="checkbox"', '<input class="p-contact-privacy__checkbox" type="checkbox"', $html);
    }
    $actions = ['contact' => ['foods_contact', 'foods_contact_nonce'], 'business' => ['foods_business_contact', 'foods_business_nonce']];
    [$action, $name] = $actions[$kind] ?? ['foods_application_' . $kind, 'foods_application_nonce'];
    $html = str_replace('</form>', wp_nonce_field($action, $name, true, false) . '</form>', $html);
    echo $html;
}
function foods_cf7_deliver($kind, $thanks) {
    $form = foods_cf7_form($kind);
    if (!$form) { wp_die('フォームの設定を確認してください。', '送信設定の確認', ['response' => 503]); }
    $_POST['_wpcf7'] = (string) $form->id();
    $_POST['_wpcf7_unit_tag'] = 'wpcf7-f' . $form->id() . '-o1';
    $_POST['_wpnonce'] = wpcf7_create_nonce();
    $result = $form->submit();
    if (($result['status'] ?? '') !== 'mail_sent') {
        wp_die(esc_html($result['message'] ?? '送信できませんでした。'), '送信エラー', ['response' => 400, 'back_link' => true]);
    }
    wp_safe_redirect(home_url($thanks));
    exit;
}
// Match confirmation emails on both the REST and native CF7 submission paths.
add_filter('wpcf7_validate_email*', static function ($result, $tag) {
    if (!in_array($tag->name, ['email_confirm', 'your_email_confirm'], true)) { return $result; }
    $original = $tag->name === 'email_confirm' ? 'email' : 'your_email';
    if (($_POST[$tag->name] ?? null) !== ($_POST[$original] ?? null)) { $result->invalidate($tag, 'メールアドレスが一致していません。'); }
    return $result;
}, 20, 2);
add_filter('wpcf7_form_tag', static function ($tag) {
    $was_array = is_array($tag);
    if ($was_array) { $tag = new WPCF7_FormTag($tag); }
    if ($tag->name === 'store' && $tag->basetype === 'select') { $tag->values = $tag->raw_values = array_values(foods_get_job_shop_choices()); }
    if ($tag->name === 'job' && $tag->basetype === 'select') { $tag->values = $tag->raw_values = array_values(foods_get_job_occupation_choices()); }
    if (in_array($tag->name, ['store', 'job'], true) && isset($_GET[$tag->name]) && is_string($_GET[$tag->name])) {
        $value = sanitize_text_field(wp_unslash($_GET[$tag->name]));
        $index = array_search($value, $tag->values, true);
        if ($index !== false) { $tag->options[] = 'default:' . ($index + 1); }
    }
    return $was_array ? get_object_vars($tag) : $tag;
});

function foods_cf7_values($kind) {
    $form = foods_cf7_form($kind);
    $values = [];
    if (!$form) { return $values; }
    foreach ($form->scan_form_tags() as $tag) {
        if (!$tag->name) { continue; }
        $raw = $_POST[$tag->name] ?? '';
        if (is_array($raw)) { $raw = implode('、', array_filter($raw, 'is_string')); }
        $values[$tag->name] = is_string($raw) ? ($tag->basetype === 'textarea' ? sanitize_textarea_field(wp_unslash($raw)) : sanitize_text_field(wp_unslash($raw))) : '';
    }
    return $values;
}

function foods_cf7_preview_validate($kind) {
    $form = foods_cf7_form($kind);
    if (!$form) { return false; }
    $values = foods_cf7_values($kind);
    foreach ($form->scan_form_tags() as $tag) {
        if (!$tag->name) { continue; }
        $value = $values[$tag->name];
        if (($tag->is_required() || $tag->basetype === 'radio') && $value === '') { return false; }
        if ($value === '') { continue; }
        if ($tag->basetype === 'email' && !is_email($value)) { return false; }
        if ($tag->basetype === 'url' && !filter_var($value, FILTER_VALIDATE_URL)) { return false; }
        if ($tag->basetype === 'number' && !is_numeric($value)) { return false; }
        if (in_array($tag->basetype, ['select', 'radio', 'checkbox'], true) && !in_array($value, $tag->values, true)) { return false; }
    }
    foreach (['email_confirm' => 'email', 'your_email_confirm' => 'your_email'] as $confirm => $email) {
        if (isset($values[$confirm]) && $values[$confirm] !== ($values[$email] ?? '')) { return false; }
    }
    return true;
}

function foods_cf7_labels($kind) {
    $form = foods_cf7_form($kind);
    if (!$form) { return []; }
    $definition = $form->prop('form');
    $labels = [];
    foreach ($form->scan_form_tags() as $tag) {
        if (!$tag->name || in_array($tag->name, ['privacy_agree', 'email_confirm', 'your_email_confirm'], true)) { continue; }
        $label = $tag->name;
        if (preg_match('/<label\b[^>]*>([^\[]*)\[' . preg_quote($tag->type, '/') . '\s+' . preg_quote($tag->name, '/') . '(?:\s|\])/', $definition, $match)) {
            $label = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($match[1])));
            $label = trim(str_replace('*必須', '', $label));
        }
        if ($tag->name === 'contact_store') { $label = 'お問い合わせ先'; }
        if ($tag->name === 'application_type') { $label = '応募区分'; }
        $labels[$tag->name] = $label;
    }
    return $labels;
}

add_action('wp_enqueue_scripts', static function () {
    wp_enqueue_style('foods-cf7-layout', get_template_directory_uri() . '/src/css/cf7-layout.css', [], filemtime(__DIR__ . '/../src/css/cf7-layout.css'));
    if (is_page() && basename((string) get_page_template()) === 'page-contact.php') {
        wp_enqueue_style('foods-contact-responsive', get_template_directory_uri() . '/src/css/contact-responsive.css', ['foods-cf7-layout'], filemtime(__DIR__ . '/../src/css/contact-responsive.css'));
    }
});
