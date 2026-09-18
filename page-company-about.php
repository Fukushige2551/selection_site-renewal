<?php
/**
 * Template Name: 会社概要
 */

$about_image_uri = get_template_directory_uri() . '/img/page/page-company-about';
$about_image_path = get_template_directory() . '/img/page/page-company-about';

// 会社概要
$company_profile = [
    ['会社名', '株式会社 セレクション'],
    ['所在地', '〒272-0132 千葉県市川市湊新田1丁目6番8号'],
    ['代表者', '代表取締役 山崎洋介'],
    ['事業内容', 'スーパーマーケット事業<br>店名：フーズマーケット<br>　　　セレクション<br>　　　生鮮市場セレクション'],
    ['諸手当', '交通費支給'],
    ['昇給', '年1回'],
    ['賞与', '年2回（7月・12月）<br>※業務実績による'],
    ['資本金', '3000万円'],
    ['売上高', '146億円（令和6年度実績）'],
    ['取引銀行', 'みずほ銀行/市川支店<br>三菱東京UFJ銀行/小岩支店<br>三井住友銀行/新小岩支店<br>りそな銀行/小岩支店<br>千葉銀行/小岩支店<br>京葉銀行/市川支店<br>千葉興業銀行/市川支店'],
    ['お取引先', '三菱食品<br>日本アクセス<br>ユアサフナショク<br>コンフェックス<br>東京都中央　大田市場<br>船橋地方卸市場<br>千葉地方卸市場<br>旭食肉協同組合<br>シージーシージャパン'],
];

// 事業所一覧
$offices = [
    ['フーズマーケットセレクション行徳店', '千葉県市川市湊新田1-6-8', '047-390-3336'],
    ['フーズマーケットセレクション八潮店', '埼玉県八潮市八潮4-10-2', '048-994-1185'],
    ['フーズマーケットセレクション三郷店', '埼玉県三郷市鷹野4-428', '048-948-1815'],
    ['フーズマーケットセレクション西原店', '千葉県柏市西原7-8-1', '047-156-8007'],
    ['フーズマーケットセレクション西船橋店', '千葉県船橋市印内町579-1', '047-420-3840'],
    ['フーズマーケットセレクション花野井店', '千葉県柏市花野井737-8', '047-137-0195'],
    ['フーズマーケットセレクションしいの木台店', '千葉県柏市しいの木台2-12', '047-388-1176'],
    ['フーズマーケットセレクション青葉台店', '千葉県柏市青葉台1-2-1', '047-171-3570'],
    ['フーズマーケットセレクション松戸店', '千葉県松戸市松戸新田418-5', '047-382-5190'],
    ['フーズマーケットセレクション西新井店', '東京都足立区関原3-12-11', '03-6806-3651'],
    ['船橋水産加工センター', '千葉県船橋市市場1-8-1', '047-411-0771'],
    ['初富研修センター', '千葉県鎌ケ谷市北初富8-1', '047-441-5703'],
];

// 沿革
$history = [
    ['1989.06', '資本金500万円にて会社設立'],
    ['1989.07', '東京都江戸川区北小岩に1号店創業　<br>スーパーセレクション北小岩店'],
    ['1994.06', '資本金3000万円に増資'],
    ['1995.04', '新卒者採用開始　<br>埼玉県八潮市に八潮店開設'],
    ['1997.12', '千葉県市川市に行徳店開設'],
    ['1998.10', '本部/本店を北小岩3丁目に移転'],
    ['1999.11', '埼玉県三郷市に三郷店開設'],
    ['2001.09', '外食事業部設立'],
    ['2002.09', '千葉県千葉市に外食1号店　<br>とりあえず吾平おゆみ野店開設'],
    ['2002.11', '千葉県白井市に外食2号店　<br>とりあえず吾平白井店開設'],
    ['2003.03', '千葉県柏市に西原店開設'],
    ['2003.06', '千葉県鎌ヶ谷市に初富店開設'],
    ['2005.11', '初富店営業不振により撤退'],
    ['2009.03', '千葉県船橋市に西船橋店開設'],
    ['2009.06', '行徳店改装リニューアルオープン'],
    ['2011.05', '西原店改装リニューアルオープン'],
    ['2012.02', '千葉県柏市に花野井店開設'],
    ['2012.03', '1号店北小岩店の閉店に伴い<br class="historyTabletInline">本部を江戸川区北小岩から<br class="historyTabletInline">千葉県市川市の行徳店に併設<br><br class="historySpacer">船橋水産加工センター開設<br><br class="historySpacer">初富研修センター開設'],
    ['2013.10', '千葉県柏市に7号店しいの木台店開設'],
    ['2014.06', '外食事業部閉鎖'],
    ['2015.06', '千葉県柏市に青葉台店開設'],
    ['2020.10', 'しいのき台店改装　<br>リニューアルオープン'],
    ['2023.05', '千葉県松戸市に松戸店開店'],
    ['2024.04', '東京都足立区に西新井店開店'],
    ['2026.02', '花野井店改装リニューアルオープン'],
    ['', '現在に至る'],
];

