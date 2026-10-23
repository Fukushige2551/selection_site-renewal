<?php
/** Expanded footer used by the news archive WEB designs. */
$news_footer_uri = get_template_directory_uri();
?>
<footer class="p-news-footer">
    <a class="p-news-footer__logo" href="<?php echo esc_url(home_url('/')); ?>">
        <img src="<?php echo esc_url($news_footer_uri . '/img/component/selection-logo.svg'); ?>" alt="FOODS MARKET Selection" width="193" height="118">
    </a>
    <nav class="p-news-footer__nav" aria-label="フッターナビゲーション">
        <div class="p-news-footer__group p-news-footer__group--shops">
            <a class="p-news-footer__title" href="<?php echo esc_url(get_post_type_archive_link('shop')); ?>">チラシ・店舗情報</a>
            <ul class="p-news-footer__shops">
                <?php foreach ($main_footer_shops as [$name, $slug]) : ?>
                    <li><a href="<?php echo esc_url(home_url('/shop/' . $slug . '/')); ?>"><?php echo esc_html($name); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="p-news-footer__group p-news-footer__group--news"><a class="p-news-footer__title" href="<?php echo esc_url(get_post_type_archive_link('news')); ?>">新着情報</a></div>
        <div class="p-news-footer__group p-news-footer__group--select"><a class="p-news-footer__title" href="<?php echo esc_url(home_url('/select/')); ?>">セレクションのこだわり</a></div>
        <div class="p-news-footer__group p-news-footer__group--company">
            <a class="p-news-footer__title" href="<?php echo esc_url(home_url('/company/')); ?>">会社情報</a>
            <ul class="p-news-footer__company-links">
                <li><a href="<?php echo esc_url($main_footer_page_url('page-company-about.php', '/company/about/')); ?>">会社概要</a></li>
                <li><a href="<?php echo esc_url($main_footer_page_url('page-recruit.php', '/recruit/')); ?>">採用情報（新卒・中途）</a></li>
                <li><a href="<?php echo esc_url($main_footer_page_url('page-company-business.php', '/company/business/')); ?>">企業の方</a></li>
            </ul>
        </div>
        <div class="p-news-footer__group p-news-footer__group--part"><a class="p-news-footer__title" href="<?php echo esc_url($main_footer_page_url('page-job.php', '/job/')); ?>">パート・アルバイト募集</a></div>
        <div class="p-news-footer__group p-news-footer__group--recipe"><a class="p-news-footer__title" href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>">レシピ</a></div>
        <div class="p-news-footer__group p-news-footer__group--online"><a class="p-news-footer__title" href="https://www.selection-ec.jp/" target="_blank" rel="noopener noreferrer">オンラインショップ</a></div>
        <div class="p-news-footer__group p-news-footer__group--privacy"><a class="p-news-footer__title" href="<?php echo esc_url(home_url('/privacy/')); ?>">プライバシーポリシー</a></div>
        <div class="p-news-footer__group p-news-footer__group--contact"><a class="p-news-footer__title" href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a></div>
    </nav>
    <ul class="p-news-footer__sns" aria-label="ソーシャルメディア">
        <li><span role="img" aria-label="YouTube"><i class="c-icon--youtube"></i></span></li>
        <li><a href="https://www.instagram.com/foods_selection/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="c-icon--instagram"></i></a></li>
        <li><a href="https://www.facebook.com/218555321614153/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="c-icon--facebook"></i></a></li>
    </ul>
    <div class="p-news-footer__banners">
        <a <?php foods_footer_app_link_attributes(); ?>><img src="<?php echo esc_url($news_footer_uri . '/img/footer/footer_banner-selection-app.webp'); ?>" alt="セレクションアプリ" width="361" height="128"></a>
        <a href="https://www.cgcjapan.co.jp/" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url($news_footer_uri . '/img/footer/footer_banner-cgc-colab.webp'); ?>" alt="セレクションはCGCの加盟店です" width="360" height="127"></a>
    </div>
    <div class="p-news-footer__address">
        <p>株式会社セレクション</p>
        <address>千葉県市川市湊新田1丁目6番8号</address>
    </div>
    <p class="p-news-footer__copyright">© 2025 FOODS MARKET Selection co,ltd.</p>
    <button class="c-btn c-btn--scroll-top" type="button" aria-label="ページトップへ戻る"></button>
</footer>
