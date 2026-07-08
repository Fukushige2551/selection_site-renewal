<?php
function foods_news_detail_get_field_value($post_id, $field_name) {
    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    return get_post_meta($post_id, $field_name, true);
}

function foods_news_detail_get_first_field_value($post_id, array $field_names) {
    foreach ($field_names as $field_name) {
        $value = foods_news_detail_get_field_value($post_id, $field_name);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    return '';
}

function foods_news_detail_get_image_data($image, $size = 'large') {
    if (empty($image)) {
        return null;
    }

    if (is_array($image)) {
        $url = $image['sizes'][$size] ?? $image['url'] ?? '';
        return $url ? [
            'id' => $image['ID'] ?? $image['id'] ?? null,
            'url' => $url,
            'alt' => $image['alt'] ?? '',
        ] : null;
    }

    if (is_numeric($image)) {
        $image_id = (int) $image;
        return [
            'id' => $image_id,
            'url' => wp_get_attachment_image_url($image_id, $size),
            'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
        ];
    }

    if (is_string($image)) {
        return [
            'id' => null,
            'url' => $image,
            'alt' => '',
        ];
    }

    return null;
}

function foods_news_detail_get_news_image_data($post_id, $size = 'large') {
    if (has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        return [
            'id' => $thumbnail_id,
            'url' => get_the_post_thumbnail_url($post_id, $size),
            'alt' => get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
        ];
    }

    foreach (['news_eyecatch_image', 'news_image', 'main_image', 'thumbnail', 'image'] as $field_name) {
        $image = foods_news_detail_get_field_value($post_id, $field_name);
        $image_data = foods_news_detail_get_image_data($image, $size);

        if (!empty($image_data['url'])) {
            return $image_data;
        }
    }

    return null;
}

function foods_news_detail_format_terms($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);

    if (is_wp_error($terms) || empty($terms)) {
        return [];
    }

    return array_map(function ($term) {
        return [
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
        ];
    }, $terms);
}

function foods_news_detail_format_publish_date($date_value, $post_id) {
    if (is_string($date_value) && preg_match('/^\d{8}$/', $date_value)) {
        return substr($date_value, 0, 4) . '.' . substr($date_value, 4, 2) . '.' . substr($date_value, 6, 2);
    }

    if ($date_value) {
        $timestamp = strtotime((string) $date_value);
        if ($timestamp) {
            return date_i18n('Y.m.d', $timestamp);
        }
    }

    return get_the_date('Y.m.d', $post_id);
}

