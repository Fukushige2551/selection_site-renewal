<?php
/**
 * Template Name: パート・アルバイト：検索結果一覧
 */

if (
    array_key_exists('keyword', $_GET) &&
    trim((string) wp_unslash($_GET['keyword'])) === '' &&
    !isset($_GET['job_shop']) &&
    !isset($_GET['job_role']) &&
    !isset($_GET['paged'])
) {
    wp_safe_redirect(home_url('/job/search/'));
    exit;
}

$job_search_get_field = static function ($name, $post_id) {
    if (function_exists('get_field')) {
        return get_field($name, $post_id);
    }

    return get_post_meta($post_id, $name, true);
};

$job_search_shops = [
    '行徳店', '西船橋店', '花野井店', 'しいの木台店', '青葉台店',
    '西原店', '松戸店', '西新井店', '三郷店', '八潮店',
];
$job_search_roles = [
    'どこでも可（レジ）', '品出し（グロサリー）', 'チェッカー', 'お惣菜',
    'ベーカリー', '水産', '寿司', '食肉', '青果',
];
$theme_uri = get_template_directory_uri();

$keyword = isset($_GET['keyword']) ? sanitize_text_field(wp_unslash($_GET['keyword'])) : '';
$selected_shops = isset($_GET['job_shop']) ? array_map('sanitize_text_field', (array) wp_unslash($_GET['job_shop'])) : [];
$selected_roles = isset($_GET['job_role'])
    ? array_map('sanitize_text_field', (array) wp_unslash($_GET['job_role']))
    : (isset($_GET['job']) ? [sanitize_text_field(wp_unslash($_GET['job']))] : []);

