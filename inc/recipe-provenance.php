<?php
/** Explicitly confirmed provenance; never infer authors or publication dates from a URL. */
add_action('acf/init', static function () {
    if (!function_exists('acf_add_local_field_group')) { return; }
    acf_add_local_field_group([
        'key' => 'group_foods_recipe_provenance', 'title' => 'レシピの著者・時間・公開日（確認済み情報）',
        'fields' => [
            ['key' => 'field_recipe_author_organization', 'name' => 'recipe_author_organization', 'label' => '原典の著者（組織名）', 'type' => 'text', 'instructions' => '出典で確認した著者の組織名。運営会社やWordPress投稿者から推測しません。'],
            ['key' => 'field_recipe_author_url', 'name' => 'recipe_author_url', 'label' => '著者の公式URL', 'type' => 'url'],
            ['key' => 'field_recipe_time_scope', 'name' => 'recipe_time_scope', 'label' => '調理時間の意味', 'type' => 'select', 'choices' => ['total' => '総所要時間（totalTime）'], 'allow_null' => 1, 'default_value' => '', 'return_format' => 'value', 'instructions' => '総所要時間と確認できた場合のみ選択。未確認では構造化データの時間を出力しません。'],
            ['key' => 'field_recipe_published_on', 'name' => 'recipe_published_on', 'label' => '当サイトの初回公開日（確認済み）', 'type' => 'date_picker', 'display_format' => 'Y/m/d', 'return_format' => 'Y-m-d', 'instructions' => '原典の公開日や取り込み作業日で代用しません。不明の場合は空欄。確認した日付はページの公開日表示にも反映します。'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'recipe']]],
        'position' => 'normal', 'menu_order' => 11, 'active' => true, 'show_in_rest' => 0,
    ]);
});

/** Names are editorial summaries of the exact instruction, used only in JSON-LD. */
add_action('acf/init', static function () {
    if (!function_exists('acf_add_local_field_group')) { return; }
    acf_add_local_field_group([
        'key' => 'group_foods_recipe_instruction_names', 'title' => '構造化データの工程名',
        'fields' => [[
            'key' => 'field_recipe_instruction_names', 'name' => 'recipe_instruction_names',
            'label' => '本文に対応する工程名', 'type' => 'repeater', 'layout' => 'table',
            'instructions' => '画面には表示しません。準備・工程の本文が一致する場合だけ工程名を出力します。本文を修正した場合は対応する本文と工程名も確認してください。',
            'sub_fields' => [
                ['key' => 'field_recipe_instruction_source_text', 'name' => 'source_text', 'label' => '対応する本文（全文）', 'type' => 'textarea', 'rows' => 3, 'new_lines' => ''],
                ['key' => 'field_recipe_instruction_step_name', 'name' => 'step_name', 'label' => '短い工程名', 'type' => 'text'],
            ],
        ]],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'recipe']]],
        'position' => 'normal', 'menu_order' => 12, 'active' => true, 'show_in_rest' => 0,
    ]);
});

/** Accept only an exact, real calendar date, without inventing a publication time. */
function foods_recipe_confirmed_date($value) {
    if (is_string($value) && preg_match('/^\d{8}$/D', $value)) {
        $value = substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2);
    }
    if (!is_string($value) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $value, $parts)
        || !checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])) { return ''; }
    return $parts[1] . '-' . $parts[2] . '-' . $parts[3];
}
