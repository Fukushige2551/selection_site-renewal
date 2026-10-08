<?php
if (!defined('ABSPATH')) { exit; }

function fsu_guard() {
    if (!current_user_can('manage_options')) { wp_die('管理者権限が必要です。'); }
    $host = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
    if (!in_array($host, ['foods-selection.co.jp.testrs.jp', 'foods-selectioncojp.local'], true)) {
        wp_die('このツールは指定STGとLocal WP専用です。サイトURLを確認してください。');
    }
}

function fsu_data() {
    $rows = json_decode(file_get_contents(__DIR__ . '/data.json'), true);
    if (!is_array($rows) || count($rows) !== 10) { throw new RuntimeException('更新データが不正です。'); }
    return $rows;
}

function fsu_fields() {
    return ['access', 'payment_methods', 'available_services', 'atm_bank_name'];
}

function fsu_match($rows, $posts) {
    $result = [];
    foreach ($rows as $row) {
        $matches = array_values(array_filter($posts, function ($p) use ($row) {
            return $p->post_title === $row['title'];
        }));
        if (count($matches) !== 1) { throw new RuntimeException($row['title'] . '：同名の店舗が0件または複数件です。店舗登録を確認してください。'); }
        $post = $matches[0];
        if ($post->post_status !== 'publish') { throw new RuntimeException($row['title'] . '：公開済みの店舗ではありません。'); }
        foreach ($posts as $other) {
            if ($other->ID !== $post->ID && $other->post_name === $row['slug']) {
                throw new RuntimeException($row['title'] . '：変更先のスラッグが他店舗で使用されています。');
            }
        }
        $result[] = ['id'=>$post->ID,'data'=>$row];
    }
    return $result;
}

function fsu_plan() {
    if (!function_exists('get_field_object') || !function_exists('update_field')) {
        throw new RuntimeException('Secure Custom Fields / ACFを有効にしてください。');
    }
    $posts = get_posts(['post_type'=>'shop','post_status'=>['publish','draft','pending','private','future','trash'],'numberposts'=>-1,'suppress_filters'=>true]);
    $plan = fsu_match(fsu_data(), $posts);
    foreach ($plan as &$item) {
        $post = get_post($item['id']);
        if (wp_unique_post_slug($item['data']['slug'], $post->ID, $post->post_status, 'shop', $post->post_parent) !== $item['data']['slug']) {
            throw new RuntimeException($post->post_title . '：スラッグが競合しています。');
        }
        $item['keys'] = [];
        foreach (fsu_fields() as $name) {
            $field = get_field_object($name, $item['id']);
            if (!$field || $field['name'] !== $name || !in_array($field['type'], ['text','textarea','checkbox'], true)) {
                throw new RuntimeException($post->post_title . '：カスタムフィールド「' . $name . '」が見つからないか形式が異なります。');
            }
            if (is_array($item['data'][$name]) && ($field['type'] !== 'checkbox' || array_diff($item['data'][$name], array_keys($field['choices'] ?? [])))) {
                throw new RuntimeException($post->post_title . '：選択肢「' . $name . '」がLocal WPと異なります。');
            }
            if (!is_array($item['data'][$name]) && !in_array($field['type'], ['text','textarea'], true)) {
                throw new RuntimeException('テキスト項目の形式が異なります。');
            }
            $item['keys'][$name] = $field['key'];
        }
    }
    unset($item);
    return $plan;
}

function fsu_snapshot($plan) {
    $out = [];
    foreach ($plan as $item) {
        $post = get_post($item['id']);
        $row = ['id'=>$post->ID,'title'=>$post->post_title,'slug'=>$post->post_name,'status'=>$post->post_status,'modified'=>$post->post_modified,'modified_gmt'=>$post->post_modified_gmt,'meta'=>[]];
        foreach (array_merge(fsu_fields(), array_map(function ($n) { return '_' . $n; }, fsu_fields()), ['_wp_old_slug']) as $name) {
            $row['meta'][$name] = get_post_meta($post->ID, $name, false);
        }
        $out[] = $row;
    }
    return $out;
}

function fsu_digest($plan, $snapshot) {
    return hash('sha256', wp_json_encode([$plan, $snapshot]));
}

