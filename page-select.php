<?php
/*
Template Name: セレクションのこだわり
*/

get_header();

$select_detail_urls = [
    'meat' => home_url('/select/meat/'),
    'fish' => home_url('/select/fish/'),
    'vegetables-fruit' => home_url('/select/vegetables-fruit/'),
    'rice' => home_url('/select/rice/'),
    'deli' => home_url('/select/deli/'),
    'washoku-daily' => home_url('/select/washoku-daily/'),
];
?>

<main id="page-select" class="p-page-select c-main">
    <nav class="c-breadcrumb" aria-label="パンくずリスト">
        <a class="c-breadcrumb__link" href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <img class="c-breadcrumb__arrow"src="<?php echo get_template_directory_uri(); ?>/img/component/svg/icon_breadcrumb.svg"alt="矢印">
        <span class="c-breadcrumb__current">セレクションのこだわり</span>
    </nav>
    <!-- ヒーロー start -->
    <div class="p-page-select__hero">
        <!-- タイトル start -->
            <h1 class="c-section__title">セレクションのこだわり</h1>
            <p class="c-section__title--sub">Commitment</p>
        <!-- タイトル end -->
        <div class="p-page-select__hero__inner">
            <img class="p-page-select__hero__img--top-sp" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/img_selectTop-sp.svg" alt="家族で食事を楽しむ様子">
            <img class="p-page-select__hero__img--top-pc" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/img_selectTop-pc.svg" alt="家族で食事を楽しむ様子">
            <img class="p-page-select__hero__img--toptext" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/img_selectToptext.svg" alt="いい商品を皆様に">
        </div>
            <p class="p-page-select__hero__text">
                Selectionは“選ぶ”を仕事にしたスーパー。<br>
                旬・鮮度・食べ方まで、売場でわかるように<br>
                整えて、毎日の定番にちょっとした発見を
                増やしていきます。
            </p>
            <p class="p-page-select__hero__text">
                バイヤーが全国から“良いもの”を選び抜き、
                店の現場で鮮度とおいしさを仕上げる
                食の専門店です。
            </p>
            <p class="p-page-select__hero__text">
                毎日の定番から週末のごちそうまで、
                「買って良かった」と思える理由を、
                売場の一つひとつに込めています。
            </p>
    </div>
    <!-- ヒーロー end -->


    <!-- セレクションのこだわり start -->
    <div class="c-bg p-page-select__commitment--bg">
        <img class="p-page-select__commitment__img--mark" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/img_select-mark.png" alt="地図マーク">
        <h2 class="p-page-select__commitment__selectTitle">
            <span>こだわりを選択</span>
        </h2>
        <section class="p-page-select__commitment">
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--01" href="<?php echo esc_url($select_detail_urls['meat']); ?>">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-01.svg" alt="こだわり　お肉">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--02" href="<?php echo esc_url($select_detail_urls['fish']); ?>">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-02.svg" alt="こだわり　お魚">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--03" href="<?php echo esc_url($select_detail_urls['vegetables-fruit']); ?>">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-03.svg" alt="こだわり　お野菜・果物">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--04" href="<?php echo esc_url($select_detail_urls['rice']); ?>">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-04.svg" alt="こだわり　お米">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--05" href="<?php echo esc_url($select_detail_urls['deli']); ?>">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-05.svg" alt="こだわり　お惣菜">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--06" href="<?php echo esc_url($select_detail_urls['washoku-daily']); ?>">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-06.svg" alt="こだわり　和日配">
                </picture>
            </a>
            <?php /*
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--07" href="/">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-07.svg" alt="こだわり　ベーカリー">
                </picture>
            </a>
            */ ?>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--08" href="/">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-08.svg" alt="こだわり　乳製品">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--09" href="/">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-09.svg" alt="こだわり　加工食品">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--10" href="/">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-10.svg" alt="こだわり　お菓子">
                </picture>
            </a>
            <a class="c-pop p-page-select__commitment__link c-pop p-page-select__commitment__link--11" href="/">
                <picture>
                    <img class="c-pop p-page-select__commitment__link__img" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select/svg/link_select-commitment-content-11.svg" alt="こだわり　お酒">
                </picture>
            </a>
        </section>
    </div>
    <!-- セレクションのこだわり end -->
</main>

<?php get_footer(); ?>

