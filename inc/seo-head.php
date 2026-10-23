<?php
/** SEO-01 / REM01-02: theme owns page head; AIOSEO continues to own sitemaps. */
function foods_seo_recipe_test_enabled() {
    return wp_get_environment_type() !== 'production'
        && isset($_GET['recipe_test_all']) && $_GET['recipe_test_all'] === '1';
}

/** Classify the effective route before looking at its backing WordPress page. */
function foods_seo_noindex() {
    // Requirements J16: flyers are embedded data, never standalone search pages.
    if (is_singular('flyer') || is_post_type_archive('flyer') || is_tax('flyer_store')) { return true; }
    if (is_search() || is_404() || is_preview() || is_feed() || is_embed()) { return true; }
    if (is_singular() && post_password_required()) { return true; }
    if (get_query_var('foods_job_search') || get_query_var('foods_job_entry')
        || get_query_var('foods_application_kind') || get_query_var('foods_business_step')) { return true; }
    // The legacy graduate route uses a backing page with the job-entry template.
    // Its GET/HEAD requests redirect to the registered requirements page.
    if (!get_query_var('foods_graduate_entry') && is_page_template([
        'page-job-entry.php', 'page-job-search.php', 'page-search.php',
        'page-contact-confirm.php', 'page-contact-thanks.php',
        'page-application-result.php', 'page-company-business-contact-result.php',
    ])) { return true; }
    if (is_post_type_archive('recipe')) {
        $search = $_GET['recipe_search'] ?? '';
        if (is_string($search) && trim(sanitize_text_field(wp_unslash($search))) !== '') { return true; }
        // Never index test URLs, including production requests where the test is disabled.
        if (isset($_GET['recipe_test_all']) && $_GET['recipe_test_all'] === '1') { return true; }
    }
    return false;
}

add_action('wp_loaded', static function () {
    // AIOSEO removes every wp_robots filter at wp_head(-1), even with aioseo_disable.
    // Restore core robots behavior without enabling its title/meta/schema output.
    if (function_exists('aioseo') && apply_filters('aioseo_disable', false)) {
        remove_action('wp_head', [aioseo()->meta->robots, 'disableWpRobotsCore'], -1);
    }
    remove_action('wp_head', 'rel_canonical');
}, 99);

add_filter('wp_robots', static function ($robots) {
    if (foods_seo_noindex() || (string) get_option('blog_public') === '0') {
        unset($robots['index'], $robots['nofollow']);
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }
    return $robots;
}, 99);

// Error/confirmation templates can stop in wp_die before wp_head runs.
add_action('template_redirect', static function () {
    if (foods_seo_noindex() && !headers_sent()) { header('X-Robots-Tag: noindex, follow', true); }
}, 0);

function foods_seo_canonical_url() {
    if (foods_seo_noindex()) { return ''; }
    if (is_singular()) { return wp_get_canonical_url(get_queried_object_id()) ?: ''; }
    $url = '';
    if (is_front_page()) {
        $url = home_url('/');
    } elseif (is_home()) {
        $posts_page = (int) get_option('page_for_posts');
        $url = $posts_page ? get_permalink($posts_page) : home_url('/');
    } elseif (is_post_type_archive()) {
        $object = get_queried_object();
        $url = $object instanceof WP_Post_Type ? get_post_type_archive_link($object->name) : '';
    } elseif (is_category() || is_tag() || is_tax()) {
        $term_url = get_term_link(get_queried_object());
        $url = is_wp_error($term_url) ? '' : $term_url;
    } elseif (is_author()) {
        $url = get_author_posts_url(get_queried_object_id());
    } elseif (is_date()) {
        $year = (int) get_query_var('year');
        $month = (int) get_query_var('monthnum');
        $day = (int) get_query_var('day');
        $url = $day ? get_day_link($year, $month, $day) : ($month ? get_month_link($year, $month) : get_year_link($year));
    }
    $paged = (int) get_query_var('paged');
    if ($url && $paged > 1) {
        global $wp_rewrite;
        $url = get_option('permalink_structure')
            ? user_trailingslashit(trailingslashit($url) . $wp_rewrite->pagination_base . '/' . $paged, 'paged')
            : add_query_arg('paged', $paged, $url);
    }
    return is_string($url) ? $url : '';
}

add_action('wp_head', static function () {
    $url = foods_seo_canonical_url();
    if ($url !== '') { echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n"; }
});