function fsu_equal($a, $b) {
    if (is_array($b)) {
        if (!is_array($a)) { return false; }
        sort($a); sort($b);
        return $a === $b;
    }
    return str_replace("\r\n", "\n", (string) $a) === str_replace("\r\n", "\n", (string) $b);
}

function fsu_diffs($plan) {
    $diffs = [];
    foreach ($plan as $item) {
        $post = get_post($item['id']);
        $pairs = ['slug'=>[$post->post_name,$item['data']['slug']]];
        foreach (fsu_fields() as $name) {
            $pairs[$name] = [get_post_meta($post->ID,$name,true),$item['data'][$name]];
        }
        foreach ($pairs as $name=>$pair) {
            if (!fsu_equal($pair[0],$pair[1])) {
                $diffs[] = ['title'=>$post->post_title,'id'=>$post->ID,'field'=>$name,'before'=>$pair[0],'after'=>$pair[1]];
            }
        }
    }
    return $diffs;
}

function fsu_format($name, $value, $id) {
    if ($name === 'slug') { return rawurldecode((string) $value); }
    if (is_array($value)) {
        $field = get_field_object($name,$id);
        return implode('、', array_map(function ($v) use ($field) { return $field['choices'][$v] ?? $v; }, $value));
    }
    return (string) $value;
}

function fsu_page() {
    fsu_guard();
    echo '<div class="wrap"><h1>店舗情報更新</h1><p>2026年10月8日に確認した資料の内容を、既存の10店舗へ反映します。店舗名で照合し、スラッグ・アクセス・決済方法・施設サービス・ATM銀行名の差分だけを更新します。</p>';
    echo '<p>投稿ID・画像・本文・その他の項目は更新対象に含みません。更新前の対象データは管理者だけが取得できるバックアップとして保存します。サイト全体のバックアップも先に取得してください。</p>';
    $backup = get_option('fsu_last_backup');
    if ($backup) {
        $url = wp_nonce_url(admin_url('admin-post.php?action=fsu_backup'), 'fsu_backup');
        echo '<p><a class="button" href="' . esc_url($url) . '">前回の更新前バックアップをダウンロード</a></p>';
    }
    if (isset($_GET['fsu_done'])) { echo '<div class="notice notice-success"><p>更新と保存内容の照合が完了しました。各店舗ページの表示を確認してください。</p></div>'; }
    if (isset($_GET['fsu_failed'])) { echo '<div class="notice notice-error"><p>' . esc_html(get_transient('fsu_error_' . get_current_user_id())) . '</p></div>'; }
    try {
        $plan = fsu_plan();
        $diffs = fsu_diffs($plan);
        $snapshot = fsu_snapshot($plan);
        set_transient('fsu_preview_' . get_current_user_id(), fsu_digest($plan,$snapshot), 15 * MINUTE_IN_SECONDS);
        echo '<p>照合済み：10店舗 ／ 差分：' . count($diffs) . '項目</p>';
        if (!$diffs) { echo '<p>更新対象の情報はすべて一致しています。更新は不要です。</p></div>'; return; }
        $labels = ['slug'=>'URLスラッグ','access'=>'アクセス','payment_methods'=>'決済方法','available_services'=>'施設・サービス','atm_bank_name'=>'ATM銀行名'];
        echo '<table class="widefat striped"><thead><tr><th>店舗</th><th>項目</th><th>現在</th><th>変更後</th></tr></thead><tbody>';
        foreach ($diffs as $d) {
            echo '<tr><td>' . esc_html($d['title']) . '</td><td>' . esc_html($labels[$d['field']]) . '</td><td>' . nl2br(esc_html(fsu_format($d['field'],$d['before'],$d['id']))) . '</td><td>' . nl2br(esc_html(fsu_format($d['field'],$d['after'],$d['id']))) . '</td></tr>';
        }
        echo '</tbody></table><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="fsu_apply">';
        wp_nonce_field('fsu_apply');
        echo '<p><label><input required type="checkbox" name="confirm" value="yes"> 上記の差分を確認し、この10店舗の対象項目を更新する</label></p><p><button class="button button-primary">バックアップを保存して差分を反映</button></p></form>';
    } catch (Throwable $e) { echo '<div class="notice notice-error"><p>' . esc_html($e->getMessage()) . '</p></div>'; }
    echo '</div>';
}

