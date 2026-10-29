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
    $html = preg_replace_callback('/<a\b[^>]*>\s*プライバシーポリシー\s*<\/a>/u', static function ($match) {
        $link = preg_replace('/\bhref=(["\']).*?\1/u', 'href="' . esc_url(home_url('/privacy/')) . '"', $match[0], 1);
        $link = preg_replace('/\s+(?:target|rel)=(["\']).*?\1/u', '', $link);
        $link = preg_replace('/<a\b/u', '<a target="_blank" rel="noopener noreferrer"', $link, 1);
        return $link;
    }, $html);
    $html = str_replace('aria-required="true"', 'aria-required="true" required', $html);
    if ($kind === 'business') {
        $html = str_replace(['住所1', '住所2（建物名）'], ['住所１', '住所２（建物名）'], $html);
        // Keep CF7's empty-value option and required validation, but match the blank design.
        $html = preg_replace_callback('/(<select\b[^>]*\bname="inquiry_type"[^>]*>)(.*?)(<\/select>)/s', static function ($match) {
            $options = preg_replace('/(<option\b[^>]*\bvalue=""[^>]*>).*?(<\/option>)/s', '$1$2', $match[2], 1);
            return $match[1] . $options . $match[3];
        }, $html);
    }
    if ($kind === 'contact') {
        $html = str_replace('<label><input type="radio"', '<label class="p-contact-store__option"><input type="radio"', $html);
        $html = str_replace('<input type="checkbox"', '<input class="p-contact-privacy__checkbox" type="checkbox"', $html);
    }
    $actions = ['contact' => ['foods_contact', 'foods_contact_nonce'], 'business' => ['foods_business_contact', 'foods_business_nonce']];
    [$action, $name] = $actions[$kind] ?? ['foods_application_' . $kind, 'foods_application_nonce'];
    if ($kind === 'job' && isset($_GET['job_id']) && is_string($_GET['job_id']) && preg_match('/^[1-9][0-9]*$/D', $_GET['job_id'])) {
        $html = str_replace('</form>', '<input type="hidden" name="job_id" value="' . esc_attr($_GET['job_id']) . '"></form>', $html);
    }
    $html = str_replace('</form>', wp_nonce_field($action, $name, true, false) . '</form>', $html);
    echo $html;
}
function foods_cf7_deliver($kind, $thanks) {
    if ($kind === 'job' && ($error = foods_job_application_error($_POST)) !== '') {
        wp_die(esc_html($error), '募集受付の確認', ['response' => 400, 'back_link' => true]);
    }
    $form = foods_cf7_form($kind);
    if (!$form) { wp_die('フォームの設定を確認してください。', '送信設定の確認', ['response' => 503]); }
    $_POST['_wpcf7'] = (string) $form->id();
    $_POST['_wpcf7_unit_tag'] = 'wpcf7-f' . $form->id() . '-o1';
    $_POST['_wpnonce'] = wpcf7_create_nonce();
    $receipt_failed = false;
    $track_receipt_failure = static function () use (&$receipt_failed) {
        $mail = WPCF7_Mail::get_current();
        if ($mail && $mail->name() === 'mail_2') { $receipt_failed = true; }
    };
    $track_short_circuit = static function ($return) use ($track_receipt_failure) {
        if ($return !== null && !$return) { $track_receipt_failure(); }
        return $return;
    };
    if (in_array($kind, ['business', 'contact', 'job', 'graduate', 'career'], true)) {
        add_action('wp_mail_failed', $track_receipt_failure);
        add_filter('pre_wp_mail', $track_short_circuit, PHP_INT_MAX);
    }
    try {
        $result = $form->submit();
    } finally {
        if (in_array($kind, ['business', 'contact', 'job', 'graduate', 'career'], true)) {
            remove_action('wp_mail_failed', $track_receipt_failure);
            remove_filter('pre_wp_mail', $track_short_circuit, PHP_INT_MAX);
        }
    }
    if (($result['status'] ?? '') !== 'mail_sent') {
        if (in_array($kind, ['business', 'contact', 'job', 'graduate', 'career'], true)) {
            wp_die(foods_cf7_business_error_html($result, $form, $kind), '送信エラー', ['response' => 400, 'back_link' => true]);
        }
        wp_die(esc_html($result['message'] ?? '送信できませんでした。'), '送信エラー', ['response' => 400, 'back_link' => true]);
    }
    $redirect = home_url($thanks);
    if ($receipt_failed) { $redirect = add_query_arg('receipt', 'failed', $redirect); }
    wp_safe_redirect($redirect);
    exit;
}

