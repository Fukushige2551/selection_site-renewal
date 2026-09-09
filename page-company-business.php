<?php
/**
 * Template Name: 事業内容
 */

$business_image_uri = get_template_directory_uri() . '/img/page/page-company-business';
$business_image_path = get_template_directory() . '/img/page/page-company-business';

// セレクションの3つのこだわり
$commitments = [
    [
        'image' => 'img_commitment-quality',
        'alt' => '新鮮な食材を使った食卓',
        'title' => '品質・鮮度・安全性への<br class="p-page-company-business__breakSmallPc">こだわり',
        'text' => 'セレクションでは、お客様に安心してお買い物いただけることを第一に、<br class="p-page-company-business__breakUntilPc">品質・鮮度・安全性を重視した商品選定を行っています。<br>産地・原料・製法・温度管理・表示内容などを丁寧に確認し、売場での見せ方や販売タイミングまで含めて<br>「いちばん良い状態」でお届けできるよう取り組んでいます。<br>価格だけでなく、商品そのものの価値が<br>伝わる売場づくりを大切にしています。',
    ],
    [
        'image' => 'img_commitment-partnership',
        'alt' => '売場づくりについて話し合う取引先とスタッフ',
        'title' => '売場づくりをともに行う<br class="p-page-company-business__breakSmallPc">共創型の取引',
        'text' => '私たちは、商品を仕入れて終わりではなく、売場でどう伝え、どう売れるかまでをお取引先様と一緒に考えることを大切にしています。<br>季節・催事・地域特性に合わせた提案、商品特徴の見える化、販促企画や試食・メニュー提案などを通じて、売上拡大とブランド価値向上の両立を目指します。<br>現場の声を活かしながら、継続的に改善できる関係づくりを進めています。',
    ],
    [
        'image' => 'img_commitment-community',
        'alt' => '地域のお客様を迎える店舗スタッフ',
        'title' => '持続可能な成長と<br class="p-page-company-business__breakSmallPc">地域に根ざした連携',
        'text' => 'セレクションは、地域密着型のスーパーマーケットとして、安定供給・適正品質・継続取引を重視しています。<br>市場環境や生活者ニーズの変化に対応しながら、地域のお客様に支持される売場を維持・発展させていくために、お取引先様との中長期的な連携を大切にしています。<br>地域の食文化を支えるパートナーとして、ともに成長していける関係を目指します。',
    ],
];

// お取引企業様の声
$testimonials = [
    [
        'image' => 'img_partner-ushijima',
        'name' => '農業生産法人<br>ウシジマ青果株式会社様',
        'location' => '熊本県 河内町',
        'title' => '<span class="p-page-company-business__titlePart">食卓の価値を共に届ける</span>存在',
        'text' => $commitments[2]['text'],
    ],
    [
        'image' => 'img_partner-asasho',
        'name' => '株式会社アサショウ<br>旭食肉協同組合',
        'location' => '千葉県 旭市',
        'title' => '産地を育てる共創関係',
        'text' => '私たちがセレクション様と長年の取引を続けてこられたのは、何よりも「お肉に対する真摯な態度」に深く共鳴しているからです。単に商品を仕入れるだけでなく、私たちは共に歩むパートナーとして、常に品質の向上を追求してきました。特に、生産現場の想いを大切にし、共に「産地を育てる企業姿勢」には、供給元として大きな誇りと信頼を感じております。愛情込めて育てた豚肉が、最高の状態で皆さまの食卓へ届く。その橋渡し役として、これほど心強い存在はありません。これからも、確かな品質と美味しさを通じて、豊かな食文化を創造しつづけていただけることを願っております。',
    ],
];

