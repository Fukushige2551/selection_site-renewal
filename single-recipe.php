<?php
function foods_recipe_detail_get_field_value($post_id, $field_name) {
    if ($field_name === 'recipe_ingredients') {
        $raw_value = get_post_meta($post_id, $field_name, true);
        if (is_array($raw_value) && !empty($raw_value)) {
            return $raw_value;
        }
    }

    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    $value = get_post_meta($post_id, $field_name, true);

    return $value !== '' ? $value : null;
}

function foods_recipe_detail_normalize_value($value) {
    if (is_string($value)) {
        $maybe_unserialized = maybe_unserialize($value);
        if ($maybe_unserialized !== $value) {
            return foods_recipe_detail_normalize_value($maybe_unserialized);
        }

        return wp_strip_all_tags($value);
    }

    if (is_array($value)) {
        $normalized = [];
        foreach ($value as $key => $item) {
            $normalized[$key] = foods_recipe_detail_normalize_value($item);
        }

        return $normalized;
    }

    return $value;
}

function foods_recipe_detail_get_image_data($image, $size = 'large') {
    if (empty($image)) {
        return null;
    }

    if (is_array($image)) {
        $image_id = $image['ID'] ?? $image['id'] ?? null;
        if ($image_id) {
            return foods_recipe_detail_get_image_data((int) $image_id, $size);
        }

        return [
            'url' => $image['sizes'][$size] ?? $image['url'] ?? '',
            'alt' => $image['alt'] ?? '',
            'width' => $image['width'] ?? null,
            'height' => $image['height'] ?? null,
        ];
    }

    if (is_numeric($image)) {
        $image_id = (int) $image;
        return [
            'url' => wp_get_attachment_image_url($image_id, $size),
            'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
            'width' => null,
            'height' => null,
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

function foods_recipe_detail_get_recipe_image_data($post_id, $size = 'large') {
    $recipe_photo = foods_recipe_detail_get_field_value($post_id, 'recipe_photo');
    $recipe_photo_data = foods_recipe_detail_get_image_data($recipe_photo, $size);

    if (!empty($recipe_photo_data['url'])) {
        return $recipe_photo_data;
    }

    if (has_post_thumbnail($post_id)) {
        return foods_recipe_detail_get_image_data(get_post_thumbnail_id($post_id), $size);
    }

    return null;
}

function foods_recipe_detail_get_terms($post_id, $taxonomy) {
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
            'permalink' => get_term_link($term),
        ];
    }, $terms);
}

function foods_recipe_detail_get_primary_term_name($terms) {
    if (empty($terms) || !is_array($terms)) {
        return '';
    }

    $first_term = reset($terms);

    return is_array($first_term) ? ($first_term['name'] ?? '') : '';
}

function foods_recipe_detail_format_duration_datetime($duration) {
    if (!is_string($duration) && !is_numeric($duration)) {
        return '';
    }

    if (preg_match('/\d+/', (string) $duration, $matches)) {
        return 'PT' . $matches[0] . 'M';
    }

    return '';
}

function foods_recipe_detail_format_ingredient_row($item) {
    if (!is_array($item)) {
        $name = is_scalar($item) ? trim(wp_strip_all_tags((string) $item)) : '';

        return $name !== '' ? [
            'type' => 'item',
            'name' => $name,
            'amount' => '',
            'note' => '',
        ] : null;
    }

    $ingredient_name = trim((string) ($item['ingredient_name'] ?? $item['name'] ?? $item['item'] ?? $item['ingredient'] ?? ''));
    $amount = trim((string) ($item['amount'] ?? $item['quantity'] ?? ''));
    $note = trim((string) ($item['note'] ?? $item['description'] ?? ''));

    if ($ingredient_name === '' && $amount === '' && $note === '') {
        return null;
    }

    return [
        'type' => 'item',
        'name' => $ingredient_name,
        'amount' => $amount,
        'note' => $note,
    ];
}