get_header('company');
?>

<main id="page-company-about" class="p-page-company-about">
    <!-- 代表メッセージ 開始 -->
    <section class="p-page-company-about__message">
        <nav class="p-page-company-about__breadcrumb" aria-label="パンくずリスト">
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span>企業情報</span><span>会社概要</span>
        </nav>
        <picture class="p-page-company-about__messagePicture">
            <?php if (file_exists($about_image_path . '/webp/img_message-pc.webp')) : ?>
                <source media="(min-width: 768px)" srcset="<?php echo esc_url($about_image_uri . '/webp/img_message-pc.webp'); ?>" type="image/webp">
            <?php endif; ?>
            <?php if (file_exists($about_image_path . '/img_message-pc.png')) : ?>
                <source media="(min-width: 768px)" srcset="<?php echo esc_url($about_image_uri . '/img_message-pc.png'); ?>">
            <?php endif; ?>
            <?php if (file_exists($about_image_path . '/webp/img_message-sp.webp')) : ?>
                <source srcset="<?php echo esc_url($about_image_uri . '/webp/img_message-sp.webp'); ?>" type="image/webp">
            <?php endif; ?>
            <img class="p-page-company-about__messageImage" src="<?php echo esc_url($about_image_uri . '/img_message-sp.png'); ?>" alt="代表取締役とセレクションの店舗・商品・スタッフ">
        </picture>
        <div class="p-page-company-about__inner">
            <header class="p-page-company-about__heading">
                <h1 class="p-page-company-about__title">代表メッセージ</h1>
                <p class="p-page-company-about__headingEn">Message</p>
            </header>
            <h2 class="p-page-company-about__messageLead">食を通じて、<br>地域の未来に責任を。</h2>
            <div class="p-page-company-about__messageText">
                <p>私たちセレクションは、大手チェーン企業と比較すれば、決して大きな企業ではありません。しかし、地域のお客様の毎日の食卓を支える存在として果たすべき責任においては、どの企業にも負けない覚悟と誇りを持っています。</p>
                <p>「セレクション」という社名には、“本当に良いものを選び抜き、お客様へ届けたい”という私たちの強い想いが込められています。単に商品を並べ、販売するだけではなく、生産者が大切に育て、つくり上げた価値ある商品を、お客様の毎日の暮らしへしっかりとつなげていくこと。それが私たちスーパーマーケットの重要な役割だと考えています。</p>
                <p>また、私たちは創業以来、「フーズマーケット」という言葉に強いこだわりを持ってきました。それは単なる食品販売業ではなく、“食”そのものに向き合う企業でありたいという意思表示です。鮮度、味、旬、品質、安全性。毎日の食卓に並ぶ一品一品に責任を持ち、お客様に「セレクションに来て良かった」と感じていただける店づくりを追求しています。</p>
                <p>しかし、これからの時代、スーパーマーケットに求められる役割は、それだけではありません。人口減少、少子高齢化、共働き世帯の増加、環境問題、食品ロス、人手不足など、社会は大きな変化の中にあります。私たちは、単に商品を販売する会社ではなく、地域社会の暮らしを支える“生活インフラ”として、こうした社会課題に真剣に向き合っていかなければならないと考えています。</p>
                <p>食品ロスを減らすことも、その大切な責任の一つです。日本では、まだ食べられる食品が大量に廃棄されています。セレクションでは、販売データや需要予測を活用しながら、適正な発注や製造、無駄を減らす売場づくりに取り組み、食材を最後まで活かし切ることに挑戦しています。無駄を減らすことは、生産者を守り、環境を守り、未来の地域社会を守ることにつながると信じています。</p>
                <p>さらに、地域に根差す企業として、環境負荷低減への取り組みにも力を入れています。地産地消による輸送距離の削減、包材使用量の見直し、省エネルギー化、リサイクル推進など、一つひとつの積み重ねを大切にしながら、持続可能な社会づくりに貢献していきたいと考えています。</p>
                <p>そして、会社にとって最も大切なのは「人」です。どれだけ時代が変わっても、最後にお客様へ価値を届けるのは人の力です。だからこそ私たちは、社員一人ひとりが誇りを持ち、成長し、挑戦できる環境づくりを大切にしています。セレクションに入社して良かった、この会社で働くことにやりがいを感じる。そう思える会社を目指し、人材育成や働く環境の改善にも力を注いでいます。</p>
                <p>私たちが大切にしているのは、「お客様への責任」「社員への責任」「地域社会への責任」という三つの責任です。この三つの責任を果たし続けることこそが、セレクションの使命であり、存在意義だと考えています。</p>
                <p>スーパーマーケットは、まだまだ進化できる。もっと地域を元気にできる。もっと暮らしを豊かにできる。私たちはそう信じています。</p>
                <p>これからもセレクションは、“食”を通じて地域社会の未来に挑戦し続けます。そして、地域のお客様から最も信頼され、必要とされるスーパーマーケットを目指して、変化を恐れず歩み続けてまいります。</p>
            </div>
            <p class="p-page-company-about__signature">
                <span class="p-page-company-about__signatureRole">代表取締役　</span><span class="p-page-company-about__signatureName">山崎 洋介</span>
                <span class="p-page-company-about__signatureEn">YAMAZAKI YOSUKE</span>
            </p>
        </div>
    </section>
    <!-- 代表メッセージ 終了 -->

    <!-- 会社概要 開始 -->
    <section class="p-page-company-about__profile">
        <div class="p-page-company-about__visual p-page-company-about__visual--profile">
            <picture>
                <?php if (file_exists($about_image_path . '/webp/img_company-profile.webp')) : ?>
                    <source srcset="<?php echo esc_url($about_image_uri . '/webp/img_company-profile.webp'); ?>" type="image/webp">
                <?php endif; ?>
                <img src="<?php echo esc_url($about_image_uri . '/img_company-profile.png'); ?>" alt="買い物かごを持つお客様">
            </picture>
            <header class="p-page-company-about__visualHeading">
                <h2>会社概要</h2>
                <p>Company Profile</p>
            </header>
        </div>
        <dl class="p-page-company-about__profileList">
            <?php foreach ($company_profile as [$label, $value]) : ?>
                <div class="p-page-company-about__profileRow">
                    <dt><?php echo esc_html($label); ?></dt>
                    <dd><?php echo $value; ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </section>
    <!-- 会社概要 終了 -->

    <!-- 事業所 開始 -->
    <section class="p-page-company-about__office">
        <header class="p-page-company-about__heading">
            <h2 class="p-page-company-about__title">事業所</h2>
            <p class="p-page-company-about__headingEn">Office</p>
        </header>
        <ul class="p-page-company-about__officeList">
            <?php foreach ($offices as [$name, $address, $tel]) : ?>
                <li class="p-page-company-about__officeItem">
                    <h3><?php echo esc_html($name); ?></h3>
                    <address><?php echo esc_html($address); ?></address>
                    <p>TEL：<?php echo esc_html($tel); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <!-- 事業所 終了 -->

    <!-- 沿革 開始 -->
    <section class="p-page-company-about__history">
        <div class="p-page-company-about__visual p-page-company-about__visual--history">
            <picture>
                <?php if (file_exists($about_image_path . '/webp/img_history.webp')) : ?>
                    <source srcset="<?php echo esc_url($about_image_uri . '/webp/img_history.webp'); ?>" type="image/webp">
                <?php endif; ?>
                <img src="<?php echo esc_url($about_image_uri . '/img_history.png'); ?>" alt="フーズマーケットセレクションの店舗">
            </picture>
            <header class="p-page-company-about__visualHeading">
                <h2>沿革</h2>
                <p>History</p>
            </header>
        </div>
        <ol class="p-page-company-about__historyList">
            <?php foreach ($history as [$date, $description]) : ?>
                <li class="p-page-company-about__historyItem">
                    <?php if ($date) : ?>
                        <time><?php echo esc_html($date); ?></time>
                    <?php endif; ?>
                    <p><?php echo $description; ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
    <!-- 沿革 終了 -->
</main>

<?php get_footer('company'); ?>
