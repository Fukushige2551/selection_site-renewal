<?php
$config = isset($args['config']) && is_array($args['config']) ? $args['config'] : [];

if (!$config || empty($config['modifier'])) {
    return;
}

$block_class = 'p-page-select-detail';
$modifier_class = $block_class . '--' . sanitize_html_class($config['modifier']);
$about = isset($config['about']) && is_array($config['about']) ? $config['about'] : [];
$news = isset($config['news']) && is_array($config['news']) ? $config['news'] : [];
$theme = isset($config['theme']) && is_array($config['theme']) ? $config['theme'] : [];
$css_variables = [];

if (!empty($theme['message_background'])) {
    $css_variables[] = '--select-message-bg:' . $theme['message_background'];
}

foreach ([
    'background' => '--select-about-background',
    'title_background' => '--select-about-title-background',
] as $asset_key => $variable_name) {
    if (!empty($about[$asset_key])) {
        $css_variables[] = $variable_name . ':url("' . foods_get_select_detail_asset_url($config, $about[$asset_key]) . '")';
    }
}

foreach ([
    'background_sp' => '--select-news-background-sp',
    'background_pc' => '--select-news-background-pc',
] as $asset_key => $variable_name) {
    if (!empty($news[$asset_key])) {
        $css_variables[] = $variable_name . ':url("' . foods_get_select_detail_asset_url($config, $news[$asset_key]) . '")';
    }
}

foreach ([
    'wave_sp_top' => '--select-about-wave-sp-top',
    'wave_sp_bottom' => '--select-about-wave-sp-bottom',
    'wave_pc_top' => '--select-about-wave-pc-top',
    'wave_pc_bottom' => '--select-about-wave-pc-bottom',
] as $asset_key => $variable_name) {
    if (!empty($about[$asset_key])) {
        $css_variables[] = $variable_name . ':url("' . foods_get_select_detail_asset_url($config, $about[$asset_key]) . '")';
    }
}

$css_variables = implode(';', $css_variables);

get_header();
?>
<main id="page-select-<?php echo esc_attr($config['modifier']); ?>" class="<?php echo esc_attr($block_class . ' ' . $modifier_class); ?> c-main"<?php echo $css_variables ? ' style="' . esc_attr($css_variables) . '"' : ''; ?>>
    <?php
    get_template_part('template-parts/select-detail/hero', null, [
        'config' => $config,
        'block_class' => $block_class,
    ]);
    if (!empty($config['message'])) {
        get_template_part('template-parts/select-detail/message', null, [
            'config' => $config,
            'block_class' => $block_class,
        ]);
    }
    if (!empty($config['about']['sections'])) {
        get_template_part('template-parts/select-detail/about', null, [
            'config' => $config,
            'block_class' => $block_class,
        ]);
    }
    if (!empty($config['news'])) {
        get_template_part('template-parts/select-detail/news', null, [
            'config' => $config,
            'block_class' => $block_class,
        ]);
    }
    ?>
</main>
<?php get_footer(); ?>
