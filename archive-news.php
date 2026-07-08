<?php
function foods_news_archive_get_field_value($post_id, $field_name) {
    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    return get_post_meta($post_id, $field_name, true);
}

function foods_news_archive_get_image_data($image, $size = 'large') {
    if (empty($image)) {
        return null;
    }

    if (is_array($image)) {
        $url = $image['sizes'][$size] ?? $image['url'] ?? '';
        return $url ? [
            'url' => $url,
            'alt' => $image['alt'] ?? '',
        ] : null;
    }

    if (is_numeric($image)) {
        return [
            'url' => wp_get_attachment_image_url((int) $image, $size),
            'alt' => get_post_meta((int) $image, '_wp_attachment_image_alt', true),
        ];
    }

    if (is_string($image)) {
        return [
            'url' => $image,
            'alt' => '',
        ];
    }

    return null;
}

function foods_news_archive_get_news_image_data($post_id, $size = 'large') {
    if (has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        return [
            'url' => get_the_post_thumbnail_url($post_id, $size),
            'alt' => get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
        ];
    }

    foreach (['news_eyecatch_image', 'news_image', 'main_image', 'thumbnail', 'image'] as $field_name) {
        $image = foods_news_archive_get_field_value($post_id, $field_name);
        $image_data = foods_news_archive_get_image_data($image, $size);

        if (!empty($image_data['url'])) {
            return $image_data;
        }
    }

    return null;
}

function foods_news_archive_get_category_label($categories) {
    if (!$categories) {
        return '';
    }

    $priority = ['notice', 'campaign', 'shop-news', 'commitment', 'important'];

    foreach ($priority as $slug) {
        foreach ($categories as $category) {
            if (($category['slug'] ?? '') === $slug) {
                return $category['name'] ?? '';
            }
        }
    }

    return $categories[0]['name'] ?? '';
}

function foods_news_archive_get_category_class($label) {
    $classes = [
        'おしらせ' => 'notice',
        'お知らせ' => 'notice',
        'キャンペーン' => 'campaign',
        '店舗から' => 'shop',
        'こだわり' => 'commitment',
        '重要' => 'important',
    ];

    return $classes[$label] ?? 'notice';
}

$theme_uri = get_template_directory_uri();
$placeholder_image = $theme_uri . '/img/component/no-image.png';

$news_paged = max(1, (int) get_query_var('paged'));
$news_query = new WP_Query([
    'post_type' => 'news',
    'post_status' => 'publish',
    'posts_per_page' => 10,
    'paged' => $news_paged,
    'orderby' => 'date',
    'order' => 'DESC',
]);
$news_posts = $news_query->posts;
$news_total_pages = (int) $news_query->max_num_pages;

$news_items = array_map(function ($news_post) {
    $categories = get_the_terms($news_post->ID, 'news_category');
    $shops = get_the_terms($news_post->ID, 'news_shop_category');
    $commitments = get_the_terms($news_post->ID, 'news_commitment');
    $formatted_categories = !is_wp_error($categories) && $categories ? array_map(function ($term) {
        return [
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
        ];
    }, $categories) : [];
    $category_label = foods_news_archive_get_category_label($formatted_categories);

    return [
        'id' => $news_post->ID,
        'title' => get_the_title($news_post->ID),
        'permalink' => get_permalink($news_post->ID),
        'date' => get_the_date('Y.m.d', $news_post->ID),
        'datetime' => get_the_date('Y-m-d', $news_post->ID),
        'image' => foods_news_archive_get_news_image_data($news_post->ID, 'large'),
        'category_label' => $category_label,
        'category_class' => foods_news_archive_get_category_class($category_label),
        'categories' => $formatted_categories,
        'shops' => !is_wp_error($shops) && $shops ? array_map(function ($term) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
            ];
        }, $shops) : [],
        'commitments' => !is_wp_error($commitments) && $commitments ? array_map(function ($term) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
            ];
        }, $commitments) : [],
    ];
}, $news_posts);

$pickup_news_items = array_slice($news_items, 0, 5);

get_header();
?>

