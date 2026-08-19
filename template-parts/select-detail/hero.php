<?php
$config = $args['config'];
$block_class = $args['block_class'];
$breadcrumb = $config['breadcrumb'];
$hero = $config['hero'];
?>
<nav class="c-breadcrumb" aria-label="パンくずリスト">
    <a class="c-breadcrumb__link" href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
    <img class="c-breadcrumb__arrow" src="<?php echo esc_url(get_template_directory_uri() . '/img/component/svg/icon_breadcrumb.svg'); ?>" alt="矢印">
    <a class="c-breadcrumb__link" href="<?php echo esc_url(home_url($breadcrumb['parent_url'])); ?>">
        <?php echo esc_html($breadcrumb['parent_label']); ?>
    </a>
    <img class="c-breadcrumb__arrow" src="<?php echo esc_url(get_template_directory_uri() . '/img/component/svg/icon_breadcrumb.svg'); ?>" alt="矢印">
    <span class="c-breadcrumb__current"><?php echo esc_html($config['title']); ?></span>
</nav>

<div class="<?php echo esc_attr($block_class); ?>__hero">
    <h1 class="c-section__title"><?php echo esc_html($config['title']); ?></h1>
    <div class="<?php echo esc_attr($block_class); ?>__hero__inner">
        <?php if (!empty($hero['lead'])) : ?>
            <p class="<?php echo esc_attr($block_class); ?>__hero__text <?php echo esc_attr($block_class); ?>__hero__text--sp"><?php echo wp_kses_post($hero['lead']); ?></p>
        <?php endif; ?>
        <?php if (!empty($hero['lead_pc'])) : ?>
            <p class="<?php echo esc_attr($block_class); ?>__hero__text <?php echo esc_attr($block_class); ?>__hero__text--pc"><?php echo wp_kses_post($hero['lead_pc']); ?></p>
        <?php endif; ?>
        <?php if (!empty($hero['image'])) : ?>
            <img class="<?php echo esc_attr($block_class); ?>__hero__img--top" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $hero['image'])); ?>" alt="<?php echo esc_attr($hero['image_alt'] ?? ''); ?>">
        <?php endif; ?>
    </div>
</div>
