<?php
function foods_recipe_archive_get_field_value($post_id, $field_name) {
    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    $value = get_post_meta($post_id, $field_name, true);

    return $value !== '' ? $value : null;
}

function foods_recipe_archive_normalize_value($value) {
    if (is_string($value)) {
        $maybe_unserialized = maybe_unserialize($value);
        if ($maybe_unserialized !== $value) {
            return foods_recipe_archive_normalize_value($maybe_unserialized);
        }

        return wp_strip_all_tags($value);
    }

    if (is_array($value)) {
        $normalized = [];
        foreach ($value as $key => $item) {
            $normalized[$key] = foods_recipe_archive_normalize_value($item);
        }

        return $normalized;
    }

    return $value;
}

function foods_recipe_archive_get_image_data($image, $size = 'large') {
    if (empty($image)) {
        return null;
    }

    if (is_array($image)) {
        $image_id = $image['ID'] ?? $image['id'] ?? null;
        if ($image_id) {
            return foods_recipe_archive_get_image_data((int) $image_id, $size);
        }

        return [
            'url' => $image['url'] ?? '',
            'alt' => $image['alt'] ?? '',
            'width' => $image['width'] ?? null,
            'height' => $image['height'] ?? null,
        ];
    }

    if (is_numeric($image)) {
        $image_id = (int) $image;
        $src = wp_get_attachment_image_src($image_id, $size);

        if (!$src) {
            return null;
        }

        return [
            'id' => $image_id,
            'url' => $src[0],
            'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
            'width' => $src[1],
            'height' => $src[2],
        ];
    }

    if (is_string($image)) {
        return [
            'url' => $image,
            'alt' => '',
            'width' => null,
            'height' => null,
        ];
    }

    return null;
}

function foods_recipe_archive_get_recipe_image_data($post_id, $size = 'large') {
    $recipe_photo = foods_recipe_archive_get_field_value($post_id, 'recipe_photo');
    $recipe_photo_data = foods_recipe_archive_get_image_data($recipe_photo, $size);

    if (!empty($recipe_photo_data['url'])) {
        return $recipe_photo_data;
    }

    if (has_post_thumbnail($post_id)) {
        return foods_recipe_archive_get_image_data(get_post_thumbnail_id($post_id), $size);
    }

    return null;
}

function foods_recipe_archive_get_primary_term_name($terms) {
    if (empty($terms) || !is_array($terms)) {
        return '';
    }

    $first_term = reset($terms);

    return is_array($first_term) ? ($first_term['name'] ?? '') : '';
}

function foods_recipe_archive_format_duration_datetime($value) {
    if (empty($value)) {
        return '';
    }

    if (preg_match('/(\d+)/', (string) $value, $matches)) {
        return 'PT' . $matches[1] . 'M';
    }

    return '';
}