function foods_cf7_business_error_html($result, $form, $kind = 'business') {
    $labels = array_merge(foods_cf7_labels($kind), [
        'email_confirm' => '確認用メールアドレス',
        'your_email_confirm' => '確認用メールアドレス',
        'privacy_agree' => 'プライバシーポリシーへの同意',
    ]);
    $translations = [
        'invalid_required' => '入力・選択してください。',
        'invalid_email' => 'メールアドレスの形式を確認してください。（例：name@example.com）',
        'invalid_url' => 'https://などを含むURLを入力してください。',
        'invalid_tel' => '半角数字の電話・FAX番号を入力してください。（例：03-1234-5678）',
        'invalid_number' => '数値を入力してください。',
        'invalid_too_short' => '入力文字数が不足しています。',
        'invalid_too_long' => '入力文字数が上限を超えています。',
        'invalid_number_too_small' => '入力値が下限を下回っています。',
        'invalid_number_too_large' => '入力値が上限を超えています。',
        'invalid_enum_value' => '選択内容を確認してください。',
    ];
    $items = [];
    foreach (($result['invalid_fields'] ?? []) as $name => $error) {
        $reason = (string) ($error['reason'] ?? '');
        $message = preg_match('/[\x{3040}-\x{30ff}\x{4e00}-\x{9fff}]/u', $reason)
            ? $reason : '入力形式・文字数・選択内容を確認してください。';
        foreach ($translations as $key => $translation) {
            if ($reason !== '' && $reason === $form->message($key)) { $message = $translation; break; }
        }
        $items[] = '<li>' . esc_html(($labels[$name] ?? '入力項目') . '：' . $message) . '</li>';
    }
    if ($items) {
        return '<p>以下の項目を確認して、お問い合わせ画面から修正してください。</p><ul>' . implode('', $items) . '</ul>';
    }
    $messages = [
        'validation_failed' => '入力内容を確認して、お問い合わせ画面から修正してください。',
        'acceptance_missing' => 'プライバシーポリシーへの同意を確認してください。',
        'spam' => '送信を受け付けられませんでした。時間をおいて再度お試しください。',
        'mail_failed' => 'メールを送信できませんでした。時間をおいて再度お試しください。',
    ];
    return '<p>' . esc_html($messages[$result['status'] ?? ''] ?? '送信できませんでした。時間をおいて再度お試しください。') . '</p>';
}
// Match confirmation emails on both the REST and native CF7 submission paths.
add_filter('wpcf7_validate_email*', static function ($result, $tag) {
    if (!in_array($tag->name, ['email_confirm', 'your_email_confirm'], true)) { return $result; }
    $original = $tag->name === 'email_confirm' ? 'email' : 'your_email';
    if (($_POST[$tag->name] ?? null) !== ($_POST[$original] ?? null)) { $result->invalidate($tag, 'メールアドレスと確認用メールアドレスが一致していません。'); }
    return $result;
}, 20, 2);
add_filter('wpcf7_form_tag', static function ($tag) {
    $was_array = is_array($tag);
    if ($was_array) { $tag = new WPCF7_FormTag($tag); }
    if ($tag->name === 'store' && $tag->basetype === 'select') { $tag->labels = $tag->values = $tag->raw_values = array_values(foods_get_job_shop_choices()); }
    if ($tag->name === 'job' && $tag->basetype === 'select') { $tag->labels = $tag->values = $tag->raw_values = array_values(foods_get_job_occupation_choices()); }
    if (in_array($tag->name, ['store', 'job'], true) && $tag->basetype === 'select'
        && foods_job_form_is_target(WPCF7_ContactForm::get_current())) {
        $id = isset($_GET['job_id']) && is_string($_GET['job_id']) && preg_match('/^[1-9][0-9]*$/D', $_GET['job_id']) ? (int) $_GET['job_id'] : 0;
        $choices = foods_job_application_choices($id);
        $options = $tag->name === 'store' ? array_keys($choices) : array_values(array_unique(array_merge([], ...array_values($choices))));
        $tag->labels = $tag->values = $tag->raw_values = $options;
    }
    if (in_array($tag->name, ['store', 'job'], true) && isset($_GET[$tag->name]) && is_string($_GET[$tag->name])) {
        $value = sanitize_text_field(wp_unslash($_GET[$tag->name]));
        $index = array_search($value, $tag->values, true);
        if ($index !== false) {
            $tag->options = array_values(array_filter($tag->options, static fn($option) => !str_starts_with($option, 'default:')));
            $tag->options[] = 'default:' . ($index + 1);
        }
    }
    return $was_array ? get_object_vars($tag) : $tag;
});

