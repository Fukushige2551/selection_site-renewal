<?php get_header(); ?>

<main id="front-page" class="p-front-page">
    <?php
    $front_hero_query = new WP_Query([
        'post_type' => 'news',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);

    $front_hero_items = array_map(static function ($news_post) {
        $post_id = $news_post->ID;
        $image_id = get_post_thumbnail_id($post_id);

        if (!$image_id) {
            $image = function_exists('get_field')
                ? get_field('news_eyecatch_image', $post_id)
                : get_post_meta($post_id, 'news_eyecatch_image', true);

            if (is_array($image)) {
                $image_id = (int) ($image['ID'] ?? $image['id'] ?? 0);
            } elseif (is_numeric($image)) {
                $image_id = (int) $image;
            }
        }

        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
        $title = get_the_title($post_id);

        return [
            'url' => get_permalink($post_id),
            'image_url' => $image_url ?: get_template_directory_uri() . '/img/component/no-image.png',
            'image_alt' => ($image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '') ?: $title,
        ];
    }, $front_hero_query->posts);

    wp_reset_postdata();

    $front_flyer_query = new WP_Query([
        'post_type' => 'flyer',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        'orderby' => [
            'date' => 'DESC',
            'ID' => 'DESC',
        ],
        'no_found_rows' => true,
    ]);

    $front_get_flyer_field = static function ($post_id, $field_name) {
        if (function_exists('get_field')) {
            $value = get_field($field_name, $post_id);

            if ($value !== null && $value !== false && $value !== '') {
                return $value;
            }
        }

        return get_post_meta($post_id, $field_name, true);
    };

    $front_get_latest_bargain_item = static function ($items) {
        if (!is_array($items) || !$items) {
            return [];
        }

        $ranked_items = [];

        foreach (array_values($items) as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $ranked_items[] = [
                'item' => $item,
                'date' => preg_replace('/\D/', '', (string) ($item['item_start_date'] ?? '')),
                'index' => $index,
            ];
        }

        usort($ranked_items, static function ($left, $right) {
            $date_comparison = strcmp($right['date'], $left['date']);

            return $date_comparison !== 0
                ? $date_comparison
                : $right['index'] <=> $left['index'];
        });

        return $ranked_items[0]['item'] ?? [];
    };

    $front_get_image_data = static function ($image, $fallback_alt = '') {
        $image_id = 0;
        $image_url = '';
        $image_alt = '';

        if (is_array($image)) {
            $image_id = (int) ($image['ID'] ?? $image['id'] ?? 0);
            $image_url = $image['url'] ?? '';
            $image_alt = $image['alt'] ?? '';
        } elseif (is_numeric($image)) {
            $image_id = (int) $image;
        } elseif (is_string($image)) {
            $image_url = $image;
        }

        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'large') ?: '';
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        }

        return [
            'url' => $image_url ?: get_template_directory_uri() . '/img/component/no-image.png',
            'alt' => $image_alt ?: $fallback_alt,
        ];
    };

    $front_format_offer_date = static function ($start_date, $end_date) {
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $format_one = static function ($date) use ($weekdays) {
            $date = preg_replace('/\D/', '', (string) $date);
            $parsed = DateTimeImmutable::createFromFormat('!Ymd', $date, wp_timezone());

            if (!$parsed) {
                return '';
            }

            return esc_html($parsed->format('n月j日'))
                . '<span class="p-front-page__special-offers__date__day">('
                . esc_html($weekdays[(int) $parsed->format('w')])
                . ')</span>';
        };

        $start = $format_one($start_date);
        $end = $format_one($end_date);

        if ($start && $end && preg_replace('/\D/', '', (string) $start_date) !== preg_replace('/\D/', '', (string) $end_date)) {
            return $start . ' -' . $end;
        }

        return $start ?: $end;
    };

    $front_format_price = static function ($price) {
        if ($price === '' || $price === null || !is_numeric($price)) {
            return '';
        }

        $price_string = (string) $price;
        $decimal_count = 0;

        if (str_contains($price_string, '.')) {
            $decimal_count = min(2, strlen(rtrim(substr(strrchr($price_string, '.'), 1), '0')));
        }

        return number_format((float) $price, $decimal_count, '.', ',');
    };

    $front_flyer_items = array_map(static function ($flyer_post) use (
        $front_get_flyer_field,
        $front_get_latest_bargain_item,
        $front_get_image_data
    ) {
        $post_id = $flyer_post->ID;
        $item = $front_get_latest_bargain_item($front_get_flyer_field($post_id, 'bargain_items'));
        $item_name = (string) ($item['item_name'] ?? '');

        return [
            'flyer_id' => $post_id,
            'item' => $item,
            'image' => $front_get_image_data($item['item_image'] ?? '', $item_name ?: '画像無し'),
            'start_date' => $item['item_start_date'] ?? $front_get_flyer_field($post_id, 'flyer_start_date'),
            'end_date' => $item['item_end_date'] ?? $front_get_flyer_field($post_id, 'flyer_end_date'),
        ];
    }, $front_flyer_query->posts);

    wp_reset_postdata();

    $front_shop_prefectures = get_terms([
        'taxonomy' => 'shop_prefecture',
        'hide_empty' => true,
    ]);

    if (is_wp_error($front_shop_prefectures)) {
        $front_shop_prefectures = [];
    }

    $front_shop_prefecture_order = array_flip(['chiba', 'tokyo', 'saitama']);
    usort($front_shop_prefectures, static function ($left, $right) use ($front_shop_prefecture_order) {
        $left_order = $front_shop_prefecture_order[$left->slug] ?? PHP_INT_MAX;
        $right_order = $front_shop_prefecture_order[$right->slug] ?? PHP_INT_MAX;

        return $left_order === $right_order
            ? strcmp($left->name, $right->name)
            : $left_order <=> $right_order;
    });

    $front_shop_posts = get_posts([
        'post_type' => 'shop',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => 'shop_order',
        'orderby' => [
            'meta_value_num' => 'ASC',
            'title' => 'ASC',
        ],
        'order' => 'ASC',
    ]);

    $front_shop_items = array_map(static function ($shop_post) use ($front_get_flyer_field) {
        $post_id = $shop_post->ID;
        $prefectures = get_the_terms($post_id, 'shop_prefecture');
        $prefecture_slugs = !is_wp_error($prefectures) && $prefectures
            ? wp_list_pluck($prefectures, 'slug')
            : [];
        $map_url = (string) $front_get_flyer_field($post_id, 'congestion_url');

        if ($map_url === '') {
            $map_embed = (string) $front_get_flyer_field($post_id, 'google_map_url');

            if (preg_match('/<iframe[^>]+src=["\']([^"\']+)/i', $map_embed, $matches)) {
                $map_url = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
            }
        }

        return [
            'id' => $post_id,
            'name' => get_the_title($post_id),
            'url' => get_permalink($post_id),
            'prefecture_slugs' => array_map('sanitize_html_class', $prefecture_slugs),
            'postal_code' => (string) $front_get_flyer_field($post_id, 'postal_code'),
            'address' => (string) $front_get_flyer_field($post_id, 'address'),
            'business_hours' => (string) $front_get_flyer_field($post_id, 'business_hours'),
            'business_hours_note' => (string) $front_get_flyer_field($post_id, 'business_hours_note'),
            'map_url' => $map_url,
        ];
    }, $front_shop_posts);

    $front_campaign_query = new WP_Query([
        'post_type' => 'news',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'orderby' => [
            'date' => 'DESC',
            'ID' => 'DESC',
        ],
        'tax_query' => [
            [
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => ['campaign'],
            ],
        ],
        'no_found_rows' => true,
    ]);

    $front_campaign_items = array_map(static function ($news_post) use (
        $front_get_flyer_field,
        $front_get_image_data
    ) {
        $post_id = $news_post->ID;
        $title = get_the_title($post_id);
        $image = '';

        if (has_post_thumbnail($post_id)) {
            $image = get_post_thumbnail_id($post_id);
        } else {
            foreach (['news_eyecatch_image', 'news_image', 'main_image', 'thumbnail', 'image'] as $field_name) {
                $image = $front_get_flyer_field($post_id, $field_name);

                if (!empty($image)) {
                    break;
                }
            }
        }

        return [
            'title' => $title,
            'url' => get_permalink($post_id),
            'date' => get_the_date('Y.m.d', $post_id),
            'datetime' => get_the_date('Y-m-d', $post_id),
            'image' => $front_get_image_data($image, $title),
        ];
    }, $front_campaign_query->posts);

    wp_reset_postdata();

    $front_recipe_query = new WP_Query([
        'post_type' => 'recipe',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'orderby' => [
            'date' => 'DESC',
            'ID' => 'DESC',
        ],
        'no_found_rows' => true,
    ]);

    $front_recipe_items = array_map(static function ($recipe_post) use (
        $front_get_flyer_field,
        $front_get_image_data
    ) {
        $post_id = $recipe_post->ID;
        $title = get_the_title($post_id);
        $image = $front_get_flyer_field($post_id, 'recipe_eyecatch_image');

        if (empty($image) && has_post_thumbnail($post_id)) {
            $image = get_post_thumbnail_id($post_id);
        }

        if (empty($image)) {
            $image = $front_get_flyer_field($post_id, 'recipe_photo');
        }

        $main_ingredients = get_the_terms($post_id, 'recipe_main_ingredient');
        $category = !is_wp_error($main_ingredients) && $main_ingredients
            ? reset($main_ingredients)->name
            : '';
        $cooking_time = wp_strip_all_tags((string) $front_get_flyer_field($post_id, 'recipe_cooking_time'));
        $duration = '';

        if (preg_match('/(\d+)/', $cooking_time, $matches)) {
            $duration = 'PT' . $matches[1] . 'M';
        }

        return [
            'title' => $title,
            'url' => get_permalink($post_id),
            'category' => $category,
            'cooking_time' => $cooking_time,
            'duration' => $duration,
            'image' => $front_get_image_data($image, $title),
        ];
    }, $front_recipe_query->posts);

    wp_reset_postdata();

    $front_news_query = new WP_Query([
        'post_type' => 'news',
        'post_status' => 'publish',
        'posts_per_page' => 4,
        'orderby' => [
            'date' => 'DESC',
            'ID' => 'DESC',
        ],
        'no_found_rows' => true,
    ]);

    $front_news_category_priority = ['notice', 'campaign', 'shop-news', 'commitment', 'important'];
    $front_news_category_classes = [
        'notice' => 'new',
        'campaign' => 'campaign',
        'shop-news' => 'shop',
        'commitment' => 'commitment',
        'important' => 'important',
    ];

    $front_news_items = array_map(static function ($news_post) use (
        $front_news_category_priority,
        $front_news_category_classes
    ) {
        $post_id = $news_post->ID;
        $categories = get_the_terms($post_id, 'news_category');
        $selected_category = null;

        if (!is_wp_error($categories) && $categories) {
            foreach ($front_news_category_priority as $category_slug) {
                foreach ($categories as $category) {
                    if ($category->slug === $category_slug) {
                        $selected_category = $category;
                        break 2;
                    }
                }
            }

            if (!$selected_category) {
                $selected_category = reset($categories);
            }
        }

        $category_slug = $selected_category ? $selected_category->slug : 'notice';

        return [
            'title' => get_the_title($post_id),
            'url' => get_permalink($post_id),
            'date' => get_the_date('Y.m.d', $post_id),
            'datetime' => get_the_date('Y-m-d', $post_id),
            'category_label' => $selected_category ? $selected_category->name : '',
            'category_class' => $front_news_category_classes[$category_slug] ?? 'new',
        ];
    }, $front_news_query->posts);

    wp_reset_postdata();

    $render_front_hero_slide = static function ($item, $index, $numbered = false, $active = false, $clone = false) {
        $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
        $classes = 'p-front-page__hero__link';
        $classes .= $numbered ? ' hero__link--' . $number : '';
        $classes .= $active ? ' is-active' : '';
        ?>
        <a href="<?php echo esc_url($item['url']); ?>"
           class="<?php echo esc_attr($classes); ?>"
           <?php if ($clone) : ?>aria-hidden="true" tabindex="-1"<?php endif; ?>>
            <img class="p-front-page__hero__link__img"
                 src="<?php echo esc_url($item['image_url']); ?>"
                 alt="<?php echo $clone ? '' : esc_attr($item['image_alt']); ?>">
        </a>
        <?php
    };

    $render_front_flyer_item = static function ($flyer, $index) use ($front_format_offer_date, $front_format_price) {
        $item = $flyer['item'];
        $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
        $base_price = $front_format_price($item['base_price'] ?? '');
        $tax_price = $front_format_price($item['tax_price'] ?? '');
        ?>
        <div class="p-front-page__special-offers__item special-offers__item--<?php echo esc_attr($number); ?> is-special<?php echo $index === 0 ? ' is-active' : ''; ?>">
            <p class="p-front-page__special-offers__date">
                <?php echo wp_kses_post($front_format_offer_date($flyer['start_date'], $flyer['end_date'])); ?>
            </p>
            <div class="p-front-page__special-offers__item__details--wrapper">
                <picture class="p-front-page__special-offers__item__img">
                    <img src="<?php echo esc_url($flyer['image']['url']); ?>" alt="<?php echo esc_attr($flyer['image']['alt']); ?>">
                </picture>
                <div class="p-front-page__special-offers__item__details">
                    <div class="p-front-page__special-offers__item__details__title">
                        <?php if (!empty($item['item_origin'])) : ?>
                            <span class="p-front-page__special-offers__item__details__title__origin"><?php echo esc_html($item['item_origin']); ?></span><br class="u-disp--tab">
                        <?php endif; ?>
                        <span class="p-front-page__special-offers__item__details__title__highlight"><?php echo esc_html($item['item_name'] ?? ''); ?></span>
                        <?php if (!empty($item['item_quantity'])) : ?>
                            <span class="p-front-page__special-offers__item__details__title__note"><?php echo esc_html($item['item_quantity']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($base_price !== '' || $tax_price !== '') : ?>
                        <div class="p-front-page__special-offers__item__details__price">
                            <?php if ($base_price !== '') : ?>
                                <span class="p-front-page__special-offers__item__details__price__label">本体</span>
                                <span class="p-front-page__special-offers__item__details__price__value"><?php echo esc_html($base_price); ?></span><span class="p-front-page__special-offers__item__details__price__unit">円</span>
                            <?php endif; ?>
                            <?php if ($tax_price !== '') : ?>
                                <p class="p-front-page__special-offers__item__details__tax">(税込 <?php echo esc_html($tax_price); ?>円)</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($item['note'])) : ?>
                <p class="p-front-page__special-offers__item--note"><?php echo wp_kses_post($item['note']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    };

    $render_front_shop_item = static function ($shop) {
        $prefecture_classes = implode(' ', array_map(
            static fn($slug) => 'is-' . $slug,
            $shop['prefecture_slugs']
        ));
        ?>
        <li class="c-shop-card <?php echo esc_attr($prefecture_classes); ?>">
            <p class="c-shop-card__name"><?php echo esc_html($shop['name']); ?></p>
            <div class="c-shop-card__wrapper">
                <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                </svg>
                <address>
                    <?php if ($shop['postal_code'] !== '') : ?>
                        〒<?php echo esc_html($shop['postal_code']); ?><br>
                    <?php endif; ?>
                    <?php echo nl2br(esc_html($shop['address'])); ?>
                    <?php if ($shop['map_url'] !== '') : ?>
                        <a class="c-shop-card__address__link" href="<?php echo esc_url($shop['map_url']); ?>" target="_blank" rel="noopener noreferrer">マップを見る</a>
                    <?php endif; ?>
                </address>
            </div>
            <div class="c-shop-card__wrapper">
                <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                </svg>
                <time>
                    <?php echo nl2br(esc_html($shop['business_hours'])); ?>
                    <?php if ($shop['business_hours_note'] !== '') : ?>
                        <br><?php echo nl2br(esc_html($shop['business_hours_note'])); ?>
                    <?php endif; ?>
                </time>
            </div>
            <a class="c-shop-card__link" href="<?php echo esc_url($shop['url']); ?>">
                <span class="c-btn c-btn--common--blue">チラシ・店舗情報</span>
            </a>
        </li>
        <?php
    };
    ?>
    <!-- ヒーロー start -->
    <div class="c-bg p-front-page__bg--hero">
        <div class="p-front-page__hero">
            <!-- 背景画像(position配置) start -->
            <div class="p-front-page__hero__cartain"></div>
            <div class="c-bg__positioned p-front-page__bg--hero--01"></div>
            <div class="c-bg__positioned p-front-page__bg--hero--02"></div>
            <div class="c-bg__positioned p-front-page__bg--hero--03"></div>
            <!-- 背景画像(position配置) end -->

            <!-- ページネーション start -->
            <div class="p-front-page__hero__pagination" aria-label="ヒーロースライダーのページネーション">
                <?php foreach ($front_hero_items as $index => $item) : ?>
                    <?php $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?>
                    <button class="p-front-page__hero__dot hero__dot--<?php echo esc_attr($number); ?><?php echo $index === 0 ? ' is-active' : ''; ?>"
                            type="button"
                            aria-label="スライド<?php echo esc_attr($index + 1); ?>を表示"></button>
                <?php endforeach; ?>
                <?php if (false) : ?>
                <button class="p-front-page__hero__dot hero__dot--01 is-active" type="button" aria-label="スライド1を表示"></button>
                <button class="p-front-page__hero__dot hero__dot--02" type="button" aria-label="スライド2を表示"></button>
                <button class="p-front-page__hero__dot hero__dot--03" type="button" aria-label="スライド3を表示"></button>
                <button class="p-front-page__hero__dot hero__dot--04" type="button" aria-label="スライド4を表示"></button>
                <button class="p-front-page__hero__dot hero__dot--05" type="button" aria-label="スライド5を表示"></button>
                <?php endif; ?>
            </div>
            <!-- ページネーション end -->

            <!-- スライダー（SP用） start -->
            <div class="p-front-page__hero__slider-sp">
                <?php foreach ($front_hero_items as $index => $item) : ?>
                    <?php $render_front_hero_slide($item, $index, false, $index === 0); ?>
                <?php endforeach; ?>
                <?php if (false) : ?>
                <a href="/" class="p-front-page__hero__link is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">おせち<br>ご予約承り中</p>
                </a>
                <a href="/" class="p-front-page__hero__link">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">2<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">3<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">4<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">5<br>ヒーロー画像</p>
                </a>
                <?php endif; ?>
            </div>
            <!-- スライダー（SP用） end -->

            <!-- スライダー（tablet用） start -->
            <div class="p-front-page__hero__slider-tablet">
                <?php if ($front_hero_items) : ?>
                    <?php $last_index = count($front_hero_items) - 1; ?>
                    <?php $render_front_hero_slide($front_hero_items[$last_index], $last_index, true, false, true); ?>
                    <?php foreach ($front_hero_items as $index => $item) : ?>
                        <?php $render_front_hero_slide($item, $index, true, $index === 0); ?>
                    <?php endforeach; ?>
                    <?php foreach ($front_hero_items as $index => $item) : ?>
                        <?php $render_front_hero_slide($item, $index, true, false, true); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (false) : ?>
                <a href="/" class="p-front-page__hero__link hero__link--05">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">5<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--01 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">おせち<br>ご予約承り中</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--02">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">2<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--03">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">3<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--04">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">4<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--05">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">5<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--01 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">おせち<br>ご予約承り中</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--02">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">2<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--03">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">3<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--04">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">4<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--05">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">5<br>ヒーロー画像</p>
                </a>
                <?php endif; ?>
            </div>
            <!-- スライダー（tablet用） end -->

            <!-- スライダー（PC用） start -->
            <div class="p-front-page__hero__slider-pc">
                <?php if ($front_hero_items) : ?>
                    <?php $last_index = count($front_hero_items) - 1; ?>
                    <?php $render_front_hero_slide($front_hero_items[$last_index], $last_index, true, true, true); ?>
                    <?php foreach ($front_hero_items as $index => $item) : ?>
                        <?php $render_front_hero_slide($item, $index, true, true); ?>
                    <?php endforeach; ?>
                    <?php foreach ($front_hero_items as $index => $item) : ?>
                        <?php $render_front_hero_slide($item, $index, true, false, true); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (false) : ?>
                <a href="/" class="p-front-page__hero__link hero__link--05 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">5<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--01 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">1<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--02 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">2<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--03 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">3<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--04 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="おせち予約はこちら">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">4<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--05 is-active">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">5<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--01" aria-hidden="true" tabindex="-1">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">1<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--02" aria-hidden="true" tabindex="-1">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">2<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--03" aria-hidden="true" tabindex="-1">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">3<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--04" aria-hidden="true" tabindex="-1">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">4<br>ヒーロー画像</p>
                </a>
                <a href="/" class="p-front-page__hero__link hero__link--05" aria-hidden="true" tabindex="-1">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_hero-slider-pc-01.webp" type="image/webp">
                        <img class="p-front-page__hero__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_hero-slider-pc-01.png" alt="">
                    </picture>
                    <p class="p-front-page__hero__catch-copy">5<br>ヒーロー画像</p>
                </a>
                <?php endif; ?>
            </div>
            <!-- スライダー（PC用） end -->
        </div>

        <!-- 今日のオススメ start -->
        <div class="p-front-page__special-offers">
            <!-- WPコンテンツ start -->
            <div class="p-front-page__special-offers__slider">
                <?php foreach ($front_flyer_items as $index => $flyer) : ?>
                    <?php $render_front_flyer_item($flyer, $index); ?>
                <?php endforeach; ?>
                <?php if (false) : ?>
                <div class="p-front-page__special-offers__item special-offers__item--05 is-special">
                    <p class="p-front-page__special-offers__date">
                        10月25日<span class="p-front-page__special-offers__date__day">(火)</span> -11月3日<span class="p-front-page__special-offers__date__day">(木)</span>
                    </p>
                    <div class="p-front-page__special-offers__item__details--wrapper">
                        <picture class="p-front-page__special-offers__item__img">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/webp/no-image.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/component/no-image.png" alt="画像無し">
                        </picture>
                        <div class="p-front-page__special-offers__item__details">
                            <div class="p-front-page__special-offers__item__details__title">
                                <span class="p-front-page__special-offers__item__details__title__origin">北海道産ほか</span><br class="u-disp--tab">
                                <span class="p-front-page__special-offers__item__details__title__highlight">サーモントラウト</span>
                                <span class="p-front-page__special-offers__item__details__title__note">100gあたり</span>
                            </div>
                            <div class="p-front-page__special-offers__item__details__price">
                                <span class="p-front-page__special-offers__item__details__price__label">本体</span>
                                <span class="p-front-page__special-offers__item__details__price__value">299</span><span class="p-front-page__special-offers__item__details__price__unit">円</span>
                                <p class="p-front-page__special-offers__item__details__tax">(税込 329.22円)</p>
                            </div>
                        </div>
                    </div>
                    <p class="p-front-page__special-offers__item--note">1000円以上お買い上げの方（本商品代金を除く） お一家族1点限り</p>
                </div>
                <div class="p-front-page__special-offers__item special-offers__item--01 is-active is-special">
                    <p class="p-front-page__special-offers__date">
                        10月25日<span class="p-front-page__special-offers__date__day">(火)</span> -11月3日<span class="p-front-page__special-offers__date__day">(木)</span>
                    </p>
                    <div class="p-front-page__special-offers__item__details--wrapper is--limited--qupon">
                        <picture class="p-front-page__special-offers__item__img">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/webp/no-image.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/component/no-image.png" alt="画像無し">
                        </picture>
                        <div class="p-front-page__special-offers__item__details">
                            <div class="p-front-page__special-offers__item__details__title">
                                <span class="p-front-page__special-offers__item__details__title__origin">北海道産ほか</span><br class="u-disp--tab">
                                <span class="p-front-page__special-offers__item__details__title__highlight">サーモントラウト</span>
                                <span class="p-front-page__special-offers__item__details__title__note">100gあたり</span>
                            </div>
                            <div class="p-front-page__special-offers__item__details__price">
                                <span class="p-front-page__special-offers__item__details__price__label">本体</span>
                                <span class="p-front-page__special-offers__item__details__price__value">299</span><span class="p-front-page__special-offers__item__details__price__unit">円</span>
                                <p class="p-front-page__special-offers__item__details__tax">(税込 329.22円)</p>
                            </div>
                        </div>
                    </div>
                    <p class="p-front-page__special-offers__item--note">1000円以上お買い上げの方（本商品代金を除く） お一家族1点限り</p>
                </div>
                <div class="p-front-page__special-offers__item special-offers__item--02 is-special">
                    <p class="p-front-page__special-offers__date">
                        9月16日<span class="p-front-page__special-offers__date__day">(火)</span>
                    </p>
                    <div class="p-front-page__special-offers__item__details--wrapper">
                        <picture class="p-front-page__special-offers__item__img">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/webp/no-image.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/component/no-image.png" alt="画像無し">
                        </picture>
                        <div class="p-front-page__special-offers__item__details">
                            <div class="p-front-page__special-offers__item__details__title">
                                <span class="p-front-page__special-offers__item__details__title__origin">北海道産ほか</span><br class="u-disp--tab">
                                <span class="p-front-page__special-offers__item__details__title__highlight">にんじん</span>
                                <span class="p-front-page__special-offers__item__details__title__note">（Mサイズ 3本 1袋）</span>
                            </div>
                            <div class="p-front-page__special-offers__item__details__price">
                                <span class="p-front-page__special-offers__item__details__price__label">本体</span>
                                <span class="p-front-page__special-offers__item__details__price__value">98</span><span class="p-front-page__special-offers__item__details__price__unit">円</span>
                                <p class="p-front-page__special-offers__item__details__tax">(税込 106.92円)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-front-page__special-offers__item special-offers__item--03 is-special">
                    <p class="p-front-page__special-offers__date">
                        9月16日<span class="p-front-page__special-offers__date__day">(火)</span> -18日<span class="p-front-page__special-offers__date__day">(木)</span>
                    </p>
                    <div class="p-front-page__special-offers__item__details--wrapper">
                        <picture class="p-front-page__special-offers__item__img">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/webp/no-image.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/component/no-image.png" alt="画像無し">
                        </picture>
                        <div class="p-front-page__special-offers__item__details">
                            <div class="p-front-page__special-offers__item__details__title">
                                <span class="p-front-page__special-offers__item__details__title__highlight">大根の煮付け</span>
                            </div>
                            <div class="p-front-page__special-offers__item__details__price">
                                <span class="p-front-page__special-offers__item__details__price__label">本体</span>
                                <span class="p-front-page__special-offers__item__details__price__value">300</span><span class="p-front-page__special-offers__item__details__price__unit">円</span>
                                <p class="p-front-page__special-offers__item__details__tax">(税込 330円)</p>
                            </div>
                        </div>
                    </div>
                    <p class="p-front-page__special-offers__item--note">1000円以上お買い上げの方（本商品代金を除く） お一家族1点限り</p>
                </div>
                <div class="p-front-page__special-offers__item special-offers__item--04 is-special">
                    <p class="p-front-page__special-offers__date">
                        10月25日<span class="p-front-page__special-offers__date__day">(火)</span> -11月3日<span class="p-front-page__special-offers__date__day">(木)</span>
                    </p>
                    <div class="p-front-page__special-offers__item__details--wrapper">
                        <picture class="p-front-page__special-offers__item__img">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/webp/no-image.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/component/no-image.png" alt="画像無し">
                        </picture>
                        <div class="p-front-page__special-offers__item__details">
                            <div class="p-front-page__special-offers__item__details__title">
                                <span class="p-front-page__special-offers__item__details__title__origin">千葉産ほか</span><br class="u-disp--tab">
                                <!-- <span class="p-front-page__special-offers__item__details__title__highlight"><br class="p-front-page__special-offers__item__details__title__highlight__br">刺身盛り合わせ</span> -->
                                <span class="p-front-page__special-offers__item__details__title__highlight">刺身盛り合わせ</span>
                                <span class="p-front-page__special-offers__item__details__title__note">（マグロ・ホタテ・エビ・タイ・サーモン）</span>
                            </div>
                            <div class="p-front-page__special-offers__item__details__price">
                                <span class="p-front-page__special-offers__item__details__price__label">本体</span>
                                <span class="p-front-page__special-offers__item__details__price__value">1,000</span><span class="p-front-page__special-offers__item__details__price__unit">円</span>
                                <p class="p-front-page__special-offers__item__details__tax">(税込 1,100円)</p>
                            </div>
                        </div>
                    </div>
                    <p class="p-front-page__special-offers__item--note">1000円以上お買い上げの方（本商品代金を除く） お一家族1点限り</p>
                </div>
                <?php endif; ?>

                <button class="c-pop c-slider--btn c-slider--btn--left" type="button" aria-label="前の特売商品へ">
                    <svg class="c-slider--btn__img" width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M23.4573 7.22216C23.4075 7.06927 23.3578 6.92366 23.3081 6.77077L23.4573 6.77077L23.4573 6.47228C23.6064 6.42131 23.7485 6.37035 23.8977 6.31939C24.1108 6.13009 24.1535 5.7952 24.189 5.4239C24.3382 5.37293 24.4802 5.32197 24.6294 5.27101C24.807 5.69327 25.013 5.93353 25.0699 6.47228C24.3666 6.64701 24.2245 7.13479 23.4644 7.22216L23.4573 7.22216ZM26.377 5.27101L26.377 5.4239L27.2508 5.4239L27.2508 5.57679L27.4 5.57679C27.3644 5.93353 27.336 5.94809 27.2508 6.17378L26.6683 6.17378L26.6683 6.02089C26.3841 5.8316 26.3912 5.78063 25.9365 5.72239C25.8868 5.47486 25.8371 5.22004 25.7874 4.97251L25.6382 4.97251C25.5174 4.47016 25.2688 4.31727 24.7644 4.22263C24.7502 3.96053 24.6152 3.6766 24.6152 3.62564L24.7644 3.62564L24.7644 3.17425L25.4961 3.17425L25.4961 3.32714C25.7376 3.50915 26.377 4.25903 26.5191 4.5284L27.3929 4.5284C27.265 5.06716 26.9737 5.2346 26.3699 5.27829L26.377 5.27101ZM27.8404 3.48003L27.8404 3.32714L27.9896 3.32714L27.9896 3.48003L28.1388 3.48003L28.1388 3.63291C27.9896 3.68388 27.8475 3.73484 27.6983 3.7858C27.7836 3.48731 27.677 3.65476 27.8475 3.48731L27.8404 3.48003ZM26.3059 27.9276L26.3059 28.0805L26.1568 28.0805C26.107 28.2334 26.0573 28.379 26.0076 28.5319C25.3753 28.4955 25.5103 28.4373 25.1338 28.2334L25.1338 27.9349L26.3059 27.9349L26.3059 27.9276ZM23.9687 28.0805L23.237 28.0805C23.1873 27.9276 23.1376 27.782 23.0879 27.6292L23.237 27.6292C23.2868 27.4763 23.3365 27.3307 23.3862 27.1778L24.1179 27.1778C24.1179 27.6073 24.0682 27.8184 23.9687 28.0733L23.9687 28.0805ZM25.1338 26.7337C25.0841 26.5808 25.0343 26.4352 24.9846 26.2823C24.8852 26.2313 24.7928 26.1804 24.6934 26.1294L24.6934 25.9765L24.1108 25.9765L24.1108 25.8236L23.9616 25.8236C23.8622 25.5251 23.7698 25.2266 23.6704 24.9281C23.9261 24.8116 24.2245 24.6879 24.4021 24.4767C25.1409 24.5204 25.2475 24.8044 25.7163 25.0737C25.7021 25.8527 25.5316 26.1367 25.4251 26.7191L25.1338 26.7191L25.1338 26.7337ZM24.5513 22.5401L24.7005 22.5401L24.7005 22.3873C25.077 22.4091 25.2119 22.4018 25.4322 22.5401L25.4322 22.693L25.8726 22.693C25.8726 22.693 26.0218 22.9114 26.1639 22.9915C26.1283 23.4138 26.107 23.5376 25.8726 23.7414C25.7092 23.9089 25.8726 23.8069 25.5813 23.8943C25.4819 23.5958 25.3895 23.2973 25.2901 22.9988L24.5584 22.9988L24.5584 22.5474L24.5513 22.5401ZM28.494 23.2099C28.4442 23.0571 28.3945 22.9114 28.3448 22.7586L28.1956 22.7586L28.1956 22.6057L28.3448 22.6057L28.3448 22.4528C28.8065 22.5693 28.8847 22.6493 28.9273 23.2027L28.4869 23.2027L28.494 23.2099ZM27.1797 22.4601C27.2721 22.096 27.336 22.1324 27.471 21.8631L27.9114 21.8631L27.9114 22.3145C27.6699 22.3654 27.4213 22.4164 27.1797 22.4673L27.1797 22.4601ZM26.8885 22.5401L26.8885 22.2416L27.1797 22.2416L27.1797 22.5401L26.8885 22.5401ZM13.1494 29.7987C12.6877 29.8133 12.4603 29.8133 12.1265 29.9516L12.1265 30.1045C11.6079 30.2792 11.0182 29.8351 10.663 29.806C10.1729 29.857 9.6898 29.9079 9.19962 29.9589C9.1499 29.857 9.10017 29.7623 9.05044 29.6604L8.75918 29.6604L8.75918 29.5075L8.46792 29.5075L8.46792 29.3546C8.26901 29.2163 8.21218 29.2527 7.88539 29.2017C7.61544 31.0073 6.56406 30.534 4.96567 30.403C4.83069 30.1336 4.76676 30.17 4.6744 29.806C4.17713 29.7186 3.758 29.573 3.21099 29.806L3.21099 29.9589L2.91973 29.9589L2.91973 30.1118C2.5006 30.2574 2.18802 30.0098 1.89676 29.9589C1.77599 29.6749 1.68364 29.5657 1.6055 29.209C0.8951 29.078 0.575424 28.7576 0.582527 27.8621C0.937725 27.651 1.19347 27.2797 0.873789 26.8137C0.774334 26.7628 0.681982 26.7118 0.582528 26.6609L0.582528 25.314C0.916413 25.132 1.14374 24.9572 1.6055 24.8626C1.70495 24.3675 1.86124 24.2874 1.89676 23.6613C1.16505 23.5448 0.753024 23.341 0.724608 22.4601C1.0727 22.2635 1.34265 22.016 1.45631 21.5646C1.27872 21.4699 1.2432 21.317 1.16505 21.2661L0.87379 21.2661L0.87379 21.1132L0.582528 21.1132L0.582528 20.9603C0.433345 20.8584 0.291266 20.7637 0.142083 20.6618L0.142083 20.5089C0.0284204 20.3342 0.156289 20.4943 -0.00710204 20.356C0.142081 19.1111 0.284164 17.8588 0.433347 16.6139C0.625154 16.5629 0.824064 16.512 1.01587 16.461L1.01587 15.7111C0.738817 15.5873 0.632255 15.4927 0.284162 15.4126C0.333888 15.0122 0.383619 14.6118 0.433347 14.2113L0.284162 14.2113L0.284162 13.4615C0.383617 13.4105 0.475971 13.3595 0.575426 13.3086L0.575426 12.2602L0.724609 12.2602C0.909311 11.969 0.951935 11.9763 1.01587 11.5103L0.284162 11.5103L0.284162 10.9133C0.674879 10.9643 1.0656 11.0152 1.45632 11.0662C1.54156 10.826 1.58419 10.7095 1.6055 10.3163C1.10112 10.0106 0.880896 9.33348 0.873791 8.51807C1.19347 8.33606 1.67654 7.87011 1.74758 7.46969C1.74758 7.46969 1.61971 7.05471 1.59839 6.8727C0.717505 6.70525 0.816962 6.26842 0.866688 5.22732C1.01587 5.17636 1.15795 5.1254 1.30713 5.07443L1.30713 4.92155L2.3301 4.92155L2.3301 4.32455C2.18092 4.27359 2.03884 4.22263 1.88966 4.17166C1.7902 3.87317 1.69785 3.57467 1.59839 3.27617C0.887999 3.14512 0.568324 2.82479 0.575427 1.92929C0.816962 1.87833 1.0656 1.82737 1.30713 1.77641C1.46342 0.57514 1.50604 0.364006 2.91263 0.429529C3.13285 0.77899 2.99077 0.553297 3.35307 0.728026C4.17003 1.12845 3.9498 0.291203 4.81649 0.575139L4.81649 0.728026C5.00829 0.77899 5.2072 0.829955 5.39901 0.880917L5.39901 1.03381C5.98153 1.21582 6.32963 0.640664 7.00451 0.582421C7.19631 0.931882 7.39522 1.28134 7.58703 1.6308L8.16955 1.6308C8.33294 1.46335 8.16955 1.57256 8.46082 1.47791C8.51054 1.23038 8.56027 0.975562 8.61 0.728026C8.84443 0.640662 8.95809 0.59698 9.34171 0.575139C10.2155 1.57984 10.3789 0.364007 11.2385 0.575139L11.2385 0.728026C11.7713 0.778991 12.3112 0.829955 12.844 0.880918L12.844 1.03381C13.4265 0.93188 14.0161 0.837236 14.5986 0.735309L14.5986 0.582422C16.0123 0.531459 17.426 0.480493 18.8397 0.429531C19.067 0.866356 19.5501 1.40511 20.1539 1.47791C20.4026 0.91732 20.5731 0.961004 20.8856 0.582422C21.3758 0.684347 21.9228 1.09205 22.6403 0.880919L22.6403 0.728027L23.5141 0.728027L23.5141 0.57514L23.9545 0.57514C24.0043 0.473215 24.054 0.378569 24.1037 0.276644C24.4447 -1.12143e-05 24.7999 -0.00729153 25.418 -0.0218522C25.4606 0.174717 25.5956 0.655224 25.7092 0.728028C26.8814 0.880917 28.0464 1.02653 29.2186 1.17942C29.3109 1.43423 29.3607 1.65264 29.3678 2.07491C28.5224 2.54813 28.8918 3.05048 29.0765 3.87317C29.3393 4.0115 29.3038 4.07702 29.659 4.17167C29.7798 4.8633 29.794 5.03803 29.5098 5.67143L29.3606 5.67143L29.3606 6.12282L29.2115 6.12282C29.1688 6.45044 29.7158 6.81445 29.5027 7.32408C29.3535 7.42601 29.2115 7.52066 29.0623 7.62258C29.112 8.47439 29.1617 9.31892 29.2115 10.1707L29.0623 10.1707L29.0623 10.4692L28.9131 10.4692C28.9628 10.9716 29.0126 11.4666 29.0623 11.969L28.9131 11.969L28.7639 12.4204L28.9131 12.4204L28.9131 12.8718C29.0623 12.9737 29.2044 13.0683 29.3535 13.1703C29.4672 13.4688 29.0552 14.0293 29.0623 14.2186L29.2115 14.2186C29.2612 14.5171 29.3109 14.8156 29.3606 15.1141C29.5098 15.216 29.6519 15.3107 29.8011 15.4126L29.8011 15.7111L29.9574 15.7111C30.1563 16.3081 29.5738 16.694 29.5169 17.058C29.5027 17.1308 29.7656 18.1282 29.6661 18.5578L29.5169 18.5578C29.5667 18.9072 29.6164 19.2567 29.6661 19.6061C29.6164 20.2541 29.5667 20.9021 29.5169 21.55C29.112 21.6592 29.1902 21.5937 29.0765 22.0014L28.9273 22.0014L28.9273 22.1543L29.0765 22.1543L29.0765 22.6057C29.1759 22.6566 29.2683 22.7076 29.3678 22.7586C29.5951 23.3191 29.1475 24.171 29.0765 24.5568C28.5437 24.6951 28.4869 25.0155 27.9043 25.1538L27.9043 25.9037C28.4158 26.0202 28.4727 26.1658 28.4869 26.7992C28.6503 26.9666 28.5437 26.7992 28.636 27.0977C28.8279 27.1486 29.0268 27.1996 29.2186 27.2506C29.1688 27.6 29.1191 27.9495 29.0694 28.299C29.1688 28.3499 29.2612 28.4009 29.3606 28.4518C29.4104 28.6994 29.4601 28.9542 29.5098 29.2017C29.3393 29.2964 29.2967 29.4493 29.2186 29.5002L28.9273 29.5002L28.9273 29.6531L28.636 29.6531C28.5863 29.755 28.5366 29.8497 28.4869 29.9516C27.9967 29.9006 27.5136 29.8497 27.0234 29.7987L27.0234 29.9516L26.7322 29.9516C26.6825 30.0535 26.6327 30.1482 26.583 30.2501C26.3273 30.4103 24.7715 30.7452 24.2458 30.5486L24.2458 30.3957C24.054 30.3447 23.8551 30.2938 23.6633 30.2428L23.6633 30.0899L22.6403 30.0899L22.6403 29.937C22.1643 29.7186 21.8802 29.6531 21.1769 29.6385C21.0845 30.1627 21.0206 30.3229 20.4452 30.3884C20.3955 30.2355 20.3457 30.0899 20.296 29.937C19.8342 29.8497 19.6851 29.7914 19.5643 29.34L18.9818 29.34L18.9818 29.4929L18.8326 29.4929L18.8326 30.403C18.4419 30.454 18.0512 30.5049 17.6604 30.5559L17.6604 30.7088C17.355 30.9272 17.3123 30.9636 16.7866 31.0073C16.4954 30.3083 16.2041 29.9443 15.1812 29.9589C14.9822 30.221 15.1456 29.9006 14.8899 30.1118L14.8899 30.2647C14.5347 30.5777 14.421 30.7088 13.7177 30.716C13.5046 30.3447 13.256 30.3011 13.1352 29.8206L13.1494 29.7987ZM20.4594 28.3062C20.3599 27.8548 20.2676 27.4107 20.1681 26.9594L20.7507 26.9594C20.8004 27.2069 20.8501 27.4617 20.8998 27.7092C21.1201 28.2043 21.2977 27.8985 21.3403 28.7576C20.7293 28.6775 20.8643 28.5465 20.4665 28.3062L20.4594 28.3062ZM7.45205 28.3062L7.45205 28.4591L7.30287 28.4591C7.14658 28.1097 7.03292 27.8985 6.72034 27.7092C6.73455 27.3161 6.77717 27.1996 6.86953 26.9594L7.45205 26.9594C7.5373 27.6437 7.87119 27.9058 7.45205 28.3062ZM5.98864 27.4035C6.2799 27.4908 6.11651 27.3816 6.2799 27.5563C5.98864 27.469 6.15203 27.5782 5.98864 27.4035ZM14.4708 25.7581L14.4708 25.6052C14.2079 25.3941 14.1227 25.263 13.7391 25.1538C13.6538 24.8553 13.7604 25.0228 13.5899 24.8553L13.5899 24.7024C13.2489 24.6515 12.9079 24.6005 12.5669 24.5495C12.3964 24.1054 12.2046 23.2682 11.8352 23.0498C11.5439 22.9988 11.2527 22.9478 10.9614 22.8969L10.9614 22.744C10.5636 22.4309 10.3647 21.9577 9.93844 21.6956C9.83898 21.0477 9.74663 20.3997 9.64717 19.7517C9.11438 19.5989 8.57448 19.4532 8.04168 19.3004C7.94223 18.798 7.84987 18.3029 7.75042 17.8006C7.60124 17.6987 7.45916 17.604 7.30997 17.5021L7.30997 16.9051C7.21052 16.8541 7.11817 16.8032 7.01871 16.7522C6.78428 16.3736 6.79849 16.097 6.43619 15.8567C6.48592 15.1214 6.67062 14.903 7.01871 14.5098C7.11817 14.4589 7.21052 14.4079 7.30997 14.357L7.30997 14.0585L7.45916 14.0585C7.69359 13.6216 7.84987 12.8499 7.8996 12.2602C8.70945 12.0636 8.20507 11.9763 8.48213 11.5103C8.61 11.2992 9.38433 11.0662 9.06465 10.4619L8.91547 10.4619C9.05755 9.90135 9.32039 9.79214 9.93844 9.71206L10.3789 9.71206C10.3789 9.71206 10.5423 9.47908 10.6701 9.41356C10.8122 9.07138 10.9259 8.83113 10.9614 8.36518C11.3663 8.25597 11.4942 8.07396 11.8352 7.91379C12.0483 7.30952 11.9844 6.51596 12.4177 6.11553L12.4177 5.96265C12.5811 5.9044 13.0429 6.19562 13.1494 6.26114L13.1494 6.41403L13.5899 6.41403C13.5401 6.1665 13.4904 5.91168 13.4407 5.66415C13.8385 5.54766 13.7746 5.62775 13.8811 5.21276C14.3145 5.11812 14.8046 4.90698 15.0533 4.61577C15.9129 4.63761 15.8702 4.90699 16.3675 5.21276C16.3675 5.75152 16.2894 6.02089 16.2183 6.41403C15.9271 6.46499 15.6358 6.51596 15.3445 6.56692L15.3445 6.71981L15.1954 6.71981C15.3161 7.67354 15.7282 7.39688 15.7779 8.36518C15.2806 8.47439 14.762 8.68552 14.4637 8.21229C14.0445 8.21229 13.8385 8.26325 13.5899 8.36518C13.4904 9.01314 13.3981 9.66109 13.2986 10.3091C12.78 10.3091 12.3112 10.3309 11.9844 10.4619L11.9844 11.0589L12.1336 11.0589C12.098 11.8671 11.9346 11.8379 11.2598 11.9544L10.8193 11.9544C10.7199 12.1073 10.6275 12.2529 10.5281 12.4058C9.99527 12.6024 9.45537 12.8062 8.92257 13.0028L8.92257 13.5998C9.07175 13.7017 9.21383 13.7964 9.36302 13.8983L9.5122 14.3497C9.80346 14.2987 10.0947 14.2478 10.386 14.1968L10.386 14.0439L12.8724 14.0439L12.8724 14.1968C13.6041 14.1458 14.3358 14.0949 15.0675 14.0439C15.1598 14.4079 15.2238 14.3715 15.3588 14.6409L16.0905 14.6409L16.0905 14.488L16.2396 14.488C16.538 14.5244 17.0211 14.9831 17.5539 14.7865L17.5539 14.6336L17.8451 14.6336C17.8949 14.5317 17.9446 14.437 17.9943 14.3351L18.2856 14.3351L18.2856 14.1822C18.726 14.2842 19.1594 14.3788 19.5998 14.4807C19.6922 14.8447 19.7561 14.8083 19.8911 15.0777C20.4949 14.9904 20.4026 14.7647 20.914 14.6263L20.914 14.4734C21.1556 14.5244 21.4042 14.5754 21.6458 14.6263L21.6458 14.7792L22.5195 14.7792L22.5195 14.9321L22.96 14.9321L22.96 15.085C23.1092 15.136 23.2512 15.1869 23.4004 15.2379C23.4999 15.4854 23.5922 15.7402 23.6917 15.9878L23.5425 15.9878L23.5425 16.2863C22.3988 16.2571 22.3064 16.4173 21.937 17.1818L21.3545 17.1818C21.2692 16.8833 21.3758 17.0507 21.2053 16.8833C20.708 16.461 20.1752 17.1599 19.4506 16.8833C19.1878 16.7813 18.861 16.3882 18.7189 16.1334C18.1009 16.1479 17.9588 16.2062 17.8451 16.7304C17.4544 16.7813 17.0637 16.8323 16.673 16.8833C16.1189 16.7013 15.6429 15.7475 14.9183 16.5848C14.6342 16.5484 14.4708 16.5993 14.3358 16.4319L14.3358 16.1334C14.2505 16.0169 14.144 16.046 14.0445 15.8349C13.121 15.813 12.6095 15.8494 12.2898 16.4319C12.0412 16.4464 11.7428 16.5848 11.7073 16.5848L11.7073 16.4319C11.2669 16.33 10.8335 16.2353 10.3931 16.1334C10.3434 15.8858 10.2936 15.631 10.2439 15.3835L9.66138 15.3835C9.41985 15.5291 9.14279 16.8177 9.37012 17.1818C10.1516 17.7788 10.9259 18.383 11.7073 18.98L11.7073 19.4314L11.8565 19.4314L11.8565 19.8828L12.0057 19.8828C12.0554 20.123 11.8636 20.1594 11.8565 20.1813C11.9062 20.5307 11.956 20.8802 12.0057 21.2297C12.1904 21.3098 12.368 21.2806 12.5882 21.3826L12.5882 21.5354L13.0287 21.5354C13.0784 21.6374 13.1281 21.732 13.1778 21.8339C13.3696 21.8849 13.5686 21.9359 13.7604 21.9868C13.8882 22.6275 14.485 23.1954 15.0746 23.3337C15.1314 24.0253 15.3374 24.4185 15.8063 24.6806C15.8063 25.2193 15.7282 25.4887 15.6571 25.8819C15.238 25.8819 14.7407 25.8964 14.485 25.729L14.4708 25.7581ZM7.8925 23.9598L7.8925 24.1127L8.04168 24.1127C7.95643 24.4622 7.83567 24.6733 7.60123 24.8626L7.60123 25.0155L7.16079 25.0155L7.16079 24.2656C7.55861 24.1637 7.49468 24.0617 7.8925 23.9671L7.8925 23.9598ZM4.24106 21.5646L4.24106 21.2661L4.53233 21.2661L4.53233 21.5646L4.24106 21.5646ZM2.77765 21.2661L2.77765 20.8147L3.21809 20.8147L3.21809 21.2661L2.77765 21.2661ZM23.6775 20.2177L23.6775 20.3706L23.8267 20.3706C23.7698 20.8511 23.6917 20.8074 23.5354 21.1205L23.2441 21.1205C23.1589 20.5672 23.1589 20.7783 23.2441 20.225L23.6846 20.225L23.6775 20.2177ZM23.5283 17.8224L23.5283 17.6695L23.237 17.6695L23.237 17.3711C23.4786 17.3201 23.7272 17.2691 23.9687 17.2182L23.9687 17.6695C23.8196 17.7205 23.6775 17.7715 23.5283 17.8224ZM3.80062 17.0726L3.80062 16.9197C4.14871 17.0143 4.07768 16.9633 4.24107 17.2182C4.09188 17.1672 3.9498 17.1162 3.80062 17.0653L3.80062 17.0726ZM17.0992 11.2337L17.0992 10.9352L17.5397 10.9352L17.5397 11.2337L17.0992 11.2337ZM4.82359 9.4354L4.82359 8.98401L5.26404 8.98401L5.26404 9.4354L4.82359 9.4354ZM1.89676 8.53263L1.89676 7.78275C2.08857 7.73178 2.28748 7.68082 2.47928 7.62986C2.60005 7.90652 2.70661 8.023 2.77055 8.37974C3.06181 8.4671 2.89842 8.3579 3.06181 8.53263L3.21099 8.53263L3.21099 8.98401C2.49349 8.93305 2.48639 8.66368 1.89676 8.53263ZM6.287 6.58876C6.287 7.12751 6.36515 7.39689 6.43619 7.79003L6.287 7.79003L6.287 7.94292L5.70448 7.94292L5.70448 7.64442L5.5553 7.64442L5.5553 7.49153L5.70448 7.49153L5.70448 6.29026C6.05968 6.38491 6.02416 6.45043 6.287 6.58876ZM18.4135 5.99177L18.4135 6.58876C18.1719 6.78533 17.9233 6.98918 17.6817 7.18576C17.632 7.33864 17.5823 7.48425 17.5326 7.63714L17.2413 7.63714C17.2484 6.79989 17.4189 6.45043 17.5326 5.83888C17.9517 5.83888 18.1577 5.88984 18.4064 5.99177L18.4135 5.99177ZM3.65144 7.18576L3.50225 7.18576L3.50225 6.73437L3.9427 6.73437L3.9427 7.33136C3.65144 7.244 3.81483 7.3532 3.65144 7.17847L3.65144 7.18576ZM2.62847 6.88726L2.62847 6.58876L3.21099 6.58876L3.21099 6.88726L2.62847 6.88726ZM20.7507 3.29073L20.7507 3.44362L20.8998 3.44362L20.8998 4.19351L20.7507 4.19351L20.7507 4.34639L19.8769 4.34639C19.8769 4.34639 19.614 4.70313 19.4364 4.79778C19.3867 4.64489 19.337 4.49928 19.2872 4.34639L19.1381 4.34639C19.1878 4.14982 19.2375 3.94597 19.2872 3.7494C20.2889 3.7494 20.1823 3.63292 20.7507 3.29802L20.7507 3.29073Z" fill="#BEBBBB"/>
                    </svg>
                </button>
                <button class="c-pop c-slider--btn c-slider--btn--right" type="button" aria-label="次の特売商品へ">
                    <svg class="c-slider--btn__img" width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.54134 7.68812C6.59107 7.52537 6.64079 7.37037 6.69052 7.20762L6.54134 7.20762L6.54134 6.88986C6.39216 6.83561 6.25008 6.78136 6.10089 6.72711C5.88778 6.5256 5.84515 6.1691 5.80963 5.77384C5.66045 5.71959 5.51837 5.66534 5.36919 5.61109C5.19159 6.0606 4.98557 6.31635 4.92874 6.88986C5.63203 7.07586 5.77411 7.59512 6.53423 7.68812L6.54134 7.68812ZM3.62161 5.61109L3.62161 5.77384L2.74783 5.77384L2.74783 5.9366L2.59864 5.9366C2.63416 6.31635 2.66258 6.33185 2.74783 6.57211L3.33035 6.57211L3.33035 6.40935C3.61451 6.20785 3.6074 6.1536 4.06206 6.0916C4.11179 5.82809 4.16151 5.55684 4.21124 5.29334L4.36042 5.29334C4.48119 4.75858 4.72983 4.59583 5.23421 4.49508C5.24842 4.21607 5.38339 3.91382 5.38339 3.85957L5.23421 3.85957L5.23421 3.37906L4.5025 3.37906L4.5025 3.54181C4.26097 3.73556 3.62161 4.53383 3.47953 4.82058L2.60575 4.82058C2.73362 5.39409 3.02488 5.57234 3.62872 5.61884L3.62161 5.61109ZM2.1582 3.70456L2.1582 3.54181L2.00902 3.54181L2.00902 3.70456L1.85983 3.70456L1.85983 3.86732C2.00902 3.92157 2.15109 3.97582 2.30028 4.03007C2.21503 3.71231 2.32159 3.89056 2.15109 3.71231L2.1582 3.70456ZM3.69265 29.7295L3.69265 29.8922L3.84184 29.8922C3.89157 30.055 3.94129 30.21 3.99102 30.3727C4.62327 30.334 4.4883 30.272 4.86481 30.055L4.86481 29.7372L3.69265 29.7372L3.69265 29.7295ZM6.02986 29.8922L6.76156 29.8922C6.81129 29.7295 6.86102 29.5744 6.91075 29.4117L6.76156 29.4117C6.71184 29.2489 6.66211 29.0939 6.61238 28.9312L5.88067 28.9312C5.88067 29.3884 5.9304 29.6132 6.02986 29.8845L6.02986 29.8922ZM4.86481 28.4584C4.91453 28.2957 4.96426 28.1407 5.01399 27.9779C5.11345 27.9237 5.2058 27.8694 5.30525 27.8152L5.30525 27.6524L5.88778 27.6524L5.88778 27.4897L6.03696 27.4897C6.13641 27.1719 6.22877 26.8542 6.32822 26.5364C6.07248 26.4124 5.77411 26.2806 5.59651 26.0559C4.8577 26.1024 4.75114 26.4047 4.28228 26.6914C4.29649 27.5207 4.46699 27.8229 4.57355 28.4429L4.86481 28.4429L4.86481 28.4584ZM5.44733 23.9944L5.29815 23.9944L5.29815 23.8316C4.92164 23.8549 4.78666 23.8471 4.56644 23.9944L4.56644 24.1571L4.126 24.1571C4.126 24.1571 3.97681 24.3896 3.83473 24.4749C3.87025 24.9244 3.89156 25.0561 4.126 25.2731C4.28939 25.4514 4.126 25.3429 4.41726 25.4359C4.51671 25.1181 4.60906 24.8004 4.70852 24.4826L5.44023 24.4826L5.44023 24.0021L5.44733 23.9944ZM1.50464 24.7074C1.55436 24.5446 1.60409 24.3896 1.65382 24.2269L1.803 24.2269L1.803 24.0641L1.65382 24.0641L1.65382 23.9014C1.19206 24.0254 1.11392 24.1106 1.07129 24.6996L1.51174 24.6996L1.50464 24.7074ZM2.81887 23.9091C2.72652 23.5216 2.66258 23.5604 2.52761 23.2736L2.08716 23.2736L2.08716 23.7541C2.32869 23.8084 2.57733 23.8626 2.81887 23.9169L2.81887 23.9091ZM3.11013 23.9944L3.11013 23.6766L2.81887 23.6766L2.81887 23.9944L3.11013 23.9944ZM16.8492 31.7212C17.3109 31.7367 17.5383 31.7367 17.8722 31.884L17.8722 32.0467C18.3907 32.2327 18.9804 31.76 19.3356 31.729C19.8257 31.7832 20.3088 31.8375 20.799 31.8917C20.8487 31.7832 20.8984 31.6825 20.9482 31.574L21.2394 31.574L21.2394 31.4112L21.5307 31.4112L21.5307 31.2485C21.7296 31.1012 21.7864 31.14 22.1132 31.0857C22.3832 33.0077 23.4345 32.504 25.0329 32.3645C25.1679 32.0777 25.2318 32.1165 25.3242 31.729C25.8215 31.636 26.2406 31.481 26.7876 31.729L26.7876 31.8917L27.0789 31.8917L27.0789 32.0545C27.498 32.2095 27.8106 31.946 28.1018 31.8917C28.2226 31.5895 28.315 31.4732 28.3931 31.0935C29.1035 30.954 29.4232 30.613 29.4161 29.6597C29.0609 29.4349 28.8051 29.0397 29.1248 28.5437C29.2243 28.4894 29.3166 28.4352 29.4161 28.3809L29.4161 26.9472C29.0822 26.7534 28.8549 26.5674 28.3931 26.4666C28.2937 25.9396 28.1374 25.8544 28.1018 25.1879C28.8336 25.0639 29.2456 24.8469 29.274 23.9091C28.9259 23.6999 28.656 23.4364 28.5423 22.9558C28.7199 22.8551 28.7554 22.6923 28.8336 22.6381L29.1248 22.6381L29.1248 22.4753L29.4161 22.4753L29.4161 22.3126C29.5653 22.2041 29.7073 22.1033 29.8565 21.9948L29.8565 21.8321C29.9702 21.6461 29.8423 21.8166 30.0057 21.6693C29.8565 20.3441 29.7144 19.011 29.5653 17.6858C29.3735 17.6315 29.1745 17.5773 28.9827 17.523L28.9827 16.7248C29.2598 16.593 29.3663 16.4923 29.7144 16.407C29.6647 15.9807 29.615 15.5545 29.5653 15.1282L29.7144 15.1282L29.7144 14.33C29.615 14.2757 29.5226 14.2215 29.4232 14.1672L29.4232 13.0512L29.274 13.0512C29.0893 12.7412 29.0467 12.7489 28.9827 12.2529L29.7144 12.2529L29.7144 11.6174C29.3237 11.6717 28.933 11.7259 28.5423 11.7802C28.457 11.5244 28.4144 11.4004 28.3931 10.9819C28.8975 10.6564 29.1177 9.93565 29.1248 9.06764C28.8051 8.87389 28.3221 8.37788 28.251 7.95162C28.251 7.95162 28.3789 7.50987 28.4002 7.31611C29.2811 7.13786 29.1816 6.67285 29.1319 5.56459C28.9827 5.51034 28.8407 5.45609 28.6915 5.40183L28.6915 5.23908L27.6685 5.23908L27.6685 4.60357C27.8177 4.54932 27.9598 4.49507 28.1089 4.44082C28.2084 4.12307 28.3008 3.80531 28.4002 3.48756C29.1106 3.34805 29.4303 3.00705 29.4232 2.05379C29.1816 1.99953 28.933 1.94528 28.6915 1.89103C28.5352 0.612263 28.4926 0.387509 27.086 0.45726C26.8658 0.829266 27.0078 0.589012 26.6455 0.775017C25.8286 1.20127 26.0488 0.310009 25.1821 0.612263L25.1821 0.775017C24.9903 0.829266 24.7914 0.883519 24.5996 0.937771L24.5996 1.10052C24.0171 1.29428 23.669 0.682015 22.9941 0.620015C22.8023 0.99202 22.6034 1.36403 22.4116 1.73603L21.829 1.73603C21.6657 1.55778 21.829 1.67403 21.5378 1.57328C21.4881 1.30978 21.4383 1.03852 21.3886 0.775018C21.1542 0.682015 21.0405 0.635514 20.6569 0.612264C19.7831 1.68178 19.6197 0.387509 18.7601 0.612264L18.7601 0.775018C18.2273 0.829267 17.6874 0.883519 17.1546 0.937772L17.1546 1.10052C16.5721 0.992021 15.9825 0.891271 15.4 0.78277L15.4 0.620015C13.9863 0.565767 12.5726 0.511514 11.1589 0.457262C10.9316 0.92227 10.4485 1.49578 9.84468 1.57328C9.59604 0.976522 9.42554 1.02302 9.11297 0.620016C8.6228 0.728517 8.07579 1.16253 7.35829 0.937773L7.35829 0.775019L6.48451 0.775019L6.48451 0.612265L6.04406 0.612265C5.99433 0.503763 5.94461 0.403013 5.89488 0.294512C5.55389 5.95184e-06 5.19869 -0.00774167 4.58065 -0.0232445C4.53802 0.186011 4.40305 0.697516 4.28938 0.775019C3.11723 0.937769 1.95218 1.09277 0.780031 1.25553C0.687679 1.52678 0.637952 1.75928 0.630848 2.20879C1.47622 2.71255 1.10681 3.24731 0.92211 4.12307C0.659264 4.27032 0.694784 4.34007 0.339586 4.44083C0.218819 5.17709 0.204611 5.36309 0.488769 6.03735L0.637952 6.03735L0.637952 6.51786L0.787135 6.51786C0.829759 6.86661 0.282754 7.25411 0.495873 7.79662C0.645056 7.90513 0.787135 8.00588 0.936318 8.11438C0.886591 9.02114 0.836863 9.92015 0.787136 10.8269L0.936319 10.8269L0.936319 11.1447L1.0855 11.1447C1.03577 11.6794 0.986046 12.2064 0.936319 12.7412L1.0855 12.7412L1.23468 13.2217L1.0855 13.2217L1.0855 13.7022C0.936319 13.8107 0.79424 13.9115 0.645057 14.02C0.531394 14.3377 0.943423 14.9345 0.936319 15.136L0.787136 15.136C0.737408 15.4537 0.687681 15.7715 0.637953 16.0892C0.48877 16.1977 0.346691 16.2985 0.197508 16.407L0.197508 16.7248L0.0412207 16.7248C-0.15769 17.3603 0.424834 17.771 0.481666 18.1585C0.495874 18.236 0.233028 19.2978 0.332483 19.7551L0.481666 19.7551C0.431939 20.1271 0.382211 20.4991 0.332483 20.8711C0.382211 21.5608 0.431939 22.2506 0.481667 22.9403C0.886592 23.0566 0.808448 22.9869 0.922112 23.4209L1.07129 23.4209L1.07129 23.5836L0.922112 23.5836L0.922112 24.0641C0.822656 24.1184 0.730305 24.1726 0.63085 24.2269C0.403523 24.8236 0.851072 25.7304 0.922112 26.1411C1.45491 26.2884 1.51174 26.6294 2.09426 26.7767L2.09426 27.5749C1.58278 27.6989 1.52595 27.8539 1.51174 28.5282C1.34835 28.7064 1.45491 28.5282 1.36256 28.8459C1.17075 28.9002 0.97184 28.9544 0.780033 29.0087C0.829761 29.3807 0.879489 29.7527 0.929216 30.1247C0.829761 30.179 0.73741 30.2332 0.637954 30.2875C0.588227 30.551 0.538499 30.8222 0.488771 31.0857C0.659266 31.1865 0.70189 31.3492 0.780033 31.4035L1.0713 31.4035L1.0713 31.5662L1.36256 31.5662C1.41229 31.6747 1.46201 31.7755 1.51174 31.884C2.00191 31.8297 2.48498 31.7755 2.97516 31.7212L2.97516 31.884L3.26642 31.884C3.31615 31.9925 3.36587 32.0932 3.4156 32.2017C3.67134 32.3722 5.22711 32.7287 5.7528 32.5195L5.7528 32.3567C5.94461 32.3025 6.14352 32.2482 6.33533 32.194L6.33533 32.0312L7.3583 32.0312L7.3583 31.8685C7.83426 31.636 8.11842 31.5662 8.82171 31.5507C8.91406 32.1087 8.978 32.2792 9.55342 32.349C9.60315 32.1862 9.65287 32.0312 9.7026 31.8685C10.1644 31.7755 10.3135 31.7135 10.4343 31.233L11.0168 31.233L11.0168 31.3957L11.166 31.3957L11.166 32.3645C11.5567 32.4187 11.9475 32.473 12.3382 32.5272L12.3382 32.69C12.6436 32.9225 12.6863 32.9612 13.212 33.0077C13.5032 32.2637 13.7945 31.8762 14.8174 31.8917C15.0164 32.1707 14.853 31.8297 15.1087 32.0545L15.1087 32.2172C15.4639 32.5505 15.5776 32.69 16.2809 32.6977C16.494 32.3025 16.7426 32.256 16.8634 31.7445L16.8492 31.7212ZM9.53921 30.1325C9.63866 29.6519 9.73102 29.1792 9.83047 28.6987L9.24795 28.6987C9.19822 28.9622 9.14849 29.2334 9.09877 29.4969C8.87854 30.024 8.70094 29.6985 8.65832 30.613C9.26926 30.5277 9.13428 30.3882 9.53211 30.1325L9.53921 30.1325ZM22.5466 30.1325L22.5466 30.2952L22.6957 30.2952C22.852 29.9232 22.9657 29.6984 23.2783 29.4969C23.2641 29.0784 23.2214 28.9544 23.1291 28.6987L22.5466 28.6987C22.4613 29.4272 22.1274 29.7062 22.5466 30.1325ZM24.01 29.1714C23.7187 29.2644 23.8821 29.1482 23.7187 29.3342C24.01 29.2412 23.8466 29.3574 24.01 29.1714ZM15.5278 27.4199L15.5278 27.2572C15.7907 27.0324 15.8759 26.8929 16.2596 26.7767C16.3448 26.4589 16.2382 26.6372 16.4087 26.4589L16.4087 26.2961C16.7497 26.2419 17.0907 26.1876 17.4317 26.1334C17.6022 25.6606 17.794 24.7694 18.1634 24.5369C18.4547 24.4826 18.7459 24.4284 19.0372 24.3741L19.0372 24.2114C19.435 23.8781 19.6339 23.3744 20.0602 23.0954C20.1596 22.4056 20.252 21.7158 20.3514 21.0261C20.8842 20.8633 21.4241 20.7083 21.9569 20.5456C22.0564 20.0108 22.1487 19.4838 22.2482 18.949C22.3974 18.8405 22.5394 18.7398 22.6886 18.6313L22.6886 17.9958C22.7881 17.9415 22.8804 17.8873 22.9799 17.833C23.2143 17.43 23.2001 17.1355 23.5624 16.8798C23.5127 16.097 23.328 15.8645 22.9799 15.446C22.8804 15.3917 22.7881 15.3375 22.6886 15.2832L22.6886 14.9655L22.5394 14.9655C22.305 14.5005 22.1487 13.679 22.099 13.0512C21.2892 12.8419 21.7935 12.7489 21.5165 12.2529C21.3886 12.0282 20.6143 11.7802 20.934 11.1369L21.0831 11.1369C20.9411 10.5402 20.6782 10.4239 20.0602 10.3387L19.6197 10.3387C19.6197 10.3387 19.4563 10.0907 19.3285 10.0209C19.1864 9.65665 19.0727 9.4009 19.0372 8.90489C18.6323 8.78864 18.5044 8.59488 18.1634 8.42438C17.9503 7.78112 18.0142 6.93636 17.5809 6.5101L17.5809 6.34735C17.4175 6.28535 16.9557 6.59536 16.8492 6.66511L16.8492 6.82786L16.4087 6.82786C16.4585 6.56435 16.5082 6.2931 16.5579 6.0296C16.1601 5.9056 16.224 5.99085 16.1175 5.54909C15.6841 5.44834 15.194 5.22358 14.9453 4.91358C14.0857 4.93683 14.1284 5.22359 13.6311 5.54909C13.6311 6.1226 13.7092 6.40935 13.7803 6.82786C14.0715 6.88211 14.3628 6.93636 14.6541 6.99061L14.6541 7.15336L14.8032 7.15336C14.6825 8.16863 14.2704 7.87412 14.2207 8.90489C14.718 9.02114 15.2366 9.24589 15.5349 8.74214C15.9541 8.74214 16.1601 8.79639 16.4087 8.90489C16.5082 9.59465 16.6005 10.2844 16.7 10.9742C17.2186 10.9742 17.6874 10.9974 18.0142 11.1369L18.0142 11.7724L17.865 11.7724C17.9006 12.6327 18.064 12.6017 18.7388 12.7257L19.1793 12.7257C19.2787 12.8884 19.3711 13.0434 19.4705 13.2062C20.0033 13.4155 20.5432 13.6325 21.076 13.8417L21.076 14.4772C20.9268 14.5857 20.7848 14.6865 20.6356 14.795L20.4864 15.2755C20.1951 15.2212 19.9039 15.167 19.6126 15.1127L19.6126 14.95L17.1262 14.95L17.1262 15.1127C16.3945 15.0585 15.6628 15.0042 14.9311 14.95C14.8388 15.3375 14.7748 15.2987 14.6398 15.5855L13.9081 15.5855L13.9081 15.4227L13.759 15.4227C13.4606 15.4615 12.9775 15.9497 12.4447 15.7405L12.4447 15.5777L12.1535 15.5777C12.1037 15.4692 12.054 15.3685 12.0043 15.26L11.713 15.26L11.713 15.0972C11.2726 15.2057 10.8392 15.3065 10.3988 15.415C10.3064 15.8025 10.2425 15.7637 10.1075 16.0505C9.50369 15.9575 9.59604 15.7172 9.08456 15.57L9.08456 15.4072C8.84302 15.4615 8.59438 15.5157 8.35285 15.57L8.35285 15.7327L7.47906 15.7327L7.47906 15.8955L7.03862 15.8955L7.03862 16.0582C6.88943 16.1125 6.74735 16.1667 6.59817 16.221C6.49872 16.4845 6.40636 16.7558 6.30691 17.0193L6.45609 17.0193L6.45609 17.337C7.59983 17.306 7.69218 17.4765 8.06159 18.2903L8.64411 18.2903C8.72936 17.9725 8.6228 18.1508 8.79329 17.9725C9.29057 17.523 9.82337 18.267 10.548 17.9725C10.8108 17.864 11.1376 17.4455 11.2797 17.1743C11.8977 17.1898 12.0398 17.2518 12.1535 17.8098C12.5442 17.864 12.9349 17.9183 13.3256 17.9725C13.8797 17.7788 14.3557 16.7635 15.0803 17.6548C15.3645 17.616 15.5278 17.6703 15.6628 17.492L15.6628 17.1743C15.7481 17.0503 15.8546 17.0813 15.9541 16.8565C16.8776 16.8333 17.3891 16.872 17.7088 17.492C17.9574 17.5075 18.2558 17.6548 18.2913 17.6548L18.2913 17.492C18.7317 17.3835 19.1651 17.2828 19.6055 17.1743C19.6552 16.9108 19.705 16.6395 19.7547 16.376L20.3372 16.376C20.5788 16.531 20.8558 17.9028 20.6285 18.2903C19.847 18.9258 19.0727 19.569 18.2913 20.2046L18.2913 20.6851L18.1421 20.6851L18.1421 21.1656L17.9929 21.1656C17.9432 21.4213 18.135 21.4601 18.1421 21.4833C18.0924 21.8553 18.0426 22.2273 17.9929 22.5993C17.8082 22.6846 17.6306 22.6536 17.4104 22.7621L17.4104 22.9248L16.9699 22.9248C16.9202 23.0333 16.8705 23.1341 16.8208 23.2426C16.629 23.2969 16.43 23.3511 16.2382 23.4054C16.1104 24.0874 15.5136 24.6919 14.924 24.8391C14.8672 25.5754 14.6612 25.9939 14.1923 26.2729C14.1923 26.8464 14.2704 27.1332 14.3415 27.5517C14.7606 27.5517 15.2579 27.5672 15.5136 27.3889L15.5278 27.4199ZM22.1061 25.5056L22.1061 25.6684L21.9569 25.6684C22.0422 26.0404 22.1629 26.2651 22.3974 26.4667L22.3974 26.6294L22.8378 26.6294L22.8378 25.8311C22.44 25.7226 22.5039 25.6141 22.1061 25.5134L22.1061 25.5056ZM25.7575 22.9558L25.7575 22.6381L25.4663 22.6381L25.4663 22.9558L25.7575 22.9558ZM27.221 22.6381L27.221 22.1576L26.7805 22.1576L26.7805 22.6381L27.221 22.6381ZM6.32112 21.5221L6.32112 21.6848L6.17193 21.6848C6.22877 22.1963 6.30691 22.1498 6.4632 22.4831L6.75446 22.4831C6.83971 21.8941 6.83971 22.1188 6.75446 21.5298L6.31401 21.5298L6.32112 21.5221ZM6.4703 18.9723L6.4703 18.8095L6.76156 18.8095L6.76156 18.4918C6.52003 18.4375 6.27139 18.3833 6.02985 18.329L6.02985 18.8095C6.17904 18.8638 6.32112 18.918 6.4703 18.9723ZM26.198 18.174L26.198 18.0113C25.8499 18.112 25.9209 18.0578 25.7575 18.329C25.9067 18.2748 26.0488 18.2205 26.198 18.1663L26.198 18.174ZM12.8994 11.9584L12.8994 11.6407L12.4589 11.6407L12.4589 11.9584L12.8994 11.9584ZM25.175 10.0442L25.175 9.56365L24.7346 9.56365L24.7346 10.0442L25.175 10.0442ZM28.1018 9.08314L28.1018 8.28488C27.91 8.23063 27.7111 8.17638 27.5193 8.12213C27.3986 8.41663 27.292 8.54063 27.2281 8.92039C26.9368 9.01339 27.1002 8.89714 26.9368 9.08314L26.7876 9.08314L26.7876 9.56365C27.5051 9.5094 27.5122 9.22264 28.1018 9.08314ZM23.7116 7.01386C23.7116 7.58737 23.6335 7.87412 23.5624 8.29263L23.7116 8.29263L23.7116 8.45538L24.2941 8.45538L24.2941 8.13763L24.4433 8.13763L24.4433 7.97487L24.2941 7.97487L24.2941 6.69611C23.9389 6.79686 23.9744 6.86661 23.7116 7.01386ZM11.5851 6.37835L11.5851 7.01386C11.8267 7.22312 12.0753 7.44012 12.3169 7.64937C12.3666 7.81212 12.4163 7.96713 12.466 8.12988L12.7573 8.12988C12.7502 7.23861 12.5797 6.86661 12.466 6.2156C12.0469 6.2156 11.8409 6.26985 11.5923 6.37835L11.5851 6.37835ZM26.3472 7.64937L26.4963 7.64937L26.4963 7.16886L26.0559 7.16886L26.0559 7.80437C26.3472 7.71137 26.1838 7.82762 26.3472 7.64162L26.3472 7.64937ZM27.3701 7.33162L27.3701 7.01386L26.7876 7.01386L26.7876 7.33162L27.3701 7.33162ZM9.24795 3.50306L9.24795 3.66581L9.09876 3.66581L9.09876 4.46407L9.24795 4.46407L9.24795 4.62683L10.1217 4.62683C10.1217 4.62683 10.3846 5.00658 10.5622 5.10733C10.6119 4.94458 10.6616 4.78958 10.7114 4.62683L10.8605 4.62683C10.8108 4.41757 10.7611 4.20057 10.7114 3.99132C9.7097 3.99132 9.81626 3.86732 9.24795 3.51081L9.24795 3.50306Z" fill="#BEBBBB"/>
                    </svg>
                </button>
            </div>
            <!-- WPコンテンツ end -->
        </div>
        <!-- 今日のオススメ end -->

        <!-- 今日のオススメ ページネーション start -->
        <div class="p-front-page__special-offers__pagination" aria-label="今日のオススメのページネーション">
            <?php foreach ($front_flyer_items as $index => $flyer) : ?>
                <?php $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?>
                <button class="p-front-page__special-offers__pagination__dot special-offers__pagination__dot--<?php echo esc_attr($number); ?><?php echo $index === 0 ? ' is-active' : ''; ?>"
                        type="button"
                        aria-label="特売商品<?php echo esc_attr($index + 1); ?>を表示"></button>
            <?php endforeach; ?>
            <?php if (false) : ?>
            <button class="p-front-page__special-offers__pagination__dot special-offers__pagination__dot--01 is-active" type="button" aria-label="特売商品1を表示"></button>
            <button class="p-front-page__special-offers__pagination__dot special-offers__pagination__dot--02" type="button" aria-label="特売商品2を表示"></button>
            <button class="p-front-page__special-offers__pagination__dot special-offers__pagination__dot--03" type="button" aria-label="特売商品3を表示"></button>
            <button class="p-front-page__special-offers__pagination__dot special-offers__pagination__dot--04" type="button" aria-label="特売商品4を表示"></button>
            <button class="p-front-page__special-offers__pagination__dot special-offers__pagination__dot--05" type="button" aria-label="特売商品5を表示"></button>
            <?php endif; ?>
        </div>
        <!-- 今日のオススメ ページネーション end -->
    </div>
    <!-- ヒーロー end -->

    <!-- 店舗情報 start -->
    <div class="c-bg p-front-page__bg--shop no-image">
        <section class="p-front-page__shop">
            <!-- 背景画像(position配置) start -->
            <div class="c-bg__positioned p-front-page__bg--shop--01"></div>
            <div class="c-bg__positioned p-front-page__bg--shop--02"></div>
            <div class="c-bg__positioned p-front-page__bg--shop--03"></div>
            <div class="c-bg__positioned p-front-page__bg--shop--04"></div>
            <div class="c-bg__positioned p-front-page__bg--shop--05"></div>
            <div class="c-bg__positioned p-front-page__bg--shop--06"></div>
            <!-- 背景画像(position配置) end -->

            <!-- タイトル start -->
            <h1 class="c-section__title">チラシ・店舗情報</h1>
            <p class="c-section__title--sub">Flyers and shop information</p>
            <!-- タイトル end -->

            <div class="p-front-page__shop__group js-handle--tab-shop-group">
                <button class="c-btn p-front-page__shop__group__tab is-active" type="button" data-shop-filter="all">ALL</button>
                <?php foreach ($front_shop_prefectures as $front_shop_prefecture): ?>
                    <button
                        class="c-btn p-front-page__shop__group__tab"
                        type="button"
                        data-shop-filter="<?php echo esc_attr(sanitize_html_class($front_shop_prefecture->slug)); ?>"
                    ><?php echo esc_html(preg_replace('/[都道府県]$/u', '', $front_shop_prefecture->name)); ?></button>
                <?php endforeach; ?>
            </div>
            <div class="js-shop-content">
                <ul class="c-shop-card-list js-handle--tab-shop-list">
                    <?php foreach ($front_shop_items as $front_shop_item): ?>
                        <?php $render_front_shop_item($front_shop_item); ?>
                    <?php endforeach; ?>
                    <?php if (false): ?>
                    <li class="c-shop-card is-chiba is-active">
                        <p class="c-shop-card__name">行徳店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒272-0132<br>千葉県市川市湊新田<br>1-6-8
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:00</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-chiba is-active">
                        <p class="c-shop-card__name">西船橋店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-chiba">
                        <p class="c-shop-card__name">西原店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-chiba">
                        <p class="c-shop-card__name">花野井店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-chiba">
                        <p class="c-shop-card__name">しいの木台店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-chiba">
                        <p class="c-shop-card__name">八潮店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-chiba">
                        <p class="c-shop-card__name">青葉台店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-chiba">
                        <p class="c-shop-card__name">松戸店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-tokyo">
                        <p class="c-shop-card__name">西新井店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-saitama">
                        <p class="c-shop-card__name">三郷店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>
                    <li class="c-shop-card is-saitama">
                        <p class="c-shop-card__name">八潮店</p>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__address" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 8.75C6.0375 8.75 5.25 7.9625 5.25 7C5.25 6.0375 6.0375 5.25 7 5.25C7.9625 5.25 8.75 6.0375 8.75 7C8.75 7.9625 7.9625 8.75 7 8.75ZM12.25 7.175C12.25 3.99875 9.93125 1.75 7 1.75C4.06875 1.75 1.75 3.99875 1.75 7.175C1.75 9.2225 3.45625 11.935 7 15.1725C10.5438 11.935 12.25 9.2225 12.25 7.175ZM7 0C10.675 0 14 2.8175 14 7.175C14 10.08 11.6637 13.5187 7 17.5C2.33625 13.5187 0 10.08 0 7.175C0 2.8175 3.325 0 7 0Z" fill="black"/>
                            </svg>
                            <address>
                                〒273-0025<br>千葉県船橋市印内町 579-1
                                <a class="c-shop-card__address__link" href="">マップを見る</a>
                            </address>
                        </div>
                        <div class="c-shop-card__wrapper">
                            <svg class="c-shop-card__time" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM7.5 13.5C4.1925 13.5 1.5 10.8075 1.5 7.5C1.5 4.1925 4.1925 1.5 7.5 1.5C10.8075 1.5 13.5 4.1925 13.5 7.5C13.5 10.8075 10.8075 13.5 7.5 13.5ZM7.875 3.75H6.75V8.25L10.65 10.65L11.25 9.675L7.875 7.65V3.75Z" fill="black"/>
                            </svg>
                            <time>9:30～22:30<br>(土日 22:00閉店)</time>
                        </div>
                        <a class="c-shop-card__link" href="">
                            <button class="c-btn c-btn--common--blue">チラシ・店舗情報</button>
                        </a>
                    </li>

                    <?php endif; ?>
                    <button class="c-pop c-slider--btn c-slider--btn--left u-disp--pc" type="button" aria-label="前の店舗へ">
                        <svg class="c-slider--btn__img" width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M23.4573 7.22216C23.4075 7.06927 23.3578 6.92366 23.3081 6.77077L23.4573 6.77077L23.4573 6.47228C23.6064 6.42131 23.7485 6.37035 23.8977 6.31939C24.1108 6.13009 24.1535 5.7952 24.189 5.4239C24.3382 5.37293 24.4802 5.32197 24.6294 5.27101C24.807 5.69327 25.013 5.93353 25.0699 6.47228C24.3666 6.64701 24.2245 7.13479 23.4644 7.22216L23.4573 7.22216ZM26.377 5.27101L26.377 5.4239L27.2508 5.4239L27.2508 5.57679L27.4 5.57679C27.3644 5.93353 27.336 5.94809 27.2508 6.17378L26.6683 6.17378L26.6683 6.02089C26.3841 5.8316 26.3912 5.78063 25.9365 5.72239C25.8868 5.47486 25.8371 5.22004 25.7874 4.97251L25.6382 4.97251C25.5174 4.47016 25.2688 4.31727 24.7644 4.22263C24.7502 3.96053 24.6152 3.6766 24.6152 3.62564L24.7644 3.62564L24.7644 3.17425L25.4961 3.17425L25.4961 3.32714C25.7376 3.50915 26.377 4.25903 26.5191 4.5284L27.3929 4.5284C27.265 5.06716 26.9737 5.2346 26.3699 5.27829L26.377 5.27101ZM27.8404 3.48003L27.8404 3.32714L27.9896 3.32714L27.9896 3.48003L28.1388 3.48003L28.1388 3.63291C27.9896 3.68388 27.8475 3.73484 27.6983 3.7858C27.7836 3.48731 27.677 3.65476 27.8475 3.48731L27.8404 3.48003ZM26.3059 27.9276L26.3059 28.0805L26.1568 28.0805C26.107 28.2334 26.0573 28.379 26.0076 28.5319C25.3753 28.4955 25.5103 28.4373 25.1338 28.2334L25.1338 27.9349L26.3059 27.9349L26.3059 27.9276ZM23.9687 28.0805L23.237 28.0805C23.1873 27.9276 23.1376 27.782 23.0879 27.6292L23.237 27.6292C23.2868 27.4763 23.3365 27.3307 23.3862 27.1778L24.1179 27.1778C24.1179 27.6073 24.0682 27.8184 23.9687 28.0733L23.9687 28.0805ZM25.1338 26.7337C25.0841 26.5808 25.0343 26.4352 24.9846 26.2823C24.8852 26.2313 24.7928 26.1804 24.6934 26.1294L24.6934 25.9765L24.1108 25.9765L24.1108 25.8236L23.9616 25.8236C23.8622 25.5251 23.7698 25.2266 23.6704 24.9281C23.9261 24.8116 24.2245 24.6879 24.4021 24.4767C25.1409 24.5204 25.2475 24.8044 25.7163 25.0737C25.7021 25.8527 25.5316 26.1367 25.4251 26.7191L25.1338 26.7191L25.1338 26.7337ZM24.5513 22.5401L24.7005 22.5401L24.7005 22.3873C25.077 22.4091 25.2119 22.4018 25.4322 22.5401L25.4322 22.693L25.8726 22.693C25.8726 22.693 26.0218 22.9114 26.1639 22.9915C26.1283 23.4138 26.107 23.5376 25.8726 23.7414C25.7092 23.9089 25.8726 23.8069 25.5813 23.8943C25.4819 23.5958 25.3895 23.2973 25.2901 22.9988L24.5584 22.9988L24.5584 22.5474L24.5513 22.5401ZM28.494 23.2099C28.4442 23.0571 28.3945 22.9114 28.3448 22.7586L28.1956 22.7586L28.1956 22.6057L28.3448 22.6057L28.3448 22.4528C28.8065 22.5693 28.8847 22.6493 28.9273 23.2027L28.4869 23.2027L28.494 23.2099ZM27.1797 22.4601C27.2721 22.096 27.336 22.1324 27.471 21.8631L27.9114 21.8631L27.9114 22.3145C27.6699 22.3654 27.4213 22.4164 27.1797 22.4673L27.1797 22.4601ZM26.8885 22.5401L26.8885 22.2416L27.1797 22.2416L27.1797 22.5401L26.8885 22.5401ZM13.1494 29.7987C12.6877 29.8133 12.4603 29.8133 12.1265 29.9516L12.1265 30.1045C11.6079 30.2792 11.0182 29.8351 10.663 29.806C10.1729 29.857 9.6898 29.9079 9.19962 29.9589C9.1499 29.857 9.10017 29.7623 9.05044 29.6604L8.75918 29.6604L8.75918 29.5075L8.46792 29.5075L8.46792 29.3546C8.26901 29.2163 8.21218 29.2527 7.88539 29.2017C7.61544 31.0073 6.56406 30.534 4.96567 30.403C4.83069 30.1336 4.76676 30.17 4.6744 29.806C4.17713 29.7186 3.758 29.573 3.21099 29.806L3.21099 29.9589L2.91973 29.9589L2.91973 30.1118C2.5006 30.2574 2.18802 30.0098 1.89676 29.9589C1.77599 29.6749 1.68364 29.5657 1.6055 29.209C0.8951 29.078 0.575424 28.7576 0.582527 27.8621C0.937725 27.651 1.19347 27.2797 0.873789 26.8137C0.774334 26.7628 0.681982 26.7118 0.582528 26.6609L0.582528 25.314C0.916413 25.132 1.14374 24.9572 1.6055 24.8626C1.70495 24.3675 1.86124 24.2874 1.89676 23.6613C1.16505 23.5448 0.753024 23.341 0.724608 22.4601C1.0727 22.2635 1.34265 22.016 1.45631 21.5646C1.27872 21.4699 1.2432 21.317 1.16505 21.2661L0.87379 21.2661L0.87379 21.1132L0.582528 21.1132L0.582528 20.9603C0.433345 20.8584 0.291266 20.7637 0.142083 20.6618L0.142083 20.5089C0.0284204 20.3342 0.156289 20.4943 -0.00710204 20.356C0.142081 19.1111 0.284164 17.8588 0.433347 16.6139C0.625154 16.5629 0.824064 16.512 1.01587 16.461L1.01587 15.7111C0.738817 15.5873 0.632255 15.4927 0.284162 15.4126C0.333888 15.0122 0.383619 14.6118 0.433347 14.2113L0.284162 14.2113L0.284162 13.4615C0.383617 13.4105 0.475971 13.3595 0.575426 13.3086L0.575426 12.2602L0.724609 12.2602C0.909311 11.969 0.951935 11.9763 1.01587 11.5103L0.284162 11.5103L0.284162 10.9133C0.674879 10.9643 1.0656 11.0152 1.45632 11.0662C1.54156 10.826 1.58419 10.7095 1.6055 10.3163C1.10112 10.0106 0.880896 9.33348 0.873791 8.51807C1.19347 8.33606 1.67654 7.87011 1.74758 7.46969C1.74758 7.46969 1.61971 7.05471 1.59839 6.8727C0.717505 6.70525 0.816962 6.26842 0.866688 5.22732C1.01587 5.17636 1.15795 5.1254 1.30713 5.07443L1.30713 4.92155L2.3301 4.92155L2.3301 4.32455C2.18092 4.27359 2.03884 4.22263 1.88966 4.17166C1.7902 3.87317 1.69785 3.57467 1.59839 3.27617C0.887999 3.14512 0.568324 2.82479 0.575427 1.92929C0.816962 1.87833 1.0656 1.82737 1.30713 1.77641C1.46342 0.57514 1.50604 0.364006 2.91263 0.429529C3.13285 0.77899 2.99077 0.553297 3.35307 0.728026C4.17003 1.12845 3.9498 0.291203 4.81649 0.575139L4.81649 0.728026C5.00829 0.77899 5.2072 0.829955 5.39901 0.880917L5.39901 1.03381C5.98153 1.21582 6.32963 0.640664 7.00451 0.582421C7.19631 0.931882 7.39522 1.28134 7.58703 1.6308L8.16955 1.6308C8.33294 1.46335 8.16955 1.57256 8.46082 1.47791C8.51054 1.23038 8.56027 0.975562 8.61 0.728026C8.84443 0.640662 8.95809 0.59698 9.34171 0.575139C10.2155 1.57984 10.3789 0.364007 11.2385 0.575139L11.2385 0.728026C11.7713 0.778991 12.3112 0.829955 12.844 0.880918L12.844 1.03381C13.4265 0.93188 14.0161 0.837236 14.5986 0.735309L14.5986 0.582422C16.0123 0.531459 17.426 0.480493 18.8397 0.429531C19.067 0.866356 19.5501 1.40511 20.1539 1.47791C20.4026 0.91732 20.5731 0.961004 20.8856 0.582422C21.3758 0.684347 21.9228 1.09205 22.6403 0.880919L22.6403 0.728027L23.5141 0.728027L23.5141 0.57514L23.9545 0.57514C24.0043 0.473215 24.054 0.378569 24.1037 0.276644C24.4447 -1.12143e-05 24.7999 -0.00729153 25.418 -0.0218522C25.4606 0.174717 25.5956 0.655224 25.7092 0.728028C26.8814 0.880917 28.0464 1.02653 29.2186 1.17942C29.3109 1.43423 29.3607 1.65264 29.3678 2.07491C28.5224 2.54813 28.8918 3.05048 29.0765 3.87317C29.3393 4.0115 29.3038 4.07702 29.659 4.17167C29.7798 4.8633 29.794 5.03803 29.5098 5.67143L29.3606 5.67143L29.3606 6.12282L29.2115 6.12282C29.1688 6.45044 29.7158 6.81445 29.5027 7.32408C29.3535 7.42601 29.2115 7.52066 29.0623 7.62258C29.112 8.47439 29.1617 9.31892 29.2115 10.1707L29.0623 10.1707L29.0623 10.4692L28.9131 10.4692C28.9628 10.9716 29.0126 11.4666 29.0623 11.969L28.9131 11.969L28.7639 12.4204L28.9131 12.4204L28.9131 12.8718C29.0623 12.9737 29.2044 13.0683 29.3535 13.1703C29.4672 13.4688 29.0552 14.0293 29.0623 14.2186L29.2115 14.2186C29.2612 14.5171 29.3109 14.8156 29.3606 15.1141C29.5098 15.216 29.6519 15.3107 29.8011 15.4126L29.8011 15.7111L29.9574 15.7111C30.1563 16.3081 29.5738 16.694 29.5169 17.058C29.5027 17.1308 29.7656 18.1282 29.6661 18.5578L29.5169 18.5578C29.5667 18.9072 29.6164 19.2567 29.6661 19.6061C29.6164 20.2541 29.5667 20.9021 29.5169 21.55C29.112 21.6592 29.1902 21.5937 29.0765 22.0014L28.9273 22.0014L28.9273 22.1543L29.0765 22.1543L29.0765 22.6057C29.1759 22.6566 29.2683 22.7076 29.3678 22.7586C29.5951 23.3191 29.1475 24.171 29.0765 24.5568C28.5437 24.6951 28.4869 25.0155 27.9043 25.1538L27.9043 25.9037C28.4158 26.0202 28.4727 26.1658 28.4869 26.7992C28.6503 26.9666 28.5437 26.7992 28.636 27.0977C28.8279 27.1486 29.0268 27.1996 29.2186 27.2506C29.1688 27.6 29.1191 27.9495 29.0694 28.299C29.1688 28.3499 29.2612 28.4009 29.3606 28.4518C29.4104 28.6994 29.4601 28.9542 29.5098 29.2017C29.3393 29.2964 29.2967 29.4493 29.2186 29.5002L28.9273 29.5002L28.9273 29.6531L28.636 29.6531C28.5863 29.755 28.5366 29.8497 28.4869 29.9516C27.9967 29.9006 27.5136 29.8497 27.0234 29.7987L27.0234 29.9516L26.7322 29.9516C26.6825 30.0535 26.6327 30.1482 26.583 30.2501C26.3273 30.4103 24.7715 30.7452 24.2458 30.5486L24.2458 30.3957C24.054 30.3447 23.8551 30.2938 23.6633 30.2428L23.6633 30.0899L22.6403 30.0899L22.6403 29.937C22.1643 29.7186 21.8802 29.6531 21.1769 29.6385C21.0845 30.1627 21.0206 30.3229 20.4452 30.3884C20.3955 30.2355 20.3457 30.0899 20.296 29.937C19.8342 29.8497 19.6851 29.7914 19.5643 29.34L18.9818 29.34L18.9818 29.4929L18.8326 29.4929L18.8326 30.403C18.4419 30.454 18.0512 30.5049 17.6604 30.5559L17.6604 30.7088C17.355 30.9272 17.3123 30.9636 16.7866 31.0073C16.4954 30.3083 16.2041 29.9443 15.1812 29.9589C14.9822 30.221 15.1456 29.9006 14.8899 30.1118L14.8899 30.2647C14.5347 30.5777 14.421 30.7088 13.7177 30.716C13.5046 30.3447 13.256 30.3011 13.1352 29.8206L13.1494 29.7987ZM20.4594 28.3062C20.3599 27.8548 20.2676 27.4107 20.1681 26.9594L20.7507 26.9594C20.8004 27.2069 20.8501 27.4617 20.8998 27.7092C21.1201 28.2043 21.2977 27.8985 21.3403 28.7576C20.7293 28.6775 20.8643 28.5465 20.4665 28.3062L20.4594 28.3062ZM7.45205 28.3062L7.45205 28.4591L7.30287 28.4591C7.14658 28.1097 7.03292 27.8985 6.72034 27.7092C6.73455 27.3161 6.77717 27.1996 6.86953 26.9594L7.45205 26.9594C7.5373 27.6437 7.87119 27.9058 7.45205 28.3062ZM5.98864 27.4035C6.2799 27.4908 6.11651 27.3816 6.2799 27.5563C5.98864 27.469 6.15203 27.5782 5.98864 27.4035ZM14.4708 25.7581L14.4708 25.6052C14.2079 25.3941 14.1227 25.263 13.7391 25.1538C13.6538 24.8553 13.7604 25.0228 13.5899 24.8553L13.5899 24.7024C13.2489 24.6515 12.9079 24.6005 12.5669 24.5495C12.3964 24.1054 12.2046 23.2682 11.8352 23.0498C11.5439 22.9988 11.2527 22.9478 10.9614 22.8969L10.9614 22.744C10.5636 22.4309 10.3647 21.9577 9.93844 21.6956C9.83898 21.0477 9.74663 20.3997 9.64717 19.7517C9.11438 19.5989 8.57448 19.4532 8.04168 19.3004C7.94223 18.798 7.84987 18.3029 7.75042 17.8006C7.60124 17.6987 7.45916 17.604 7.30997 17.5021L7.30997 16.9051C7.21052 16.8541 7.11817 16.8032 7.01871 16.7522C6.78428 16.3736 6.79849 16.097 6.43619 15.8567C6.48592 15.1214 6.67062 14.903 7.01871 14.5098C7.11817 14.4589 7.21052 14.4079 7.30997 14.357L7.30997 14.0585L7.45916 14.0585C7.69359 13.6216 7.84987 12.8499 7.8996 12.2602C8.70945 12.0636 8.20507 11.9763 8.48213 11.5103C8.61 11.2992 9.38433 11.0662 9.06465 10.4619L8.91547 10.4619C9.05755 9.90135 9.32039 9.79214 9.93844 9.71206L10.3789 9.71206C10.3789 9.71206 10.5423 9.47908 10.6701 9.41356C10.8122 9.07138 10.9259 8.83113 10.9614 8.36518C11.3663 8.25597 11.4942 8.07396 11.8352 7.91379C12.0483 7.30952 11.9844 6.51596 12.4177 6.11553L12.4177 5.96265C12.5811 5.9044 13.0429 6.19562 13.1494 6.26114L13.1494 6.41403L13.5899 6.41403C13.5401 6.1665 13.4904 5.91168 13.4407 5.66415C13.8385 5.54766 13.7746 5.62775 13.8811 5.21276C14.3145 5.11812 14.8046 4.90698 15.0533 4.61577C15.9129 4.63761 15.8702 4.90699 16.3675 5.21276C16.3675 5.75152 16.2894 6.02089 16.2183 6.41403C15.9271 6.46499 15.6358 6.51596 15.3445 6.56692L15.3445 6.71981L15.1954 6.71981C15.3161 7.67354 15.7282 7.39688 15.7779 8.36518C15.2806 8.47439 14.762 8.68552 14.4637 8.21229C14.0445 8.21229 13.8385 8.26325 13.5899 8.36518C13.4904 9.01314 13.3981 9.66109 13.2986 10.3091C12.78 10.3091 12.3112 10.3309 11.9844 10.4619L11.9844 11.0589L12.1336 11.0589C12.098 11.8671 11.9346 11.8379 11.2598 11.9544L10.8193 11.9544C10.7199 12.1073 10.6275 12.2529 10.5281 12.4058C9.99527 12.6024 9.45537 12.8062 8.92257 13.0028L8.92257 13.5998C9.07175 13.7017 9.21383 13.7964 9.36302 13.8983L9.5122 14.3497C9.80346 14.2987 10.0947 14.2478 10.386 14.1968L10.386 14.0439L12.8724 14.0439L12.8724 14.1968C13.6041 14.1458 14.3358 14.0949 15.0675 14.0439C15.1598 14.4079 15.2238 14.3715 15.3588 14.6409L16.0905 14.6409L16.0905 14.488L16.2396 14.488C16.538 14.5244 17.0211 14.9831 17.5539 14.7865L17.5539 14.6336L17.8451 14.6336C17.8949 14.5317 17.9446 14.437 17.9943 14.3351L18.2856 14.3351L18.2856 14.1822C18.726 14.2842 19.1594 14.3788 19.5998 14.4807C19.6922 14.8447 19.7561 14.8083 19.8911 15.0777C20.4949 14.9904 20.4026 14.7647 20.914 14.6263L20.914 14.4734C21.1556 14.5244 21.4042 14.5754 21.6458 14.6263L21.6458 14.7792L22.5195 14.7792L22.5195 14.9321L22.96 14.9321L22.96 15.085C23.1092 15.136 23.2512 15.1869 23.4004 15.2379C23.4999 15.4854 23.5922 15.7402 23.6917 15.9878L23.5425 15.9878L23.5425 16.2863C22.3988 16.2571 22.3064 16.4173 21.937 17.1818L21.3545 17.1818C21.2692 16.8833 21.3758 17.0507 21.2053 16.8833C20.708 16.461 20.1752 17.1599 19.4506 16.8833C19.1878 16.7813 18.861 16.3882 18.7189 16.1334C18.1009 16.1479 17.9588 16.2062 17.8451 16.7304C17.4544 16.7813 17.0637 16.8323 16.673 16.8833C16.1189 16.7013 15.6429 15.7475 14.9183 16.5848C14.6342 16.5484 14.4708 16.5993 14.3358 16.4319L14.3358 16.1334C14.2505 16.0169 14.144 16.046 14.0445 15.8349C13.121 15.813 12.6095 15.8494 12.2898 16.4319C12.0412 16.4464 11.7428 16.5848 11.7073 16.5848L11.7073 16.4319C11.2669 16.33 10.8335 16.2353 10.3931 16.1334C10.3434 15.8858 10.2936 15.631 10.2439 15.3835L9.66138 15.3835C9.41985 15.5291 9.14279 16.8177 9.37012 17.1818C10.1516 17.7788 10.9259 18.383 11.7073 18.98L11.7073 19.4314L11.8565 19.4314L11.8565 19.8828L12.0057 19.8828C12.0554 20.123 11.8636 20.1594 11.8565 20.1813C11.9062 20.5307 11.956 20.8802 12.0057 21.2297C12.1904 21.3098 12.368 21.2806 12.5882 21.3826L12.5882 21.5354L13.0287 21.5354C13.0784 21.6374 13.1281 21.732 13.1778 21.8339C13.3696 21.8849 13.5686 21.9359 13.7604 21.9868C13.8882 22.6275 14.485 23.1954 15.0746 23.3337C15.1314 24.0253 15.3374 24.4185 15.8063 24.6806C15.8063 25.2193 15.7282 25.4887 15.6571 25.8819C15.238 25.8819 14.7407 25.8964 14.485 25.729L14.4708 25.7581ZM7.8925 23.9598L7.8925 24.1127L8.04168 24.1127C7.95643 24.4622 7.83567 24.6733 7.60123 24.8626L7.60123 25.0155L7.16079 25.0155L7.16079 24.2656C7.55861 24.1637 7.49468 24.0617 7.8925 23.9671L7.8925 23.9598ZM4.24106 21.5646L4.24106 21.2661L4.53233 21.2661L4.53233 21.5646L4.24106 21.5646ZM2.77765 21.2661L2.77765 20.8147L3.21809 20.8147L3.21809 21.2661L2.77765 21.2661ZM23.6775 20.2177L23.6775 20.3706L23.8267 20.3706C23.7698 20.8511 23.6917 20.8074 23.5354 21.1205L23.2441 21.1205C23.1589 20.5672 23.1589 20.7783 23.2441 20.225L23.6846 20.225L23.6775 20.2177ZM23.5283 17.8224L23.5283 17.6695L23.237 17.6695L23.237 17.3711C23.4786 17.3201 23.7272 17.2691 23.9687 17.2182L23.9687 17.6695C23.8196 17.7205 23.6775 17.7715 23.5283 17.8224ZM3.80062 17.0726L3.80062 16.9197C4.14871 17.0143 4.07768 16.9633 4.24107 17.2182C4.09188 17.1672 3.9498 17.1162 3.80062 17.0653L3.80062 17.0726ZM17.0992 11.2337L17.0992 10.9352L17.5397 10.9352L17.5397 11.2337L17.0992 11.2337ZM4.82359 9.4354L4.82359 8.98401L5.26404 8.98401L5.26404 9.4354L4.82359 9.4354ZM1.89676 8.53263L1.89676 7.78275C2.08857 7.73178 2.28748 7.68082 2.47928 7.62986C2.60005 7.90652 2.70661 8.023 2.77055 8.37974C3.06181 8.4671 2.89842 8.3579 3.06181 8.53263L3.21099 8.53263L3.21099 8.98401C2.49349 8.93305 2.48639 8.66368 1.89676 8.53263ZM6.287 6.58876C6.287 7.12751 6.36515 7.39689 6.43619 7.79003L6.287 7.79003L6.287 7.94292L5.70448 7.94292L5.70448 7.64442L5.5553 7.64442L5.5553 7.49153L5.70448 7.49153L5.70448 6.29026C6.05968 6.38491 6.02416 6.45043 6.287 6.58876ZM18.4135 5.99177L18.4135 6.58876C18.1719 6.78533 17.9233 6.98918 17.6817 7.18576C17.632 7.33864 17.5823 7.48425 17.5326 7.63714L17.2413 7.63714C17.2484 6.79989 17.4189 6.45043 17.5326 5.83888C17.9517 5.83888 18.1577 5.88984 18.4064 5.99177L18.4135 5.99177ZM3.65144 7.18576L3.50225 7.18576L3.50225 6.73437L3.9427 6.73437L3.9427 7.33136C3.65144 7.244 3.81483 7.3532 3.65144 7.17847L3.65144 7.18576ZM2.62847 6.88726L2.62847 6.58876L3.21099 6.58876L3.21099 6.88726L2.62847 6.88726ZM20.7507 3.29073L20.7507 3.44362L20.8998 3.44362L20.8998 4.19351L20.7507 4.19351L20.7507 4.34639L19.8769 4.34639C19.8769 4.34639 19.614 4.70313 19.4364 4.79778C19.3867 4.64489 19.337 4.49928 19.2872 4.34639L19.1381 4.34639C19.1878 4.14982 19.2375 3.94597 19.2872 3.7494C20.2889 3.7494 20.1823 3.63292 20.7507 3.29802L20.7507 3.29073Z" fill="#BEBBBB"/>
                        </svg>
                    </button>
                    <button class="c-pop c-slider--btn c-slider--btn--right u-disp--pc" type="button" aria-label="次の店舗へ">
                        <svg class="c-slider--btn__img" width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.54134 7.68812C6.59107 7.52537 6.64079 7.37037 6.69052 7.20762L6.54134 7.20762L6.54134 6.88986C6.39216 6.83561 6.25008 6.78136 6.10089 6.72711C5.88778 6.5256 5.84515 6.1691 5.80963 5.77384C5.66045 5.71959 5.51837 5.66534 5.36919 5.61109C5.19159 6.0606 4.98557 6.31635 4.92874 6.88986C5.63203 7.07586 5.77411 7.59512 6.53423 7.68812L6.54134 7.68812ZM3.62161 5.61109L3.62161 5.77384L2.74783 5.77384L2.74783 5.9366L2.59864 5.9366C2.63416 6.31635 2.66258 6.33185 2.74783 6.57211L3.33035 6.57211L3.33035 6.40935C3.61451 6.20785 3.6074 6.1536 4.06206 6.0916C4.11179 5.82809 4.16151 5.55684 4.21124 5.29334L4.36042 5.29334C4.48119 4.75858 4.72983 4.59583 5.23421 4.49508C5.24842 4.21607 5.38339 3.91382 5.38339 3.85957L5.23421 3.85957L5.23421 3.37906L4.5025 3.37906L4.5025 3.54181C4.26097 3.73556 3.62161 4.53383 3.47953 4.82058L2.60575 4.82058C2.73362 5.39409 3.02488 5.57234 3.62872 5.61884L3.62161 5.61109ZM2.1582 3.70456L2.1582 3.54181L2.00902 3.54181L2.00902 3.70456L1.85983 3.70456L1.85983 3.86732C2.00902 3.92157 2.15109 3.97582 2.30028 4.03007C2.21503 3.71231 2.32159 3.89056 2.15109 3.71231L2.1582 3.70456ZM3.69265 29.7295L3.69265 29.8922L3.84184 29.8922C3.89157 30.055 3.94129 30.21 3.99102 30.3727C4.62327 30.334 4.4883 30.272 4.86481 30.055L4.86481 29.7372L3.69265 29.7372L3.69265 29.7295ZM6.02986 29.8922L6.76156 29.8922C6.81129 29.7295 6.86102 29.5744 6.91075 29.4117L6.76156 29.4117C6.71184 29.2489 6.66211 29.0939 6.61238 28.9312L5.88067 28.9312C5.88067 29.3884 5.9304 29.6132 6.02986 29.8845L6.02986 29.8922ZM4.86481 28.4584C4.91453 28.2957 4.96426 28.1407 5.01399 27.9779C5.11345 27.9237 5.2058 27.8694 5.30525 27.8152L5.30525 27.6524L5.88778 27.6524L5.88778 27.4897L6.03696 27.4897C6.13641 27.1719 6.22877 26.8542 6.32822 26.5364C6.07248 26.4124 5.77411 26.2806 5.59651 26.0559C4.8577 26.1024 4.75114 26.4047 4.28228 26.6914C4.29649 27.5207 4.46699 27.8229 4.57355 28.4429L4.86481 28.4429L4.86481 28.4584ZM5.44733 23.9944L5.29815 23.9944L5.29815 23.8316C4.92164 23.8549 4.78666 23.8471 4.56644 23.9944L4.56644 24.1571L4.126 24.1571C4.126 24.1571 3.97681 24.3896 3.83473 24.4749C3.87025 24.9244 3.89156 25.0561 4.126 25.2731C4.28939 25.4514 4.126 25.3429 4.41726 25.4359C4.51671 25.1181 4.60906 24.8004 4.70852 24.4826L5.44023 24.4826L5.44023 24.0021L5.44733 23.9944ZM1.50464 24.7074C1.55436 24.5446 1.60409 24.3896 1.65382 24.2269L1.803 24.2269L1.803 24.0641L1.65382 24.0641L1.65382 23.9014C1.19206 24.0254 1.11392 24.1106 1.07129 24.6996L1.51174 24.6996L1.50464 24.7074ZM2.81887 23.9091C2.72652 23.5216 2.66258 23.5604 2.52761 23.2736L2.08716 23.2736L2.08716 23.7541C2.32869 23.8084 2.57733 23.8626 2.81887 23.9169L2.81887 23.9091ZM3.11013 23.9944L3.11013 23.6766L2.81887 23.6766L2.81887 23.9944L3.11013 23.9944ZM16.8492 31.7212C17.3109 31.7367 17.5383 31.7367 17.8722 31.884L17.8722 32.0467C18.3907 32.2327 18.9804 31.76 19.3356 31.729C19.8257 31.7832 20.3088 31.8375 20.799 31.8917C20.8487 31.7832 20.8984 31.6825 20.9482 31.574L21.2394 31.574L21.2394 31.4112L21.5307 31.4112L21.5307 31.2485C21.7296 31.1012 21.7864 31.14 22.1132 31.0857C22.3832 33.0077 23.4345 32.504 25.0329 32.3645C25.1679 32.0777 25.2318 32.1165 25.3242 31.729C25.8215 31.636 26.2406 31.481 26.7876 31.729L26.7876 31.8917L27.0789 31.8917L27.0789 32.0545C27.498 32.2095 27.8106 31.946 28.1018 31.8917C28.2226 31.5895 28.315 31.4732 28.3931 31.0935C29.1035 30.954 29.4232 30.613 29.4161 29.6597C29.0609 29.4349 28.8051 29.0397 29.1248 28.5437C29.2243 28.4894 29.3166 28.4352 29.4161 28.3809L29.4161 26.9472C29.0822 26.7534 28.8549 26.5674 28.3931 26.4666C28.2937 25.9396 28.1374 25.8544 28.1018 25.1879C28.8336 25.0639 29.2456 24.8469 29.274 23.9091C28.9259 23.6999 28.656 23.4364 28.5423 22.9558C28.7199 22.8551 28.7554 22.6923 28.8336 22.6381L29.1248 22.6381L29.1248 22.4753L29.4161 22.4753L29.4161 22.3126C29.5653 22.2041 29.7073 22.1033 29.8565 21.9948L29.8565 21.8321C29.9702 21.6461 29.8423 21.8166 30.0057 21.6693C29.8565 20.3441 29.7144 19.011 29.5653 17.6858C29.3735 17.6315 29.1745 17.5773 28.9827 17.523L28.9827 16.7248C29.2598 16.593 29.3663 16.4923 29.7144 16.407C29.6647 15.9807 29.615 15.5545 29.5653 15.1282L29.7144 15.1282L29.7144 14.33C29.615 14.2757 29.5226 14.2215 29.4232 14.1672L29.4232 13.0512L29.274 13.0512C29.0893 12.7412 29.0467 12.7489 28.9827 12.2529L29.7144 12.2529L29.7144 11.6174C29.3237 11.6717 28.933 11.7259 28.5423 11.7802C28.457 11.5244 28.4144 11.4004 28.3931 10.9819C28.8975 10.6564 29.1177 9.93565 29.1248 9.06764C28.8051 8.87389 28.3221 8.37788 28.251 7.95162C28.251 7.95162 28.3789 7.50987 28.4002 7.31611C29.2811 7.13786 29.1816 6.67285 29.1319 5.56459C28.9827 5.51034 28.8407 5.45609 28.6915 5.40183L28.6915 5.23908L27.6685 5.23908L27.6685 4.60357C27.8177 4.54932 27.9598 4.49507 28.1089 4.44082C28.2084 4.12307 28.3008 3.80531 28.4002 3.48756C29.1106 3.34805 29.4303 3.00705 29.4232 2.05379C29.1816 1.99953 28.933 1.94528 28.6915 1.89103C28.5352 0.612263 28.4926 0.387509 27.086 0.45726C26.8658 0.829266 27.0078 0.589012 26.6455 0.775017C25.8286 1.20127 26.0488 0.310009 25.1821 0.612263L25.1821 0.775017C24.9903 0.829266 24.7914 0.883519 24.5996 0.937771L24.5996 1.10052C24.0171 1.29428 23.669 0.682015 22.9941 0.620015C22.8023 0.99202 22.6034 1.36403 22.4116 1.73603L21.829 1.73603C21.6657 1.55778 21.829 1.67403 21.5378 1.57328C21.4881 1.30978 21.4383 1.03852 21.3886 0.775018C21.1542 0.682015 21.0405 0.635514 20.6569 0.612264C19.7831 1.68178 19.6197 0.387509 18.7601 0.612264L18.7601 0.775018C18.2273 0.829267 17.6874 0.883519 17.1546 0.937772L17.1546 1.10052C16.5721 0.992021 15.9825 0.891271 15.4 0.78277L15.4 0.620015C13.9863 0.565767 12.5726 0.511514 11.1589 0.457262C10.9316 0.92227 10.4485 1.49578 9.84468 1.57328C9.59604 0.976522 9.42554 1.02302 9.11297 0.620016C8.6228 0.728517 8.07579 1.16253 7.35829 0.937773L7.35829 0.775019L6.48451 0.775019L6.48451 0.612265L6.04406 0.612265C5.99433 0.503763 5.94461 0.403013 5.89488 0.294512C5.55389 5.95184e-06 5.19869 -0.00774167 4.58065 -0.0232445C4.53802 0.186011 4.40305 0.697516 4.28938 0.775019C3.11723 0.937769 1.95218 1.09277 0.780031 1.25553C0.687679 1.52678 0.637952 1.75928 0.630848 2.20879C1.47622 2.71255 1.10681 3.24731 0.92211 4.12307C0.659264 4.27032 0.694784 4.34007 0.339586 4.44083C0.218819 5.17709 0.204611 5.36309 0.488769 6.03735L0.637952 6.03735L0.637952 6.51786L0.787135 6.51786C0.829759 6.86661 0.282754 7.25411 0.495873 7.79662C0.645056 7.90513 0.787135 8.00588 0.936318 8.11438C0.886591 9.02114 0.836863 9.92015 0.787136 10.8269L0.936319 10.8269L0.936319 11.1447L1.0855 11.1447C1.03577 11.6794 0.986046 12.2064 0.936319 12.7412L1.0855 12.7412L1.23468 13.2217L1.0855 13.2217L1.0855 13.7022C0.936319 13.8107 0.79424 13.9115 0.645057 14.02C0.531394 14.3377 0.943423 14.9345 0.936319 15.136L0.787136 15.136C0.737408 15.4537 0.687681 15.7715 0.637953 16.0892C0.48877 16.1977 0.346691 16.2985 0.197508 16.407L0.197508 16.7248L0.0412207 16.7248C-0.15769 17.3603 0.424834 17.771 0.481666 18.1585C0.495874 18.236 0.233028 19.2978 0.332483 19.7551L0.481666 19.7551C0.431939 20.1271 0.382211 20.4991 0.332483 20.8711C0.382211 21.5608 0.431939 22.2506 0.481667 22.9403C0.886592 23.0566 0.808448 22.9869 0.922112 23.4209L1.07129 23.4209L1.07129 23.5836L0.922112 23.5836L0.922112 24.0641C0.822656 24.1184 0.730305 24.1726 0.63085 24.2269C0.403523 24.8236 0.851072 25.7304 0.922112 26.1411C1.45491 26.2884 1.51174 26.6294 2.09426 26.7767L2.09426 27.5749C1.58278 27.6989 1.52595 27.8539 1.51174 28.5282C1.34835 28.7064 1.45491 28.5282 1.36256 28.8459C1.17075 28.9002 0.97184 28.9544 0.780033 29.0087C0.829761 29.3807 0.879489 29.7527 0.929216 30.1247C0.829761 30.179 0.73741 30.2332 0.637954 30.2875C0.588227 30.551 0.538499 30.8222 0.488771 31.0857C0.659266 31.1865 0.70189 31.3492 0.780033 31.4035L1.0713 31.4035L1.0713 31.5662L1.36256 31.5662C1.41229 31.6747 1.46201 31.7755 1.51174 31.884C2.00191 31.8297 2.48498 31.7755 2.97516 31.7212L2.97516 31.884L3.26642 31.884C3.31615 31.9925 3.36587 32.0932 3.4156 32.2017C3.67134 32.3722 5.22711 32.7287 5.7528 32.5195L5.7528 32.3567C5.94461 32.3025 6.14352 32.2482 6.33533 32.194L6.33533 32.0312L7.3583 32.0312L7.3583 31.8685C7.83426 31.636 8.11842 31.5662 8.82171 31.5507C8.91406 32.1087 8.978 32.2792 9.55342 32.349C9.60315 32.1862 9.65287 32.0312 9.7026 31.8685C10.1644 31.7755 10.3135 31.7135 10.4343 31.233L11.0168 31.233L11.0168 31.3957L11.166 31.3957L11.166 32.3645C11.5567 32.4187 11.9475 32.473 12.3382 32.5272L12.3382 32.69C12.6436 32.9225 12.6863 32.9612 13.212 33.0077C13.5032 32.2637 13.7945 31.8762 14.8174 31.8917C15.0164 32.1707 14.853 31.8297 15.1087 32.0545L15.1087 32.2172C15.4639 32.5505 15.5776 32.69 16.2809 32.6977C16.494 32.3025 16.7426 32.256 16.8634 31.7445L16.8492 31.7212ZM9.53921 30.1325C9.63866 29.6519 9.73102 29.1792 9.83047 28.6987L9.24795 28.6987C9.19822 28.9622 9.14849 29.2334 9.09877 29.4969C8.87854 30.024 8.70094 29.6985 8.65832 30.613C9.26926 30.5277 9.13428 30.3882 9.53211 30.1325L9.53921 30.1325ZM22.5466 30.1325L22.5466 30.2952L22.6957 30.2952C22.852 29.9232 22.9657 29.6984 23.2783 29.4969C23.2641 29.0784 23.2214 28.9544 23.1291 28.6987L22.5466 28.6987C22.4613 29.4272 22.1274 29.7062 22.5466 30.1325ZM24.01 29.1714C23.7187 29.2644 23.8821 29.1482 23.7187 29.3342C24.01 29.2412 23.8466 29.3574 24.01 29.1714ZM15.5278 27.4199L15.5278 27.2572C15.7907 27.0324 15.8759 26.8929 16.2596 26.7767C16.3448 26.4589 16.2382 26.6372 16.4087 26.4589L16.4087 26.2961C16.7497 26.2419 17.0907 26.1876 17.4317 26.1334C17.6022 25.6606 17.794 24.7694 18.1634 24.5369C18.4547 24.4826 18.7459 24.4284 19.0372 24.3741L19.0372 24.2114C19.435 23.8781 19.6339 23.3744 20.0602 23.0954C20.1596 22.4056 20.252 21.7158 20.3514 21.0261C20.8842 20.8633 21.4241 20.7083 21.9569 20.5456C22.0564 20.0108 22.1487 19.4838 22.2482 18.949C22.3974 18.8405 22.5394 18.7398 22.6886 18.6313L22.6886 17.9958C22.7881 17.9415 22.8804 17.8873 22.9799 17.833C23.2143 17.43 23.2001 17.1355 23.5624 16.8798C23.5127 16.097 23.328 15.8645 22.9799 15.446C22.8804 15.3917 22.7881 15.3375 22.6886 15.2832L22.6886 14.9655L22.5394 14.9655C22.305 14.5005 22.1487 13.679 22.099 13.0512C21.2892 12.8419 21.7935 12.7489 21.5165 12.2529C21.3886 12.0282 20.6143 11.7802 20.934 11.1369L21.0831 11.1369C20.9411 10.5402 20.6782 10.4239 20.0602 10.3387L19.6197 10.3387C19.6197 10.3387 19.4563 10.0907 19.3285 10.0209C19.1864 9.65665 19.0727 9.4009 19.0372 8.90489C18.6323 8.78864 18.5044 8.59488 18.1634 8.42438C17.9503 7.78112 18.0142 6.93636 17.5809 6.5101L17.5809 6.34735C17.4175 6.28535 16.9557 6.59536 16.8492 6.66511L16.8492 6.82786L16.4087 6.82786C16.4585 6.56435 16.5082 6.2931 16.5579 6.0296C16.1601 5.9056 16.224 5.99085 16.1175 5.54909C15.6841 5.44834 15.194 5.22358 14.9453 4.91358C14.0857 4.93683 14.1284 5.22359 13.6311 5.54909C13.6311 6.1226 13.7092 6.40935 13.7803 6.82786C14.0715 6.88211 14.3628 6.93636 14.6541 6.99061L14.6541 7.15336L14.8032 7.15336C14.6825 8.16863 14.2704 7.87412 14.2207 8.90489C14.718 9.02114 15.2366 9.24589 15.5349 8.74214C15.9541 8.74214 16.1601 8.79639 16.4087 8.90489C16.5082 9.59465 16.6005 10.2844 16.7 10.9742C17.2186 10.9742 17.6874 10.9974 18.0142 11.1369L18.0142 11.7724L17.865 11.7724C17.9006 12.6327 18.064 12.6017 18.7388 12.7257L19.1793 12.7257C19.2787 12.8884 19.3711 13.0434 19.4705 13.2062C20.0033 13.4155 20.5432 13.6325 21.076 13.8417L21.076 14.4772C20.9268 14.5857 20.7848 14.6865 20.6356 14.795L20.4864 15.2755C20.1951 15.2212 19.9039 15.167 19.6126 15.1127L19.6126 14.95L17.1262 14.95L17.1262 15.1127C16.3945 15.0585 15.6628 15.0042 14.9311 14.95C14.8388 15.3375 14.7748 15.2987 14.6398 15.5855L13.9081 15.5855L13.9081 15.4227L13.759 15.4227C13.4606 15.4615 12.9775 15.9497 12.4447 15.7405L12.4447 15.5777L12.1535 15.5777C12.1037 15.4692 12.054 15.3685 12.0043 15.26L11.713 15.26L11.713 15.0972C11.2726 15.2057 10.8392 15.3065 10.3988 15.415C10.3064 15.8025 10.2425 15.7637 10.1075 16.0505C9.50369 15.9575 9.59604 15.7172 9.08456 15.57L9.08456 15.4072C8.84302 15.4615 8.59438 15.5157 8.35285 15.57L8.35285 15.7327L7.47906 15.7327L7.47906 15.8955L7.03862 15.8955L7.03862 16.0582C6.88943 16.1125 6.74735 16.1667 6.59817 16.221C6.49872 16.4845 6.40636 16.7558 6.30691 17.0193L6.45609 17.0193L6.45609 17.337C7.59983 17.306 7.69218 17.4765 8.06159 18.2903L8.64411 18.2903C8.72936 17.9725 8.6228 18.1508 8.79329 17.9725C9.29057 17.523 9.82337 18.267 10.548 17.9725C10.8108 17.864 11.1376 17.4455 11.2797 17.1743C11.8977 17.1898 12.0398 17.2518 12.1535 17.8098C12.5442 17.864 12.9349 17.9183 13.3256 17.9725C13.8797 17.7788 14.3557 16.7635 15.0803 17.6548C15.3645 17.616 15.5278 17.6703 15.6628 17.492L15.6628 17.1743C15.7481 17.0503 15.8546 17.0813 15.9541 16.8565C16.8776 16.8333 17.3891 16.872 17.7088 17.492C17.9574 17.5075 18.2558 17.6548 18.2913 17.6548L18.2913 17.492C18.7317 17.3835 19.1651 17.2828 19.6055 17.1743C19.6552 16.9108 19.705 16.6395 19.7547 16.376L20.3372 16.376C20.5788 16.531 20.8558 17.9028 20.6285 18.2903C19.847 18.9258 19.0727 19.569 18.2913 20.2046L18.2913 20.6851L18.1421 20.6851L18.1421 21.1656L17.9929 21.1656C17.9432 21.4213 18.135 21.4601 18.1421 21.4833C18.0924 21.8553 18.0426 22.2273 17.9929 22.5993C17.8082 22.6846 17.6306 22.6536 17.4104 22.7621L17.4104 22.9248L16.9699 22.9248C16.9202 23.0333 16.8705 23.1341 16.8208 23.2426C16.629 23.2969 16.43 23.3511 16.2382 23.4054C16.1104 24.0874 15.5136 24.6919 14.924 24.8391C14.8672 25.5754 14.6612 25.9939 14.1923 26.2729C14.1923 26.8464 14.2704 27.1332 14.3415 27.5517C14.7606 27.5517 15.2579 27.5672 15.5136 27.3889L15.5278 27.4199ZM22.1061 25.5056L22.1061 25.6684L21.9569 25.6684C22.0422 26.0404 22.1629 26.2651 22.3974 26.4667L22.3974 26.6294L22.8378 26.6294L22.8378 25.8311C22.44 25.7226 22.5039 25.6141 22.1061 25.5134L22.1061 25.5056ZM25.7575 22.9558L25.7575 22.6381L25.4663 22.6381L25.4663 22.9558L25.7575 22.9558ZM27.221 22.6381L27.221 22.1576L26.7805 22.1576L26.7805 22.6381L27.221 22.6381ZM6.32112 21.5221L6.32112 21.6848L6.17193 21.6848C6.22877 22.1963 6.30691 22.1498 6.4632 22.4831L6.75446 22.4831C6.83971 21.8941 6.83971 22.1188 6.75446 21.5298L6.31401 21.5298L6.32112 21.5221ZM6.4703 18.9723L6.4703 18.8095L6.76156 18.8095L6.76156 18.4918C6.52003 18.4375 6.27139 18.3833 6.02985 18.329L6.02985 18.8095C6.17904 18.8638 6.32112 18.918 6.4703 18.9723ZM26.198 18.174L26.198 18.0113C25.8499 18.112 25.9209 18.0578 25.7575 18.329C25.9067 18.2748 26.0488 18.2205 26.198 18.1663L26.198 18.174ZM12.8994 11.9584L12.8994 11.6407L12.4589 11.6407L12.4589 11.9584L12.8994 11.9584ZM25.175 10.0442L25.175 9.56365L24.7346 9.56365L24.7346 10.0442L25.175 10.0442ZM28.1018 9.08314L28.1018 8.28488C27.91 8.23063 27.7111 8.17638 27.5193 8.12213C27.3986 8.41663 27.292 8.54063 27.2281 8.92039C26.9368 9.01339 27.1002 8.89714 26.9368 9.08314L26.7876 9.08314L26.7876 9.56365C27.5051 9.5094 27.5122 9.22264 28.1018 9.08314ZM23.7116 7.01386C23.7116 7.58737 23.6335 7.87412 23.5624 8.29263L23.7116 8.29263L23.7116 8.45538L24.2941 8.45538L24.2941 8.13763L24.4433 8.13763L24.4433 7.97487L24.2941 7.97487L24.2941 6.69611C23.9389 6.79686 23.9744 6.86661 23.7116 7.01386ZM11.5851 6.37835L11.5851 7.01386C11.8267 7.22312 12.0753 7.44012 12.3169 7.64937C12.3666 7.81212 12.4163 7.96713 12.466 8.12988L12.7573 8.12988C12.7502 7.23861 12.5797 6.86661 12.466 6.2156C12.0469 6.2156 11.8409 6.26985 11.5923 6.37835L11.5851 6.37835ZM26.3472 7.64937L26.4963 7.64937L26.4963 7.16886L26.0559 7.16886L26.0559 7.80437C26.3472 7.71137 26.1838 7.82762 26.3472 7.64162L26.3472 7.64937ZM27.3701 7.33162L27.3701 7.01386L26.7876 7.01386L26.7876 7.33162L27.3701 7.33162ZM9.24795 3.50306L9.24795 3.66581L9.09876 3.66581L9.09876 4.46407L9.24795 4.46407L9.24795 4.62683L10.1217 4.62683C10.1217 4.62683 10.3846 5.00658 10.5622 5.10733C10.6119 4.94458 10.6616 4.78958 10.7114 4.62683L10.8605 4.62683C10.8108 4.41757 10.7611 4.20057 10.7114 3.99132C9.7097 3.99132 9.81626 3.86732 9.24795 3.51081L9.24795 3.50306Z" fill="#BEBBBB"/>
                        </svg>
                    </button>
                </ul>
            </div>
            <a class="p-front-page__shop__all" href="">
                <button class="c-btn c-btn--common--green">一覧</button>
            </a>
        </section>
    </div>
    <!-- 店舗情報 end -->

    <!-- キャンペーン start -->
    <div class="c-bg p-front-page__bg--campaign">
        <section class="p-front-page__campaign">
            <!-- 背景画像(position配置) start -->
            <div class="c-bg__positioned p-front-page__bg--campaign--01"></div>
            <div class="c-bg__positioned p-front-page__bg--campaign--02"></div>
            <div class="c-bg__positioned p-front-page__bg--campaign--03"></div>
            <!-- 背景画像(position配置) end -->

            <!-- タイトル start -->
            <h1 class="c-section__title">キャンペーン情報</h1>
            <p class="c-section__title--sub">Campaign Information</p>
            <!-- タイトル end -->

            <div class="p-front-page__campaign__pc-flex">
                <?php foreach ($front_campaign_items as $front_campaign_item): ?>
                    <div class="c-campaign-content-wrapper">
                        <div class="c-campaign-content">
                            <a href="<?php echo esc_url($front_campaign_item['url']); ?>">
                                <picture>
                                    <img
                                        class="c-pop c-campaign-content__img"
                                        src="<?php echo esc_url($front_campaign_item['image']['url']); ?>"
                                        alt="<?php echo esc_attr($front_campaign_item['image']['alt']); ?>"
                                    >
                                </picture>
                                <time class="c-campaign-content__time" datetime="<?php echo esc_attr($front_campaign_item['datetime']); ?>">
                                    <?php echo esc_html($front_campaign_item['date']); ?>
                                </time>
                            </a>
                        </div>
                        <p class="c-campaign-content-caption"><?php echo esc_html($front_campaign_item['title']); ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (false): ?>
                <div class="c-campaign-content-wrapper">
                    <div class="c-campaign-content">
                        <a href="">
                            <picture class="u-disp--sp">
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-01.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-01.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <picture class="u-disp--pc">
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-pc-01.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-pc-01.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <time class="c-campaign-content__time" datetime="2025-11-18">2025.11.18</time>
                        </a>
                    </div>
                    <p class="c-campaign-content-caption">セレクション<br>おせちご予約始まります！</p>
                </div>

                <div class="c-campaign-content-wrapper">
                    <div class="c-campaign-content">
                        <a href="">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-02.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-02.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <p class="c-campaign-content__phrase">リサイクル<br>ポイント<span class="c-campaign-content__phrase__highlight">3</span>倍</p>
                            <time class="c-campaign-content__time" datetime="2025-11-10">2025.11.10</time>
                        </a>
                    </div>
                    <p class="c-campaign-content-caption">店舗限定！<br>リサイクルポイント3倍</p>
                </div>

                <div class="c-campaign-content-wrapper">
                    <div class="c-campaign-content">
                        <a href="">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-03.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-03.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <time class="c-campaign-content__time" datetime="2025-10-31">2025.10.31</time>
                        </a>
                    </div>
                    <p class="c-campaign-content-caption">セレクションクリスマスケーキ<br>ご予約始まります！</p>
                </div>

                <div class="c-campaign-content-wrapper">
                    <div class="c-campaign-content">
                        <a href="">
                            <picture class="u-disp--sp">
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-01.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-01.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <picture class="u-disp--pc">
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-pc-01.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-pc-01.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <time class="c-campaign-content__time" datetime="2025-11-18">2025.11.18</time>
                        </a>
                    </div>
                    <p class="c-campaign-content-caption">セレクション<br>おせちご予約始まります！</p>
                </div>

                <div class="c-campaign-content-wrapper">
                    <div class="c-campaign-content">
                        <a href="">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-02.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-02.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <p class="c-campaign-content__phrase">リサイクル<br>ポイント<span class="c-campaign-content__phrase__highlight">3</span>倍</p>
                            <time class="c-campaign-content__time" datetime="2025-11-10">2025.11.10</time>
                        </a>
                    </div>
                    <p class="c-campaign-content-caption">店舗限定！<br>リサイクルポイント3倍</p>
                </div>

                <div class="c-campaign-content-wrapper">
                    <div class="c-campaign-content">
                        <a href="">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_campaign-content-03.webp" type="image/webp">
                                <img class="c-pop c-campaign-content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_campaign-content-03.png" alt="セレクションおせちご予約始まります！">
                            </picture>
                            <time class="c-campaign-content__time" datetime="2025-10-31">2025.10.31</time>
                        </a>
                    </div>
                    <p class="c-campaign-content-caption">セレクションクリスマスケーキ<br>ご予約始まります！</p>
                </div>
                <?php endif; ?>
            </div>

            <a class="p-front-page__campaign__all" href="<?php echo esc_url(get_post_type_archive_link('news')); ?>">
                <button class="c-btn c-btn--common--blue">もっと見る</button>
            </a>
        </section>
    </div>
    <!-- キャンペーン end -->

    <!-- レシピ/バナー start -->
    <div class="c-bg p-front-page__bg--recipe no-image">
        <section class="p-front-page__recipe">
            <!-- 背景画像(position配置) start -->
            <div class="c-bg__positioned p-front-page__bg--recipe--01"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--02"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--03"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--04"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--05"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--06"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--07"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--08"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--09"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--10"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--11"></div>
            <div class="c-bg__positioned p-front-page__bg--recipe--12"></div>
            <!-- 背景画像(position配置) end -->

            <!-- タイトル start -->
            <h1 class="c-section__title">レシピ</h1>
            <p class="c-section__title--sub">Recipe</p>
            <!-- タイトル end -->

            <div class="c-recipe-card-wrapper">
                <?php foreach ($front_recipe_items as $front_recipe_item): ?>
                    <a href="<?php echo esc_url($front_recipe_item['url']); ?>" class="c-recipe-card-wrapper-link">
                        <article class="c-recipe-card">
                            <p class="c-recipe-card__title"><?php echo esc_html($front_recipe_item['title']); ?></p>
                            <?php if ($front_recipe_item['category'] !== ''): ?>
                                <p class="c-recipe-card__category"><?php echo esc_html($front_recipe_item['category']); ?></p>
                            <?php endif; ?>
                            <picture>
                                <img
                                    class="c-recipe-card__img"
                                    src="<?php echo esc_url($front_recipe_item['image']['url']); ?>"
                                    alt="<?php echo esc_attr($front_recipe_item['image']['alt']); ?>"
                                >
                            </picture>
                            <?php if ($front_recipe_item['cooking_time'] !== ''): ?>
                                <time
                                    class="c-recipe-card__time"
                                    <?php if ($front_recipe_item['duration'] !== ''): ?>datetime="<?php echo esc_attr($front_recipe_item['duration']); ?>"<?php endif; ?>
                                >
                                    <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                                    </svg>
                                    <?php echo esc_html($front_recipe_item['cooking_time']); ?>
                                </time>
                            <?php endif; ?>
                        </article>
                    </a>
                <?php endforeach; ?>
                <?php if (false): ?>
                <a href="" class="c-recipe-card-wrapper-link">
                    <div class="c-recipe-card">
                        <p class="c-recipe-card__title">暑い日にピッタリ！さっぱり冷やし中華</p>
                        <p class="c-recipe-card__category">麺</p>
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recipe-content-01.webp" type="image/webp">
                            <img class="c-recipe-card__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recipe-content-01.png" alt="暑い日にピッタリ！さっぱり冷やし中華">
                        </picture>
                        <time class="c-recipe-card__time" datetime="PT30M">
                            <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                            </svg>
                            30分
                        </time>
                    </div>
                </a>
                <a href="" class="c-recipe-card-wrapper-link">
                    <div class="c-recipe-card">
                        <p class="c-recipe-card__title">ピリ辛！麻婆豆腐</p>
                        <p class="c-recipe-card__category">お豆腐</p>
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recipe-content-02.webp" type="image/webp">
                            <img class="c-recipe-card__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recipe-content-02.png" alt="ピリ辛！麻婆豆腐">
                        </picture>
                        <time class="c-recipe-card__time" datetime="PT30M">
                            <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                            </svg>
                            30分
                        </time>
                    </div>
                </a>
                <a href="" class="c-recipe-card-wrapper-link">
                    <div class="c-recipe-card">
                        <p class="c-recipe-card__title">彩り豊かな夏野菜カレー</p>
                        <p class="c-recipe-card__category">ご飯</p>
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recipe-content-03.webp" type="image/webp">
                            <img class="c-recipe-card__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recipe-content-03.png" alt="彩り豊かな夏野菜カレー">
                        </picture>
                        <time class="c-recipe-card__time" datetime="PT30M">
                            <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                            </svg>
                            30分
                        </time>
                    </div>
                </a>
                <a href="" class="c-recipe-card-wrapper-link">
                    <div class="c-recipe-card">
                        <p class="c-recipe-card__title">暑い日にピッタリ！さっぱり冷やし中華</p>
                        <p class="c-recipe-card__category">麺</p>
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recipe-content-01.webp" type="image/webp">
                            <img class="c-recipe-card__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recipe-content-01.png" alt="暑い日にピッタリ！さっぱり冷やし中華">
                        </picture>
                        <time class="c-recipe-card__time" datetime="PT30M">
                            <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                            </svg>
                            30分
                        </time>
                    </div>
                </a>
                <a href="" class="c-recipe-card-wrapper-link">
                    <div class="c-recipe-card">
                        <p class="c-recipe-card__title">ピリ辛！麻婆豆腐</p>
                        <p class="c-recipe-card__category">お豆腐</p>
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recipe-content-02.webp" type="image/webp">
                            <img class="c-recipe-card__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recipe-content-02.png" alt="ピリ辛！麻婆豆腐">
                        </picture>
                        <time class="c-recipe-card__time" datetime="PT30M">
                            <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                            </svg>
                            30分
                        </time>
                    </div>
                </a>
                <a href="" class="c-recipe-card-wrapper-link">
                    <div class="c-recipe-card">
                        <p class="c-recipe-card__title">彩り豊かな夏野菜カレー</p>
                        <p class="c-recipe-card__category">ご飯</p>
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recipe-content-03.webp" type="image/webp">
                            <img class="c-recipe-card__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recipe-content-03.png" alt="彩り豊かな夏野菜カレー">
                        </picture>
                        <time class="c-recipe-card__time" datetime="PT30M">
                            <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                            </svg>
                            30分
                        </time>
                    </div>
                </a>
                <?php endif; ?>
            </div>

            <a class="p-front-page__recipe__all" href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>">
                <button class="c-btn c-btn--common--green">もっと見る</button>
            </a>
            <a class="p-front-page__recipe__hureai" href="">ふれ愛交差点レシピ集はこちら</a>

            <div class="p-front-page__recipe__banner--wrapper">
                <a class="c-pop p-front-page__recipe__banner u-margin-top--38" href="">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_banner-content-01.webp" type="image/webp">
                        <img class="p-front-page__recipe__banner__img--app" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_banner-content-01.png" alt="公式アプリ">
                    </picture>
                </a>
                <a class="c-pop p-front-page__recipe__banner" href="">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_banner-content-02.webp" type="image/webp">
                        <img class="p-front-page__recipe__banner__img--online-shop" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_banner-content-02.png" alt="セレクションのネットスーパー">
                    </picture>
                </a>
            </div>
        </section>
    </div>
    <!-- レシピ/バナー end -->

    <!-- セレクションのこだわり start -->
    <div class="c-bg p-front-page__bg--commitment">
        <section class="p-front-page__commitment">
            <!-- 背景画像(position配置) start -->
            <div class="c-bg__positioned p-front-page__bg--commitment--01"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--02"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--03"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--04"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--05"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--06"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--07"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--08"></div>
            <div class="c-bg__positioned p-front-page__bg--commitment--09"></div>
            <!-- 背景画像(position配置) end -->

            <!-- タイトル start -->
            <h1 class="c-section__title">セレクションのこだわり</h1>
            <p class="c-section__title--sub">Commitment</p>
            <!-- タイトル end -->

            <p class="p-front-page__commitment__text">
                セレクションは、“楽しむ・楽しませる”を
                テーマに、バイヤーが全国から
                選び抜いた本当に価値ある商品を
                お届けしています。
            </p>
            <p class="p-front-page__commitment__text">
                生産地・味・鮮度・価格のバランスを
                徹底的に見極め、毎日の食卓が
                少し誇らしくなる品揃えを目指しています。
            </p>
            <p class="p-front-page__commitment__text">
                ぜひセレクションのこだわりを
                ご体感ください。
            </p>

            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--01" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-01.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-01.png" alt="こだわり　お肉">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--02" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-02.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-02.png" alt="こだわり　お魚">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--03" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-03.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-03.png" alt="こだわり　お野菜・果物">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--04" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-04.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-04.png" alt="こだわり　お米">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--05" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-05.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-05.png" alt="こだわり　お肉">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--06" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-06.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-06.png" alt="こだわり　お魚">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--07" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-07.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-07.png" alt="こだわり　お野菜・果物">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--08" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-08.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-08.png" alt="こだわり　お米">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--09" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-09.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-09.png" alt="こだわり　お野菜・果物">
                </picture>
            </a>
            <a class="c-pop p-front-page__commitment__link c-pop p-front-page__commitment__link--10" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_commitment-content-10.webp" type="image/webp">
                    <img class="c-pop p-front-page__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_commitment-content-10.png" alt="こだわり　お米">
                </picture>
            </a>

            <a class="p-front-page__commitment__all" href="">
                <button class="c-btn c-btn--common--blue">もっと見る</button>
            </a>
        </section>
    </div>
    <!-- セレクションのこだわり end -->

    <!-- 新着情報 start -->
    <div class="c-bg p-front-page__bg--news">
        <section class="p-front-page__news">
            <!-- タイトル start -->
            <h1 class="c-section__title">新着情報</h1>
            <p class="c-section__title--sub">News</p>
            <!-- タイトル end -->

            <?php foreach ($front_news_items as $front_news_item) : ?>
                <a class="c-news-card-wrapper" href="<?php echo esc_url($front_news_item['url']); ?>">
                    <div class="c-news-card">
                        <div class="c-news-card__header">
                            <time class="c-news-card__date" datetime="<?php echo esc_attr($front_news_item['datetime']); ?>"><?php echo esc_html($front_news_item['date']); ?></time>
                            <?php if ($front_news_item['category_label']) : ?>
                                <span class="c-news-card__genre c-news-card__genre--<?php echo esc_attr($front_news_item['category_class']); ?>"><?php echo esc_html($front_news_item['category_label']); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="c-news-card__body"><?php echo esc_html($front_news_item['title']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>

            <a class="p-front-page__news__all" href="<?php echo esc_url(get_post_type_archive_link('news')); ?>">
                <button class="c-btn c-btn--common--blue">もっと見る</button>
            </a>
        </section>
    </div>
    <!-- 新着情報 end -->

    <!-- SNS start -->
    <div class="c-bg p-front-page__bg--sns no-image">
        <section class="p-front-page__sns">
            <!-- タイトル start -->
            <h1 class="c-section__title">SNS</h1>
            <!-- タイトル end -->

            <h2 class="p-front-page__sns__subtitle">Instagram</h2>
            <div class="p-front-page__sns__content">
                <picture class="p-front-page__sns__content__picture p-front-page__sns__content__picture--large">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-01.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-01.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-02.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-02.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-03.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-03.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-04.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-04.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-05.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-05.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-06.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-06.png" alt="セレクション公式Instagram">
                </picture>

                <picture class="p-front-page__sns__content__picture p-front-page__sns__content__picture--large u-disp--pc">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-01.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-01.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture  u-disp--pc">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-02.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-02.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture u-disp--pc">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-03.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-03.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture u-disp--pc">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-04.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-04.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture u-disp--pc">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-05.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-05.png" alt="セレクション公式Instagram">
                </picture>
                <picture class="p-front-page__sns__content__picture u-disp--pc">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-06.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-06.png" alt="セレクション公式Instagram">
                </picture>
            </div>
            <a class="p-front-page__sns__all p-front-page__sns__all--instagram" href="/">
                <button class="c-btn c-btn--common--green--large">Instagramはこちら</button>
            </a>

            <h2 class="p-front-page__sns__subtitle">You Tube</h2>
            <a href="/">
                <picture class="c-btn--youtube--play">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_sns-content-07.webp" type="image/webp">
                    <img class="p-front-page__sns__content__img p-front-page__sns__content__img--07" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_sns-content-07.png" alt="セレクション公式YouTube">
                </picture>
            </a>

            <a class="p-front-page__sns__all p-front-page__sns__all--youtube" href="/">
                <button class="c-btn c-btn--common--blue--large">Youtubeチャンネルはこちら</button>
            </a>
        </section>
    </div>
    <!-- SNS end -->

    <!-- 採用情報 start -->
    <div class="c-bg p-front-page__bg--recruit">
        <section class="p-front-page__recruit">
            <!-- タイトル start -->
            <h1 class="c-section__title">採用情報</h1>
            <p class="c-section__title--sub">Recruitment</p>
            <!-- タイトル end -->

            <a class="p-front-page__recruit__link u-disp--sp" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recruit-content-01.webp" type="image/webp">
                    <img class="p-front-page__recruit__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recruit-content-01.png" alt="採用情報">
                </picture>
            </a>
            <a class="p-front-page__recruit__link u-disp--pc" href="/">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/bg_recruit-content-pc-01.webp" type="image/webp">
                    <img class="c-pop p-front-page__recruit__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/bg_recruit-content-pc-01.png" alt="採用情報">
                </picture>
            </a>
        </section>
    </div>
    <!-- 採用情報 end -->

    <!-- 会社情報 start -->
    <div class="c-bg p-front-page__bg--company no-image">
        <div class="p-front-page__company__picture--wrapper">
            <a class="c-pop p-front-page__company__link p-front-page__company__link--company" href="/">
                <picture class="p-front-page__company__picture">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_company-content-01.webp" type="image/webp">
                    <img class="p-front-page__company__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_company-content-01.png" alt="会社情報">
                </picture>
            </a>
            <a class="c-pop p-front-page__company__link p-front-page__company__link--beef" href="/">
                <picture class="p-front-page__company__picture">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/front-page/webp/link_company-content-02.webp" type="image/webp">
                    <img class="p-front-page__company__img" src="<?php echo get_template_directory_uri(); ?>/img/page/front-page/link_company-content-02.png" alt="牛肉個体識別情報検索">
                </picture>
            </a>
        </div>

        <div class="p-front-page__company__link-wrapper">
            <a class="p-front-page__company__link p-front-page__company__link--company" href="/">
                <button class="c-btn p-btn--common--blue">企業情報</button>
            </a>
            <a class="p-front-page__company__link p-front-page__company__link--beef" href="/">
                <button class="c-btn c-btn--common--green">企業の方へ</button>
            </a>
        </div>
    </div>
    <!-- 会社情報 end -->

</main>

<?php get_footer(); ?>
