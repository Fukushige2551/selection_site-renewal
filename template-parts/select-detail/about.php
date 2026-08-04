<?php
$config = $args['config'];
$block_class = $args['block_class'];
$about = $config['about'];
$title_sp = $about['decorations']['title_sp'];
$title_pc = $about['decorations']['title_pc'];
?>
<section class="<?php echo esc_attr($block_class); ?>__about">
    <div class="<?php echo esc_attr($block_class); ?>__about__bg">
        <div class="<?php echo esc_attr($block_class); ?>__about__title--wrap">
            <h2 class="<?php echo esc_attr($block_class); ?>__about__title--text"><span><?php echo esc_html($about['title']); ?></span></h2>
            <img class="<?php echo esc_attr($block_class); ?>__about__title--decoration1" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $title_sp['src'])); ?>" alt="<?php echo esc_attr($title_sp['alt']); ?>">
        </div>

        <?php foreach ($about['sections'] as $section_index => $section) : ?>
            <div class="<?php echo esc_attr($block_class); ?>__about__content">
                <h3 class="<?php echo esc_attr($block_class); ?>__about__content--title"><?php echo wp_kses_post($section['title']); ?></h3>

                <?php if (0 === $section_index) : ?>
                    <img class="<?php echo esc_attr($block_class); ?>__about__title--decorationPC" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $title_pc['src'])); ?>" alt="<?php echo esc_attr($title_pc['alt']); ?>">
                <?php endif; ?>

                <?php foreach ($section['decorations'] as $decoration) : ?>
                    <?php if ('content' === $decoration['placement']) : ?>
                        <img class="<?php echo esc_attr($block_class . '__about__content--' . $decoration['key']); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php $main_image = $section['main_image']; ?>
                <picture>
                    <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $main_image['webp'])); ?>" type="image/webp">
                    <img class="<?php echo esc_attr($block_class); ?>__about__content--image" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $main_image['src'])); ?>" alt="<?php echo esc_attr($main_image['alt']); ?>">
                </picture>
                <p><?php echo esc_html($section['text']); ?></p>

                <?php if (!empty($section['sub_title'])) : ?>
                    <h3 class="<?php echo esc_attr($block_class); ?>__about__content--sub"><?php echo esc_html($section['sub_title']); ?></h3>
                <?php endif; ?>

                <?php if (!empty($section['secondary_image'])) : ?>
                    <?php $secondary_image = $section['secondary_image']; ?>
                    <picture>
                        <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $secondary_image['webp'])); ?>" type="image/webp">
                        <img class="<?php echo esc_attr($block_class); ?>__about__content--image" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $secondary_image['src'])); ?>" alt="<?php echo esc_attr($secondary_image['alt']); ?>">
                    </picture>
                    <p><?php echo esc_html($section['secondary_text']); ?></p>
                <?php endif; ?>

                <div class="<?php echo esc_attr($block_class); ?>__about__content--imgWrap">
                    <?php foreach ($section['decorations'] as $decoration) : ?>
                        <?php if ('gallery_before' === $decoration['placement']) : ?>
                            <img class="<?php echo esc_attr($block_class . '__about__content--' . $decoration['key']); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($section['gallery'] as $gallery_image) : ?>
                        <picture>
                            <source srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $gallery_image['webp'])); ?>" type="image/webp">
                            <img class="<?php echo esc_attr($block_class); ?>__about__content--imgSmall" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $gallery_image['src'])); ?>" alt="<?php echo esc_attr($gallery_image['alt']); ?>">
                        </picture>
                    <?php endforeach; ?>

                    <?php foreach ($section['decorations'] as $decoration) : ?>
                        <?php if ('gallery_after' === $decoration['placement']) : ?>
                            <img class="<?php echo esc_attr($block_class); ?>__about__title--decoration7" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt']); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
