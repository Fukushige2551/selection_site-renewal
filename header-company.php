<?php
/**
 * 企業ページ共通ヘッダー
 */
wp_enqueue_script(
    'foods-company-header',
    get_template_directory_uri() . '/src/js/header-company.js',
    [],
    filemtime(get_template_directory() . '/src/js/header-company.js'),
    true
);
// グローバルナビゲーションのリンク一覧
$company_header_links = [
    ['label' => '会社情報', 'url' => home_url('/company/')],
    ['label' => 'チラシ・店舗情報', 'url' => get_post_type_archive_link('shop')],
    ['label' => 'セレクションのこだわり', 'url' => home_url('/select/')],
    ['label' => 'レシピ', 'url' => get_post_type_archive_link('recipe')],
    ['label' => 'パート・アルバイト募集', 'url' => get_post_type_archive_link('recruit_part_time'), 'modifier' => 'recruit'],
    ['label' => 'お問い合わせ', 'url' => home_url('/contact/'), 'modifier' => 'contact'],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Noto+Serif+JP:wght@200..900&family=REM:wght@500&family=Scope+One&family=Secular+One&family=Sen&family=Ysabeau:ital,wght@0,1..1000;1,1..1000&family=Zen+Kaku+Gothic+Antique:wght@400;500;700&family=Zen+Kaku+Gothic+New:wght@400;500;700;900&family=Zen+Kurenaido&family=Zen+Maru+Gothic:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>
<body <?php body_class('has-company-header'); ?>>
<?php wp_body_open(); ?>

<!-- 企業ページ共通ヘッダー 開始 -->
<header class="l-company-header" data-company-header>
    <div class="l-company-header__bar">
        <a class="l-company-header__logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="セレクション トップページへ">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/img/header/header_logo-sp.svg'); ?>" width="123" height="61" alt="FOODS MARKET Selection">
        </a>
        <button class="l-company-header__toggle" type="button" aria-label="メニューを開く" aria-controls="company-header-navigation" aria-expanded="false" hidden>
            <span></span><span></span><span></span>
        </button>
        <nav class="l-company-header__nav" id="company-header-navigation" aria-label="グローバルナビゲーション">
            <ul class="l-company-header__links">
                <?php foreach ($company_header_links as $company_header_link) : ?>
                    <li>
                        <a class="l-company-header__link<?php echo !empty($company_header_link['modifier']) ? ' l-company-header__link--' . esc_attr($company_header_link['modifier']) : ''; ?>" href="<?php echo esc_url($company_header_link['url']); ?>">
                            <?php echo esc_html($company_header_link['label']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</header>
<!-- 企業ページ共通ヘッダー 終了 -->
