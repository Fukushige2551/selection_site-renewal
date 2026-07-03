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
                            セレクションは、部位の特徴を活かした
                            カットと提案で、
                            料理がきれいに決まる売場づくりを
                            徹底しています。<br>
                            おいしさの土台は、鮮度と素材の安心。
                            私たちは、飼育や原材料まで目を向けた
                            肉を選び、必要以上の添加物に頼らない
                            無添加ハム・ウィンナーなども
                            揃えています。<br>
                            さらに、北総豚をはじめ
                            生産者とつながる取り組みで、
                            味わいと品質を安定して届けます。
                            そして最後は“料理目線”。<br>
                            「炒める」「煮る」「揚げる」<br>
                            「焼く」など<br>
                            用途が一目で伝わるカット、<br>
                            味付け・下ごしらえの提案まで
                            セットにして、<br>
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

    <section class="p-page-select-meat__about">
         <div class="p-page-select-meat__about__bg">
            <div class="p-page-select-meat__about__title--wrap">
                <h2 class="p-page-select-meat__about__title--text"><span>こだわりの現場から</span></h2>
                <img class="p-page-select-meat__about__title--decoration1" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/meat_about_deco1.svg" alt="ファーム作業人">
            </div>
            <div class="p-page-select-meat__about__content">
                 <h3 class="p-page-select-meat__about__content--title"> 美味北総豚<br><span>代表生産者</span> 井上農場</h3>
                <img class="p-page-select-meat__about__content--decoration2" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/meat_about_deco2.svg" alt="お肉のこだわり">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img1.webp" type="image/webp">
                    <img class="p-page-select-meat__about__content--image" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img1.png" alt="おせち予約はこちら">
                </picture>
                <p>
                    豚肉の味は、品種と飼育環境が大きく影響されるといわれています。「美味北総豚」は千葉県九十九里の緑豊かで温暖な気候の北総大地の 中で日々の健康管理、飼料や飼育環境に目を配りストレスを極力かけず大切に育てられています。
                </p>
                <h3 class="p-page-select-meat__about__content--sub"> 豊かな自然と丁寧な飼育から生まれる千葉が誇るブランド豚</h3>
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img2.webp" type="image/webp">
                    <img class="p-page-select-meat__about__content--image" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img2.png" alt="おせち予約はこちら">
                </picture>
                <p>
                    肉質はきめ細かくやわらかく、脂にはしつこさがなく、口に入れた瞬間に広がる自然な甘みと旨味が特長のおすすめの豚肉です。店舗から近い産地より産地直結でほぼ毎日仕入れているので鮮度も自信をもっておすすめします。
                </p>
                <div class="p-page-select-meat__about__content--imgWrap">
                     <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img3.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img3.png" alt="おせち予約はこちら">
                    </picture>
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img4.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img4.png" alt="おせち予約はこちら">
                    </picture>
                </div>
            </div>

            <div class="p-page-select-meat__about__content">
                 <h3 class="p-page-select-meat__about__content--title"> 毎日たべらるリーズナブルな国産牛肉<br>かみむらファーム かみむら牛</h3>
                <img class="p-page-select-meat__about__content--decoration3" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/meat_about_deco3.svg" alt="お肉のこだわり">
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img5.webp" type="image/webp">
                    <img class="p-page-select-meat__about__content--image" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img5.png" alt="おせち予約はこちら">
                </picture>
                <p>
                    かみむらファームでは、牛のエサづくりから製造・加工まで一貫して行い、ニーズに応える技術を集結。『一頭一頭、完璧なトレーサビリティ』を掲げ、カミチクファームの飼料用米・イネを発酵飼料に加工して肥育する「玄米黒牛」など、あっさり口どけの良い脂を目指すオリジナルブランド牛を展開。安全・安心で毎日食卓へ届けています。
                </p>
                <div class="p-page-select-meat__about__content--imgWrap">
                    <img class="p-page-select-meat__about__content--decoration4" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/meat_about_deco4.svg" alt="お肉のこだわり">
                     <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img6.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img6.png" alt="おせち予約はこちら">
                    </picture>
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img7.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img7.png" alt="おせち予約はこちら">
                    </picture>
                </div>
            </div>

             <div class="p-page-select-meat__about__content">
                 <h3 class="p-page-select-meat__about__content--title"> 素材だけで、ここまで旨い <br>The Better Table ホワイトスモーク</h3>
                <picture>
                    <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img8.webp" type="image/webp">
                    <img class="p-page-select-meat__about__content--image" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img8.png" alt="おせち予約はこちら">
                </picture>
                <p>
                    かみむらファームでは、牛のエサづくりから製造・加工まで一貫して行い、ニーズに応える技術を集結。『一頭一頭、完璧なトレーサビリティ』を掲げ、カミチクファームの飼料用米・イネを発酵飼料に加工して肥育する「玄米黒牛」など、あっさり口どけの良い脂を目指すオリジナルブランド牛を展開。安全・安心で毎日食卓へ届けています。
                </p>
                <div class="p-page-select-meat__about__content--imgWrap">
                    <img class="p-page-select-meat__about__content--decoration5" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/meat_about_deco5.svg" alt="お肉のこだわり">
                     <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img9.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img9.png" alt="おせち予約はこちら">
                    </picture>
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img10.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img10.png" alt="おせち予約はこちら">
                    </picture>
                      <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img11.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img11.png" alt="おせち予約はこちら">
                    </picture>
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/webp/meat_about_img12.webp" type="image/webp">
                        <img class="p-page-select-meat__about__content--imgSmall" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/meat_about_img12.png" alt="おせち予約はこちら">
                    </picture>
                </div>
            </div>
        </div>
    </section>

    <section class="p-page-select-meat__news">
        <div class="p-page-select-meat__news__bg">
            <img class="p-page-select-meat__news__decoration6" src="<?php echo get_template_directory_uri(); ?>/img/page/page-select-meat/svg/meat_about_deco6.svg" alt="お肉のこだわり">
            <h2 class="p-page-select-meat__news__title">お肉のこだわりをもっと見る</h2>

            <div class="p-page-select-meat__news__wrap">
                <article class="p-page-select-meat__news__item">
                    <div class="p-page-select-meat__news__head">
                        <time class="p-page-select-meat__news__date">2025.08.25</time>
                        <span class="p-page-select-meat__news__tag">おしらせ</span>
                    </div>

                    <div class="p-page-select-meat__news__body">
                        <img class="p-page-select-meat__news__image" src="画像パス" alt="">
                        <p class="p-page-select-meat__news__text">
                            テストテストテストテストテストテストテストテストテストテストテストテストテス...
                        </p>
                    </div>
                </article>

                <article class="p-page-select-meat__news__item">
                    <div class="p-page-select-meat__news__head">
                        <time class="p-page-select-meat__news__date">2025.08.25</time>
                        <span class="p-page-select-meat__news__tag p-page-select-meat__news__tag--campaign">キャンペーン</span>
                    </div>

                    <div class="p-page-select-meat__news__body">
                        <img class="p-page-select-meat__news__image" src="画像パス" alt="">
                        <p class="p-page-select-meat__news__text">
                            テストテストテストテストテストテストテストテストテストテストテストテストテス...
                        </p>
                    </div>
                </article>

                <a class="p-page-select-meat__news__link" href="#">お肉のこだわり一覧</a>
                </div>
                <a class="p-page-select-meat__news__seeMore" href="">
                    <button class="c-btn c-btn--common--green">一覧へ戻る</button>
                </a> 
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>

