<?php
$config = isset($args['config']) && is_array($args['config']) ? $args['config'] : [];

if (!$config || empty($config['modifier'])) {
    return;
}

$block_class = 'p-page-select-detail';
$modifier_class = $block_class . '--' . sanitize_html_class($config['modifier']);
$about = $config['about'];
$news = $config['news'];
$theme = $config['theme'];
$css_variables = implode(';', [
    '--select-message-bg:' . $theme['message_background'],
    '--select-message-background-pc:url("' . foods_get_select_detail_asset_url($config, $config['message']['background_pc']) . '")',
    '--select-about-background:url("' . foods_get_select_detail_asset_url($config, $about['background']) . '")',
    '--select-about-wave-sp-top:url("' . foods_get_select_detail_asset_url($config, $about['wave_sp_top']) . '")',
    '--select-about-wave-sp-bottom:url("' . foods_get_select_detail_asset_url($config, $about['wave_sp_bottom']) . '")',
    '--select-about-wave-pc-top:url("' . foods_get_select_detail_asset_url($config, $about['wave_pc_top']) . '")',
    '--select-about-wave-pc-bottom:url("' . foods_get_select_detail_asset_url($config, $about['wave_pc_bottom']) . '")',
    '--select-about-title-background:url("' . foods_get_select_detail_asset_url($config, $about['title_background']) . '")',
    '--select-news-background-sp:url("' . foods_get_select_detail_asset_url($config, $news['background_sp']) . '")',
    '--select-news-background-pc:url("' . foods_get_select_detail_asset_url($config, $news['background_pc']) . '")',
]);

get_header();
?>
<main id="page-select-<?php echo esc_attr($config['modifier']); ?>" class="<?php echo esc_attr($block_class . ' ' . $modifier_class); ?> c-main" style="<?php echo esc_attr($css_variables); ?>">
    <?php
    get_template_part('template-parts/select-detail/hero', null, [
        'config' => $config,
        'block_class' => $block_class,
    ]);
    get_template_part('template-parts/select-detail/message', null, [
        'config' => $config,
        'block_class' => $block_class,
    ]);
    get_template_part('template-parts/select-detail/about', null, [
        'config' => $config,
        'block_class' => $block_class,
    ]);
    get_template_part('template-parts/select-detail/news', null, [
        'config' => $config,
        'block_class' => $block_class,
    ]);
    ?>
</main>
<?php get_footer(); ?>
