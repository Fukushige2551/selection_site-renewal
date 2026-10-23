<?php
/**
 * Template Name: 採用情報
 */

$image_uri = get_template_directory_uri() . '/img/page/page-recruit';
$image_path = get_template_directory() . '/img/page/page-recruit';
$theme_image_uri = get_template_directory_uri() . '/img/page/front-page';
$recruit_video_uri = get_template_directory_uri() . '/video/page-recruit';
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
$president_webp_file = $image_path . '/img_message-president-source-01.webp';
$president_webp_uri = $image_uri . '/img_message-president-source-01.webp';
if (file_exists($president_webp_file)) {
  $president_webp_uri .= '?v=' . filemtime($president_webp_file);
}

$recruit_work_page = get_page_by_path('recruit/work', OBJECT, 'page');
$recruit_work_url = $recruit_work_page
  ? get_permalink($recruit_work_page)
  : home_url('/recruit/work/');

$about_items = [
  ['01', '私たちについて', '会社名である”セレクション”の由来は？<br>会社の目指す形とともに紹介します', 'img_work-source', $recruit_work_url . '#about-selection'],
  ['02', '仕事内容', '食品を扱う現場ではどんな仕事をしているのか<br>製造・販売・仕入れ、企画、それぞれのお仕事<br>について紹介します', 'img_career-source', $recruit_work_url . '#jobs'],
  ['03', 'キャリアアップ', '一人ひとりの成長を全力でサポート。<br>将来のキャリアの描き方を紹介します。', 'img_day-flow-source', $recruit_work_url . '#career'],
  ['04', '教育・資格支援制度', '小売り販売で活かせる資格の取得をサポート！<br>自身の力になるキャリアアップを支援します', 'img_training-original', $recruit_work_url . '#training'],
  ['05', '1日の仕事の流れ', '入社●年目の先輩社員の1日をご紹介', 'img_day-flow-original', $recruit_work_url . '#daily-flow'],
];

get_header('company');
get_template_part('template-parts/recruit/hero-navigation');
?>

<main id="page-recruit" class="p-page-recruit">
  <nav class="p-page-recruit__breadcrumb" aria-label="パンくずリスト">
    <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span><span>採用情報</span>
  </nav>

  <section class="p-page-recruit__hero">
    <video class="p-page-recruit__hero-media js-recruit-hero-video" data-src-pc="<?php echo esc_url($recruit_video_uri . '/recruit-pc.mp4'); ?>" data-src-sp="<?php echo esc_url($recruit_video_uri . '/recruit-sp.mp4'); ?>" autoplay muted loop playsinline aria-label="セレクション採用プロモーション動画"></video>
  </section>

  <section class="p-page-recruit__message">
    <img class="p-page-recruit__message-word" src="<?php echo esc_url($image_uri . '/svg/text_message.svg'); ?>" alt="">
    <header class="p-page-recruit__section-heading"><h1>代表メッセージ</h1></header>
    <h2><span class="p-page-recruit__message-slogan-line">地域の<img class="p-page-recruit__outline-mark" src="<?php echo esc_url($image_uri . '/svg/outline-bracket.svg'); ?>" alt="">おいしい<img class="p-page-recruit__outline-mark p-page-recruit__outline-mark--close" src="<?php echo esc_url($image_uri . '/svg/outline-bracket.svg'); ?>" alt="">を</span><span class="p-page-recruit__message-slogan-line">支える仲間へ。</span></h2>
    <div class="p-page-recruit__message-copy"><p>お客様がセレクションに来て良かったと思っていただける、<br class="p-page-recruit__message-break">雰囲気作りや接客サービス向上はもちろんのこと、当社の社員に対しても、<br class="p-page-recruit__message-break">セレクションに入社して良かったと思える環境の創造に力を注いでおります。<br class="p-page-recruit__message-break">それは、第一に御来店いただくお客様に対する責任、第二に社員に対する責任、第三に地域社会に対する責任。<br class="p-page-recruit__message-break">この三つの責任を果たすことが、私どもの「信条」であります。<br class="p-page-recruit__message-break">そして、この三つの当たり前なことを、常に愚直に取り組むことこそ、<br class="p-page-recruit__message-break">私どもの使命であると確信しております。</p></div>
    <p class="p-page-recruit__president"><span>代表取締役　</span><strong>山崎 洋介</strong><b>YAMAZAKI YOSUKE</b></p>
    <picture class="p-page-recruit__president-photo">
      <source srcset="<?php echo esc_url($president_webp_uri); ?>" type="image/webp">
      <img src="<?php echo esc_url($recruit_image('img_message-president-source-01.png', 'bg_campaign-content-01.png')); ?>" alt="代表取締役 山崎洋介">
    </picture>
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

  <section id="recruit-interviews" class="p-page-recruit__voices">
    <img class="p-page-recruit__voices-word" src="<?php echo esc_url($voices_word_uri); ?>" alt="">
    <header class="p-page-recruit__section-heading"><h2>先輩の声</h2></header>
    <img class="p-page-recruit__voices-decoration" src="<?php echo esc_url($recruit_image('img_voice-decoration.png', 'bg_recruit.png')); ?>" alt="">
    <p>「セレクションってどんなところ？」<br>そんな疑問に答えるべく、<br>働いている先輩たちに<br>インタビューしてみました。<br>仕事のこと、仲間のこと、未来のこと。<br>ちょっと覗いてみてください。</p>
    <?php get_template_part('template-parts/recruit/voices'); ?>
  </section>

  <?php get_template_part('template-parts/recruit/entry'); ?>
</main>

<div class="p-page-recruit__entry-person-layer" aria-hidden="true">
  <img src="<?php echo esc_url($recruit_image('img_entry-person.png', 'bg_recruit.png')); ?>" alt="" class="p-page-recruit__entry-person">
</div>

<?php get_footer('company'); ?>