function foods_form_kana_names() { return ['name_kana', 'contact_name_kana', 'your_name_kana']; }
function foods_form_format_error($name, $value) {
    if ($value === '') { return ''; }
    if (in_array($name, foods_form_kana_names(), true) && !preg_match('/^[ァ-ヺー・　 ｦ-ﾟ]+$/uD', $value)) {
        return 'カナはカタカナと空白で入力してください。';
    }
    if ($name === 'birth_date') {
        if (!preg_match('/^([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/D', $value, $match)
            || !checkdate((int) $match[2], (int) $match[3], (int) $match[1]) || str_replace('/', '-', $value) > current_time('Y-m-d')) {
            return '生年月日は2000/01/01の形式で、実在する今日以前の日付を入力してください。';
        }
    }
    return '';
}
function foods_form_normalize_kana($name, $value) {
    return in_array($name, foods_form_kana_names(), true) && function_exists('mb_convert_kana')
        ? mb_convert_kana($value, 'KVas', 'UTF-8') : $value;
}
add_filter('wpcf7_contact_form_properties', static function ($properties, $form) {
    $ids = get_option('foods_cf7_forms', []);
    if (!in_array((int) $form->id(), array_map('intval', array_values($ids)), true)
        && !in_array($form->name(), ['foods-form-contact', 'foods-form-business', 'foods-form-job', 'foods-form-graduate', 'foods-form-career'], true)) { return $properties; }
    $properties['form'] = preg_replace_callback('/\[(?:text|date)(\*?)\s+birth_date\b([^\]]*)\]/', static function ($match) {
        $options = preg_replace('/\s+(?:max|min):[^\s\]]+|\s+placeholder(?:\s+"[^"]*")?/', '', $match[2]);
        return '[text' . $match[1] . ' birth_date' . $options . ' placeholder "2000/01/01"]';
    }, $properties['form']);
    return $properties;
}, 30, 2);
add_filter('wpcf7_validate', static function ($result, $tags) {
    foreach ($tags as $tag) {
        if (!in_array($tag->name, array_merge(foods_form_kana_names(), ['birth_date']), true)) { continue; }
        $raw = $_POST[$tag->name] ?? '';
        $value = is_string($raw) ? trim(wp_unslash($raw)) : '';
        $error = foods_form_format_error($tag->name, $value);
        if ($error !== '') { $result->invalidate($tag, $error); }
    }
    return $result;
}, 40, 2);
add_filter('wpcf7_posted_data', static function ($data) {
    foreach (foods_form_kana_names() as $name) {
        if (isset($data[$name]) && is_string($data[$name]) && foods_form_format_error($name, $data[$name]) === '') {
            $data[$name] = foods_form_normalize_kana($name, $data[$name]);
        }
    }
    return $data;
});
function foods_cf7_values($kind) {
    $form = foods_cf7_form($kind);
    $values = [];
    if (!$form) { return $values; }
    foreach ($form->scan_form_tags() as $tag) {
        if (!$tag->name) { continue; }
        $raw = $_POST[$tag->name] ?? '';
        if (is_array($raw)) { $raw = implode('、', array_filter($raw, 'is_string')); }
        if (in_array($kind, ['business', 'contact'], true) && isset(foods_business_byte_limits($kind)[$tag->name])) {
            // Preserve legacy text in hidden values; templates escape it at output.
            $values[$tag->name] = is_string($raw) ? rtrim(wp_unslash($raw)) : '';
        } else {
            $values[$tag->name] = is_string($raw) ? ($tag->basetype === 'textarea' ? sanitize_textarea_field(wp_unslash($raw)) : sanitize_text_field(wp_unslash($raw))) : '';
        }
    }
    foreach (foods_form_kana_names() as $name) {
        if (isset($values[$name]) && foods_form_format_error($name, $values[$name]) === '') { $values[$name] = foods_form_normalize_kana($name, $values[$name]); }
    }
    if ($kind === 'job') {
        $values['job_id'] = isset($_POST['job_id']) && is_string($_POST['job_id']) ? sanitize_text_field(wp_unslash($_POST['job_id'])) : '';
    }
    return $values;
}

