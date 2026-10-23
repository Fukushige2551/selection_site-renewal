<?php
/** Applications depend on reception status, never on the unknown schema publication date. */
function foods_job_can_apply($id) {
    $post = get_post($id);
    return $post && $post->post_type === 'recruit_part_time' && $post->post_status === 'publish'
        && $post->post_password === '' && get_post_meta($post->ID, 'job_schema_status', true) === 'open';
}

function foods_job_application_choices($id = 0) {
    $posts = $id ? [get_post($id)] : get_posts(['post_type' => 'recruit_part_time', 'post_status' => 'publish', 'posts_per_page' => -1]);
    $choices = [];
    foreach ($posts as $post) {
        if (!$post || !foods_job_can_apply($post->ID)) { continue; }
        $store = trim(wp_strip_all_tags((string) get_post_meta($post->ID, 'shop_name', true)));
        $role = trim(wp_strip_all_tags((string) get_post_meta($post->ID, 'job_type', true)));
        if ($store !== '' && $role !== '') { $choices[$store][$role] = $role; }
    }
    return array_map('array_values', $choices);
}

function foods_job_application_error($values) {
    $id = $values['job_id'] ?? '';
    $store = $values['store'] ?? '';
    $role = $values['job'] ?? '';
    if (!is_string($id) || !is_string($store) || !is_string($role)) { return '応募先の求人を選び直してください。'; }
    if ($id !== '') {
        if (!preg_match('/^[1-9][0-9]*$/D', $id) || !foods_job_can_apply((int) $id)) {
            return 'この求人は現在応募を受け付けていません。求人一覧から募集中の求人を選び直してください。';
        }
        $choices = foods_job_application_choices((int) $id);
    } else {
        $choices = foods_job_application_choices();
    }
    return isset($choices[$store]) && in_array($role, $choices[$store], true)
        ? '' : '選択された店舗・職種は現在応募を受け付けていません。求人一覧から選び直してください。';
}

function foods_job_form_is_target($form) {
    $ids = get_option('foods_cf7_forms', []);
    return $form && ((int) $form->id() === (int) ($ids['job'] ?? 0) || $form->name() === 'foods-form-job');
}

// Cover direct CF7 REST submissions as well as the theme's confirmation/send endpoints.
add_filter('wpcf7_validate', static function ($result, $tags) {
    if (!foods_job_form_is_target(WPCF7_ContactForm::get_current())) { return $result; }
    $error = foods_job_application_error($_POST);
    if ($error !== '') {
        foreach ($tags as $tag) { if ($tag->name === 'job') { $result->invalidate($tag, $error); break; } }
    }
    return $result;
}, 50, 2);

add_action('wpcf7_before_send_mail', static function ($form, &$abort, $submission) {
    if (!foods_job_form_is_target($form)) { return; }
    $error = foods_job_application_error($_POST);
    if ($error !== '') {
        $abort = true;
        $submission->set_status('validation_failed');
        $submission->set_response($error);
    }
}, 10, 3);
