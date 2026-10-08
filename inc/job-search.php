<?php
/** Search uses the existing job page so no database or permalink flush is needed. */
add_action('parse_request', static function ($wp) {
    if (trim($wp->request, '/') === 'job/search') {
        $wp->query_vars = ['pagename' => 'job', 'foods_job_search' => 1];
    } elseif (preg_match('#^job/detail/([1-9][0-9]*)/?$#D', $wp->request, $matches)) {
        $wp->query_vars = [
            'post_type' => 'recruit_part_time',
            'p' => (int) $matches[1],
            'foods_job_detail' => 1,
        ];
    }
});

add_filter('post_type_link', static function ($url, $post) {
    if ($post->post_type === 'recruit_part_time') {
        return home_url('/job/detail/' . $post->ID);
    }
    return $url;
}, 10, 2);

add_filter('page_template', static function ($template) {
    return get_query_var('foods_job_search')
        ? __DIR__ . '/../page-job-search.php'
        : $template;
});

add_filter('redirect_canonical', static function ($redirect) {
    return get_query_var('foods_job_search') || get_query_var('foods_job_detail') ? false : $redirect;
});

function foods_job_role_matches($job_type, $role) {
    // The existing landing page and filter use different labels for these roles.
    $aliases = [
        'レジスタッフ（チェッカー）' => ['レジ', 'チェッカー'],
        'どこでも可（レジ）' => ['レジ', 'チェッカー'],
        'チェッカー' => ['レジ', 'チェッカー'],
        '品出し（グロサリー）' => ['品出し', 'グロサリー'],
        '惣菜' => ['惣菜'],
        'お惣菜' => ['惣菜'],
    ];
    foreach ($aliases[$role] ?? [$role] as $label) {
        if (mb_stripos($job_type, $label) !== false) {
            return true;
        }
    }
    return false;
}
