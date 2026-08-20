<?php
$config = $args['config'];
$block_class = $args['block_class'];
$about = $config['about'];
$title_sp = $about['decorations']['title_sp'] ?? null;
$title_pc = $about['decorations']['title_pc'] ?? null;
$background_decorations = isset($about['background_decorations']) && is_array($about['background_decorations'])
    ? $about['background_decorations']
    : [];
$gallery_decorations = array_values(array_filter(
    $background_decorations,
    static function ($decoration) {
        return isset($decoration['key']) && in_array($decoration['key'], ['wave', 'fisherman'], true);
    }
));
$anchored_decorations = [];
foreach ($background_decorations as $decoration) {
    if (!empty($decoration['key'])) {
        $anchored_decorations[$decoration['key']] = $decoration;
    }
}
?>
<section class="<?php echo esc_attr($block_class); ?>__about">
    <div class="<?php echo esc_attr($block_class); ?>__about__bg">
        <?php foreach ($background_decorations as $decoration) : ?>
            <?php if ('wave' === ($decoration['key'] ?? '') && !empty($decoration['src'])) : ?>
                <img class="<?php echo esc_attr($block_class . '__about__backgroundDecoration ' . $block_class . '__about__backgroundDecoration--wave ' . $block_class . '__about__backgroundDecoration--laptopAnchor'); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt'] ?? ''); ?>">
            <?php endif; ?>
        <?php endforeach; ?>

        <?php foreach ($background_decorations as $decoration) : ?>
            <?php if (!empty($decoration['key']) && 'wave' !== $decoration['key'] && empty($decoration['anchored_only']) && !empty($decoration['src'])) : ?>
                <?php $anchor_class = in_array($decoration['key'], ['fisherman', 'cat-and-worker', 'calico-cat', 'worker'], true) ? ' ' . $block_class . '__about__backgroundDecoration--legacyAnchor' : ''; ?>
                <img class="<?php echo esc_attr($block_class . '__about__backgroundDecoration ' . $block_class . '__about__backgroundDecoration--' . sanitize_html_class($decoration['key']) . $anchor_class); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt'] ?? ''); ?>">
            <?php endif; ?>
        <?php endforeach; ?>

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
                <?php if ('three' === ($section['gallery_variant'] ?? '')) : ?>
                    <?php foreach ($gallery_decorations as $decoration) : ?>
                        <img class="<?php echo esc_attr($block_class . '__about__backgroundDecoration ' . $block_class . '__about__backgroundDecoration--' . sanitize_html_class($decoration['key']) . ' ' . $block_class . '__about__backgroundDecoration--desktopAnchor'); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt'] ?? ''); ?>">
                    <?php endforeach; ?>
                <?php endif; ?>
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
                        <?php if (!empty($decoration['src_pc'])) : ?><picture><?php endif; ?>
                            <?php if (!empty($decoration['src_pc'])) : ?>
                                <source media="(min-width: 1024px)" srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src_pc'])); ?>">
                            <?php endif; ?>
                            <img class="<?php echo esc_attr($block_class . '__about__content--' . $decoration['key']); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                        <?php if (!empty($decoration['src_pc'])) : ?></picture><?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php $main_image = $section['main_image']; ?>
                <div class="<?php echo esc_attr($block_class); ?>__about__content--mainImageWrap">
                    <picture>
                        <?php if (!empty($main_image['webp'])) : ?>
                            <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $main_image['webp'])); ?>" type="image/webp">
                        <?php endif; ?>
                        <img class="<?php echo esc_attr($block_class); ?>__about__content--image" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $main_image['src'])); ?>" alt="<?php echo esc_attr($main_image['alt']); ?>">
                    </picture>
                    <?php foreach ($decorations as $decoration) : ?>
                        <?php if ('main_image' === $decoration['placement']) : ?>
                            <img class="<?php echo esc_attr($block_class . '__about__content--' . $decoration['key']); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
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
                    <?php
                    $secondary_anchor_keys = $secondary_image['decoration_anchors'] ?? [];
                    $secondary_anchor_keys = is_array($secondary_anchor_keys) ? $secondary_anchor_keys : [$secondary_anchor_keys];
                    $secondary_image_decorations = array_values(array_filter(array_map(
                        static function ($anchor_key) use ($anchored_decorations) {
                            return $anchored_decorations[$anchor_key] ?? null;
                        },
                        $secondary_anchor_keys
                    )));
                    $secondary_inline_decorations = array_values(array_filter(
                        $decorations,
                        static function ($decoration) {
                            return 'secondary_image' === ($decoration['placement'] ?? '');
                        }
                    ));
                    ?>
                    <?php if ($secondary_image_decorations) : ?>
                        <div class="<?php echo esc_attr($block_class); ?>__about__content--secondaryImageAnchor">
                    <?php endif; ?>
                    <?php if ($secondary_inline_decorations) : ?>
                        <div class="<?php echo esc_attr($block_class); ?>__about__content--secondaryImageWrap">
                    <?php endif; ?>
                    <picture>
                        <?php if (!empty($secondary_image['webp'])) : ?>
                            <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $secondary_image['webp'])); ?>" type="image/webp">
                        <?php endif; ?>
                        <img class="<?php echo esc_attr($block_class); ?>__about__content--image" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $secondary_image['src'])); ?>" alt="<?php echo esc_attr($secondary_image['alt']); ?>">
                    </picture>
                    <?php foreach ($secondary_inline_decorations as $decoration) : ?>
                        <img class="<?php echo esc_attr($block_class . '__about__content--' . sanitize_html_class($decoration['key'])); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt'] ?? ''); ?>">
                    <?php endforeach; ?>
                    <?php if ($secondary_inline_decorations) : ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($secondary_image_decorations) : ?>
                        <?php foreach ($secondary_image_decorations as $anchor_decoration) : ?>
                            <img class="<?php echo esc_attr($block_class . '__about__backgroundDecoration ' . $block_class . '__about__backgroundDecoration--' . sanitize_html_class($anchor_decoration['key']) . ' ' . $block_class . '__about__backgroundDecoration--desktopAnchor'); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $anchor_decoration['src'])); ?>" alt="<?php echo esc_attr($anchor_decoration['alt'] ?? ''); ?>">
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
                        <?php
                        $anchor_keys = $gallery_image['decoration_anchors'] ?? [];
                        $anchor_keys = is_array($anchor_keys) ? $anchor_keys : [$anchor_keys];
                        $gallery_anchor_decorations = array_values(array_filter(array_map(
                            static function ($anchor_key) use ($anchored_decorations) {
                                return $anchored_decorations[$anchor_key] ?? null;
                            },
                            $anchor_keys
                        )));
                        ?>
                        <?php if ($gallery_anchor_decorations) : ?>
                            <div class="<?php echo esc_attr($block_class); ?>__about__content--catWorkerAnchor">
                        <?php endif; ?>
                        <picture>
                            <?php if (!empty($gallery_image['webp'])) : ?>
                                <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $gallery_image['webp'])); ?>" type="image/webp">
                            <?php endif; ?>
                            <img class="<?php echo esc_attr($block_class); ?>__about__content--imgSmall" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $gallery_image['src'])); ?>" alt="<?php echo esc_attr($gallery_image['alt']); ?>">
                        </picture>
                        <?php if ($gallery_anchor_decorations) : ?>
                            <?php foreach ($gallery_anchor_decorations as $anchor_decoration) : ?>
                                <img class="<?php echo esc_attr($block_class . '__about__backgroundDecoration ' . $block_class . '__about__backgroundDecoration--' . sanitize_html_class($anchor_decoration['key']) . ' ' . $block_class . '__about__backgroundDecoration--desktopAnchor'); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $anchor_decoration['src'])); ?>" alt="<?php echo esc_attr($anchor_decoration['alt'] ?? ''); ?>">
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($decorations as $decoration) : ?>
                        <?php if ('gallery_after' === $decoration['placement']) : ?>
                            <?php if (!empty($decoration['src_pc'])) : ?><picture><?php endif; ?>
                                <?php if (!empty($decoration['src_pc'])) : ?>
                                    <source media="(min-width: 1024px)" srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src_pc'])); ?>">
                                <?php endif; ?>
                                <img class="<?php echo esc_attr($block_class . '__about__content--' . $decoration['key'] . ' ' . $block_class . '__about__title--decoration7'); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                            <?php if (!empty($decoration['src_pc'])) : ?></picture><?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
