<?php
/**
 * Interview pages use flat WordPress page slugs without an interview index page.
 * Assign page-recruit-interview.php to recruit-interview-01, -02 and -03.
 * Save Settings > Permalinks once after deploying these rewrite rules.
 */
require_once __DIR__ . '/recruit-interview-config.php';

function foods_get_recruit_interview_number($post) {
    $post = get_post($post);
    if (!$post || $post->post_type !== 'page'
        || get_page_template_slug($post->ID) !== 'page-recruit-interview.php') {
        return '';
    }
    if (!preg_match('/^recruit-interview-(01|02|03)$/D', $post->post_name, $matches)) {
        return '';
    }
    return $matches[1];
}

function foods_register_recruit_interview_routes() {
    foreach (['01', '02', '03'] as $number) {
        add_rewrite_rule(
            '^recruit/interview/' . $number . '/?$',
            'index.php?pagename=recruit-interview-' . $number,
            'top'
        );
    }
}
add_action('init', 'foods_register_recruit_interview_routes');

function foods_recruit_interview_permalink($link, $post_id, $sample) {
    $number = foods_get_recruit_interview_number($post_id);
    return $number !== ''
        ? home_url(user_trailingslashit('/recruit/interview/' . $number))
        : $link;
}
add_filter('page_link', 'foods_recruit_interview_permalink', 10, 3);

function foods_get_recruit_interview_url($number) {
    if (!in_array($number, ['01', '02', '03'], true)) {
        return '';
    }
    $page = get_page_by_path('recruit-interview-' . $number, OBJECT, 'page');
    if ($page && foods_get_recruit_interview_number($page) === $number) {
        return get_permalink($page);
    }
    return home_url(user_trailingslashit('/recruit/interview/' . $number));
}
