<?php
/** SEO-SD09: confirmed vacancy information, without migrating or guessing values. */
function foods_job_schema_fields() {
    $definitions = [
        ['status', '受付状態', 'select', ['open' => '募集中', 'closed' => '受付終了'], '「受付終了」にして更新すると求人一覧から除外し、詳細に募集終了を表示して、応募とJobPosting出力を停止します。未確認の新規求人は未選択のままにします。'],
        ['date_posted', '実際の募集開始日', 'date_picker', [], '最初に募集を掲載した日。WordPressへの移行日・更新日ではありません。'],
        ['deadline', '応募期限の有無', 'select', ['none' => '期限なし', 'specified' => '期限あり'], '確認後に選択してください。未選択では出力しません。'],
        ['valid_through', '応募受付の終了日時', 'date_time_picker', [], '期限ありの場合に入力。WordPress設定のタイムゾーン（' . wp_timezone_string() . '）で扱います。'],
        ['title', '確認済みの職務名', 'text', [], '一つの求人の職務名のみ。店舗名・会社名・給与・「募集中」等は含めません。'],
        ['employer', '雇用主の法人名', 'text', [], '実際の雇用主を確認して入力してください。運営会社名の自動補完はしません。'],
        ['country', '勤務地の国コード', 'text', [], '確認済みのISO 3166-1 alpha-2コード。例：JP。初期値は空欄です。'],
        ['region', '勤務地の都道府県', 'text', [], '募集原稿に掲載している実際の勤務先。会社本部の所在地で代用しません。'],
        ['locality', '勤務地の市区町村', 'text', [], '確認済みの市区町村を入力してください。'],
        ['street', '勤務地の町名・番地・建物名', 'text', [], 'アクセス案内や電話番号は含めず、住所を入力してください。'],
        ['postal_code', '勤務地の郵便番号（任意）', 'text', [], '確認できる場合だけ入力してください。'],
        ['employment_type', '雇用区分（任意）', 'select', foods_job_schema_employment_types(), '確認できる場合だけ選択。未選択ならこの項目を省略します。'],
        ['verified', '求人情報の確認', 'true_false', [], '一つの求人であること、職種・仕事内容・給与・勤務条件・勤務地・応募方法が現在の正式な募集情報と一致することを確認してチェックしてください。管理欄の確認済み情報は募集要項にも表示されます。'],
    ];
    $fields = [];
    foreach ($definitions as [$name, $label, $type, $choices, $instructions]) {
        $field = ['key' => 'field_foods_job_schema_' . $name, 'name' => 'job_schema_' . $name,
            'label' => $label, 'type' => $type, 'instructions' => $instructions,
            'required' => 0, 'default_value' => $type === 'true_false' ? 0 : ''];
        if ($type === 'select') {
            $field += ['choices' => $choices, 'allow_null' => 1, 'return_format' => 'value', 'multiple' => 0];
        } elseif ($type === 'date_picker') {
            $field += ['display_format' => 'Y/m/d', 'return_format' => 'Y-m-d'];
        } elseif ($type === 'date_time_picker') {
            $field += ['display_format' => 'Y/m/d H:i:s', 'return_format' => 'Y-m-d H:i:s',
                'conditional_logic' => [[['field' => 'field_foods_job_schema_deadline', 'operator' => '==', 'value' => 'specified']]]];
        }
        $fields[] = $field;
    }
    $location_fields = [];
    foreach (['name' => '店舗・拠点名', 'country' => '国コード', 'region' => '都道府県', 'locality' => '市区町村', 'street' => '町名・番地・建物名', 'postal_code' => '郵便番号（任意）'] as $name => $label) {
        $location_fields[] = ['key' => 'field_foods_job_location_' . $name, 'name' => $name, 'label' => $label, 'type' => 'text', 'required' => 0];
    }
    $fields[] = ['key' => 'field_foods_job_schema_locations', 'name' => 'job_schema_locations',
        'label' => '複数の勤務地（確認済み）', 'type' => 'repeater', 'layout' => 'row', 'min' => 0,
        'instructions' => '同じ求人の勤務地を原稿に記載された範囲で登録します。登録がある場合は単一勤務地欄に代わって使用し、住所が不足する行がある場合はJobPostingを出力しません。',
        'sub_fields' => $location_fields];
    return $fields;
}

