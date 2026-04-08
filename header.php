<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+New:wght@500&display=swap" rel="stylesheet">
</head>

<body <?php body_class(); ?>>

<header class="l-header">
    <a href="/" class="js-toggle--nav js-transparent">
        <picture class="l-header__bascket">
            <source srcset="<?php echo get_template_directory_uri(); ?>/img/header/btn_bascket.webp" type="image/webp">
            <img class="l-header__bascket__img" src="<?php echo get_template_directory_uri(); ?>/img/header/btn_bascket.png" alt="オンラインショップ">
        </picture>
    </a>

    <picture class="l-header__logo">
        <source srcset="<?php echo get_template_directory_uri(); ?>/img/header/header_logo-sp.webp" type="image/webp">
        <img class="l-header__logo__img" src="<?php echo get_template_directory_uri(); ?>/img/header/header_logo-sp.png" alt="会社ロゴ">
    </picture>

    <button class="js-toggle--nav" aria-label="メニューを開く" aria-expanded="false" type="button">
        <picture class="l-header__hamburger">
            <source srcset="<?php echo get_template_directory_uri(); ?>/img/header/btn_hamburger.webp" type="image/webp">
            <img class="l-header__hamburger__img" src="<?php echo get_template_directory_uri(); ?>/img/header/btn_hamburger.png" alt="ハンバーガーメニューアイコン">
        </picture>
    </button>
</header>

<!-- ナビゲーション（表示・非表示切り替え） -->
<nav class="l-header__nav is-closed">
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
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/header/btn_bascket--white.webp" type="image/webp">
                    <img class="l-header__nav__online-shop__img" src="<?php echo get_template_directory_uri(); ?>/img/header/btn_bascket--white.png" alt="オンラインショップのバナー画像">
                </picture>
                オンラインショップ
            </button>
        </a>
    </div>
</nav>