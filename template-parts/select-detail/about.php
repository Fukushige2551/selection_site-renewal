<?php
$config = $args['config'];
$block_class = $args['block_class'];
$about = $config['about'];
$title_sp = $about['decorations']['title_sp'] ?? null;
$title_pc = $about['decorations']['title_pc'] ?? null;
?>
<section class="<?php echo esc_attr($block_class); ?>__about">
    <div class="<?php echo esc_attr($block_class); ?>__about__bg">
        <div class="<?php echo esc_attr($block_class); ?>__about__title--wrap">
            <?php if (!empty($about['pickup'])) : ?>
                <img class="<?php echo esc_attr($block_class); ?>__about__title--pickup" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $about['pickup'])); ?>" alt="PICK UP">
            <?php endif; ?>
            <h2 class="<?php echo esc_attr($block_class); ?>__about__title--text"><span><?php echo esc_html($about['title']); ?></span></h2>
            <?php if (is_array($title_sp) && !empty($title_sp['src'])) : ?>
                <img class="<?php echo esc_attr($block_class); ?>__about__title--decoration1" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $title_sp['src'])); ?>" alt="<?php echo esc_attr($title_sp['alt'] ?? ''); ?>">
            <?php endif; ?>
        </div>

        <?php foreach ($about['sections'] as $section_index => $section) : ?>
            <?php
            $section_classes = [$block_class . '__about__content'];
            if (!empty($section['gallery_variant'])) {
                $section_classes[] = $block_class . '__about__content--gallery-' . sanitize_html_class($section['gallery_variant']);
            }
            $decorations = isset($section['decorations']) && is_array($section['decorations']) ? $section['decorations'] : [];
            $text_blocks = isset($section['text_blocks']) && is_array($section['text_blocks']) ? $section['text_blocks'] : [($section['text'] ?? '')];
            ?>
            <div class="<?php echo esc_attr(implode(' ', $section_classes)); ?>">
                <h3 class="<?php echo esc_attr($block_class); ?>__about__content--title">
                    <?php if (!empty($section['location'])) : ?>
                        <span class="<?php echo esc_attr($block_class); ?>__about__content--location"><?php echo esc_html($section['location']); ?></span>
                    <?php endif; ?>
                    <?php echo wp_kses_post($section['title']); ?>
                </h3>

                <?php if (0 === $section_index && is_array($title_pc) && !empty($title_pc['src'])) : ?>
                    <img class="<?php echo esc_attr($block_class); ?>__about__title--decorationPC" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $title_pc['src'])); ?>" alt="<?php echo esc_attr($title_pc['alt']); ?>">
                <?php endif; ?>

                <?php foreach ($decorations as $decoration) : ?>
                    <?php if ('content' === $decoration['placement']) : ?>
                        <img class="<?php echo esc_attr($block_class . '__about__content--' . $decoration['key']); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php $main_image = $section['main_image']; ?>
                <picture>
                    <?php if (!empty($main_image['webp'])) : ?>
                        <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $main_image['webp'])); ?>" type="image/webp">
                    <?php endif; ?>
                    <img class="<?php echo esc_attr($block_class); ?>__about__content--image" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $main_image['src'])); ?>" alt="<?php echo esc_attr($main_image['alt']); ?>">
                </picture>
                <div class="<?php echo esc_attr($block_class); ?>__about__content--copy">
                    <?php foreach ($text_blocks as $text_block) : ?>
                        <?php if ('' !== $text_block) : ?><p><?php echo esc_html($text_block); ?></p><?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($section['sub_title'])) : ?>
                    <h4 class="<?php echo esc_attr($block_class); ?>__about__content--sub"><?php echo wp_kses_post($section['sub_title']); ?></h4>
                <?php endif; ?>

                <?php if (!empty($section['secondary_image'])) : ?>
                    <?php $secondary_image = $section['secondary_image']; ?>
                    <picture>
                        <?php if (!empty($secondary_image['webp'])) : ?>
                            <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $secondary_image['webp'])); ?>" type="image/webp">
                        <?php endif; ?>
                        <img class="<?php echo esc_attr($block_class); ?>__about__content--image" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $secondary_image['src'])); ?>" alt="<?php echo esc_attr($secondary_image['alt']); ?>">
                    </picture>
                    <?php
                    $secondary_text_blocks = isset($section['secondary_text_blocks']) && is_array($section['secondary_text_blocks'])
                        ? $section['secondary_text_blocks']
                        : [($section['secondary_text'] ?? '')];
                    ?>
                    <div class="<?php echo esc_attr($block_class); ?>__about__content--copy">
                        <?php foreach ($secondary_text_blocks as $text_block) : ?>
                            <?php if ('' !== $text_block) : ?><p><?php echo esc_html($text_block); ?></p><?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="<?php echo esc_attr($block_class); ?>__about__content--imgWrap">
                    <?php foreach ($decorations as $decoration) : ?>
                        <?php if ('gallery_before' === $decoration['placement']) : ?>
                            <img class="<?php echo esc_attr($block_class . '__about__content--' . $decoration['key']); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($section['gallery'] as $gallery_image) : ?>
                        <picture>
                            <?php if (!empty($gallery_image['webp'])) : ?>
                                <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $gallery_image['webp'])); ?>" type="image/webp">
                            <?php endif; ?>
                            <img class="<?php echo esc_attr($block_class); ?>__about__content--imgSmall" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $gallery_image['src'])); ?>" alt="<?php echo esc_attr($gallery_image['alt']); ?>">
                        </picture>
                    <?php endforeach; ?>

                    <?php foreach ($decorations as $decoration) : ?>
                        <?php if ('gallery_after' === $decoration['placement']) : ?>
                            <img class="<?php echo esc_attr($block_class); ?>__about__title--decoration7" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