function foods_job_schema_employment_types() {
    return ['FULL_TIME' => 'フルタイム', 'PART_TIME' => 'パート・アルバイト', 'CONTRACTOR' => '契約',
        'TEMPORARY' => '臨時', 'INTERN' => 'インターン', 'VOLUNTEER' => 'ボランティア',
        'PER_DIEM' => '日単位', 'OTHER' => 'その他'];
}

add_action('acf/init', static function () {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(['key' => 'group_foods_job_schema', 'title' => '求人構造化データ（確認後に入力）',
            'fields' => foods_job_schema_fields(), 'location' => array_merge([[['param' => 'post_type', 'operator' => '==', 'value' => 'recruit_part_time']]], foods_recruit_requirement_locations()),
            'position' => 'normal', 'menu_order' => 10, 'active' => true, 'show_in_rest' => 0]);
    }
});

add_filter('manage_recruit_part_time_posts_columns', static function ($columns) {
    $columns['foods_job_reception'] = '募集受付';
    return $columns;
});

add_action('manage_recruit_part_time_posts_custom_column', static function ($column, $id) {
    if ($column !== 'foods_job_reception') { return; }
    $status = get_post_meta($id, 'job_schema_status', true);
    $labels = ['open' => '募集中', 'closed' => '受付終了'];
    echo esc_html($labels[$status] ?? '未確認');
    if ($status === 'open' && !foods_job_schema_date(get_post_meta($id, 'job_schema_date_posted', true))) {
        echo '<br><span class="description">募集開始日未確認：JobPosting出力保留</span>';
    }
}, 10, 2);

function foods_job_schema_values($id) {
    $data = [];
    foreach (foods_job_schema_fields() as $field) {
        if ($field['name'] === 'job_schema_locations') {
            $locations = function_exists('get_field') ? get_field($field['name'], $id) : [];
            $data['locations'] = is_array($locations) ? $locations : [];
            continue;
        }
        $value = get_post_meta($id, $field['name'], true);
        $data[substr($field['name'], strlen('job_schema_'))] = is_scalar($value) ? trim((string) $value) : '';
    }
    return $data;
}

/** Validate every registered workplace; never substitute headquarters or drop invalid rows. */
function foods_job_schema_locations($data) {
    $locations = !empty($data['locations']) && is_array($data['locations']) ? $data['locations'] : [$data];
    $nodes = [];
    foreach ($locations as $location) {
        if (!is_array($location)) { return []; }
        foreach (['country', 'region', 'locality', 'street'] as $key) {
            if (foods_seo_text($location[$key] ?? '') === '') { return []; }
        }
        if (!preg_match('/^[A-Z]{2}$/D', $location['country'])) { return []; }
        $address = ['@type' => 'PostalAddress', 'addressCountry' => $location['country'],
            'addressRegion' => foods_seo_text($location['region']), 'addressLocality' => foods_seo_text($location['locality']),
            'streetAddress' => foods_seo_text($location['street'])];
        if (foods_seo_text($location['postal_code'] ?? '') !== '') { $address['postalCode'] = foods_seo_text($location['postal_code']); }
        $node = ['@type' => 'Place', 'address' => $address];
        if (foods_seo_text($location['name'] ?? '') !== '') { $node['name'] = foods_seo_text($location['name']); }
        $nodes[] = $node;
    }
    return $nodes;
}

/** Strict calendar parsing in the site's timezone; never use server defaults. */
function foods_job_schema_date($value, $with_time = false) {
    $format = $with_time ? 'Y-m-d H:i:s' : (preg_match('/^\d{8}$/D', $value) ? 'Ymd' : 'Y-m-d');
    $date = DateTimeImmutable::createFromFormat('!' . $format, $value, wp_timezone());
    return $date && $date->format($format) === $value ? $date : null;
}