function foods_recipe_archive_get_terms($post_id, $taxonomy) {
    if (!taxonomy_exists($taxonomy)) {
        return [];
    }

    $terms = get_the_terms($post_id, $taxonomy);
    if (!$terms || is_wp_error($terms)) {
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

function foods_recipe_archive_format_item($recipe_post) {
    $recipe_id = $recipe_post->ID;
    $recipe_categories = foods_recipe_archive_get_terms($recipe_id, 'recipe_category');
    $recipe_tags = foods_recipe_archive_get_terms($recipe_id, 'recipe_tag');
    $categories = foods_recipe_archive_get_terms($recipe_id, 'category');
    $tags = foods_recipe_archive_get_terms($recipe_id, 'post_tag');
    $keyword_terms = array_merge($recipe_categories, $recipe_tags, $categories, $tags);

    return [
        'id' => $recipe_id,
        'title' => get_the_title($recipe_id),
        'slug' => $recipe_post->post_name,
        'permalink' => get_permalink($recipe_id),
        'date' => get_the_date('Y.m.d', $recipe_id),
        'datetime' => get_the_date('Y-m-d', $recipe_id),
        'excerpt' => get_the_excerpt($recipe_id),
        'content' => wp_strip_all_tags(apply_filters('the_content', $recipe_post->post_content)),
        'image' => foods_recipe_archive_get_recipe_image_data($recipe_id, 'large'),
        'summary' => foods_recipe_archive_normalize_value(foods_recipe_archive_get_field_value($recipe_id, 'recipe_summary')),
        'cooking_time' => foods_recipe_archive_normalize_value(foods_recipe_archive_get_field_value($recipe_id, 'recipe_cooking_time')),
        'servings' => foods_recipe_archive_normalize_value(foods_recipe_archive_get_field_value($recipe_id, 'recipe_servings')),
        'ingredients' => foods_recipe_archive_normalize_value(foods_recipe_archive_get_field_value($recipe_id, 'recipe_ingredients')),
        'preparation' => foods_recipe_archive_normalize_value(foods_recipe_archive_get_field_value($recipe_id, 'recipe_preparation')),
        'steps' => foods_recipe_archive_normalize_value(foods_recipe_archive_get_field_value($recipe_id, 'recipe_steps')),
        'tips' => foods_recipe_archive_normalize_value(foods_recipe_archive_get_field_value($recipe_id, 'recipe_tips')),
        'terms' => [
            'recipe_categories' => $recipe_categories,
            'recipe_tags' => $recipe_tags,
            'categories' => $categories,
            'tags' => $tags,
        ],
        'search' => [
            'text' => get_the_title($recipe_id),
            'keywords' => array_values(array_unique(array_filter(array_merge(
                wp_list_pluck($keyword_terms, 'name'),
                wp_list_pluck($keyword_terms, 'slug')
            )))),
        ],
    ];
}

$recipe_paged = max(1, (int) get_query_var('paged'));
$recipe_query = new WP_Query([
    'post_type' => 'recipe',
    'posts_per_page' => 10,
    'paged' => $recipe_paged,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
]);
$recipe_total_pages = (int) $recipe_query->max_num_pages;

$recipe_items = [];

if ($recipe_query->have_posts()) {
    foreach ($recipe_query->posts as $recipe_post) {
        $recipe_items[] = foods_recipe_archive_format_item($recipe_post);
    }
}

wp_reset_postdata();

get_header();
?>

<main class="p-recipe-archive c-main" aria-label="レシピ一覧">
    <nav class="c-breadcrumb p-recipe-archive__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span>レシピ</span>
    </nav>

    <div class="p-recipe-archive__heading">
        <h1 class="p-recipe-archive__title c-section__title">レシピ</h1>
        <p class="p-recipe-archive__title-en c-section__title--sub">RECIPE</p>
    </div>

    <section class="p-recipe-archive__search" aria-label="レシピ検索">
        <div class="p-recipe-archive__search-tabs" role="tablist" aria-label="検索方法">
            <button class="p-recipe-archive__search-tab is-active" type="button" role="tab" aria-selected="true" data-recipe-search-tab="ingredient">
                食材から探す
            </button>
            <button class="p-recipe-archive__search-tab" type="button" role="tab" aria-selected="false" data-recipe-search-tab="name">
                料理名から探す
            </button>
        </div>

        <div class="p-recipe-archive__ingredient-panel" aria-label="食材カテゴリ">
            <button class="p-recipe-archive__ingredient-button" type="button" data-recipe-category="vegetable">野菜</button>
            <button class="p-recipe-archive__ingredient-button" type="button" data-recipe-category="meat">お肉</button>
            <button class="p-recipe-archive__ingredient-button" type="button" data-recipe-category="fish">お魚</button>
            <button class="p-recipe-archive__ingredient-button" type="button" data-recipe-category="noodle-rice">麺・ご飯</button>
            <button class="p-recipe-archive__ingredient-button" type="button" data-recipe-category="side-dish">おかず</button>
            <button class="p-recipe-archive__ingredient-button" type="button" data-recipe-category="other">その他</button>
        </div>

        <section class="p-recipe-archive__keyword" aria-labelledby="recipe-archive-keyword-title">
            <h2 id="recipe-archive-keyword-title" class="p-recipe-archive__keyword-title">
                <span class="p-recipe-archive__keyword-title-text">キーワード</span>
            </h2>
            <div class="p-recipe-archive__keyword-list" aria-label="キーワード一覧">
                <?php
                $recipe_keywords = [
                    '豚肉',
                    'ひき肉',
                    'お豆腐',
                    'お正月',
                    'ハロウィンメニュー',
                    'サクッと簡単',
                    '家族みんなで',
                    'ナス',
                    'トマト',
                    'サンマ',
                    'きゅうり',
                    'じゃがいも',
                    '大根',
                    '子供が喜ぶ',
                    '誕生日',
                    '春野菜',
                    '夏野菜',
                    '秋野菜',
                ];

                foreach ($recipe_keywords as $recipe_keyword) :
                    ?>
                    <button class="p-recipe-archive__keyword-button" type="button" data-recipe-keyword="<?php echo esc_attr($recipe_keyword); ?>">
                        #<?php echo esc_html($recipe_keyword); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
    </section>

    <?php if (!empty($recipe_items)) : ?>
        <section class="p-recipe-archive__list" aria-label="新着レシピ">
            <div class="p-recipe-archive__decorations" aria-hidden="true">
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--scale"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--cup"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--spoon"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--bowl"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--glove"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--cutter"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--ladle"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--apron"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--tools"></span>
            </div>
            <h2 class="p-recipe-archive__list-title">新着レシピ</h2>
            <div class="c-recipe-card-wrapper p-recipe-archive__card-list">
                <?php foreach ($recipe_items as $recipe_item) :
                    $recipe_title = $recipe_item['title'] ?? '';
                    $recipe_url = $recipe_item['permalink'] ?? '#';
                    $recipe_image = $recipe_item['image'] ?? null;
                    $recipe_image_url = !empty($recipe_image['url']) ? $recipe_image['url'] : get_template_directory_uri() . '/img/component/no-image.png';
                    $recipe_image_alt = !empty($recipe_image['alt']) ? $recipe_image['alt'] : $recipe_title;
                    $recipe_category = foods_recipe_archive_get_primary_term_name($recipe_item['terms']['recipe_categories'] ?? []);
                    if ($recipe_category === '') {
                        $recipe_category = foods_recipe_archive_get_primary_term_name($recipe_item['terms']['categories'] ?? []);
                    }
                    $recipe_time = $recipe_item['cooking_time'] ?? '';
                    $recipe_datetime = foods_recipe_archive_format_duration_datetime($recipe_time);
                    ?>
                    <a href="<?php echo esc_url($recipe_url); ?>" class="c-recipe-card-wrapper-link p-recipe-archive__card-link">
                        <article class="c-recipe-card p-recipe-archive__card">
                            <p class="c-recipe-card__title"><?php echo esc_html($recipe_title); ?></p>
                            <?php if ($recipe_category !== '') : ?>
                                <p class="c-recipe-card__category"><?php echo esc_html($recipe_category); ?></p>
                            <?php endif; ?>
                            <img class="c-recipe-card__img" src="<?php echo esc_url($recipe_image_url); ?>" alt="<?php echo esc_attr($recipe_image_alt); ?>">
                            <?php if ($recipe_time !== '') : ?>
                                <time class="c-recipe-card__time"<?php echo $recipe_datetime !== '' ? ' datetime="' . esc_attr($recipe_datetime) . '"' : ''; ?>>
                                    <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                        <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                                    </svg>
                                    <?php echo esc_html($recipe_time); ?>
                                </time>
                            <?php endif; ?>
                        </article>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php if ($recipe_total_pages > 1) : ?>
                <nav class="c-archive-pagination p-recipe-archive__pagination" aria-label="???????????">
                    <?php for ($page_number = 1; $page_number <= $recipe_total_pages; $page_number++) : ?>
                        <?php if ($page_number === $recipe_paged) : ?>
                            <span class="c-archive-pagination__item is-current" aria-current="page"><?php echo esc_html($page_number); ?></span>
                        <?php else : ?>
                            <a class="c-archive-pagination__item" href="<?php echo esc_url(get_pagenum_link($page_number)); ?>"><?php echo esc_html($page_number); ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($recipe_paged < $recipe_total_pages) : ?>
                        <a class="c-archive-pagination__next" href="<?php echo esc_url(get_pagenum_link($recipe_paged + 1)); ?>" aria-label="??????"></a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
            <div class="p-recipe-archive__source">
                <span class="p-recipe-archive__source-label">【出典】</span>
                <a class="p-recipe-archive__source-link" href="https://www.kurashiru.com/" target="_blank" rel="noopener noreferrer">
                    <img class="p-recipe-archive__source-logo" src="<?php echo esc_url(get_template_directory_uri() . '/img/page/recipe/logo_kurashiru.svg'); ?>" alt="????">
                    <span class="p-recipe-archive__source-url">https://www.kurashiru.com/</span>
                </a>
            </div>
        </section>
    <?php endif; ?>
</main>

<script>
    window.foodsRecipeArchiveItems = <?php echo wp_json_encode($recipe_items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>

<?php
get_footer();


