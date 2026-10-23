<?php
/* Template Name: セレクションのこだわり */
get_header();
$select_cards = json_decode(file_get_contents(get_template_directory() . '/inc/select-top-design.json'), true);
$select_labels = ['お店づくりのこだわり', 'お肉', 'お魚', 'お野菜・果物', 'お米', 'お惣菜', '和日配', '加工食品', 'お菓子', 'お酒'];
$select_asset = get_template_directory_uri() . '/img/page/page-select/';
$select_page_slugs = ['vegetables-fruit' => 'fruit-vegetables'];
?>
<main id="page-select" class="p-page-select c-main">
    <?php ob_start(); ?>
<nav class="c-breadcrumb" aria-label="パンくずリスト">
        <a class="c-breadcrumb__link" href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <img class="c-breadcrumb__arrow" src="<?php echo esc_url(get_template_directory_uri() . '/img/component/svg/icon_breadcrumb.svg'); ?>" alt="">
        <span class="c-breadcrumb__current">セレクションのこだわり</span>
    </nav>
<?php echo foods_schema_breadcrumb_html(ob_get_clean()); ?>
    <section class="p-page-select__hero">
        <h1 class="c-section__title">セレクションのこだわり</h1>
        <p class="c-section__title--sub">Commitment</p>
        <picture class="p-page-select__hero-picture">
            <source media="(min-width: 768px)" srcset="<?php echo esc_url($select_asset . 'svg/img_selectTop-pc.svg'); ?>">
            <img src="<?php echo esc_url($select_asset . 'svg/img_selectTop-sp.svg'); ?>" alt="家族で食事を楽しむ様子">
        </picture>
        <div class="p-page-select__message-title">
            <p>いい商品を皆様に</p>
            <img src="<?php echo esc_url($select_asset . 'svg/img_select-message-underline.svg'); ?>" alt="">
        </div>
        <p class="p-page-select__hero__text">
            <span>Selectionは“選ぶ”を仕事にしたスーパー。</span>
            <span>旬・鮮度・食べ方まで、売場でわかるように</span>
            <span>整えて、毎日の定番にちょっとした発見を<br class="select-sp-break">増やしていきます。</span>
            <span>バイヤーが全国から“良いもの”を選び抜き、</span>
            <span>店の現場で鮮度とおいしさを仕上げる<br class="select-sp-break">食の専門店です。</span>
            <span>毎日の定番から週末のごちそうまで、<br class="select-sp-break">「買って良かった」と思える理由を、</span>
            <span>売場の一つひとつに込めています。</span>
        </p>
    </section>
    <section class="p-page-select__categories" aria-labelledby="select-category-title">
        <div class="p-page-select__category-background" aria-hidden="true"></div>
        <img class="p-page-select__mark" src="<?php echo esc_url($select_asset . 'png/img_select-stamp.png'); ?>" alt="">
        <h2 id="select-category-title" class="p-page-select__category-title">こだわりを選択</h2>
        <div class="p-page-select__links">
        <?php foreach ($select_cards as $index => $card): ?>
            <a class="p-page-select__card p-page-select__card--<?php echo esc_attr($card['slug']); ?>" href="<?php echo esc_url(home_url('/select/' . ($select_page_slugs[$card['slug']] ?? $card['slug']) . '/')); ?>" aria-label="<?php echo esc_attr($select_labels[$index]); ?>">
            <?php foreach (['sp', 'pc'] as $layout):
                $design = $card[$layout]; ?>
                <span class="p-page-select__card-art p-page-select__card-art--<?php echo esc_attr($layout); ?>" aria-hidden="true" style="width:<?php echo esc_attr($design['width']); ?>px;height:<?php echo esc_attr($design['height']); ?>px">
                <?php foreach ($design['layers'] as $layer):
                    $matrix = implode(',', array_map(static fn($number) => round($number, 5), $layer['matrix']));
                    $style = 'width:' . $layer['w'] . 'px;height:' . $layer['h'] . 'px;transform:matrix(' . $matrix . ');';
                    if (isset($layer['text'])):
                        $style .= 'font-size:' . $layer['size'] . 'px;letter-spacing:' . $layer['spacing'] . 'px;text-align:' . $layer['align'] . ';';
                ?>
                    <span class="p-page-select__card-label" style="<?php echo esc_attr($style); ?>"><?php echo nl2br(esc_html($layer['text'])); ?></span>
                    <?php else: ?>
                    <img class="p-page-select__card-layer" src="<?php echo esc_url($select_asset . $layer['file']); ?>" alt="" style="<?php echo esc_attr($style); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
                </span>
            <?php endforeach; ?>
            </a>
        <?php endforeach; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>