/** Only real stored fields that the template actually renders, never demo fallbacks. */
function foods_job_schema_source_rows($id, $visible_rows) {
    $imported = (bool) get_post_meta($id, '_foods_legacy_job_id', true);
    $sources = $imported
        ? ['募集職種' => '_foods_legacy_job_occupation', '職種別 勤務時間・時給' => '_foods_legacy_job_conditions', '待遇' => 'benefits', '勤務地' => 'work_location_access', 'お問合せ' => 'application_method']
        : ['職種' => 'job_type', '給与' => 'salary', '勤務地' => 'work_location_access', '勤務時間' => 'working_hours', '待遇/福利厚生' => 'benefits', 'その他' => 'application_method'];
    $required = $imported
        ? ['_foods_legacy_job_occupation', '_foods_legacy_job_conditions', 'work_location_access']
        : ['job_type', 'salary', 'work_location_access', 'working_hours'];
    foreach ($required as $key) {
        if (foods_seo_text(get_post_meta($id, $key, true)) === '') {
            return []; // Do not turn a fragment or demo-backed vacancy into a job description.
        }
    }
    $rows = [];
    foreach ($sources as $label => $key) {
        $raw = get_post_meta($id, $key, true);
        if (isset($visible_rows[$label]) && is_scalar($raw) && foods_seo_text($raw) !== '') {
            $rows[$label] = $visible_rows[$label];
        }
    }
    return $rows;
}

function foods_job_schema_description($rows) {
    $html = '';
    foreach ($rows as $label => $value) {
        if (is_array($value) && isset($value['label'], $value['value'])) {
            $label = $value['label'];
            $value = $value['value'];
        }
        $value = is_scalar($value) ? preg_replace('~</(?:p|div|span|small|strong|li)>~i', "$0\n", (string) $value) : $value;
        $text = is_scalar($value) ? trim(wp_strip_all_tags(preg_replace('~<br\s*/?>~i', "\n", (string) $value))) : '';
        if ($text !== '') {
            $html .= '<p>' . esc_html($label) . '<br>' . nl2br(esc_html($text)) . '</p>';
        }
    }
    return $html;
}

/** Pure validation/build step, shared by all current and future vacancies. */
function foods_job_schema_target($post) {
    return $post && ($post->post_type === 'recruit_part_time'
        || ($post->post_type === 'page' && in_array(get_page_template_slug($post),
            ['page-recruit-entry.php', 'page-recruit-career-entry.php'], true)));
}

