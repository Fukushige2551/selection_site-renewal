<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>

    <!--
        # fonts
        - Noto Serif JP：見出しや強調したいテキストに使用。日本語のセリフ体で、上品で読みやすい印象を与える。
        - Zen Kaku Gothic New：
            - regular（400）：本文や説明文など、通常のテキストに使用。日本語のゴシック体で、シンプルでモダンな印象を与える。
            - medium（500）：サブ見出しや強調したいテキストに使用。日本語のゴシック体で、やや強調された印象を与える。
    -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Noto+Serif+JP:wght@200..900&family=Scope+One&family=Secular+One&family=Ysabeau:ital,wght@0,1..1000;1,1..1000&family=Zen+Kaku+Gothic+Antique:wght@400;500;700&family=Zen+Kaku+Gothic+New:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body <?php body_class(); ?>>

<!-- ヘッダー(SP) start -->
<header class="l-header l-header--sp">
    <a href="/" class="l-header__btn l-header__btn__bascket js-toggle--nav js-transparent">
        <picture class="l-header__bascket">
            <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/btn_bascket.webp" type="image/webp">
            <img class="l-header__bascket__img" src="<?php echo get_template_directory_uri(); ?>/img/component/btn_bascket.png" alt="オンラインショップ">
        </picture>
    </a>

    <a href="/" class="l-header__logo js-transparent" aria-label="トップページへ"></a>

    <button class="l-header__btn js-toggle--nav" aria-label="メニューを開く" aria-expanded="false" type="button">
        <picture class="l-header__hamburger">
            <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/btn_hamburger.webp" type="image/webp">
            <img class="l-header__hamburger__img" src="<?php echo get_template_directory_uri(); ?>/img/component/btn_hamburger.png" alt="ハンバーガーメニューアイコン">
        </picture>
    </button>
</header>
<!-- ヘッダー(SP) end -->

<!-- ナビゲーション（SP/Tablet） start -->
<nav class="l-header__nav l-header__nav--sp is-closed">
    <div class="l-header__nav__overlay"></div>
    <div class="l-header__nav__inner">
        <div class="l-header__nav__group store-info u-border-top--header js-toggle--pulldown">
            <p class="l-header__nav__group__title c-btn--pulldown">
                <span class="l-header__nav__group__title--position-fix">チラシ・店舗情報</span>
            </p>
        </div>

        <div class="store-info__group js-toggle--pulldown">
            <p class="store-info__prefecture c-btn--pulldown">千葉県</p>
            <ul>
                <li class="store-info__store"><a href="">行徳店</a></li>
                <li class="store-info__store"><a href="">西船橋店</a></li>
                <li class="store-info__store"><a href="">西原店</a></li>
                <li class="store-info__store"><a href="">花野井店</a></li>
                <li class="store-info__store"><a href="">しいの木台店</a></li>
                <li class="store-info__store"><a href="">八潮店</a></li>
                <li class="store-info__store"><a href="">青葉台店</a></li>
                <li class="store-info__store"><a href="">松戸店</a></li>
            </ul>
        </div>

        <div class="store-info__group js-toggle--pulldown">
            <p class="store-info__prefecture c-btn--pulldown">東京都</p>
            <ul>
                <li class="store-info__store"><a href="">西新井店</a></li>
            </ul>
        </div>

        <div class="store-info__group js-toggle--pulldown is-saitama">
            <p class="store-info__prefecture c-btn--pulldown">埼玉県</p>
            <ul>
                <li class="store-info__store"><a href="">三郷店</a></li>
                <li class="store-info__store u-border-bottom--none"><a href="">八潮店</a></li>
            </ul>
        </div>

        <div class="l-header__nav__group u-border-top--header js-toggle--pulldown">
            <p class="l-header__nav__group__title c-btn--pulldown">
                <span class="l-header__nav__group__title--position-fix">レシピ</span>
            </p>
        </div>
        <div class="l-header__nav__group">
            <p class="l-header__nav__group__title">
                <span class="l-header__nav__group__title--position-fix">パート・アルバイト募集</span>
            </p>
        </div>
        <div class="l-header__nav__group">
            <p class="l-header__nav__group__title">
                <span class="l-header__nav__group__title--position-fix">お問い合わせ</span>
            </p>
        </div>

        <a href="https://foods-selection.stores.jp/" class="l-header__nav__online-shop" target="_blank" rel="noopener noreferrer">
            <button class="c-btn c-btn--online-shop" type="button" aria-label="オンラインショップを開く" aria-expanded="true">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/btn_bascket--white.webp" type="image/webp">
                    <img class="l-header__nav__online-shop__img" src="<?php echo get_template_directory_uri(); ?>/img/component/btn_bascket--white.png" alt="オンラインショップのバナー画像">
                </picture>
                オンラインショップ
            </button>
        </a>
    </div>
</nav>
<!-- ナビゲーション（SP/Tablet） end -->

<!-- ヘッダー(PC) start -->
<header class="l-header-pc" aria-label="PCヘッダー">
    <div class="l-header-pc__inner">
        <nav class="l-header-pc__nav" aria-label="グローバルナビゲーション">
            <a class="l-header-pc__nav__link" href="">チラシ・店舗情報</a>
            <a class="l-header-pc__nav__link" href="">レシピ</a>
            <a class="l-header-pc__nav__link" href="">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/header/webp/header-logo-pc.webp" type="image/webp">
                    <img class="l-header-pc__nav__logo" src="<?php echo get_template_directory_uri(); ?>/img/header/header-logo-pc.png" alt="セレクションのロゴ画像">
                </picture>
            </a>
            <a class="l-header-pc__nav__link recruit--part-time" href="">パート・アルバイト募集</a>
            <a class="l-header-pc__nav__link" href="">お問い合わせ</a>
            <a class="l-header-pc__nav__link online-shop" href="https://foods-selection.stores.jp/" target="_blank" rel="noopener noreferrer">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/component/btn_bascket--white.webp" type="image/webp">
                    <img class="l-header-pc__nav__online-shop__img" src="<?php echo get_template_directory_uri(); ?>/img/component/btn_bascket--white.png" alt="オンラインショップのバナー画像">
                </picture>
                オンラインショップ
            </a>
        </nav>
    </div>
</header>
<!-- ヘッダー(PC) end -->