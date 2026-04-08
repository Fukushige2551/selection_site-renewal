<footer class="l-footer">
    <picture class="l-footer__logo">
        <a href="/">
            <source srcset="<?php echo get_template_directory_uri(); ?>/img/footer/footer_logo-sp.webp" type="image/webp">
            <img class="l-footer__logo__img" src="<?php echo get_template_directory_uri(); ?>/img/footer/footer_logo-sp.png" alt="会社ロゴ">
        </a>
    </picture>

    <!-- ナビゲーション -->
    <nav class="l-footer__nav">
        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item list-title">チラシ・店舗情報</a>
            <ul class="l-footer__nav__list store-info">
                <li class="l-footer__nav__list__item"><a href="">行徳店</a></li>
                <li class="l-footer__nav__list__item"><a href="">花野井店</a></li>
                <li class="l-footer__nav__list__item"><a href="">三郷店</a></li>
                <li class="l-footer__nav__list__item"><a href="">しいの木台店</a></li>
                <li class="l-footer__nav__list__item"><a href="">八潮店</a></li>
                <li class="l-footer__nav__list__item"><a href="">青葉台店</a></li>
                <li class="l-footer__nav__list__item"><a href="">西原店</a></li>
                <li class="l-footer__nav__list__item"><a href="">松戸店</a></li>
                <li class="l-footer__nav__list__item"><a href="">西船橋店</a></li>
                <li class="l-footer__nav__list__item"><a href="">西新井店</a></li>
            </ul>
        </div>

        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item">新着情報</a>
        </div>
        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item">セレクションのこだわり</a>
        </div>

        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item list-title">会社情報</a>
            <ul class="l-footer__nav__list company-info">
                <li class="l-footer__nav__list__item"><a href="">会社概要</a></li>
                <li class="l-footer__nav__list__item"><a href="">採用情報（新卒・中途）</a></li>
                <li class="l-footer__nav__list__item"><a href="">企業の方</a></li>
            </ul>
        </div>

        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item">パート・アルバイト募集</a>
        </div>
        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item">レシピ</a>
        </div>
        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item">オンラインショップ</a>
        </div>
        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item">プライバシーポリシー</a>
        </div>
        <div class="l-footer__nav__group">
            <a class="l-footer__nav__item">お問い合わせ</a>
        </div>
    </nav>

    <!-- SNS -->
    <ul class="l-footer__sns">
        <li class="l-footer__sns__item">
            <a href=""><i class="l-footer__sns__img youtube c-icon--youtube"></i></a>
        </li>
        <li class="l-footer__sns__item">
            <a href=""><i class="l-footer__sns__img instagram c-icon--instagram"></i></a>
        </li>
        <li class="l-footer__sns__item">
            <a href=""><i class="l-footer__sns__img facebook c-icon--facebook"></i></a>
        </li>
    </ul>

    <!-- バナー -->
    <a href="" class="l-footer__banner">
        <picture>
            <source srcset="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-selection-app.webp" type="image/webp">
            <img class="l-footer__banner__img selection-app" src="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-selection-app.jpg" alt="セレクションアプリ">
        </picture>
    </a>
    <a href="" class="l-footer__banner">
        <picture>
            <source srcset="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-cgc-colab.webp" type="image/webp">
            <img class="l-footer__banner__img cgc-colab" src="<?php echo get_template_directory_uri(); ?>/img/footer/footer_banner-cgc-colab.jpg" alt="CGCコラボ">
        </picture>
    </a>

    <p class="l-footer__company">株式会社セレクション</p>
    <address class="l-footer__address">千葉県市川市湊新田1丁目6番8号</address>
    <p class="l-footer__copyright">© 2025 FOODS MARKET Selection co,ltd.</p>
</footer>

<?php wp_footer(); ?>

</body>
</html>