<?php
/**
 * Template Name: 採用情報
 */

$image_uri = get_template_directory_uri() . '/img/page/page-recruit';
$image_path = get_template_directory() . '/img/page/page-recruit';
$theme_image_uri = get_template_directory_uri() . '/img/page/front-page';
$use_recruit_video = false; // 動画の準備後に true へ変更します。
$recruit_image = static function ($filename, $fallback) use ($image_uri, $image_path, $theme_image_uri) {
  return file_exists($image_path . '/' . $filename)
    ? $image_uri . '/' . $filename . '?v=' . filemtime($image_path . '/' . $filename)
    : $theme_image_uri . '/' . $fallback;
};
$voices_word_file = $image_path . '/svg/text_voices-from-seniors.svg';
$voices_word_uri = $image_uri . '/svg/text_voices-from-seniors.svg';
if (file_exists($voices_word_file)) {
  $voices_word_uri .= '?v=' . filemtime($voices_word_file);
}

$about_items = [
  ['01', '私たちについて', '会社名である”セレクション”の由来は？<br>会社の目指す形とともに紹介します', 'img_work-source', '#'],
  ['02', '仕事内容', '食品を扱う現場ではどんな仕事をしているのか<br>製造・販売・仕入れ、企画、それぞれのお仕事<br>について紹介します', 'img_career-source', '#'],
  ['03', 'キャリアアップ', '一人ひとりの成長を全力でサポート。<br>将来のキャリアの描き方を紹介します。', 'img_day-flow-source', '#'],
  ['04', '教育・資格支援制度', '小売り販売で活かせる資格の取得をサポート！<br>自身の力になるキャリアアップを支援します', 'img_training-original', '#'],
  ['05', '1日の仕事の流れ', '入社●年目の先輩社員の1日をご紹介', 'img_day-flow-original', '#'],
];

$voice_items = [
  ['現場の気持ちがわかる<br>店長でありたい', '2010 年　新卒採用', '店舗運営部 店長：S さん', 'img_voice-left-bg-source.png', 'img_voice-left-person-source.png'],
  ['お客様の期待を<br>裏切らない店づくり<br>人とのつながりを大切に', '1991 年　新卒採用', '店舗運営部 部長：K さん', 'img_voice-raw-01.png', 'img_voice-raw-03.png'],
  ['自分の仕事が、<br>誰かの笑顔につながる', '2012 年　新卒採用', 'チェッカーチーム<br>本部トレーナー：K さん', 'img_voice-right-bg-source.png', 'img_voice-right-person-source.png'],
];

get_header('company');
?>