function foods_recipe_detail_normalize_ingredient_rows($ingredients) {
    if (empty($ingredients)) {
        return [];
    }

    if (is_string($ingredients)) {
        $rows = [];
        $lines = preg_split('/\r\n|\r|\n/', $ingredients);
        foreach ($lines as $line) {
            $line = trim(wp_strip_all_tags($line));
            if ($line === '') {
                continue;
            }

            $parts = preg_split('/\s{2,}|\t/', $line);
            $rows[] = [
                'type' => 'item',
                'name' => trim($parts[0] ?? $line),
                'amount' => count($parts) > 1 ? trim(implode(' ', array_slice($parts, 1))) : '',
                'note' => '',
            ];
        }

        return [
            [
                'section_name' => '',
                'items' => $rows,
            ],
        ];
    }

    if (!is_array($ingredients)) {
        return [];
    }

    $sections = [];
    foreach ($ingredients as $section) {
        if (!is_array($section)) {
            $row = foods_recipe_detail_format_ingredient_row($section);
            if ($row) {
                $sections[] = [
                    'section_name' => '',
                    'items' => [$row],
                ];
            }
            continue;
        }

        if (array_key_exists('items', $section)) {
            $section_name = trim((string) ($section['section_name'] ?? $section['group_name'] ?? $section['name'] ?? ''));
            $source_items = is_array($section['items']) ? $section['items'] : [];
            $formatted_items = [];

            foreach ($source_items as $item) {
                if (is_array($item) && (($item['item_type'] ?? '') === 'compound' || !empty($item['compound_items']))) {
                    $compound_items = [];
                    $source_compound_items = is_array($item['compound_items'] ?? null) ? $item['compound_items'] : [];
                    foreach ($source_compound_items as $compound_item) {
                        $compound_row = foods_recipe_detail_format_ingredient_row($compound_item);
                        if ($compound_row) {
                            $compound_items[] = $compound_row;
                        }
                    }

                    if (!empty($compound_items)) {
                        $formatted_items[] = [
                            'type' => 'compound',
                            'group_name' => trim((string) ($item['compound_name'] ?? $item['group_name'] ?? $item['ingredient_name'] ?? '')),
                            'items' => $compound_items,
                        ];
                    }
                    continue;
                }

                $row = foods_recipe_detail_format_ingredient_row($item);
                if ($row) {
                    $formatted_items[] = $row;
                }
            }

            if ($section_name !== '' || !empty($formatted_items)) {
                $sections[] = [
                    'section_name' => $section_name,
                    'items' => $formatted_items,
                ];
            }
            continue;
        }

        $legacy_group_name = trim((string) ($section['group_name'] ?? $section['name'] ?? ''));
        $legacy_items = $section['ingredients'] ?? [];
        if (!is_array($legacy_items)) {
            $legacy_items = [];
        }

        $formatted_legacy_items = [];
        foreach ($legacy_items as $legacy_item) {
            $row = foods_recipe_detail_format_ingredient_row($legacy_item);
            if ($row) {
                $formatted_legacy_items[] = $row;
            }
        }

        if ($legacy_group_name !== '') {
            $sections[] = [
                'section_name' => '',
                'items' => [
                    [
                        'type' => 'compound',
                        'group_name' => $legacy_group_name,
                        'items' => $formatted_legacy_items,
                    ],
                ],
            ];
        } elseif (!empty($formatted_legacy_items)) {
            $sections[] = [
                'section_name' => '',
                'items' => $formatted_legacy_items,
            ];
        }
    }

    return $sections;
}

