<?php
/** Shared published shop names for vacancy editing and applications. */
function foods_get_job_shop_choices() {
    $shops = get_posts([
        'post_type' => 'shop',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
    ]);
    $choices = [];
    foreach ($shops as $shop) {
        $title = foods_get_shop_short_name(get_the_title($shop));
        $choices[$title] = $title;
    }
    $choices['船橋水産加工センター'] = '船橋水産加工センター';
    return $choices;
}

add_filter('acf/load_field/key=field_recruit_part_time_shop_name', static function ($field) {
    $field['choices'] = foods_get_job_shop_choices();
    return $field;
});

/** Shared single-choice occupations for vacancy editing and applications. */
function foods_get_job_occupation_choices() {
    $labels = ['チェッカー（レジ）', '水産', '青果', '惣菜', '食肉', 'グロサリー', '早朝品出し', 'アルバイト（全部門）'];
    $labels = array_merge($labels, [
        'チェッカー（レジ）、品出し', '品出しパートナー募集', '販売部', 'クリ－ン部',
        '販売部（チェッカー・品出し他）', '夜間店長', '食品品出し(レジ作業含む)',
        '製造部', '水産加工', '夜間レジ', '清掃', '加工品（品出し）', 'アルバイト',
    ]);
    return array_combine($labels, $labels);
}

/** Search uses the existing job page so no database or permalink flush is needed. */
add_action('parse_request', static function ($wp) {
    if (trim($wp->request, '/') === 'job/search') {
        $wp->query_vars = ['pagename' => 'job', 'foods_job_search' => 1];
    } elseif (trim($wp->request, '/') === 'job/entry') {
        $wp->query_vars = ['pagename' => 'job', 'foods_job_entry' => 1];
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
    if (get_query_var('foods_job_entry')) {
        return __DIR__ . '/../page-job-entry.php';
    }
    return get_query_var('foods_job_search')
        ? __DIR__ . '/../page-job-search.php'
        : $template;
});

add_filter('redirect_canonical', static function ($redirect) {
    return get_query_var('foods_job_search') || get_query_var('foods_job_detail') || get_query_var('foods_job_entry') ? false : $redirect;
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