function fsu_restore($snapshot) {
    global $wpdb;
    foreach ($snapshot as $row) {
        $result = $wpdb->update($wpdb->posts, ['post_name'=>$row['slug'],'post_modified'=>$row['modified'],'post_modified_gmt'=>$row['modified_gmt']], ['ID'=>$row['id']]);
        if ($result === false) { throw new RuntimeException('店舗スラッグの復元に失敗しました。'); }
        foreach ($row['meta'] as $key=>$values) {
            delete_post_meta($row['id'],$key);
            foreach ($values as $value) { if (!add_post_meta($row['id'],$key,$value)) { throw new RuntimeException('店舗フィールドの復元に失敗しました。'); } }
        }
        clean_post_cache($row['id']);
    }
}

function fsu_apply() {
    fsu_guard(); check_admin_referer('fsu_apply');
    if (($_POST['confirm'] ?? '') !== 'yes') { wp_die('差分の確認が必要です。'); }
    $lock = false; $started = false; $snapshot = [];
    try {
        $lock = add_option('fsu_update_lock', time(), '', false);
        if (!$lock) { throw new RuntimeException('更新処理が実行中です。再実行せず管理者へ確認してください。'); }
        $plan = fsu_plan(); $snapshot = fsu_snapshot($plan);
        $expected = get_transient('fsu_preview_' . get_current_user_id());
        if (!$expected || !hash_equals($expected,fsu_digest($plan,$snapshot))) { throw new RuntimeException('確認後にデータが変わったか、15分が経過しました。差分を再確認してください。'); }
        $diffs = fsu_diffs($plan);
        if (!$diffs) { throw new RuntimeException('差分がありません。'); }
        $backup = ['version'=>1,'site'=>home_url(),'time'=>gmdate('c'),'before'=>$snapshot,'changes'=>$diffs];
        $key = 'fsu_backup_' . gmdate('Ymd_His') . '_' . wp_generate_password(6,false,false);
        if (!add_option($key,$backup,'',false) || get_option($key) !== $backup) { throw new RuntimeException('バックアップの保存に失敗しました。'); }
        update_option('fsu_last_backup',$key,false);
        $started = true;
        foreach ($plan as $item) {
            $id=$item['id'];
            if (get_post($id)->post_name !== $item['data']['slug']) {
                $result=wp_update_post(['ID'=>$id,'post_name'=>$item['data']['slug']],true);
                if (is_wp_error($result) || get_post($id)->post_name !== $item['data']['slug']) { throw new RuntimeException('スラッグの更新に失敗しました。'); }
            }
            foreach (fsu_fields() as $name) {
                if (!fsu_equal(get_post_meta($id,$name,true),$item['data'][$name])) {
                    update_field($item['keys'][$name],$item['data'][$name],$id);
                }
                if (!fsu_equal(get_post_meta($id,$name,true),$item['data'][$name])) { throw new RuntimeException('フィールドの保存内容が一致しません。'); }
            }
        }
        delete_transient('fsu_preview_' . get_current_user_id());
        delete_option('fsu_update_lock');
        wp_safe_redirect(admin_url('tools.php?page=foods-shop-update&fsu_done=1')); exit;
    } catch (Throwable $e) {
        $message=$e->getMessage();
        if ($started) {
            try { fsu_restore($snapshot); $message .= ' 対象データを更新前へ復元しました。'; }
            catch (Throwable $restore_error) { $message .= ' 復元に失敗しました。バックアップを取得し、再実行せず管理者へ連絡してください。'; }
        }
        if ($lock) { delete_option('fsu_update_lock'); }
        set_transient('fsu_error_' . get_current_user_id(),$message,10*MINUTE_IN_SECONDS);
        wp_safe_redirect(admin_url('tools.php?page=foods-shop-update&fsu_failed=1')); exit;
    }
}

function fsu_download() {
    fsu_guard(); check_admin_referer('fsu_backup');
    $key=get_option('fsu_last_backup');
    $backup=$key ? get_option($key) : false;
    if (!$backup) { wp_die('バックアップがありません。'); }
    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="foods-shop-backup.json"');
    echo wp_json_encode($backup,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); exit;
}
