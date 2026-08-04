<?php
$config = $args['config'];
$block_class = $args['block_class'];
$news = $config['news'];
?>
<section class="<?php echo esc_attr($block_class); ?>__news">
    <div class="<?php echo esc_attr($block_class); ?>__news__bg">
        <img class="<?php echo esc_attr($block_class); ?>__news__decoration6" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $news['decoration'])); ?>" alt="<?php echo esc_attr($config['title']); ?>">
        <h2 class="<?php echo esc_attr($block_class); ?>__news__title"><?php echo esc_html($news['title']); ?></h2>

        <div class="<?php echo esc_attr($block_class); ?>__news__wrap">
            <?php
            $news_query = new WP_Query([
                'post_type' => 'news',
                'post_status' => 'publish',
                'posts_per_page' => 6,
                'orderby' => [
                    'date' => 'DESC',
                    'ID' => 'DESC',
                ],
                'tax_query' => [[
                    'taxonomy' => $news['taxonomy'],
                    'field' => 'slug',
                    'terms' => [$news['term']],
                ]],
                'no_found_rows' => true,
            ]);

            while ($news_query->have_posts()) :
                $news_query->the_post();
                $news_id = get_the_ID();
                $news_terms = get_the_terms($news_id, 'news_category');
                $news_term = !is_wp_error($news_terms) && $news_terms ? reset($news_terms) : null;
                $news_tag_slug = $news_term ? sanitize_html_class($news_term->slug) : 'notice';
                $news_tag_name = $news_term ? $news_term->name : 'おしらせ';
                $news_image = get_the_post_thumbnail_url($news_id, 'medium_large');

                if (!$news_image) {
                    $news_image = get_template_directory_uri() . '/img/component/no-image.png';
                }
                ?>
                <article class="<?php echo esc_attr($block_class); ?>__news__item">
                    <div class="<?php echo esc_attr($block_class); ?>__news__head">
                        <time class="<?php echo esc_attr($block_class); ?>__news__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time>
                        <span class="<?php echo esc_attr($block_class); ?>__news__tag <?php echo esc_attr($block_class . '__news__tag--' . $news_tag_slug); ?>"><?php echo esc_html($news_tag_name); ?></span>
                    </div>

                    <div class="<?php echo esc_attr($block_class); ?>__news__body">
                        <img class="<?php echo esc_attr($block_class); ?>__news__image" src="<?php echo esc_url($news_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                        <p class="<?php echo esc_attr($block_class); ?>__news__text"><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 90, '…')); ?></p>
                    </div>
                </article>
            <?php
            endwhile;
            wp_reset_postdata();
            ?>

            <a class="<?php echo esc_attr($block_class); ?>__news__link" href="<?php echo esc_url(get_post_type_archive_link('news')); ?>"><?php echo esc_html($news['archive_label']); ?></a>
        </div>
        <a class="<?php echo esc_attr($block_class); ?>__news__seeMore" href="<?php echo esc_url(home_url('/select/')); ?>">
            <span class="c-btn c-btn--common--green">一覧へ戻る</span>
        </a>
    </div>
</section>