function foods_news_detail_get_category_class($slug) {
    $classes = [
        'information' => 'notice',
        'notice' => 'notice',
        'campaign' => 'campaign',
        'shop-news' => 'shop',
        'commitment' => 'commitment',
        'important' => 'important',
    ];

    return $classes[$slug] ?? 'notice';
}
function foods_news_detail_get_related_items($post_id, $limit = 2) {
    $limit = max(1, (int) $limit);
    $related_posts = [];
    $exclude_ids = [(int) $post_id];
    $categories = get_the_terms($post_id, 'news_category');

    if (!is_wp_error($categories) && !empty($categories)) {
        $category_ids = wp_list_pluck($categories, 'term_id');
        $related_posts = get_posts([
            'post_type' => 'news',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'post__not_in' => $exclude_ids,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => [
                [
                    'taxonomy' => 'news_category',
                    'field' => 'term_id',
                    'terms' => $category_ids,
                ],
            ],
        ]);
        $exclude_ids = array_merge($exclude_ids, wp_list_pluck($related_posts, 'ID'));
    }

    if (count($related_posts) < $limit) {
        $fallback_posts = get_posts([
            'post_type' => 'news',
            'post_status' => 'publish',
            'posts_per_page' => $limit - count($related_posts),
            'post__not_in' => $exclude_ids,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        $related_posts = array_merge($related_posts, $fallback_posts);
    }

    return array_map(function ($related_post) {
        $categories = foods_news_detail_format_terms($related_post->ID, 'news_category');
        $primary_category = $categories[0] ?? null;

        return [
            'id' => $related_post->ID,
            'title' => get_the_title($related_post->ID),
            'permalink' => get_permalink($related_post->ID),
            'date' => get_the_date('Y.m.d', $related_post->ID),
            'datetime' => get_the_date('Y-m-d', $related_post->ID),
            'image' => foods_news_detail_get_news_image_data($related_post->ID, 'medium'),
            'category_label' => $primary_category['name'] ?? '',
            'category_class' => $primary_category ? foods_news_detail_get_category_class($primary_category['slug']) : 'notice',
        ];
    }, array_slice($related_posts, 0, $limit));
}

function foods_news_detail_prepare_body($html) {
    $html = (string) $html;

    if (trim(wp_strip_all_tags($html)) === '') {
        return [
            'html' => '',
            'toc' => [],
        ];
    }

    if (!class_exists('DOMDocument') || !class_exists('DOMXPath')) {
        return [
            'html' => $html,
            'toc' => [],
        ];
    }

    $previous_errors = libxml_use_internal_errors(true);
    $document = new DOMDocument('1.0', 'UTF-8');
    $document->loadHTML('<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="foods-news-detail-body">' . $html . '</div></body></html>');
    libxml_clear_errors();
    libxml_use_internal_errors($previous_errors);

    $wrapper = $document->getElementById('foods-news-detail-body');
    if (!$wrapper) {
        return [
            'html' => $html,
            'toc' => [],
        ];
    }

    $xpath = new DOMXPath($document);
    $headings = $xpath->query('.//h2 | .//h3', $wrapper);
    $toc = [];
    $used_ids = [];

    foreach ($headings as $index => $heading) {
        $title = trim(wp_strip_all_tags($heading->textContent));
        if ($title === '') {
            continue;
        }

        $base_id = sanitize_title($title);
        if ($base_id === '') {
            $base_id = 'section-' . ($index + 1);
        }

        $id = $heading->getAttribute('id') ?: $base_id;
        $unique_id = $id;
        $count = 2;
        while (isset($used_ids[$unique_id])) {
            $unique_id = $id . '-' . $count;
            $count++;
        }

        $heading->setAttribute('id', $unique_id);
        $used_ids[$unique_id] = true;
        $toc[] = [
            'id' => $unique_id,
            'title' => $title,
            'level' => strtolower($heading->nodeName),
        ];
    }

    $body_html = '';
    foreach ($wrapper->childNodes as $child) {
        $body_html .= $document->saveHTML($child);
    }

    return [
        'html' => $body_html,
        'toc' => $toc,
    ];
}

get_header();
?>

<main class="p-news-detail c-main">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $news_id = get_the_ID();
            $theme_uri = get_template_directory_uri();
            $news_categories = foods_news_detail_format_terms($news_id, 'news_category');
            $news_publish_date = foods_news_detail_get_field_value($news_id, 'news_publish_date');
            $news_display_date = foods_news_detail_format_publish_date($news_publish_date, $news_id);
            $news_image = foods_news_detail_get_news_image_data($news_id, 'large');
            $news_summary = foods_news_detail_get_first_field_value($news_id, ['news_summary', 'news_overview', 'news_lead']);
            $news_summary = $news_summary ?: get_the_excerpt($news_id);
            $news_body = foods_news_detail_get_field_value($news_id, 'news_body');
            $news_content = apply_filters('the_content', get_post_field('post_content', $news_id));
            $news_body_source = $news_body ? apply_filters('the_content', $news_body) : $news_content;
            $news_body_data = foods_news_detail_prepare_body($news_body_source);
            $news_body_html = $news_body_data['html'];
            $news_toc_items = $news_body_data['toc'];
            $news_title = get_the_title($news_id);
            $news_permalink = get_permalink($news_id);
            $news_share_url = rawurlencode($news_permalink);
            $news_share_text = rawurlencode($news_title);
            $news_previous_post = get_previous_post(false, '', 'news_category');
            $news_next_post = get_next_post(false, '', 'news_category');
            $news_archive_url = get_post_type_archive_link('news') ?: home_url('/news/');
            $recruit_archive_url = get_post_type_archive_link('recruit_part_time') ?: home_url('/recruit/');
            $related_news_items = foods_news_detail_get_related_items($news_id, 10);
            $news_detail_item = [
                'id' => $news_id,
                'title' => $news_title,
                'permalink' => $news_permalink,
                'date' => get_the_date('Y.m.d', $news_id),
                'datetime' => get_the_date('Y-m-d', $news_id),
                'publish_date' => $news_publish_date,
                'display_date' => $news_display_date,
                'summary' => $news_summary,
                'content' => $news_content,
                'body' => $news_body,
                'toc' => $news_toc_items,
                'image' => $news_image,
                'categories' => $news_categories,
                'shops' => foods_news_detail_format_terms($news_id, 'news_shop_category'),
                'commitments' => foods_news_detail_format_terms($news_id, 'news_commitment'),
            ];
            ?>
            <nav class="c-breadcrumb p-news-detail__breadcrumb" aria-label="パンくず">
                <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
                <span class="c-breadcrumb__separator" aria-hidden="true"></span>
                <a href="<?php echo esc_url(get_post_type_archive_link('news')); ?>">新着情報</a>
                <span class="c-breadcrumb__separator" aria-hidden="true"></span>
                <span><?php the_title(); ?></span>
            </nav>

            <section class="p-news-detail__header" aria-labelledby="news-detail-title">
                <div class="p-news-detail__meta">
                    <time class="p-news-detail__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d', $news_id)); ?>"><?php echo esc_html($news_display_date); ?></time>
                    <?php if ($news_categories) : ?>
                        <ul class="p-news-detail__categories" aria-label="カテゴリ">
                            <?php foreach ($news_categories as $category) : ?>
                                <li class="p-news-detail__category p-news-detail__category--<?php echo esc_attr(foods_news_detail_get_category_class($category['slug'])); ?>"><?php echo esc_html($category['name']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <h1 id="news-detail-title" class="p-news-detail__title"><?php the_title(); ?></h1>
            </section>

            <?php if ($news_image || $news_summary || trim(wp_strip_all_tags($news_body_html))) : ?>
                <section class="p-news-detail__content" aria-label="新着情報本文">
                    <?php if ($news_image) : ?>
                        <figure class="p-news-detail__figure">
                            <img class="p-news-detail__image" src="<?php echo esc_url($news_image['url']); ?>" alt="<?php echo esc_attr($news_image['alt'] ?: get_the_title($news_id)); ?>">
                        </figure>
                    <?php endif; ?>
                    <?php if ($news_summary) : ?>
                        <div class="p-news-detail__summary"><?php echo wp_kses_post(wpautop($news_summary)); ?></div>
                    <?php endif; ?>
                    <?php if ($news_toc_items) : ?>
                        <nav class="p-news-detail__toc" aria-label="目次">
                            <p class="p-news-detail__toc-title">目次</p>
                            <ol class="p-news-detail__toc-list">
                                <?php foreach ($news_toc_items as $toc_item) : ?>
                                    <li class="p-news-detail__toc-item p-news-detail__toc-item--<?php echo esc_attr($toc_item['level']); ?>">
                                        <a href="#<?php echo esc_attr($toc_item['id']); ?>"><?php echo esc_html($toc_item['title']); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    <?php endif; ?>
                    <?php if (trim(wp_strip_all_tags($news_body_html))) : ?>
                        <div class="p-news-detail__body"><?php echo wp_kses_post($news_body_html); ?></div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="p-news-detail__actions" aria-label="記事の共有と移動">
                <div class="p-news-detail__share">
                    <p class="p-news-detail__share-title">シェアする</p>
                    <ul class="p-news-detail__share-list">
                        <li class="p-news-detail__share-item">
                            <a class="p-news-detail__share-link p-news-detail__share-link--x" href="https://twitter.com/intent/tweet?url=<?php echo esc_attr($news_share_url); ?>&text=<?php echo esc_attr($news_share_text); ?>" target="_blank" rel="noopener noreferrer" aria-label="Xでシェアする"></a>
                        </li>
                        <li class="p-news-detail__share-item">
                            <a class="p-news-detail__share-link p-news-detail__share-link--instagram" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagramを開く"></a>
                        </li>
                        <li class="p-news-detail__share-item">
                            <a class="p-news-detail__share-link p-news-detail__share-link--facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr($news_share_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebookでシェアする"></a>
                        </li>
                    </ul>
                </div>
                <div class="p-news-detail__actions-divider" aria-hidden="true"></div>
                <nav class="p-news-detail__pager" aria-label="前後の新着情報">
                    <?php if ($news_previous_post) : ?>
                        <a class="c-pop p-news-detail__pager-arrow p-news-detail__pager-arrow--prev" href="<?php echo esc_url(get_permalink($news_previous_post)); ?>" aria-label="前の新着情報へ">
                            <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                        </a>
                    <?php else : ?>
                        <span class="p-news-detail__pager-arrow p-news-detail__pager-arrow--prev is-disabled" aria-disabled="true">
                            <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                        </span>
                    <?php endif; ?>
                    <a class="c-pop p-news-detail__back-link" href="<?php echo esc_url($news_archive_url); ?>">一覧へ戻る</a>
                    <?php if ($news_next_post) : ?>
                        <a class="c-pop p-news-detail__pager-arrow p-news-detail__pager-arrow--next" href="<?php echo esc_url(get_permalink($news_next_post)); ?>" aria-label="次の新着情報へ">
                            <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                        </a>
                    <?php else : ?>
                        <span class="p-news-detail__pager-arrow p-news-detail__pager-arrow--next is-disabled" aria-disabled="true">
                            <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                        </span>
                    <?php endif; ?>
                </nav>
            </section>

            <?php if ($related_news_items) : ?>
                <section class="p-news-detail__related" aria-labelledby="news-detail-related-title">
                    <h2 id="news-detail-related-title" class="p-news-detail__related-title">関連記事</h2>
                    <div class="p-news-detail__related-list js-news-detail-related-list">
                        <?php foreach ($related_news_items as $related_index => $related_item) : ?>
                            <?php
                            $related_image_url = $related_item['image']['url'] ?? ($theme_uri . '/img/component/no-image.png');
                            $related_image_alt = $related_item['image']['alt'] ?? '';
                            ?>
                            <a class="p-news-detail__related-card js-news-detail-related-card" href="<?php echo esc_url($related_item['permalink']); ?>" data-related-index="<?php echo esc_attr($related_index); ?>" <?php echo $related_index < 2 ? '' : 'hidden'; ?>>
                                <div class="p-news-detail__related-header">
                                    <time class="p-news-detail__related-date" datetime="<?php echo esc_attr($related_item['datetime']); ?>"><?php echo esc_html($related_item['date']); ?></time>
                                    <?php if ($related_item['category_label']) : ?>
                                        <span class="c-news-category-tag c-news-category-tag--<?php echo esc_attr($related_item['category_class']); ?> p-news-detail__related-category p-news-detail__related-category--<?php echo esc_attr($related_item['category_class']); ?>"><?php echo esc_html($related_item['category_label']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-news-detail__related-body">
                                    <picture class="p-news-detail__related-image">
                                        <img src="<?php echo esc_url($related_image_url); ?>" alt="<?php echo esc_attr($related_image_alt ?: $related_item['title']); ?>">
                                    </picture>
                                    <p class="p-news-detail__related-text"><?php echo esc_html($related_item['title']); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a class="p-news-detail__related-link" href="<?php echo esc_url($news_archive_url); ?>">新着一覧はこちら</a>
                    <?php if (count($related_news_items) > 2) : ?>
                        <div class="p-news-detail__related-pager" aria-label="関連記事のページ送り">
                            <button class="p-news-detail__related-pager-button p-news-detail__related-pager-button--prev js-news-detail-related-prev" type="button" aria-label="前の関連記事へ">
                                <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                            </button>
                            <div class="p-single-shop__slider-dots p-news-detail__related-dots js-news-detail-related-dots">
                                <?php for ($related_page_index = 0; $related_page_index < (int) ceil(count($related_news_items) / 2); $related_page_index++) : ?>
                                    <button
                                        type="button"
                                        class="<?php echo $related_page_index === 0 ? 'is-active' : ''; ?>"
                                        data-related-page="<?php echo esc_attr($related_page_index); ?>"
                                        aria-label="関連記事<?php echo esc_attr($related_page_index + 1); ?>ページ目を表示"
                                        aria-pressed="<?php echo $related_page_index === 0 ? 'true' : 'false'; ?>"
                                    ></button>
                                <?php endfor; ?>
                            </div>
                            <button class="p-news-detail__related-pager-button p-news-detail__related-pager-button--next js-news-detail-related-next" type="button" aria-label="次の関連記事へ">
                                <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                            </button>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <a class="p-news-detail__recruit-banner c-pop" href="<?php echo esc_url($recruit_archive_url); ?>">
                <img src="<?php echo esc_url($theme_uri . '/img/page/news/news-detail-recruit-banner.svg'); ?>" alt="採用情報">
            </a>

            <script>
                window.foodsNewsDetailItem = <?php echo wp_json_encode($news_detail_item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
                console.log('foodsNewsDetailItem', window.foodsNewsDetailItem);
            </script>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php get_footer(); ?>