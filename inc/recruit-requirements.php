<?php
/** SEO-SD10: editable source shared by the requirements table and JobPosting. */
function foods_recruit_requirement_locations() {
    return array_map(static fn($template) => [['param' => 'page_template', 'operator' => '==', 'value' => $template]],
        ['page-recruit-entry.php', 'page-recruit-career-entry.php']);
}

function foods_recruit_requirement_tags() {
    return ['br' => [], 'small' => [], 'strong' => [], 'span' => ['class' => []]];
}

add_action('acf/init', static function () {
    if (!function_exists('acf_add_local_field_group')) { return; }
    acf_add_local_field_group([
        'key' => 'group_foods_recruit_requirements', 'title' => '募集要項（新卒・キャリア）',
        'location' => foods_recruit_requirement_locations(), 'position' => 'normal', 'menu_order' => 5,
        'fields' => [[
            'key' => 'field_foods_recruit_requirements', 'name' => 'recruit_requirements',
            'label' => '募集要項', 'type' => 'repeater', 'layout' => 'row', 'min' => 0,
            'button_label' => '募集要項の項目を追加',
            'instructions' => '画面と構造化データが共通で参照する正式な募集原稿です。項目名・内容・順序を編集できます。本文はHTML（br、small、strong、span class）に対応。単一求人・原稿確認のチェックは、変更後に再確認してください。新規ページに原稿は自動補完されません。',
            'sub_fields' => [
                ['key' => 'field_foods_recruit_requirement_label', 'name' => 'label', 'label' => '項目名', 'type' => 'text', 'required' => 1],
                ['key' => 'field_foods_recruit_requirement_value', 'name' => 'value', 'label' => '内容', 'type' => 'textarea', 'rows' => 4, 'new_lines' => '', 'required' => 0],
            ],
        ]],
    ]);
});

function foods_recruit_requirements($id) {
    $stored = function_exists('get_field') ? get_field('recruit_requirements', $id) : [];
    if (!is_array($stored)) { return []; }
    $rows = [];
    foreach ($stored as $row) {
        if (!is_array($row) || !is_scalar($row['label'] ?? null) || !is_scalar($row['value'] ?? null)) { continue; }
        $label = foods_seo_text($row['label']);
        $value = wp_kses((string) $row['value'], foods_recruit_requirement_tags());
        if ($label !== '' && foods_seo_text($value) !== '') { $rows[] = [$label, $value]; }
    }
    return $rows;
}

function foods_schema_recruit_page($id, $requirements) {
    if (!foods_schema_enabled() || !is_page() || get_queried_object_id() !== (int) $id
        || !foods_job_schema_target(get_post($id))) { return; }
    $rows = [];
    foreach ($requirements as [$label, $value]) {
        // Preserve repeated headings without dropping any of the public description.
        $rows[] = ['label' => $label, 'value' => $value];
    }
    $labels = array_column($rows, 'label');
    if (!in_array('職種', $labels, true) || !in_array('勤務時間', $labels, true)
        || (!in_array('給与', $labels, true) && !in_array('初任給', $labels, true))) { return; }
    $result = foods_job_schema_build(get_post($id), foods_job_schema_values($id), $rows);
    if ($result['node']) { foods_schema_add($result['node']); }
}
