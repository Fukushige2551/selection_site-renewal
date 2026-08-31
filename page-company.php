<?php
/**
 * Template Name: 企業情報
 */

get_header('company');
?>

<main id="page-company" class="p-page-company">

    <!-- ヒーロー 開始 -->
    <section class="p-page-company__hero">
        <div class="p-page-company__hero__slider js-company-hero-slider">

            <div class="p-page-company__hero__slide is-active">
                <picture>
                    <source media="(min-width: 768px)" srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_company-fv01-pc.webp" type="image/webp">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_company-fv01.webp" type="image/webp">
                    <img class="p-page-company__hero__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_company-fv01.png" alt="家族で食事を楽しむ様子">
                </picture>
            </div>

            <div class="p-page-company__hero__slide">
                <picture>
                    <source media="(min-width: 768px)" srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_company-fv02-pc.webp" type="image/webp">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_company-fv02.webp" type="image/webp">
                    <img class="p-page-company__hero__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_company-fv02.png" alt="新鮮な野菜">
                </picture>
            </div>

            <div class="p-page-company__hero__slide">
                <picture>
                    <source media="(min-width: 768px)" srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_company-fv03-pc.webp" type="image/webp">
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_company-fv03.webp" type="image/webp">
                    <img class="p-page-company__hero__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_company-fv03.png" alt="食卓に並ぶ料理">
                </picture>
            </div>

            <div class="p-page-company__hero__cloud">
                <picture class="p-page-company__hero__cloudPicture">
                    <source media="(min-width: 768px)" srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/hero_cloud_pc.svg">
                    <img class="p-page-company__hero__cloudImg" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/hero_cloud.svg" alt="">
                </picture>

                <div class="p-page-company__hero__content">
                    <h1 class="p-page-company__hero__title">選び抜いたおいしさで、<br>食卓にいつも笑顔を。</h1>

                    <p class="p-page-company__hero__text">
                        セレクションは、産地・メーカーと<br class="sp-only">
                        真っすぐ向き合い、<br class="sp-only">
                        バイヤーが全国から“良いもの”を選び抜く<br>
                        スーパーマーケットです。<br>
                        鮮度・品質・売場づくりにこだわり、<br>
                        地域の毎日に「おいしい」を<br class="sp-only">
                        届け続けます。
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- ヒーロー 終了 -->

    <picture>
        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_hero_person.webp" type="image/webp">
        <img class="p-page-company__person" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_hero_person.png" alt="">
    </picture>

    <!-- 私たちセレクションについて 開始 -->
    <section class="p-page-company__about">
        <div class="p-page-company__about__inner">

            <div class="p-page-company__about__content">
                <div class="p-page-company__about__heading">
                    <img class="p-page-company__about__decor" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/about_text_decor.svg" alt="">
                    <h2 class="p-page-company__about__title">私たち<br class="sp-only">セレクションについて</h2>
                </div>

                <p class="p-page-company__about__text">
                    新鮮な食材、心のこもった接客、地域の方々の<br class="sp-only">
                    毎日に寄り添うお店づくり。<br class="pc-only"> 私たちは「食卓に<br class="sp-only">
                    笑顔を届ける」ことを原点に、<br class="pc-only">スーパーマー<br class="sp-only">
                    ケット事業を展開しています。<br class="pc-only">これからも、地<br class="sp-only">
                    域のお客様とともに歩み続けます。
                </p>
            </div>

            <!-- 写真 開始 -->
            <div class="p-page-company__about__sliderWrap">

                <div class="p-page-company__about__slider js-about-slider">
                    <div class="p-page-company__about__track js-about-track">

                        <div class="p-page-company__about__item p-page-company__about__item--1">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_about01.webp" type="image/webp">
                                <img class="p-page-company__about__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_about01.png" alt="野菜売場のスタッフ">
                            </picture>
                        </div>

                        <div class="p-page-company__about__item p-page-company__about__item--2">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_about02.webp" type="image/webp">
                                <img class="p-page-company__about__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_about02.png" alt="新鮮な野菜">
                            </picture>
                        </div>

                        <div class="p-page-company__about__item p-page-company__about__item--3">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_about03.webp" type="image/webp">
                                <img class="p-page-company__about__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_about03.png" alt="買い物を楽しむ家族">
                            </picture>
                        </div>

                        <div class="p-page-company__about__item p-page-company__about__item--4">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_about04.webp" type="image/webp">
                                <img class="p-page-company__about__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_about04.png" alt="精肉売場">
                            </picture>
                        </div>

                        <div class="p-page-company__about__item p-page-company__about__item--5">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_about05.webp" type="image/webp">
                                <img class="p-page-company__about__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_about05.png" alt="店舗スタッフ">
                            </picture>
                        </div>

                    </div>

                    <div class="p-page-company__about__dots">
                        <span class="p-page-company__about__dot is-active"></span>
                        <span class="p-page-company__about__dot"></span>
                        <span class="p-page-company__about__dot"></span>
                        <span class="p-page-company__about__dot"></span>
                        <span class="p-page-company__about__dot"></span>
                    </div>
                </div>

            </div>
            <!-- 写真 終了 -->

        </div>
    </section>
    <!-- 私たちセレクションについて 終了 -->

    <!-- 生産者・取引先の皆さまへ 開始 -->
    <section class="p-page-company__partner">
        <div class="p-page-company__partner__inner">

            <div class="p-page-company__partner__body">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_partner_person.webp" type="image/webp">
                    <img class="p-page-company__partner__person" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_partner_person.png" alt="">
                </picture>

                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_partner_products.webp" type="image/webp">
                    <img class="p-page-company__partner__products" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_partner_products.png" alt="">
                </picture>

                <div class="p-page-company__partner__content">
                    <h2 class="p-page-company__partner__title">生産者・<br>取引先の皆さまへ</h2>

                    <p class="p-page-company__partner__text">
                        私たちは<span>「共においしさを届けるパートナー」</span>として、
                        生産者・メーカー・物流企業の皆さまとの
                        信頼関係を大切にしています。
                    </p>

                    <p class="p-page-company__partner__guide">
                        商品提案やお取引に関する<br>
                        ご案内は<a class="p-page-company__partner__guideLink" href="/">こちらから</a><br>
                        ご覧ください。
                    </p>
                </div>

                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_partner_showcase.webp" type="image/webp">
                    <img class="p-page-company__partner__showcase" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_partner_showcase.png" alt="">
                </picture>

                <img class="p-page-company__partner__decor" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/partner_text_decor.svg" alt="">

                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_partner_main.webp" type="image/webp">
                    <img class="p-page-company__partner__mainImg" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_partner_main.png" alt="">
                </picture>
            </div>

            <a class="p-page-company__partner__more" href="/">
                <span class="p-page-company__partner__moreText">VIEW MORE</span>
                <span class="p-page-company__partner__moreArrow"></span>
            </a>

        </div>
    </section>
    <!-- 生産者・取引先の皆さまへ 終了 -->

    <!-- 採用情報 開始 -->
    <section class="p-page-company__recruit">
        <div class="p-page-company__recruit__inner">

            <div class="p-page-company__recruit__content">
                <h2 class="p-page-company__recruit__title">採用情報</h2>

                <p class="p-page-company__recruit__text">
                    売場は毎日が本番。<br>
                    だからこそ、<br>
                    学べる・任される・成長できる<br>
                    環境があります。<br>
                    生鮮の技術、惣菜づくり、接客、<br>
                    数値管理、マネジメントまで---<br>
                    現場で身につく力が<br>
                    そのままキャリアになります。<br>
                    新卒・キャリア採用から<br>
                    パートナー／アルバイトまで、<br>
                    あなたの経験と希望に合う働き方を<br>
                    見つけてください。
                </p>
            </div>

            <picture>
                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_recruit_person01.webp" type="image/webp">
                <img class="p-page-company__recruit__person--top" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_recruit_person01.png" alt="">
            </picture>

            <img class="p-page-company__recruit__decor" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/recruit_text_decor.svg" alt="">

            <div class="p-page-company__recruit__images">

                <div class="p-page-company__recruit__imageBlock">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_recruit01.webp" type="image/webp">
                        <img class="p-page-company__recruit__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_recruit01.png" alt="">
                    </picture>

                    <p class="p-page-company__recruit__label p-page-company__recruit__label--career">新卒・中途採用</p>
                </div>

                <div class="p-page-company__recruit__imageBlock">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_recruit02.webp" type="image/webp">
                        <img class="p-page-company__recruit__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_recruit02.png" alt="">
                    </picture>
                </div>

            </div>

            <picture>
                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_recruit_person02.webp" type="image/webp">
                <img class="p-page-company__recruit__person--bottom" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_recruit_person02.png" alt="">
            </picture>

        </div>
    </section>
    <!-- 採用情報 終了 -->

    <!-- 企業情報 開始 -->
    <section class="p-page-company__info">
        <div class="p-page-company__info__inner">

            <img class="p-page-company__info__decor" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/company_text_decor.svg" alt="">

            <div class="p-page-company__info__heading">
                <h2 class="p-page-company__info__title">企業情報</h2>
                <p class="p-page-company__info__text">
                    代表挨拶・会社概要・事業所・沿革など<br>
                    セレクションの歩みをご紹介します。
                </p>
            </div>

            <nav class="p-page-company__info__nav">
                <a class="p-page-company__info__item" href="/">
                    <img class="p-page-company__info__icon" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/icon_message.svg" alt="">
                    <span>代表挨拶</span>
                </a>

                <a class="p-page-company__info__item" href="/">
                    <img class="p-page-company__info__icon" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/icon_company.svg" alt="">
                    <span>会社概要</span>
                </a>

                <a class="p-page-company__info__item" href="/">
                    <img class="p-page-company__info__icon" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/icon_office.svg" alt="">
                    <span>事業所</span>
                </a>

                <a class="p-page-company__info__item" href="/">
                    <img class="p-page-company__info__icon" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/svg/icon_history.svg" alt="">
                    <span>沿革</span>
                </a>
            </nav>

            <picture>
                <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-company/webp/img_company_info_person.webp" type="image/webp">
                <img class="p-page-company__info__person" src="<?php echo get_template_directory_uri(); ?>/img/page/page-company/img_company_info_person.png" alt="">
            </picture>

        </div>
    </section>
    <!-- 企業情報 終了 -->

</main>

<?php get_footer('company'); ?>