function foods_cf7_preview_validate($kind, &$errors = null) {
    $errors = [];
    $form = foods_cf7_form($kind);
    if (!$form) { $errors[] = 'フォームの設定が見つかりません。'; return false; }
    $values = foods_cf7_values($kind);
    $labels = foods_cf7_labels($kind);
    if (in_array($kind, ['graduate', 'career'], true)) {
        $labels = array_merge($labels, foods_application_config($kind)['fields'], ['email_confirm' => '確認用メールアドレス']);
    }
    foreach ($form->scan_form_tags() as $tag) {
        if (!$tag->name) { continue; }
        $value = $values[$tag->name];
        $label = $labels[$tag->name] ?? ($tag->name === 'privacy_agree' ? 'プライバシーポリシーへの同意' : $tag->name);
        if (in_array($kind, ['business', 'contact'], true) && isset(foods_business_byte_limits($kind)[$tag->name])) {
            $limit = foods_business_byte_limits($kind)[$tag->name];
            $sending = in_array($_POST['action'] ?? '', ['foods_business_contact_send', 'foods_contact_send'], true);
            $raw = $_POST[$tag->name] ?? '';
            if (is_string($raw) && foods_business_byte_length(wp_unslash($raw), $tag->name, $sending) > $limit) { $errors[] = $label . 'はUTF-8で' . $limit . 'バイト以内で入力してください。'; }
        }
        if (($tag->is_required() || $tag->basetype === 'radio') && $value === '') { $errors[] = $label . 'を入力・選択してください。'; continue; }
        if ($value === '') { continue; }
        $format_error = foods_form_format_error($tag->name, $value);
        if ($format_error !== '') { $errors[] = $label . '：' . $format_error; }
        if ($kind === 'job' && $tag->name === 'age' && !preg_match('/^[0-9]+$/D', $value)) {
            $errors[] = '年齢は0以上の整数で入力してください。';
        }
        if ($tag->basetype === 'email' && !is_email($value)) { $errors[] = 'メールアドレスの形式を確認してください。（例：name@example.com）'; }
        if ($tag->basetype === 'url' && !filter_var($value, FILTER_VALIDATE_URL)) { $errors[] = $label . 'はhttps://などを含むURLを入力してください。'; }
        if (in_array($kind, ['business', 'graduate', 'career'], true) && $tag->basetype === 'tel' && !wpcf7_is_tel($value)) { $errors[] = $label . 'は半角数字の番号を入力してください。（例：03-1234-5678）'; }
        if ($tag->basetype === 'number' && !is_numeric($value)) { $errors[] = $label . 'は数値を入力してください。'; }
        if (in_array($tag->basetype, ['select', 'radio', 'checkbox'], true) && !in_array($value, $tag->values, true)) { $errors[] = $label . 'の選択内容を確認してください。'; }
    }
    foreach (['email_confirm' => 'email', 'your_email_confirm' => 'your_email'] as $confirm => $email) {
        if (isset($values[$confirm]) && $values[$confirm] !== ($values[$email] ?? '')) { $errors[] = 'メールアドレスと確認用メールアドレスが一致していません。'; }
    }
    return !$errors;
}