function foods_recipe_detail_normalize_steps($steps) {
    if (empty($steps)) {
        return [];
    }

    if (is_string($steps)) {
        $rows = [];
        foreach (preg_split('/\r\n|\r|\n/', $steps) as $line) {
            $line = trim(wp_strip_all_tags($line));
            if ($line !== '') {
                $rows[] = [
                    'text' => $line,
                    'point' => '',
                ];
            }
        }

        return $rows;
    }

    if (!is_array($steps)) {
        return [];
    }

    $rows = [];
    foreach ($steps as $step) {
        $point = '';
        if (is_array($step)) {
            $text = $step['step_text'] ?? $step['text'] ?? $step['content'] ?? reset($step);
            $point = $step['step_point'] ?? $step['point'] ?? $step['tip'] ?? '';
        } else {
            $text = $step;
        }

        if (!is_scalar($text)) {
            continue;
        }

        $text = trim(wp_strip_all_tags((string) $text));
        $point = is_scalar($point) ? trim(wp_strip_all_tags((string) $point)) : '';
        if ($text !== '') {
            $rows[] = [
                'text' => $text,
                'point' => $point,
            ];
        }
    }

    return $rows;
}
function foods_recipe_detail_get_related_items($post_id, $limit = 12) {
    $limit = max(1, (int) $limit);
    $related_posts = [];
    $exclude_ids = [(int) $post_id];
    $category_terms = get_the_terms($post_id, 'recipe_category');

    if (!is_wp_error($category_terms) && !empty($category_terms)) {
        $related_posts = get_posts([
            'post_type' => 'recipe',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'post__not_in' => $exclude_ids,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => [
                [
                    'taxonomy' => 'recipe_category',
                    'field' => 'term_id',
                    'terms' => wp_list_pluck($category_terms, 'term_id'),
                ],
            ],
        ]);
        $exclude_ids = array_merge($exclude_ids, wp_list_pluck($related_posts, 'ID'));
    }

    if (count($related_posts) < $limit) {
        $fallback_posts = get_posts([
            'post_type' => 'recipe',
            'post_status' => 'publish',
            'posts_per_page' => $limit - count($related_posts),
            'post__not_in' => $exclude_ids,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        $related_posts = array_merge($related_posts, $fallback_posts);
    }

    return array_map('foods_recipe_detail_format_item', array_slice($related_posts, 0, $limit));
}
function foods_recipe_detail_format_item($recipe_post) {
    $recipe_id = $recipe_post->ID;
    $recipe_categories = foods_recipe_detail_get_terms($recipe_id, 'recipe_category');
    $recipe_main_ingredients = foods_recipe_detail_get_terms($recipe_id, 'recipe_main_ingredient');
    $recipe_tags = foods_recipe_detail_get_terms($recipe_id, 'recipe_tag');
    $categories = foods_recipe_detail_get_terms($recipe_id, 'category');
    $tags = foods_recipe_detail_get_terms($recipe_id, 'post_tag');

    return [
        'id' => $recipe_id,
        'title' => get_the_title($recipe_id),
        'slug' => $recipe_post->post_name,
        'permalink' => get_permalink($recipe_id),
        'date' => get_the_date('Y.m.d', $recipe_id),
        'datetime' => get_the_date('Y-m-d', $recipe_id),
        'excerpt' => get_the_excerpt($recipe_id),
        'content' => wp_strip_all_tags(apply_filters('the_content', $recipe_post->post_content)),
        'image' => foods_recipe_detail_get_recipe_image_data($recipe_id, 'large'),
        'summary' => foods_recipe_detail_normalize_value(foods_recipe_detail_get_field_value($recipe_id, 'recipe_summary')),
        'cooking_time' => foods_recipe_detail_normalize_value(foods_recipe_detail_get_field_value($recipe_id, 'recipe_cooking_time')),
        'servings' => foods_recipe_detail_normalize_value(foods_recipe_detail_get_field_value($recipe_id, 'recipe_servings')),
        'ingredients' => foods_recipe_detail_normalize_value(foods_recipe_detail_get_field_value($recipe_id, 'recipe_ingredients')),
        'preparation' => foods_recipe_detail_normalize_value(foods_recipe_detail_get_field_value($recipe_id, 'recipe_preparation')),
        'steps' => foods_recipe_detail_normalize_value(foods_recipe_detail_get_field_value($recipe_id, 'recipe_steps')),
        'tips' => foods_recipe_detail_normalize_value(foods_recipe_detail_get_field_value($recipe_id, 'recipe_tips')),
        'terms' => [
            'recipe_categories' => $recipe_categories,
            'recipe_main_ingredients' => $recipe_main_ingredients,
            'recipe_tags' => $recipe_tags,
            'categories' => $categories,
            'tags' => $tags,
        ],
    ];
}

$recipe_detail_item = foods_recipe_detail_format_item(get_post());
$recipe_title = $recipe_detail_item['title'] ?? get_the_title();
$recipe_time = $recipe_detail_item['cooking_time'] ?? '';
$recipe_datetime = foods_recipe_detail_format_duration_datetime($recipe_time);
$recipe_category = foods_recipe_detail_get_primary_term_name($recipe_detail_item['terms']['recipe_main_ingredients'] ?? []);
if ($recipe_category === '') {
    $recipe_category = foods_recipe_detail_get_primary_term_name($recipe_detail_item['terms']['recipe_categories'] ?? []);
}
if ($recipe_category === '') {
    $recipe_category = foods_recipe_detail_get_primary_term_name($recipe_detail_item['terms']['categories'] ?? []);
}
$recipe_image = $recipe_detail_item['image'] ?? null;
$recipe_image_url = !empty($recipe_image['url']) ? $recipe_image['url'] : get_template_directory_uri() . '/img/component/no-image.png';
$recipe_image_alt = !empty($recipe_image['alt']) ? $recipe_image['alt'] : $recipe_title;
$recipe_summary = $recipe_detail_item['summary'] ?? '';
if ($recipe_summary === '') {
    $recipe_summary = $recipe_detail_item['excerpt'] ?? '';
}
if ($recipe_summary === '') {
    $recipe_summary = $recipe_detail_item['content'] ?? '';
}
$recipe_servings = $recipe_detail_item['servings'] ?? '';
$recipe_ingredient_sections = foods_recipe_detail_normalize_ingredient_rows($recipe_detail_item['ingredients'] ?? []);
$recipe_preparation = trim((string) ($recipe_detail_item['preparation'] ?? ''));
$recipe_steps = foods_recipe_detail_normalize_steps($recipe_detail_item['steps'] ?? []);
$recipe_tips = trim((string) ($recipe_detail_item['tips'] ?? ''));
$has_recipe_method = $recipe_preparation !== '' || !empty($recipe_steps);
$recipe_keyword_terms = [];
foreach (['recipe_tags', 'recipe_main_ingredients', 'recipe_categories', 'tags'] as $recipe_keyword_source) {
    foreach (($recipe_detail_item['terms'][$recipe_keyword_source] ?? []) as $recipe_keyword_term) {
        if (!empty($recipe_keyword_term['name'])) {
            $recipe_keyword_terms[$recipe_keyword_term['slug'] ?? $recipe_keyword_term['name']] = $recipe_keyword_term['name'];
        }
    }
}
$recipe_archive_url = get_post_type_archive_link('recipe') ?: home_url('/recipe/');
$shop_archive_url = get_post_type_archive_link('shop') ?: home_url('/shop/');
$recipe_permalink = $recipe_detail_item['permalink'] ?? get_permalink();
$recipe_share_url = rawurlencode($recipe_permalink);
$recipe_share_text = rawurlencode($recipe_title);
$recipe_previous_post = get_previous_post(false, '', 'recipe_category');
$recipe_next_post = get_next_post(false, '', 'recipe_category');
$recipe_kurashiru_url = 'https://www.kurashiru.com/';
$related_recipe_items = foods_recipe_detail_get_related_items($recipe_detail_item['id'] ?? get_the_ID(), 12);
$theme_uri = get_template_directory_uri();

get_header();
?>

<main class="p-recipe-detail c-main" aria-label="&#12524;&#12471;&#12500;&#35443;&#32048;">
    <nav class="c-breadcrumb p-recipe-detail__breadcrumb" aria-label="&#12497;&#12531;&#12367;&#12378;">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <a href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>">&#12524;&#12471;&#12500;</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span><?php the_title(); ?></span>
    </nav>

    <article class="p-recipe-detail__article">
        <div class="p-recipe-detail__meta">
            <?php if ($recipe_time !== '') : ?>
                <time class="p-recipe-detail__time"<?php echo $recipe_datetime !== '' ? ' datetime="' . esc_attr($recipe_datetime) . '"' : ''; ?>>
                    <svg class="p-recipe-detail__time-icon" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                        <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                    </svg>
                    <span class="p-recipe-detail__time-label">&#35519;&#29702;&#26178;&#38291;&#65306;</span>
                    <strong class="p-recipe-detail__time-value"><?php echo esc_html($recipe_time); ?></strong>
                </time>
            <?php endif; ?>
            <?php if ($recipe_category !== '') : ?>
                <p class="p-recipe-detail__category"><?php echo esc_html($recipe_category); ?></p>
            <?php endif; ?>
        </div>
        <h1 class="p-recipe-detail__title"><?php echo esc_html($recipe_title); ?></h1>
        <figure class="p-recipe-detail__figure">
            <img class="p-recipe-detail__image" src="<?php echo esc_url($recipe_image_url); ?>" alt="<?php echo esc_attr($recipe_image_alt); ?>">
        </figure>
        <?php if ($recipe_summary !== '') : ?>
            <p class="p-recipe-detail__summary"><?php echo nl2br(esc_html($recipe_summary)); ?></p>
        <?php endif; ?>

        <?php if (!empty($recipe_ingredient_sections)) : ?>
            <section class="p-recipe-detail__ingredients" aria-labelledby="recipe-detail-ingredients-title">
                <div class="p-recipe-detail__ingredients-heading">
                    <h2 id="recipe-detail-ingredients-title" class="p-recipe-detail__section-title">&#26448;&#26009;</h2>
                    <?php if ($recipe_servings !== '') : ?>
                        <p class="p-recipe-detail__servings">&#65288;<?php echo esc_html($recipe_servings); ?>&#65289;</p>
                    <?php endif; ?>
                </div>
                <div class="p-recipe-detail__ingredient-sections">
                    <?php foreach ($recipe_ingredient_sections as $section_index => $ingredient_section) : ?>
                        <?php
                        $section_name = $ingredient_section['section_name'] ?? '';
                        $is_primary_material_section = $section_index === 0 && ($section_name === '' || $section_name === html_entity_decode('&#26448;&#26009;', ENT_QUOTES, 'UTF-8'));
                        ?>
                        <div class="p-recipe-detail__ingredient-section">
                            <?php if (!$is_primary_material_section && $section_name !== '') : ?>
                                <h3 class="p-recipe-detail__ingredient-section-title"><?php echo esc_html($section_name); ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($ingredient_section['items'])) : ?>
                                <dl class="p-recipe-detail__ingredient-list">
                                    <?php foreach ($ingredient_section['items'] as $ingredient_item) : ?>
                                        <?php if (($ingredient_item['type'] ?? '') === 'compound') : ?>
                                            <div class="p-recipe-detail__ingredient-compound">
                                                <?php if (!empty($ingredient_item['group_name'])) : ?>
                                                    <dt class="p-recipe-detail__ingredient-compound-title"><?php echo esc_html($ingredient_item['group_name']); ?></dt>
                                                <?php endif; ?>
                                                <?php if (!empty($ingredient_item['items'])) : ?>
                                                    <dd class="p-recipe-detail__ingredient-compound-body">
                                                        <dl class="p-recipe-detail__ingredient-list p-recipe-detail__ingredient-list--nested">
                                                            <?php foreach ($ingredient_item['items'] as $compound_item) : ?>
                                                                <div class="p-recipe-detail__ingredient-item">
                                                                    <dt class="p-recipe-detail__ingredient-name"><?php echo esc_html($compound_item['name']); ?></dt>
                                                                    <dd class="p-recipe-detail__ingredient-amount"><?php echo esc_html($compound_item['amount']); ?></dd>
                                                                    <?php if (!empty($compound_item['note'])) : ?>
                                                                        <dd class="p-recipe-detail__ingredient-note"><?php echo nl2br(esc_html($compound_item['note'])); ?></dd>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </dl>
                                                    </dd>
                                                <?php endif; ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="p-recipe-detail__ingredient-item">
                                                <dt class="p-recipe-detail__ingredient-name"><?php echo esc_html($ingredient_item['name']); ?></dt>
                                                <dd class="p-recipe-detail__ingredient-amount"><?php echo esc_html($ingredient_item['amount']); ?></dd>
                                                <?php if (!empty($ingredient_item['note'])) : ?>
                                                    <dd class="p-recipe-detail__ingredient-note"><?php echo nl2br(esc_html($ingredient_item['note'])); ?></dd>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </dl>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($has_recipe_method) : ?>
            <section class="p-recipe-detail__method" aria-labelledby="recipe-detail-method-title">
                <h2 id="recipe-detail-method-title" class="p-recipe-detail__method-title">&#65339;&#20316;&#12426;&#26041;&#65341;</h2>
                <div class="p-recipe-detail__method-body">
                    <?php if ($recipe_preparation !== '') : ?>
                        <p class="p-recipe-detail__preparation">
                            <strong>&#12304;&#28310;&#20633;&#12305;</strong><?php echo nl2br(esc_html($recipe_preparation)); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($recipe_steps)) : ?>
                        <ol class="p-recipe-detail__step-list">
                            <?php foreach ($recipe_steps as $step) : ?>
                                <li class="p-recipe-detail__step-item">
                                    <span class="p-recipe-detail__step-text"><?php echo nl2br(esc_html($step['text'])); ?></span>
                                    <?php if (($step['point'] ?? '') !== '') : ?>
                                        <div class="p-recipe-detail__point">
                                            <p class="p-recipe-detail__point-title"><span aria-hidden="true">&#9679;</span>&#12509;&#12452;&#12531;&#12488;</p>
                                            <p class="p-recipe-detail__point-text"><?php echo nl2br(esc_html($step['point'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>


                </div>
            </section>
        <?php endif; ?>

        <?php if ($recipe_tips !== '') : ?>
            <section class="p-recipe-detail__tips" aria-labelledby="recipe-detail-tips-title">
                <h2 id="recipe-detail-tips-title" class="p-recipe-detail__tips-title">&#65339;&#12467;&#12484;&#12539;&#12509;&#12452;&#12531;&#12488;&#65341;</h2>
                <p class="p-recipe-detail__tips-text"><?php echo nl2br(esc_html($recipe_tips)); ?></p>
            </section>
        <?php endif; ?>

        <?php if (!empty($recipe_keyword_terms)) : ?>
            <ul class="p-recipe-detail__keywords" aria-label="&#12461;&#12540;&#12527;&#12540;&#12489;">
                <?php foreach ($recipe_keyword_terms as $recipe_keyword) : ?>
                    <li class="p-recipe-detail__keyword">#<?php echo esc_html($recipe_keyword); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="p-recipe-detail__source">
            <span class="p-recipe-detail__source-label">&#12304;&#20986;&#20856;&#12305;</span>
            <a class="p-recipe-detail__source-link" href="<?php echo esc_url($recipe_kurashiru_url); ?>" target="_blank" rel="noopener noreferrer">
                <img class="p-recipe-detail__source-logo" src="<?php echo esc_url($theme_uri . '/img/page/recipe/logo_kurashiru.svg'); ?>" alt="&#12463;&#12521;&#12471;&#12523;">
                <span class="p-recipe-detail__source-url"><?php echo esc_html($recipe_kurashiru_url); ?></span>
            </a>
        </div>

        <a class="p-recipe-detail__shop-link c-pop" href="<?php echo esc_url($shop_archive_url); ?>">
            <img class="p-recipe-detail__shop-link-image" src="<?php echo esc_url($theme_uri . '/img/page/recipe/recipe-detail-shop-link.png'); ?>" alt="&#12481;&#12521;&#12471;&#12539;&#24215;&#33303;&#19968;&#35239;">
        </a>

        <section class="p-recipe-detail__actions" aria-label="&#12524;&#12471;&#12500;&#12398;&#20849;&#26377;&#12392;&#31227;&#21205;">
            <div class="p-recipe-detail__share">
                <time class="p-recipe-detail__share-date" datetime="<?php echo esc_attr($recipe_detail_item['datetime'] ?? get_the_date('Y-m-d')); ?>"><?php echo esc_html($recipe_detail_item['date'] ?? get_the_date('Y.m.d')); ?></time>
                <p class="p-recipe-detail__share-title">&#12471;&#12455;&#12450;&#12377;&#12427;</p>
                <ul class="p-recipe-detail__share-list">
                    <li class="p-recipe-detail__share-item">
                        <a class="p-recipe-detail__share-link p-recipe-detail__share-link--x" href="https://twitter.com/intent/tweet?url=<?php echo esc_attr($recipe_share_url); ?>&text=<?php echo esc_attr($recipe_share_text); ?>" target="_blank" rel="noopener noreferrer" aria-label="X&#12391;&#12471;&#12455;&#12450;&#12377;&#12427;"></a>
                    </li>
                    <li class="p-recipe-detail__share-item">
                        <a class="p-recipe-detail__share-link p-recipe-detail__share-link--instagram" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram&#12434;&#38283;&#12367;"></a>
                    </li>
                    <li class="p-recipe-detail__share-item">
                        <a class="p-recipe-detail__share-link p-recipe-detail__share-link--facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr($recipe_share_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook&#12391;&#12471;&#12455;&#12450;&#12377;&#12427;"></a>
                    </li>
                </ul>
            </div>
            <div class="p-recipe-detail__actions-divider" aria-hidden="true"></div>
            <nav class="p-recipe-detail__pager" aria-label="&#21069;&#24460;&#12398;&#12524;&#12471;&#12500;">
                <?php if ($recipe_previous_post) : ?>
                    <a class="c-pop p-recipe-detail__pager-arrow p-recipe-detail__pager-arrow--prev" href="<?php echo esc_url(get_permalink($recipe_previous_post)); ?>" aria-label="&#21069;&#12398;&#12524;&#12471;&#12500;&#12408;">
                        <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                    </a>
                <?php else : ?>
                    <span class="p-recipe-detail__pager-arrow p-recipe-detail__pager-arrow--prev is-disabled" aria-disabled="true">
                        <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                    </span>
                <?php endif; ?>
                <a class="c-pop p-recipe-detail__back-link" href="<?php echo esc_url($recipe_archive_url); ?>">&#19968;&#35239;&#12408;&#25147;&#12427;</a>
                <?php if ($recipe_next_post) : ?>
                    <a class="c-pop p-recipe-detail__pager-arrow p-recipe-detail__pager-arrow--next" href="<?php echo esc_url(get_permalink($recipe_next_post)); ?>" aria-label="&#27425;&#12398;&#12524;&#12471;&#12500;&#12408;">
                        <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                    </a>
                <?php else : ?>
                    <span class="p-recipe-detail__pager-arrow p-recipe-detail__pager-arrow--next is-disabled" aria-disabled="true">
                        <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                    </span>
                <?php endif; ?>
            </nav>
        </section>
    </article>

    <?php if (!empty($related_recipe_items)) : ?>
        <section class="p-recipe-archive__list" aria-labelledby="recipe-detail-related-title">
            <div class="p-recipe-archive__decorations" aria-hidden="true">
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--scale"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--cup"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--spoon"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--bowl"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--glove"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--cutter"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--cutter-medium"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--cutter-small"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--ladle"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--apron"></span>
                <span class="p-recipe-archive__decoration p-recipe-archive__decoration--tools"></span>
            </div>
            <h2 id="recipe-detail-related-title" class="p-recipe-archive__list-title">&#38306;&#36899;&#12524;&#12471;&#12500;</h2>
            <div class="c-recipe-card-wrapper p-recipe-archive__card-list js-recipe-detail-related-list">
                <?php foreach ($related_recipe_items as $related_index => $related_recipe_item) : ?>
                    <?php
                    $related_recipe_title = $related_recipe_item['title'] ?? '';
                    $related_recipe_url = $related_recipe_item['permalink'] ?? '#';
                    $related_recipe_image = $related_recipe_item['image'] ?? null;
                    $related_recipe_image_url = !empty($related_recipe_image['url']) ? $related_recipe_image['url'] : $theme_uri . '/img/component/no-image.png';
                    $related_recipe_image_alt = !empty($related_recipe_image['alt']) ? $related_recipe_image['alt'] : $related_recipe_title;
                    $related_recipe_category = foods_recipe_detail_get_primary_term_name($related_recipe_item['terms']['recipe_categories'] ?? []);
                    if ($related_recipe_category === '') {
                        $related_recipe_category = foods_recipe_detail_get_primary_term_name($related_recipe_item['terms']['categories'] ?? []);
                    }
                    $related_recipe_time = $related_recipe_item['cooking_time'] ?? '';
                    $related_recipe_datetime = foods_recipe_detail_format_duration_datetime($related_recipe_time);
                    ?>
                    <a href="<?php echo esc_url($related_recipe_url); ?>" class="c-recipe-card-wrapper-link p-recipe-archive__card-link js-recipe-detail-related-card" data-related-index="<?php echo esc_attr($related_index); ?>" <?php echo $related_index < 3 ? '' : 'hidden'; ?>>
                        <article class="c-recipe-card p-recipe-archive__card">
                            <p class="c-recipe-card__title"><?php echo esc_html($related_recipe_title); ?></p>
                            <?php if ($related_recipe_category !== '') : ?>
                                <p class="c-recipe-card__category"><?php echo esc_html($related_recipe_category); ?></p>
                            <?php endif; ?>
                            <img class="c-recipe-card__img" src="<?php echo esc_url($related_recipe_image_url); ?>" alt="<?php echo esc_attr($related_recipe_image_alt); ?>">
                            <?php if ($related_recipe_time !== '') : ?>
                                <time class="c-recipe-card__time"<?php echo $related_recipe_datetime !== '' ? ' datetime="' . esc_attr($related_recipe_datetime) . '"' : ''; ?>>
                                    <svg class="c-recipe-card__clock" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                        <path d="M18.1919 6.55391L18.3109 6.41125L18.3449 6.43643C18.532 6.58748 18.7787 6.65461 19.0168 6.62944C19.2549 6.60426 19.476 6.48678 19.6291 6.30216L19.9268 5.94132C20.0799 5.7567 20.1479 5.52173 20.1224 5.27838C20.0969 5.03502 19.9778 4.82522 19.7907 4.67417L18.0558 3.29794C17.8687 3.14688 17.6306 3.07975 17.3839 3.10493C17.1373 3.1301 16.9247 3.24759 16.7716 3.4322L16.4825 3.79305C16.3294 3.97766 16.2613 4.21263 16.2869 4.45599C16.3124 4.66578 16.4059 4.8504 16.5505 5.00145L16.4655 5.10215C15.3003 4.31333 13.8801 3.7427 12.4513 3.47416V2.94548H12.749C13.2593 2.94548 13.6675 2.54268 13.676 2.03079V0.948261C13.6675 0.427976 13.2253 0 12.6895 0H8.46274C7.92696 0 7.48473 0.436368 7.48473 0.973436V2.04757C7.48473 2.55107 7.90145 2.96227 8.41172 2.96227H8.70937V3.49094C7.32314 3.75109 5.96243 4.28816 4.83983 5.00145C4.94189 4.86718 5.00992 4.71613 5.02693 4.5483C5.05245 4.31333 4.98441 4.07836 4.83133 3.88535L4.53367 3.52451C4.38059 3.33989 4.16798 3.22241 3.91285 3.19724C3.67472 3.17206 3.4366 3.24759 3.2495 3.39024L1.50608 4.77487C1.31898 4.92592 1.19992 5.13572 1.1744 5.37908C1.14889 5.61404 1.21693 5.84901 1.37001 6.04202L1.66766 6.40286C1.82074 6.58748 2.04186 6.70496 2.27999 6.73853C2.47559 6.75531 2.6797 6.71335 2.84979 6.61265C1.03833 8.51757 0.0348037 10.9847 0.000785774 13.5526C-0.0332321 16.3302 1.03833 18.9484 3.01137 20.9373C4.97591 22.9177 7.60379 24.0254 10.4103 24.059H10.5293C16.1678 24.059 20.7857 19.7037 21.0409 14.1484C21.1684 11.3036 20.1564 8.60988 18.1919 6.55391ZM4.63573 7.85462C6.20906 6.30216 8.30116 5.44621 10.5293 5.44621C12.7575 5.44621 14.8496 6.30216 16.4229 7.85462C17.9963 9.40709 18.8637 11.4714 18.8637 13.6701C18.8637 15.8687 17.9963 17.933 16.4229 19.4855C14.8496 21.038 12.7575 21.8939 10.5293 21.8939C8.30116 21.8939 6.20906 21.038 4.63573 19.4855C3.0624 17.933 2.19494 15.8687 2.19494 13.6701C2.19494 11.4714 3.0624 9.40709 4.63573 7.85462Z" fill="#222222"/>
                                    </svg>
                                    <?php echo esc_html($related_recipe_time); ?>
                                </time>
                            <?php endif; ?>
                        </article>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php if (count($related_recipe_items) > 3) : ?>
                <div class="p-recipe-detail__related-pager" aria-label="&#38306;&#36899;&#12524;&#12471;&#12500;&#12398;&#12506;&#12540;&#12472;&#36865;&#12426;">
                    <button class="p-recipe-detail__related-pager-button p-recipe-detail__related-pager-button--prev js-recipe-detail-related-prev" type="button" aria-label="&#21069;&#12398;&#38306;&#36899;&#12524;&#12471;&#12500;&#12408;">
                        <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                    </button>
                    <div class="p-single-shop__slider-dots p-recipe-detail__related-dots js-recipe-detail-related-dots">
                        <?php for ($related_page_index = 0; $related_page_index < (int) ceil(count($related_recipe_items) / 3); $related_page_index++) : ?>
                            <button
                                type="button"
                                class="<?php echo $related_page_index === 0 ? 'is-active' : ''; ?>"
                                data-related-page="<?php echo esc_attr($related_page_index); ?>"
                                aria-label="&#38306;&#36899;&#12524;&#12471;&#12500;<?php echo esc_attr($related_page_index + 1); ?>&#12506;&#12540;&#12472;&#30446;&#12434;&#34920;&#31034;"
                                aria-pressed="<?php echo $related_page_index === 0 ? 'true' : 'false'; ?>"
                            ></button>
                        <?php endfor; ?>
                    </div>
                    <button class="p-recipe-detail__related-pager-button p-recipe-detail__related-pager-button--next js-recipe-detail-related-next" type="button" aria-label="&#27425;&#12398;&#38306;&#36899;&#12524;&#12471;&#12500;&#12408;">
                        <img src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                    </button>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<script>
    window.foodsRecipeDetailItem = <?php echo wp_json_encode($recipe_detail_item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>

<?php
get_footer();

