<?php
/*
Template Name: Select Meat
*/
get_header();
?>
<main id="page-select-meat" class="p-page-select-meat">
    <nav class="c-breadcrumb" aria-label="パンくずリスト">
        <a class="c-breadcrumb__link" href="/">TOP</a>
            <img class="c-breadcrumb__arrow"
            src="<?php echo get_template_directory_uri(); ?>/img/component/svg/icon_breadcrumb.svg"
            alt="矢印">
        <a class="c-breadcrumb__link" href="/select/">
            セレクションのこだわり
        </a>
        <img class="c-breadcrumb__arrow"
            src="<?php echo get_template_directory_uri(); ?>/img/component/svg/icon_breadcrumb.svg"
            alt="矢印">
        <span class="c-breadcrumb__current">お肉のこだわり</span>
    </nav>
    <!-- ヒーロー start -->
    <div class="p-page-select-meat__hero">
         <!-- タイトル start -->
            <h1 class="c-section__title">お肉のこだわり</h1>
        <!-- タイトル end -->
         <div class="p-page-select-meat__hero__inner">
             <p class="p-page-select-meat__hero__text">
               毎日の食卓から特別な日の一皿まで。<br>
                産地や鮮度、部位ごとの特長を見極め、<br>
                「美味しさ」と「安心」の両立にこだわった<br>
                お肉をお届けしています。
            </p>
            <img class="p-page-select-meat__hero__img--top-sp" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/img_meatTop-sp.svg" alt="家族で食事を楽しむ様子">
            <img class="p-page-select-meat__hero__img--top-pc" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/img_meatTop-pc.svg" alt="家族で食事を楽しむ様子">
        </div>
    </div>
    <!-- ヒーロー end -->
   
    <section class="p-page-select-meat__message">
        <div class="p-page-select-meat__message__bg">
            <div class="p-page-select-meat__message__header">
                <div class="p-page-select-meat__message--bg">
                    <img class="p-page-select-meat__message--bubbleSp" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/img_message-sp.svg"alt="吹き出し">
                    <img class="p-page-select-meat__message--bubblePc" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/img_message-pc.svg"alt="吹き出し">
                    <img class="p-page-select-meat__message--buyer" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/img_message-buyer.svg"alt="バイヤー">
                     <div class="p-page-select-meat__message--text">
                        <p>
                            毎日の使いやすさと、週末のごちそう感。
                            その両方を叶える“肉の売場”。
                            セレクションは、部位の特徴を活かしたカットと提案で、
                            料理がきれいに決まる売場づくりを
                            徹底しています。
                            おいしさの土台は、鮮度と素材の安心。
                            私たちは、飼育や原材料まで目を向けた
                            肉を選び、必要以上の添加物に頼らない
                            無添加ハム・ウィンナーなども
                            揃えています。
                            さらに、北総豚をはじめ
                            生産者とつながる取り組みで、
                            味わいと品質を安定して届けます。
                            そして最後は“料理目線”。
                            「炒める」「煮る」「揚げる」
                            「焼く」など
                            用途が一目で伝わるカット、
                            味付け・下ごしらえの提案まで
                            セットにして、
                            迷わず選べるように。
                            平日は時短でおいしく、
                            週末はしっかりごちそうに。
                            毎日の食卓を、お肉から支えます。
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

  
</main>

<?php get_footer(); ?>