function foods_business_byte_limits($kind = 'business') {
    if ($kind === 'contact') { return ['your_name' => 50, 'your_email' => 100, 'message' => 2000]; }
    return ['contact_name' => 50, 'email' => 100, 'message' => 1000];
}
function foods_business_byte_length($value, $name, $sending = false) {
    // Hidden HTML attributes are decoded by the browser; only newline-to-br persists.
    if ($sending) { $value = rtrim($value); }
    if ($name === 'message') { $value = preg_replace('/\r\n|\r|\n/', $sending ? '<br>' : "\r\n", $value); }
    return strlen($value);
}
add_filter('wpcf7_validate', static function ($result, $tags) {
    $submission = WPCF7_Submission::get_instance();
    $form = $submission ? $submission->get_contact_form() : null;
    $kind = foods_cf7_legacy_kind($form);
    if (!$kind) { return $result; }
    foreach ($tags as $tag) {
        $limit = foods_business_byte_limits($kind)[$tag->name] ?? null;
        if (!$limit) { continue; }
        $raw = $_POST[$tag->name] ?? '';
        $value = is_string($raw) ? wp_unslash($raw) : '';
        if (is_string($value) && foods_business_byte_length($value, $tag->name, true) > $limit) {
            $result->invalidate($tag, 'UTF-8で' . $limit . 'バイト以内で入力してください。');
        }
    }
    return $result;
}, 30, 2);

// Preserve raw legacy text and convert kana only when rendering contact mail.
add_filter('wpcf7_mail_tag_replaced', static function ($replaced, $submitted, $html, $tag) {
    $submission = WPCF7_Submission::get_instance();
    $form = $submission ? $submission->get_contact_form() : null;
    $kind = foods_cf7_legacy_kind($form);
    if (!$kind) { return $replaced; }
    $name = $tag->field_name();
    if (!array_key_exists($name, foods_business_byte_limits($kind))) { return $replaced; }
    $raw = $_POST[$name] ?? null;
    $value = is_string($raw) ? wp_unslash($raw) : (string) $submitted;
    $value = rtrim($value);
    if ($name === 'message') { $value = str_replace('<br>', "\n", $value); }
    if (!in_array($name, ['email', 'your_email'], true) && function_exists('mb_convert_kana')) { $value = mb_convert_kana($value, 'K', 'UTF-8'); }
    return $html ? esc_html($value) : $value;
}, 30, 4);

function foods_cf7_legacy_kind($form) {
    if (!$form) { return null; }
    foreach (['business', 'contact'] as $kind) {
        $target = foods_cf7_form($kind);
        if ($target && (int) $target->id() === (int) $form->id()) { return $kind; }
    }
    return null;
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
    $manifest = foods_get_vite_manifest();
    $enqueue = static function ($handle, $entry, $dependencies = []) use ($manifest) {
        $file = $manifest[$entry]['file'] ?? null;
        if (!$file) { return; }
        $path = get_template_directory() . '/dist/' . $file;
        if (!is_file($path)) { return; }
        wp_enqueue_style($handle, get_template_directory_uri() . '/dist/' . $file, $dependencies, filemtime($path));
    };
    $enqueue('foods-cf7-layout', 'src/css/cf7-layout.css');
    if (is_page() && basename((string) get_page_template()) === 'page-contact.php') {
        $enqueue('foods-contact-responsive', 'src/css/contact-responsive.css', ['foods-cf7-layout']);
    }
});