function foods_job_schema_build($post, $data, $rows, $now = null) {
    $errors = [];
    $now = $now ?: current_datetime();
    if (!foods_job_schema_target($post) || $post->post_status !== 'publish' || $post->post_password !== '') {
        $errors[] = '公開中・パスワードなしの対象求人詳細が対象です。';
    }
    if (($data['status'] ?? '') !== 'open') { $errors[] = '受付状態が「募集中」ではありません。'; }
    if (($data['verified'] ?? '') !== '1') { $errors[] = '求人原稿の確認が完了していません。'; }
    foreach (['title' => '職務名', 'employer' => '雇用主'] as $key => $label) {
        if (foods_seo_text($data[$key] ?? '') === '') { $errors[] = $label . 'が未入力です。'; }
    }
    $locations = foods_job_schema_locations($data);
    if (!$locations) { $errors[] = '勤務地の国コード・都道府県・市区町村・町名番地が未入力または不正です。'; }
    $start = foods_job_schema_date($data['date_posted'] ?? '');
    if (!$start || $start > $now) { $errors[] = '実際の募集開始日が未入力・不正、または未来の日付です。'; }
    $deadline = $data['deadline'] ?? '';
    $end = null;
    if ($deadline === 'specified') {
        $end = foods_job_schema_date($data['valid_through'] ?? '', true);
        if (!$end || $end <= $now || ($start && $end < $start)) { $errors[] = '応募期限が未入力・不正、または期限切れです。'; }
    } elseif ($deadline !== 'none') {
        $errors[] = '応募期限の有無が未確認です。';
    }
    $type = $data['employment_type'] ?? '';
    if ($type !== '' && !isset(foods_job_schema_employment_types()[$type])) { $errors[] = '雇用区分が不正です。'; }
    $description = foods_job_schema_description($rows);
    if (!$rows || foods_seo_text($description) === '') { $errors[] = '表示中の正式な募集説明がありません。'; }
    if ($errors) { return ['node' => null, 'errors' => $errors]; }
    $url = get_permalink($post);
    $employer = ['@type' => 'Organization', 'name' => foods_seo_text($data['employer'])];
    if (preg_replace('/\s+/u', '', $employer['name']) === preg_replace('/\s+/u', '', foods_company_identity()['name'])) {
        $employer['@id'] = home_url('/#organization');
    }
    $node = ['@type' => 'JobPosting', '@id' => $url . '#job', 'url' => $url,
        'title' => foods_seo_text($data['title']), 'description' => $description,
        'datePosted' => $start->format('Y-m-d'), 'hiringOrganization' => $employer,
        'jobLocation' => count($locations) === 1 ? $locations[0] : $locations];
    if ($end) { $node['validThrough'] = $end->format(DATE_ATOM); }
    if ($type !== '') { $node['employmentType'] = $type; }
    return ['node' => $node, 'errors' => []];
}

/** Publish only confirmed metadata; keep draft/unchecked management inputs private. */
function foods_job_schema_public_rows($data) {
    if (($data['verified'] ?? '') !== '1') { return []; }
    $rows = [];
    $labels = ['title' => '職務名', 'employer' => '雇用主', 'country' => '勤務国', 'region' => '勤務地（都道府県）', 'locality' => '勤務地（市区町村）', 'street' => '勤務地（町名・番地）', 'postal_code' => '勤務地の郵便番号'];
    foreach ($labels as $key => $label) {
        if (!empty($data['locations']) && in_array($key, ['country', 'region', 'locality', 'street', 'postal_code'], true)) { continue; }
        if (foods_seo_text($data[$key] ?? '') !== '') { $rows[$label] = $data[$key]; }
    }
    if (!empty($data['locations'])) {
        foreach (foods_job_schema_locations($data) as $index => $place) {
            $address = $place['address'];
            $rows['勤務地 ' . ($index + 1)] = ($place['name'] ?? '') . ' ' . $address['addressRegion'] . $address['addressLocality'] . $address['streetAddress'];
        }
    }
    $statuses = ['open' => '募集中', 'closed' => '受付終了'];
    $end = ($data['deadline'] ?? '') === 'specified' ? foods_job_schema_date($data['valid_through'] ?? '', true) : null;
    if (isset($statuses[$data['status'] ?? ''])) {
        $rows['受付状態'] = $end && $end <= current_datetime() ? '受付終了' : $statuses[$data['status']];
    }
    $start = foods_job_schema_date($data['date_posted'] ?? '');
    if ($start) { $rows['募集開始日'] = $start->format('Y-m-d'); }
    if (($data['deadline'] ?? '') === 'specified') {
        $end = foods_job_schema_date($data['valid_through'] ?? '', true);
        if ($end) { $rows['応募期限'] = $end->format('Y-m-d H:i:s T'); }
    }
    $types = foods_job_schema_employment_types();
    if (isset($types[$data['employment_type'] ?? ''])) { $rows['雇用形態'] = $types[$data['employment_type']]; }
    return $rows;
}

function foods_schema_job($id, $visible_rows) {
    if (!foods_schema_enabled() || !is_singular('recruit_part_time') || get_queried_object_id() !== (int) $id) { return; }
    $data = foods_job_schema_values($id);
    $source_rows = foods_job_schema_source_rows($id, $visible_rows);
    $result = foods_job_schema_build(get_post($id), $data, $source_rows);
    if ($result['node']) { foods_schema_add($result['node']); }
}
