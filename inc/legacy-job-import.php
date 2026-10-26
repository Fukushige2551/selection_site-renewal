<?php
/** Keep the source site's categories available without creating public archives. */
add_action('init', static function () {
    register_taxonomy('legacy_job_category', ['recruit_part_time', 'post'], [
        'label' => '移行元の採用カテゴリー', 'hierarchical' => true,
        'public' => false, 'show_ui' => true, 'show_admin_column' => true,
        'rewrite' => false, 'query_var' => false,
    ]);
});

// The source's regular-employee announcement remains an ordinary post, not a part-time vacancy.
add_filter('single_template', static function ($template) {
    if (is_singular('post') && get_post_meta(get_queried_object_id(), '_foods_legacy_job_id', true)) {
        return __DIR__ . '/../single-legacy-recruit.php';
    }
    return $template;
});