<main id="page-recruit" class="p-page-recruit">
  <nav class="p-page-recruit__breadcrumb" aria-label="パンくずリスト">
    <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span><span>採用情報</span>
  </nav>

  <section class="p-page-recruit__hero">
    <?php if ($use_recruit_video) : ?>
      <!-- 動画追加時は src と poster を実ファイルへ変更してください。 -->
      <video class="p-page-recruit__hero-media" poster="<?php echo esc_url($theme_image_uri . '/bg_recruit-content-01.jpg'); ?>" autoplay muted loop playsinline>
        <source src="<?php echo esc_url($image_uri . '/movie_recruit.mp4'); ?>" type="video/mp4">
      </video>
    <?php else : ?>
      <picture>
        <source media="(min-width: 768px)" type="image/webp" srcset="<?php echo esc_url($image_uri . '/webp/img_recruit-fv-pc.webp'); ?>">
        <source media="(min-width: 768px)" srcset="<?php echo esc_url($image_uri . '/img_recruit-fv-pc.jpg'); ?>">
        <img class="p-page-recruit__hero-media" src="<?php echo esc_url($image_uri . '/img_recruit-fv.png'); ?>" alt="セレクション店舗で働くスタッフ">
      </picture>
    <?php endif; ?>
    <span class="p-page-recruit__movie-label">動画</span>
  </section>

  <section class="p-page-recruit__message">
    <img class="p-page-recruit__message-word" src="<?php echo esc_url($image_uri . '/svg/text_message.svg'); ?>" alt="">
    <header class="p-page-recruit__section-heading"><h1>代表メッセージ</h1></header>
    <h2><span class="p-page-recruit__message-slogan-line">地域の<img class="p-page-recruit__outline-mark" src="<?php echo esc_url($image_uri . '/svg/outline-bracket.svg'); ?>" alt="">おいしい<img class="p-page-recruit__outline-mark p-page-recruit__outline-mark--close" src="<?php echo esc_url($image_uri . '/svg/outline-bracket.svg'); ?>" alt="">を</span><span class="p-page-recruit__message-slogan-line">支える仲間へ。</span></h2>
    <div class="p-page-recruit__message-copy"><p>お客様がセレクションに来て良かったと思っていただける、雰囲気作りや接客サービス向上はもちろんのこと、当社の社員に対しても、セレクションに入社して良かったと思える環境の創造に力を注いでおります。それは、第一に御来店いただくお客様に対する責任、第二に社員に対する責任、第三に地域社会に対する責任。この三つの責任を果たすことが、私どもの「信条」であります。そして、この三つの当たり前なことを、常に愚直に取り組むことこそ、私どもの使命であると確信しております。</p></div>
    <p class="p-page-recruit__president"><span>代表取締役　</span><strong>山崎 洋介</strong><b>YAMAZAKI YOSUKE</b></p>
    <picture class="p-page-recruit__president-photo"><img src="<?php echo esc_url($recruit_image('img_message-president-source-01.png', 'bg_campaign-content-01.png')); ?>" alt="代表取締役 山崎洋介"></picture>
  </section>

  <section class="p-page-recruit__about">
    <img class="p-page-recruit__about-word" src="<?php echo esc_url($image_uri . '/svg/text_about-selection.svg'); ?>" alt="">
    <img class="p-page-recruit__about-decoration" src="<?php echo esc_url($recruit_image('img_about-decoration.png', 'bg_recruit.png')); ?>" alt="">
    <header class="p-page-recruit__section-heading p-page-recruit__section-heading--light"><h2>セレクションを<br>知る</h2></header>
    <p class="p-page-recruit__about-lead"><span>セレクションってどんな会社？</span><span>気になる項目から</span><span>チェックしてみてください！</span></p>
    <div class="p-page-recruit__about-list">
      <?php foreach ($about_items as [$number, $title, $text, $image, $link]) : ?>
        <article class="p-page-recruit__about-item">
          <span class="p-page-recruit__about-number"><?php echo esc_html($number); ?></span>
          <h3><?php echo esc_html($title); ?></h3><p><?php echo wp_kses($text, ['br' => []]); ?></p>
          <picture><img src="<?php echo esc_url($recruit_image($image . '.png', 'bg_recruit-content-01.jpg')); ?>" alt=""></picture>
          <?php if ($number === '02') : ?>
            <img class="p-page-recruit__about-girl" src="<?php echo esc_url($image_uri . '/img_about-girl-02.png'); ?>" alt="">
          <?php endif; ?>
          <a href="<?php echo esc_url($link); ?>">詳しく見る<span aria-hidden="true"></span></a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="p-page-recruit__voices">
    <img class="p-page-recruit__voices-word" src="<?php echo esc_url($voices_word_uri); ?>" alt="">
    <header class="p-page-recruit__section-heading"><h2>先輩の声</h2></header>
    <img class="p-page-recruit__voices-decoration" src="<?php echo esc_url($recruit_image('img_voice-decoration.png', 'bg_recruit.png')); ?>" alt="">
    <p>「セレクションってどんなところ？」<br>そんな疑問に答えるべく、<br>働いている先輩たちに<br>インタビューしてみました。<br>仕事のこと、仲間のこと、未来のこと。<br>ちょっと覗いてみてください。</p>
    <div class="p-page-recruit__voice-slider" data-recruit-slider>
      <div class="p-page-recruit__voice-track">
        <?php foreach ($voice_items as $index => [$voice_title, $voice_year, $voice_role, $voice_bg, $voice_person]) : ?>
          <article class="p-page-recruit__voice-card<?php echo $index === 1 ? ' is-active' : ''; ?>" data-slide="<?php echo esc_attr($index); ?>">
            <div class="p-page-recruit__voice-photo">
              <img class="p-page-recruit__voice-background" src="<?php echo esc_url($image_uri . '/' . $voice_bg); ?>" alt="">
              <img class="p-page-recruit__voice-person" src="<?php echo esc_url($image_uri . '/' . $voice_person); ?>" alt="">
            </div>
            <div class="p-page-recruit__voice-body">
              <h3><?php echo wp_kses($voice_title, ['br' => []]); ?></h3>
              <div class="p-page-recruit__voice-meta"><span><?php echo esc_html($voice_year); ?></span><strong><?php echo wp_kses($voice_role, ['br' => []]); ?></strong></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="p-page-recruit__voice-dots" role="group" aria-label="先輩社員の紹介を切り替える">
      <button type="button" aria-label="1人目の先輩社員" aria-pressed="false">01</button>
      <button type="button" class="is-active" aria-label="2人目の先輩社員" aria-pressed="true">02</button>
      <button type="button" aria-label="3人目の先輩社員" aria-pressed="false">03</button>
    </div>
  </section>

  <div class="p-page-recruit__entry-group-layer" aria-hidden="true">
    <img class="p-page-recruit__entry-group" src="<?php echo esc_url($image_uri . '/img_entry-group-02.png'); ?>" alt="">
  </div>

  <section class="p-page-recruit__entry">
    <div class="p-page-recruit__entry-word" aria-hidden="true">
      <img src="<?php echo esc_url($image_uri . '/svg/decoration_entry.svg'); ?>" alt="">
      <img src="<?php echo esc_url($image_uri . '/svg/text_entry-waiting.svg'); ?>" alt="">
    </div>
    <div class="p-page-recruit__entry-title"><span>ENTRY</span><p>募集要項、ご応募はこちらから</p></div>
    <a href="<?php echo esc_url(home_url('/recruit/entry/')); ?>">新卒採用<span aria-hidden="true">→</span></a>
    <a href="<?php echo esc_url(home_url('/recruit/entry/')); ?>">キャリア採用<span aria-hidden="true">→</span></a>
  </section>
</main>

<div class="p-page-recruit__entry-person-layer" aria-hidden="true">
  <img src="<?php echo esc_url($recruit_image('img_entry-person.png', 'bg_recruit.png')); ?>" alt="" class="p-page-recruit__entry-person">
</div>

<?php get_footer('company'); ?>
