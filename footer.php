<?php
$main_footer_page_url = static function ($template, $fallback_path) {
    $pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => $template,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    return $pages ? get_permalink($pages[0]) : home_url($fallback_path);
};

$main_footer_shops = [
    ['行徳店', 'gyoutoku'], ['花野井店', 'hananoi'], ['三郷店', 'misato'],
    ['しいの木台店', 'shiinokidai'], ['八潮店', 'yashio'], ['青葉台店', 'aobadai'],
    ['西原店', 'nishihara'], ['松戸店', 'matsudo'], ['西船橋店', 'nishifunabashi'],
    ['西新井店', 'nishiarai'],
];

$main_footer_departments = [
    ['お店づくり', $main_footer_page_url('page-select-shop.php', '/select/shop/')],
    ['お野菜・果物', $main_footer_page_url('page-select-vegetables-fruit.php', '/select/vegetables-fruit/')],
    ['お肉', $main_footer_page_url('page-select-meat.php', '/select/meat/')],
    ['お魚', $main_footer_page_url('page-select-fish.php', '/select/fish/')],
    ['お菓子', $main_footer_page_url('page-select-sweets.php', '/select/sweets/')],
    ['お米', $main_footer_page_url('page-select-rice.php', '/select/rice/')],
    ['乳製品', ''],
    ['和日配', $main_footer_page_url('page-select-washoku-daily.php', '/select/washoku-daily/')],
    ['お惣菜', $main_footer_page_url('page-select-deli.php', '/select/deli/')],
    ['加工食品', $main_footer_page_url('page-select-foods.php', '/select/foods/')],
    ['お酒', $main_footer_page_url('page-select-alcohol.php', '/select/alcohol/')],
];

$main_footer_link = static function ($label, $url) {
    if ($url) {
        echo '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
    } else {
        echo '<span>' . esc_html($label) . '</span>';
    }
};
?>
<footer class="l-footer">
    <picture class="l-footer__logo l-footer__logo--sp">
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <img class="l-footer__logo__img" src="<?php echo esc_url(get_template_directory_uri() . '/img/component/selection-logo.svg'); ?>" alt="FOODS MARKET Selection" width="311" height="189">
        </a>
    </picture>
    <picture class="l-footer__logo l-footer__logo--pc">
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <img class="l-footer__logo__img" src="<?php echo esc_url(get_template_directory_uri() . '/img/component/selection-logo.svg'); ?>" alt="FOODS MARKET Selection" width="311" height="189">
        </a>
    </picture>

    <!-- ナビゲーション -->
    <nav class="l-footer__nav" aria-label="フッターナビゲーション">
        <details class="l-footer__nav__group l-footer__nav__group--shops">
            <summary class="l-footer__nav__item"><a href="<?php echo esc_url(get_post_type_archive_link('shop')); ?>">チラシ・店舗情報</a><span aria-hidden="true">＋</span></summary>
            <ul class="l-footer__nav__list shop-info">
                <?php foreach ($main_footer_shops as [$name, $slug]) : ?>
                    <li class="l-footer__nav__list__item"><a href="<?php echo esc_url(home_url('/shop/' . $slug . '/')); ?>"><?php echo esc_html($name); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </details>

        <div class="l-footer__nav__group l-footer__nav__group--news"><a class="l-footer__nav__item" href="<?php echo esc_url(get_post_type_archive_link('news')); ?>">新着情報</a></div>

        <details class="l-footer__nav__group l-footer__nav__group--select">
            <summary class="l-footer__nav__item"><a href="<?php echo esc_url(home_url('/select/')); ?>">セレクションのこだわり</a><span aria-hidden="true">＋</span></summary>
            <ul class="l-footer__nav__list select-info">
                <?php foreach ($main_footer_departments as [$name, $url]) : ?>
                    <li class="l-footer__nav__list__item"><?php $main_footer_link($name, $url); ?></li>
                <?php endforeach; ?>
            </ul>
        </details>

        <details class="l-footer__nav__group l-footer__nav__group--company">
            <summary class="l-footer__nav__item"><a href="<?php echo esc_url(home_url('/company/')); ?>">会社情報</a><span aria-hidden="true">＋</span></summary>
            <ul class="l-footer__nav__list company-info">
                <li class="l-footer__nav__list__item"><a href="<?php echo esc_url($main_footer_page_url('page-company-about.php', '/company/about/')); ?>">会社概要</a></li>
                <li class="l-footer__nav__list__item"><a href="<?php echo esc_url($main_footer_page_url('page-recruit.php', '/recruit/')); ?>">採用情報（新卒・中途）</a></li>
                <li class="l-footer__nav__list__item"><a href="<?php echo esc_url($main_footer_page_url('page-company-business.php', '/company/business/')); ?>">企業の方</a></li>
            </ul>
        </details>

        <div class="l-footer__nav__group l-footer__nav__group--part-time"><a class="l-footer__nav__item" href="<?php echo esc_url($main_footer_page_url('page-job.php', '/job/')); ?>">パート・アルバイト募集</a></div>
        <div class="l-footer__nav__group l-footer__nav__group--recipe"><a class="l-footer__nav__item" href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>">レシピ</a></div>
        <div class="l-footer__nav__group l-footer__nav__group--online"><a class="l-footer__nav__item" href="https://foods-selection.shops.jp/" target="_blank" rel="noopener noreferrer">オンラインショップ</a></div>
        <div class="l-footer__nav__group l-footer__nav__group--privacy"><a class="l-footer__nav__item" href="<?php echo esc_url(home_url('/privacy/')); ?>">プライバシーポリシー</a></div>
        <div class="l-footer__nav__group l-footer__nav__group--contact"><a class="l-footer__nav__item" href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a></div>
    </nav>

    <!-- SNS -->
    <ul class="l-footer__sns">
        <?php /* TODO: 公式YouTubeチャンネル開設後、リンクを設定して再表示する。
        <li class="l-footer__sns__item">
            <a href=""><i class="c-pop l-footer__sns__img youtube c-icon--youtube"></i></a>
        </li>
        */ ?>
        <li class="l-footer__sns__item">
            <a href="https://www.instagram.com/foods_selection/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="c-pop l-footer__sns__img instagram c-icon--instagram"></i></a>
        </li>
        <li class="l-footer__sns__item">
            <a href="https://www.facebook.com/218555321614153/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="c-pop l-footer__sns__img facebook c-icon--facebook"></i></a>
        </li>
    </ul>

    <!-- バナー -->
    <div class="l-footer__banner__group">
        <a href="" class="l-footer__banner">
            <picture>
                <source srcset="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-selection-app.webp" type="image/webp">
                <img class="c-pop l-footer__banner__img selection-app" src="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-selection-app.jpg" alt="セレクションアプリ">
            </picture>
        </a>
        <a href="https://www.cgcjapan.co.jp/" class="l-footer__banner" target="_blank" rel="noopener noreferrer">
            <picture>
                <source srcset="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-cgc-colab.webp" type="image/webp">
                <img class="c-pop l-footer__banner__img cgc-colab" src="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-cgc-colab.jpg" alt="CGCコラボ">
            </picture>
        </a>
    </div>

    <p class="l-footer__company">株式会社セレクション</p>
    <address class="l-footer__address">千葉県市川市湊新田1丁目6番8号</address>
    <p class="l-footer__copyright">© 2025 FOODS MARKET Selection co,ltd.</p>

    <!-- ページトップボタン -->
    <button class="c-btn c-btn--scroll-top" type="button" aria-label="ページトップへ戻る"></button>
</footer>

<?php wp_footer(); ?>

</body>
</html>