$all_jobs = get_posts([
    'post_type'      => 'recruit_part_time',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$matched_jobs = array_values(array_filter($all_jobs, static function ($job_post) use ($job_search_get_field, $keyword, $selected_shops, $selected_roles) {
    $job_id = $job_post->ID;
    $shop_name = trim(wp_strip_all_tags((string) $job_search_get_field('shop_name', $job_id)));
    $job_type = trim(wp_strip_all_tags((string) $job_search_get_field('job_type', $job_id)));
    $salary = trim(wp_strip_all_tags((string) $job_search_get_field('salary', $job_id)));
    $access = trim(wp_strip_all_tags((string) $job_search_get_field('work_location_access', $job_id)));
    $railway_line = trim(wp_strip_all_tags((string) $job_search_get_field('railway_line', $job_id)));
    $station_name = trim(wp_strip_all_tags((string) $job_search_get_field('station_name', $job_id)));
    $walking_time = trim(wp_strip_all_tags((string) $job_search_get_field('walking_time', $job_id)));
    $haystack = implode(' ', [get_the_title($job_id), $shop_name, $job_type, $salary, $access, $railway_line, $station_name, $walking_time]);

    if ($keyword !== '' && mb_stripos($haystack, $keyword) === false) {
        return false;
    }
    if ($selected_shops && !array_filter($selected_shops, static fn($shop) => mb_stripos($haystack, $shop) !== false)) {
        return false;
    }
    if ($selected_roles && !array_filter($selected_roles, static fn($role) => mb_stripos($haystack, $role) !== false)) {
        return false;
    }

    return true;
}));

$per_page = 10;
$current_page = max(1, isset($_GET['paged']) ? absint($_GET['paged']) : 1);
$total_jobs = count($matched_jobs);
$is_local_preview = false;

if (
    !$matched_jobs &&
    ($keyword !== '' || $selected_shops || $selected_roles) &&
    function_exists('wp_get_environment_type') &&
    wp_get_environment_type() === 'local'
) {
    $is_local_preview = true;
    $total_jobs = 60;
}

$total_pages = max(1, (int) ceil($total_jobs / $per_page));
$current_page = min($current_page, $total_pages);
$visible_jobs = $is_local_preview
    ? array_fill(0, $per_page, null)
    : array_slice($matched_jobs, ($current_page - 1) * $per_page, $per_page);

$conditions = array_filter(array_merge([$keyword], $selected_shops, $selected_roles));
$filter_is_open = isset($_GET['filter']) && $_GET['filter'] === 'open';

get_header('company');
?>
<main class="p-job-search-results">
    <nav class="p-job-search-results__breadcrumb" aria-label="パンくずリスト">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span>›</span>
        <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span>›</span>
        <a href="<?php echo esc_url(home_url('/job/')); ?>">パートアルバイト募集</a><span>›</span>
        <span>検索結果</span>
    </nav>

    <header class="p-job-search-results__summary">
        <h1>検索結果：<strong><?php echo esc_html($total_jobs); ?></strong>件</h1>
        <div class="p-job-search-results__summary-row">
            <p><span class="p-job-search-results__condition-label">検索条件：</span><span class="p-job-search-results__condition-list"><?php if ($conditions) : ?><?php foreach (array_values($conditions) as $condition) : ?><span><?php echo esc_html($condition); ?></span><?php endforeach; ?><?php else : ?><span>すべて</span><?php endif; ?></span></p>
            <button class="p-job-search-results__filter-toggle js-job-search-filter-toggle" type="button" aria-controls="job-search-filter" aria-expanded="<?php echo $filter_is_open ? 'true' : 'false'; ?>"><span class="p-job-search-results__filter-label">絞り込み検索</span><span class="p-job-search-results__filter-icon" aria-hidden="true"></span></button>
        </div>
    </header>

    <?php if (!$visible_jobs) : ?>
        <p class="p-job-search-results__empty">条件に一致する求人情報が見つかりませんでした。</p>
    <?php endif; ?>

    <section id="job-search-filter" class="p-job-search-filter js-job-search-filter" aria-hidden="<?php echo $filter_is_open ? 'false' : 'true'; ?>"<?php echo $filter_is_open ? '' : ' hidden'; ?>>
        <form action="<?php echo esc_url(home_url('/job/search/')); ?>" method="get">
            <section class="p-job-search-filter__keyword">
                <h2><img src="<?php echo esc_url($theme_uri . '/img/page/page-job/icon-keyword.svg'); ?>" alt="">フリーワードで探す</h2>
                <input type="search" name="keyword" value="<?php echo esc_attr($keyword); ?>" placeholder="店舗名、駅名、住所など">
                <button type="submit">検索</button>
            </section>

            <fieldset>
                <legend><img src="<?php echo esc_url($theme_uri . '/img/page/page-job/icon-location.svg'); ?>" alt="">勤務地から探す</legend>
                <div class="p-job-search-filter__options p-job-search-filter__options--shops">
                    <?php foreach ($job_search_shops as $shop) : ?>
                        <label><input type="checkbox" name="job_shop[]" value="<?php echo esc_attr($shop); ?>"<?php checked(in_array($shop, $selected_shops, true)); ?>><span><?php echo esc_html($shop); ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend><img src="<?php echo esc_url($theme_uri . '/img/page/page-job/icon-job.svg'); ?>" alt="">職種から探す</legend>
                <div class="p-job-search-filter__options p-job-search-filter__options--roles">
                    <?php foreach ($job_search_roles as $role) : ?>
                        <label><input type="checkbox" name="job_role[]" value="<?php echo esc_attr($role); ?>"<?php checked(in_array($role, $selected_roles, true)); ?>><span><?php
                            if ($role === 'どこでも可（レジ）') {
                                echo '<span class="p-job-search-filter__role-main">どこでも可</span><span class="p-job-search-filter__role-sub">（レジ）</span>';
                            } elseif ($role === '品出し（グロサリー）') {
                                echo '<span class="p-job-search-filter__role-main">品出し</span><span class="p-job-search-filter__role-sub">（グロサリー）</span>';
                            } else {
                                echo esc_html($role);
                            }
                        ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <button class="p-job-search-filter__submit" type="submit">検索</button>
        </form>
    </section>

    <?php if ($visible_jobs) : ?>
        <section class="p-job-search-results__content js-job-search-results"<?php echo $filter_is_open ? ' hidden' : ''; ?>>
            <div class="p-job-search-results__grid">
                <?php foreach ($visible_jobs as $job_post) :
                    if ($is_local_preview) {
                        $job_id = 0;
                        $card = [
                            'shop_name'   => $selected_shops[0] ?? '行徳店',
                            'job_type'    => ($selected_roles[0] ?? '') === 'どこでも可（レジ）' ? 'レジスタッフ（アルバイト）' : ($selected_roles[0] ?? 'レジスタッフ（アルバイト）'),
                            'image_url'   => $theme_uri . '/img/page/page-job/job-register.png',
                            'salary'      => '時給 1100円～',
                            'railway_line'=> '東西線',
                            'station_name'=> '行徳駅',
                            'walking_time'=> '徒歩5分',
                            'legacy_access' => '',
                            'detail_url'  => '#',
                            'entry_url'   => '#',
                        ];
                    } else {
                        $job_id = $job_post->ID;
                        $card = [
                            'shop_name'   => trim(wp_strip_all_tags((string) $job_search_get_field('shop_name', $job_id))),
                            'job_type'    => trim(wp_strip_all_tags((string) $job_search_get_field('job_type', $job_id))),
                            'image_url'   => get_the_post_thumbnail_url($job_id, 'medium') ?: $theme_uri . '/img/page/page-job/job-register.png',
                            'salary'      => trim(wp_strip_all_tags((string) $job_search_get_field('salary', $job_id))),
                            'railway_line'=> trim(wp_strip_all_tags((string) $job_search_get_field('railway_line', $job_id))),
                            'station_name'=> trim(wp_strip_all_tags((string) $job_search_get_field('station_name', $job_id))),
                            'walking_time'=> trim(wp_strip_all_tags((string) $job_search_get_field('walking_time', $job_id))),
                            'legacy_access' => trim(wp_strip_all_tags((string) $job_search_get_field('work_location_access', $job_id))),
                            'detail_url'  => get_permalink($job_id),
                            'entry_url'   => add_query_arg(['job_id' => $job_id], home_url('/job/entry/')),
                        ];
                    }
                    $card_title = trim($card['shop_name'] . '　' . $card['job_type']) ?: get_the_title($job_id);
                ?>
                    <article class="p-job-result-card">
                        <h2><?php echo esc_html($card_title); ?></h2>
                        <div class="p-job-result-card__body">
                            <img src="<?php echo esc_url($card['image_url']); ?>" alt="">
                            <dl>
                                <div><dt>【給与】</dt><dd><?php echo esc_html($card['salary'] ?: '給与の詳細をご確認ください'); ?></dd></div>
                                <div><dt>【アクセス】</dt><dd><?php if ($card['railway_line'] || $card['station_name'] || $card['walking_time']) : ?><span><?php echo esc_html(trim($card['railway_line'] . ' ' . $card['station_name'])); ?></span><?php if ($card['walking_time']) : ?><br><span><?php echo esc_html($card['walking_time']); ?></span><?php endif; ?><?php else : ?><?php echo nl2br(esc_html($card['legacy_access'] ?: '勤務地の詳細をご確認ください')); ?><?php endif; ?></dd></div>
                            </dl>
                        </div>
                        <div class="p-job-result-card__actions">
                            <a href="<?php echo esc_url($card['detail_url']); ?>">詳細をみる</a>
                            <a href="<?php echo esc_url($card['entry_url']); ?>">今すぐ応募</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1) : ?>
                <nav class="p-job-search-results__pagination" aria-label="ページ送り">
                    <?php
                    $pagination_start = max(1, min($current_page - 2, $total_pages - 4));
                    $pagination_end = min($total_pages, $pagination_start + 4);
                    if ($current_page > 1) :
                        $previous_url = add_query_arg(array_merge($_GET, ['paged' => $current_page - 1]), home_url('/job/search/'));
                    ?>
                        <a class="p-job-search-results__pagination-arrow is-previous" href="<?php echo esc_url($previous_url); ?>" aria-label="前のページ"></a>
                    <?php endif; ?>
                    <?php for ($page_number = $pagination_start; $page_number <= $pagination_end; $page_number++) :
                        $page_url = add_query_arg(array_merge($_GET, ['paged' => $page_number]), home_url('/job/search/'));
                    ?>
                        <a class="<?php echo $page_number === $current_page ? 'is-current' : ''; ?>" href="<?php echo esc_url($page_url); ?>"><?php echo esc_html($page_number); ?></a>
                    <?php endfor; ?>
                    <?php if ($current_page < $total_pages) :
                        $next_url = add_query_arg(array_merge($_GET, ['paged' => $current_page + 1]), home_url('/job/search/'));
                    ?>
                        <a class="p-job-search-results__pagination-arrow is-next" href="<?php echo esc_url($next_url); ?>" aria-label="次のページ"></a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
<?php get_footer('company'); ?>
