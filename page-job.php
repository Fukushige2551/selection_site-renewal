<?php
/**
 * Template Name: パート・アルバイト募集
 */
$job_roles = [
    ['レジスタッフ<br>（チェッカー）', 'お会計<br>接客スタッフ', 'job-register.png'],
    ['品出し<br>（グロサリー）', '品出し<br>売場管理スタッフ', 'job-grocery.png'],
    ['惣菜', 'お惣菜作り', 'job-deli.png'],
    ['ベーカリー', 'パン製造', 'job-bakery.png'],
    ['水産', 'お魚カット<br>パック詰め', 'job-fish.png'],
    ['寿司', 'お寿司製造', 'job-sushi.png'],
    ['食肉', 'お肉カット<br>パック詰め', 'job-meat.png'],
    ['青果', '野菜・果物カット<br>パック詰め', 'job-produce.png'],
];
$job_faqs = [
    ['未経験でも応募できますか？', 'はい、未経験の方も大歓迎です。<br>入社後に丁寧な研修を行いますので<br>ご安心ください。'],
    ['シフトの希望は出せますか？', 'はい、あらかじめ面接時に希望を聞かせていただきます。入社後は1か月ごとのシフトを前月20日をめどに作成しますのでご相談ください。家庭の事情やプライベートなども考慮してみんなで助け合っています。'],
    ['週に何日から勤務できますか？', '経験者であれば1日でも可能です。経験がなければ2日～5日になります。'],
    ['学生や主婦（夫）、フリーターでも働けますか？', 'はい、ライフプランに合わせた柔軟な働き方ができます。お子様を送った後の9:30～迎えに行くまでの14時なども可能です。'],
    ['制服はありますか？', '販売チーム（レジ、品出し）、青果チームはエプロンのみ支給します。製造チーム（惣菜、水産、食肉）は白衣、帽子、エプロンを支給します。'],
    ['給与の支払い方法・締日・支払日は？', '毎月末締めの翌月10日払いになります。'],
    ['掛け持ちは可能ですか？', 'はい、可能です。ただし、一定の制限がありますのでお問い合わせください。'],
    ['面接時に持参するものを教えてください。', '商品を作る製造部（惣菜、水産、寿司、食肉、青果）と販売部（品出し、売場作り、レジ）などがあります。'],
];
$job_search_shops = get_posts([
    'post_type'      => 'shop',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);
$job_search_suggestions = [];
foreach ($job_search_shops as $shop) {
    $shop_id = $shop->ID;
    $shop_search_values = [
        get_the_title($shop_id),
        function_exists('get_field') ? get_field('address', $shop_id) : get_post_meta($shop_id, 'address', true),
        function_exists('get_field') ? get_field('access', $shop_id) : get_post_meta($shop_id, 'access', true),
    ];

    foreach ($shop_search_values as $value) {
        $value = trim(wp_strip_all_tags((string) $value));
        if ($value !== '') {
            $job_search_suggestions[] = $value;
        }
    }
}
$job_search_suggestions = array_values(array_unique($job_search_suggestions));
if (!$job_search_suggestions) {
    $job_search_suggestions = [
        '行徳店',
        '花野井店',
        '三郷店',
        'しいの木台店',
        '八潮店',
        '青葉台店',
        '西原店',
        '松戸店',
        '西船橋店',
        '西新井店',
    ];
}
get_header('company');
?>
<main class="p-job">
    <nav class="p-job__breadcrumb" aria-label="パンくずリスト">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span>›</span>
        <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span>›</span>
        <span>パートアルバイト募集</span>
    </nav>

    <section class="p-job__hero">
        <div class="p-job__hero-image"><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/img_job-hero.png'); ?>" alt="家族で食卓を囲む様子"></div>
        <div class="p-job__hero-heading"><p>食卓にいつも笑顔を</p><h1>地域の皆さまの<span class="p-job__sp-break"><br></span>“おいしい毎日”を<br>一緒に支えませんか？</h1></div>
        <p class="p-job__hero-copy">地域のお客様に笑顔をお届けする、<span class="p-job__sp-break"><br></span>スーパーマーケットで一緒に働きませんか？<br>レジや品出し、惣菜づくりなど、<span class="p-job__sp-break"><br></span>お仕事はいろいろ。<br>未経験の方でも、先輩スタッフが丁寧に<span class="p-job__sp-break"><br></span>サポートしますので安心してスタートできます。</p>
    </section>

    <section class="p-job-search">
        <h2><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/icon-keyword.svg'); ?>" alt="">フリーワードで探す</h2>
        <form action="<?php echo esc_url(home_url('/job/search/')); ?>" method="get">
            <input type="search" name="keyword" list="job-search-suggestions" autocomplete="off" aria-label="フリーワード" placeholder="店舗名、駅名、住所など">
            <datalist id="job-search-suggestions">
                <?php foreach ($job_search_suggestions as $suggestion) : ?>
                    <option value="<?php echo esc_attr($suggestion); ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <button type="submit">検索</button>
        </form>
    </section>

    <section class="p-job-works">
        <h2><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/icon-location.svg'); ?>" alt="">勤務地から探す</h2>
        <div class="p-job-works__map">
            <iframe src="https://www.google.com/maps?q=%E3%82%BB%E3%83%AC%E3%82%AF%E3%82%B7%E3%83%A7%E3%83%B3+%E3%82%B9%E3%83%BC%E3%83%91%E3%83%BC%E3%83%9E%E3%83%BC%E3%82%B1%E3%83%83%E3%83%88&output=embed" title="セレクション店舗マップ" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <h2><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/icon-job.svg'); ?>" alt="">職種から探す</h2>
        <h3 class="p-job-works__division">＜販売部＞</h3>
        <div class="p-job-works__grid">
            <?php foreach ($job_roles as $index => $role) : ?>
                <?php if ($index === 2) : ?><h3 class="p-job-works__division">＜製造部＞</h3><?php endif; ?>
                <a class="p-job-role" href="<?php echo esc_url(add_query_arg('job', wp_strip_all_tags($role[0]), home_url('/job/search/'))); ?>">
                    <span class="p-job-role__image"><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/' . $role[2]); ?>" alt=""></span>
                    <span class="p-job-role__body"><strong><?php echo wp_kses($role[0], ['br' => []]); ?></strong><small><?php echo wp_kses($role[1], ['br' => []]); ?></small></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="p-job-about">
        <h2>セレクションの<br>パート・アルバイトに<br>ついて</h2>
        <p class="p-job-about__en">About Part-Time Positions<br>at Selection</p>
        <img class="p-job-about__photo" src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/about-job-pc.png'); ?>" alt="セレクションで働くスタッフ">
        <p class="p-job-about__copy">セレクションでは、<span class="p-job__sp-break"><br></span>「地域のお客様の食卓を笑顔にする」ことを<span class="p-job__sp-break"><br></span>大切にしています。<br>お店を支えるのは、<span class="p-job__sp-break"><br></span>明るくて優しいスタッフのみなさん。<br>家庭や学校と両立しながら、<span class="p-job__sp-break"><br></span>地域の暮らしを一緒に支えてくれる<span class="p-job__sp-break"><br></span>仲間を募集しています。<br>未経験からでも安心してスタートできる環境が整っています。</p>
        <div class="p-job-points">
            <article class="p-job-point p-job-point--1">
                <span class="p-job-point__icon"><img class="p-job-point__icon-main" src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/point-1-main.svg'); ?>" alt=""><img class="p-job-point__icon-pin" src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/point-1-pin.svg'); ?>" alt=""></span>
                <p class="p-job-point__label">Point <strong>1</strong></p><hr><h3>ライフスタイルに合わせた<br>働き方</h3>
                <p>地域のお客様に笑顔をお届けする、<br>スーパーマーケットで一緒に<br>働きませんか？<br>レジや品出し、惣菜づくりなど、<br>お仕事はいろいろ。<br>未経験の方でも、先輩スタッフが丁寧に<br>サポートしますので<br>安心してスタートできます。</p>
            </article>
            <article class="p-job-point p-job-point--2">
                <span class="p-job-point__icon"><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/point-2-icon.svg'); ?>" alt=""></span>
                <p class="p-job-point__label">Point <strong>2</strong></p><hr><h3>未経験でも安心の<br>サポート体制</h3>
                <p>レジや品出し、惣菜調理などのお仕事は、<br>先輩スタッフが丁寧に教えます。<br>初めての方でも安心して覚えられるよう、<br>マニュアルや研修もご用意しています。<br>「できた！」が増えるたびに、<br>やりがいも実感できます。</p>
            </article>
            <article class="p-job-point p-job-point--3">
                <span class="p-job-point__icon"><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-job/point-3-icon.svg'); ?>" alt=""></span>
                <p class="p-job-point__label">Point <strong>3</strong></p><hr><h3>地域に愛される<br>あたたかい職場</h3>
                <p>お客様とのちょっとした会話や、<br>「ありがとう」の言葉が<br>励みになる職場です。<br>年齢や経験に関係なく、<br>助け合いながら働けるあたたかい<br>雰囲気が自慢。地域の一員として、<br>笑顔あふれるお店づくりを<br>一緒に楽しみましょう。</p>
            </article>
        </div>
    </section>

    <section class="p-job-faq">
        <h2>よくあるご質問</h2><p>Q &amp; A</p>
        <div class="p-job-faq__list">
            <?php foreach ($job_faqs as $index => $faq) : ?>
                <details><summary><span>Q.</span><?php echo esc_html($faq[0]); ?><i></i></summary><div><span>A.</span><p><?php echo wp_kses($faq[1], ['br' => []]); ?></p></div></details>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php get_footer('company'); ?>