// お取引のメリット
$benefits = [
    ['<span class="p-page-company-business__titlePart">長期的で安定した</span><span class="p-page-company-business__titlePart">パートナーシップ</span>', 'セレクションは、短期的な条件だけでなく、継続的に成果を生み出せる関係づくりを重視しています。<br>市場環境やニーズの変化にも柔軟に対応しながら、長期的な視点でお取引先様とともに成長していくことを目指します。'],
    ['売場提案・販促連動による販売機会の拡大', '季節催事・生活行事・地域特性に合わせて、売場展開や販促企画を連動させることで、商品の魅力をお客様に伝える機会を広げます。<br>商品供給だけでなく、売れる見せ方・伝え方まで含めて相談できる関係を大切にしています。'],
    ['<span class="p-page-company-business__titlePart">現場対話を活かした</span><span class="p-page-company-business__titlePart">継続改善と価値向上</span>', '店舗現場・バイヤー・お取引先様の対話を通じて、商品仕様、荷姿、販促表現、供給体制などを継続的に見直し、より良い売場づくりにつなげていきます。<br>お互いの強みを活かしながら、無理なく続く取引と価値向上を目指します。'],
];

// 新規店舗の募集条件
$location_conditions = [
    ['立地条件', '駅周辺または集合・戸建て住宅が集積している住宅地及び近隣に同業他社が少ない立地を希望しています。'],
    ['敷地面積及び建築面積', '敷地面積　600～2000坪 建物面積<br>350～600坪（売場面積200～450坪）'],
    ['マーケットボリューム', '半径500m　3000世帯以上<br>半径750m　5000世帯以上'],
    ['契約形態', '土地売買、土地賃貸借、建物賃貸借（居抜き含む）など、案件条件に応じて検討いたします。'],
    ['賃料', '立地条件、面積、建物状況、契約条件等を踏まえ、個別にご相談させていただきます。'],
];

get_header('company');
?>