<main class="p-news-archive c-main">
    <nav class="c-breadcrumb p-news-archive__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span>新着情報</span>
    </nav>

    <div class="p-news-archive__heading">
        <h1 class="p-news-archive__title c-section__title">新着情報</h1>
        <p class="p-news-archive__title-en c-section__title--sub">NEWS</p>
    </div>
    <div class="p-news-archive__content">
        <div class="p-news-archive__decorations" aria-hidden="true">
            <span class="p-news-archive__decoration p-news-archive__decoration--flag"></span>
            <span class="p-news-archive__decoration p-news-archive__decoration--female"></span>
            <span class="p-news-archive__decoration p-news-archive__decoration--male"></span>
            <span class="p-news-archive__decoration p-news-archive__decoration--shop"></span>
        </div>
        <div class="p-news-archive__filter" aria-label="新着情報カテゴリー">
            <button class="p-news-archive__filter-button p-news-archive__filter-button--notice" type="button">おしらせ</button>
            <button class="p-news-archive__filter-button p-news-archive__filter-button--campaign" type="button">キャンペーン</button>
            <button class="p-news-archive__filter-button p-news-archive__filter-button--shop" type="button">店舗から</button>
            <button class="p-news-archive__filter-button p-news-archive__filter-button--commitment" type="button">こだわり</button>
            <button class="p-news-archive__filter-button p-news-archive__filter-button--important" type="button">重要</button>
        </div>

        <?php if ($pickup_news_items) : ?>
            <section class="p-news-archive__pickup" aria-label="新着情報ピックアップ">
                <div class="p-news-archive__pickup-list">
                    <?php foreach ($pickup_news_items as $news_index => $news_item) : ?>
                        <?php
                        $news_image_url = $news_item['image']['url'] ?? $placeholder_image;
                        $news_image_alt = $news_item['image']['alt'] ?? '';
                        ?>
                        <div class="c-campaign-content-wrapper p-news-archive__pickup-card js-news-archive-pickup-card" <?php echo $news_index === 0 ? '' : 'hidden'; ?>>
                            <div class="c-campaign-content">
                                <a href="<?php echo esc_url($news_item['permalink']); ?>">
                                    <picture>
                                        <img class="c-pop c-campaign-content__img" src="<?php echo esc_url($news_image_url); ?>" alt="<?php echo esc_attr($news_image_alt ?: $news_item['title']); ?>">
                                    </picture>
                                    <time class="c-campaign-content__time" datetime="<?php echo esc_attr($news_item['datetime']); ?>"><?php echo esc_html($news_item['date']); ?></time>
                                </a>
                            </div>
                            <?php if ($news_item['category_label']) : ?>
                                <div class="p-news-archive__pickup-tags">
                                    <span class="p-news-archive__pickup-tag p-news-archive__pickup-tag--<?php echo esc_attr($news_item['category_class']); ?>"><?php echo esc_html($news_item['category_label']); ?></span>
                                </div>
                            <?php endif; ?>
                            <p class="c-campaign-content-caption p-news-archive__pickup-caption">セレクション<br><?php echo esc_html($news_item['title']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($pickup_news_items) > 1) : ?>
                    <button class="c-pop c-slider--btn c-slider--btn--left p-news-archive__pickup-pager-button js-news-archive-pickup-prev" type="button" aria-label="前の新着情報へ">
                        <img class="c-slider--btn__img" src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                    </button>
                    <button class="c-pop c-slider--btn c-slider--btn--right p-news-archive__pickup-pager-button js-news-archive-pickup-next" type="button" aria-label="次の新着情報へ">
                        <img class="c-slider--btn__img" src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                    </button>
                    <div class="p-single-shop__slider-dots p-news-archive__pickup-dots js-news-archive-pickup-dots" aria-label="新着情報ピックアップのページ送り">
                        <?php foreach ($pickup_news_items as $news_index => $news_item) : ?>
                            <button
                                type="button"
                                class="<?php echo $news_index === 0 ? 'is-active' : ''; ?>"
                                data-pickup-index="<?php echo esc_attr($news_index); ?>"
                                aria-label="新着情報<?php echo esc_attr($news_index + 1); ?>を表示"
                                aria-pressed="<?php echo $news_index === 0 ? 'true' : 'false'; ?>"
                            ></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        <?php if ($news_items) : ?>
            <section class="p-news-archive__list" aria-label="新着情報一覧">
                <?php foreach ($news_items as $news_item) : ?>
                    <?php
                    $news_image_url = $news_item['image']['url'] ?? $placeholder_image;
                    $news_image_alt = $news_item['image']['alt'] ?? '';
                    $news_genre_class = $news_item['category_class'] === 'notice' ? 'new' : $news_item['category_class'];
                    ?>
                    <a class="c-news-card-wrapper p-news-archive__list-card-wrapper" href="<?php echo esc_url($news_item['permalink']); ?>">
                        <div class="c-news-card p-news-archive__list-card">
                            <div class="c-news-card__header p-news-archive__list-header">
                                <time class="c-news-card__date" datetime="<?php echo esc_attr($news_item['datetime']); ?>"><?php echo esc_html($news_item['date']); ?></time>
                                <?php if ($news_item['category_label']) : ?>
                                    <span class="c-news-card__genre c-news-card__genre--<?php echo esc_attr($news_genre_class); ?> p-news-archive__list-genre p-news-archive__list-genre--<?php echo esc_attr($news_item['category_class']); ?>"><?php echo esc_html($news_item['category_label']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="p-news-archive__list-body-row">
                                <picture class="p-news-archive__list-image">
                                    <img src="<?php echo esc_url($news_image_url); ?>" alt="<?php echo esc_attr($news_image_alt ?: $news_item['title']); ?>">
                                </picture>
                                <p class="c-news-card__body p-news-archive__list-body"><?php echo esc_html($news_item['title']); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>
            <?php if ($news_total_pages > 1) : ?>
                <nav class="c-archive-pagination p-news-archive__pagination" aria-label="新着情報一覧のページ送り">
                    <?php for ($page_number = 1; $page_number <= $news_total_pages; $page_number++) : ?>
                        <?php if ($page_number === $news_paged) : ?>
                            <span class="c-archive-pagination__item p-news-archive__pagination-item is-current" aria-current="page"><?php echo esc_html($page_number); ?></span>
                        <?php else : ?>
                            <a class="c-archive-pagination__item p-news-archive__pagination-item" href="<?php echo esc_url(get_pagenum_link($page_number)); ?>"><?php echo esc_html($page_number); ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($news_paged < $news_total_pages) : ?>
                        <a class="c-archive-pagination__next p-news-archive__pagination-next" href="<?php echo esc_url(get_pagenum_link($news_paged + 1)); ?>" aria-label="次のページへ"></a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>        <?php endif; ?>
    </div>
</main>

<script>
    console.log('foodsNewsArchiveItems', <?php echo wp_json_encode($news_items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>);
</script>

<?php
get_footer();