<main id="page-company-business" class="p-page-company-business">
    <!-- メインビジュアル・メッセージ 開始 -->
    <section class="p-page-company-business__intro" aria-labelledby="business-title">
    <nav class="p-page-company-business__breadcrumb" aria-label="パンくずリスト">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span><a href="<?php echo esc_url(home_url('/company/')); ?>">企業情報</a></span>
        <span aria-current="page">企業の方へ</span>
    </nav>
        <picture class="p-page-company-business__heroPicture">
            <?php if (file_exists($business_image_path . '/webp/img_business-fv.webp')) : ?>
                <source srcset="<?php echo esc_url($business_image_uri . '/webp/img_business-fv.webp'); ?>" type="image/webp">
            <?php endif; ?>
            <img src="<?php echo esc_url($business_image_uri . '/img_business-fv.png'); ?>" alt="セレクションと地域の生産者・取引先の皆さま" fetchpriority="high">
        </picture>
        <header class="p-page-company-business__heading">
            <h1 id="business-title" class="p-page-company-business__title">生産者・取引先の皆さまへ</h1>
            <p class="p-page-company-business__headingEn">To Our Producers and<br class="p-page-company-business__breakSp"> Business Partners</p>
        </header>
        <h2 class="p-page-company-business__lead">地域の食をともにつくる、<br class="p-page-company-business__breakSp">共創パートナーの皆さまへ</h2>
        <p class="p-page-company-business__introText">セレクションは、選び抜かれた品質の商品を<br class="p-page-company-business__breakSp">お客様に届けるだけでなく、<br>生産者・仕入れ先の皆さまと共に<br class="p-page-company-business__breakSp">市場価値を高め、<br>持続的な成長を実現することを<br class="p-page-company-business__breakSp">目指しています。<br>品質・鮮度・安全性にこだわり抜く<br class="p-page-company-business__breakSp">当社だからこそ、お取引先の皆さまと共に、<br>新しい価値を生み出す未来を<br class="p-page-company-business__breakSp">描いていきたいと考えています。</p>
    </section>
    <!-- メインビジュアル・メッセージ 終了 -->

    <!-- 3つのこだわり 開始 -->
    <section class="p-page-company-business__commitments" aria-labelledby="business-commitments">
        <header class="p-page-company-business__heading">
            <h2 id="business-commitments" class="p-page-company-business__title"><span class="p-page-company-business__titlePart">セレクションの</span><span class="p-page-company-business__titlePart"><span class="p-page-company-business__titleNumber">3</span>つのこだわり</span></h2>
            <p class="p-page-company-business__headingEn">Three Key Commitments</p>
        </header>
        <ol class="p-page-company-business__commitmentList">
            <?php foreach ($commitments as $index => $commitment) : ?>
                <li class="p-page-company-business__commitment">
                    <div class="p-page-company-business__commitmentVisual">
                        <picture>
                            <?php if (file_exists($business_image_path . '/webp/' . $commitment['image'] . '.webp')) : ?>
                                <source srcset="<?php echo esc_url($business_image_uri . '/webp/' . $commitment['image'] . '.webp'); ?>" type="image/webp">
                            <?php endif; ?>
                            <img src="<?php echo esc_url($business_image_uri . '/' . $commitment['image'] . '.png'); ?>" alt="<?php echo esc_attr($commitment['alt']); ?>" loading="lazy">
                        </picture>
                    </div>
                    <div class="p-page-company-business__commitmentText">
                        <span class="p-page-company-business__number" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                        <h3><?php echo $commitment['title']; ?></h3>
                        <p><?php echo $commitment['text']; ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
    <!-- 3つのこだわり 終了 -->

    <!-- お取引企業様の声 開始 -->
    <section class="p-page-company-business__testimonials" aria-labelledby="business-testimonials">
        <header class="p-page-company-business__heading">
            <h2 id="business-testimonials" class="p-page-company-business__title">お取引企業様の声</h2>
            <p class="p-page-company-business__headingEn">Client Testimonials</p>
        </header>
        <div class="p-page-company-business__testimonialList">
            <?php foreach ($testimonials as $testimonial) : ?>
                <article class="p-page-company-business__testimonial">
                    <div class="p-page-company-business__partner">
                        <picture>
                            <?php if (file_exists($business_image_path . '/webp/' . $testimonial['image'] . '.webp')) : ?>
                                <source srcset="<?php echo esc_url($business_image_uri . '/webp/' . $testimonial['image'] . '.webp'); ?>" type="image/webp">
                            <?php endif; ?>
                            <img src="<?php echo esc_url($business_image_uri . '/' . $testimonial['image'] . '.png'); ?>" alt="" loading="lazy">
                        </picture>
                        <div>
                            <?php if ($testimonial['location']) : ?>
                                <p class="p-page-company-business__partnerLocation"><?php echo esc_html($testimonial['location']); ?></p>
                            <?php endif; ?>
                            <p class="p-page-company-business__partnerName"><?php echo $testimonial['name']; ?></p>
                        </div>
                    </div>
                    <div class="p-page-company-business__quote">
                        <h3><?php echo $testimonial['title']; ?></h3>
                        <p><?php echo $testimonial['text']; ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- お取引企業様の声 終了 -->

    <!-- お取引のメリット 開始 -->
    <section class="p-page-company-business__benefits" aria-labelledby="business-benefits">
        <div class="p-page-company-business__benefitsVisual">
            <picture>
                <?php if (file_exists($business_image_path . '/webp/img_benefits.webp')) : ?>
                    <source srcset="<?php echo esc_url($business_image_uri . '/webp/img_benefits.webp'); ?>" type="image/webp">
                <?php endif; ?>
                <img src="<?php echo esc_url($business_image_uri . '/img_benefits.png'); ?>" alt="お取引先とセレクションのパートナーシップ" loading="lazy">
            </picture>
            <header class="p-page-company-business__heading">
                <h2 id="business-benefits" class="p-page-company-business__title">お取引のメリット</h2>
                <p class="p-page-company-business__headingEn">Benefits of the transaction</p>
            </header>
        </div>
        <ol class="p-page-company-business__benefitList">
            <?php foreach ($benefits as $index => [$title, $description]) : ?>
                <li class="p-page-company-business__benefit">
                    <div class="p-page-company-business__benefitIcon<?php echo $index === 1 ? ' p-page-company-business__benefitIcon--wide' : ''; ?>" aria-hidden="true">
                        <img src="<?php echo esc_url($business_image_uri . '/svg/icon_merit-' . sprintf('%02d', $index + 1) . '.svg'); ?>" alt="">
                    </div>
                    <p class="p-page-company-business__benefitNumber">Merit<?php echo esc_html($index + 1); ?></p>
                    <h3><?php echo $title; ?></h3>
                    <p class="p-page-company-business__benefitText"><?php echo $description; ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
        <aside class="p-page-company-business__contact" aria-label="お取引のお問い合わせ">
            <!-- 装飾文字は読み上げ対象から除外する。 -->
            <img class="p-page-company-business__contactDecoration" src="<?php echo esc_url($business_image_uri . '/svg/text_seeking-partners.svg'); ?>" alt="" aria-hidden="true">
            <p>セレクションでは、<br>地域のお客様に選ばれる売場を<br>ともにつくるパートナー企業様を<br class="p-page-company-business__breakUntilPc">募集しています。<br>お取引のご相談、商品のご提案、<br>新規お取り組みに<br class="p-page-company-business__breakUntilPc">関するお問い合わせはお気軽に<br>ご連絡ください。</p>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>"><img class="p-page-company-business__contactMail" src="<?php echo esc_url($business_image_uri . '/svg/icon_mail.svg'); ?>" alt="">
                お問い合わせはこちら
                <img class="p-page-company-business__contactArrow" src="<?php echo esc_url($business_image_uri . '/svg/icon_contact-arrow.svg'); ?>" alt="">
            </a>
        </aside>
    </section>
    <!-- お取引のメリット 終了 -->

    <!-- 新規店舗物件情報募集 開始 -->
    <section class="p-page-company-business__locations" aria-labelledby="business-locations">
        <header class="p-page-company-business__heading">
            <h2 id="business-locations" class="p-page-company-business__title">新規店舗物件情報募集</h2>
            <p class="p-page-company-business__headingEn"><span class="p-page-company-business__titlePart">Call for New Store</span> <span class="p-page-company-business__titlePart">Location Proposals</span></p>
        </header>
        <p class="p-page-company-business__locationsIntro">株式会社セレクションでは、<br class="p-page-company-business__breakUntilPc">フーズマーケット「セレクション」の<br>新規出店・移転に向けた物件情報を<br class="p-page-company-business__breakUntilPc">募集しております。<br>土地・建物・居抜き物件など、<br>出店候補となる情報がございましたら、<br class="p-page-company-business__breakUntilPc">店舗開発部までご連絡ください。</p>
        <div class="p-page-company-business__conditions">
            <section class="p-page-company-business__condition">
                <h3>出店エリア</h3>
                <div class="p-page-company-business__conditionBody">
                    <dl class="p-page-company-business__areas">
                        <dt>［東京エリア］</dt>
                        <dd>江戸川区、葛飾区、江東区、足立区、墨田区、北区、練馬区、板橋区、荒川区</dd>
                        <dt>［埼玉エリア］</dt>
                        <dd>八潮市、三郷市、吉川市、草加市、越谷市、鳩ヶ谷市、川口市、戸田市、さいたま市、蕨市</dd>
                        <dt>［千葉エリア］</dt>
                        <dd>市川市、浦安市、松戸市、流山市、柏市、我孫子市、鎌ヶ谷市、船橋市、八千代市、千葉市、習志野市、四街道市</dd>
                    </dl>
                </div>
            </section>
            <?php foreach ($location_conditions as [$title, $description]) : ?>
                <section class="p-page-company-business__condition">
                    <h3><?php echo esc_html($title); ?></h3>
                    <div class="p-page-company-business__conditionBody">
                        <p><?php echo $description; ?></p>
                    </div>
                </section>
            <?php endforeach; ?>
            <section class="p-page-company-business__condition">
                <h3>連絡先</h3>
                <div class="p-page-company-business__conditionBody">
                    <p>店舗開発部：角谷<br>メールアドレス：<br><a href="mailto:kaihatsu@foods-selection.co.jp">kaihatsu@foods-selection.co.jp</a><br>FAX：047-390-3039</p>
                </div>
            </section>
        </div>
    </section>
    <!-- 新規店舗物件情報募集 終了 -->
</main>

<?php get_footer('company'); ?>
