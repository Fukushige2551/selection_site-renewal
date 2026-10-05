<?php
/**
 * Template Name: 社員インタビュー
 */
$interview_number = foods_get_recruit_interview_number(get_queried_object_id());
$interviews = foods_get_recruit_interviews();
$interview = $interviews[$interview_number] ?? null;
if (!$interview) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    include get_404_template();
    return;
}
$image_uri = get_template_directory_uri() . '/img/page/page-recruit';
$interview_image_uri = get_template_directory_uri() . '/img/page/page-recruit-interview';
$compressed_image_uri = $interview_image_uri . '/compressed';
get_header('company');
?>
<main class="p-page-recruit p-interview p-interview--<?php echo esc_attr($interview_number); ?>">
  <nav class="p-interview__breadcrumb" aria-label="パンくずリスト">
    <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
    <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span aria-hidden="true">›</span>
    <span aria-current="page">社員インタビュー</span>
  </nav>
  <header class="p-interview__hero">
    <picture class="p-interview__portrait">
      <source media="(min-width: 1024px)" srcset="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-hero-pc@2x.png'); ?>" width="1606" height="966">
      <source media="(min-width: 768px)" srcset="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-hero-tab@2x.png'); ?> 1536w, <?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-hero-tab-max-width@2x.png'); ?> 2046w" sizes="100vw" width="1536" height="860">
      <img src="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-hero-sp@2x.png'); ?>" srcset="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-hero-sp@2x.png'); ?> 780w, <?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-hero-sp-max-width@2x.png'); ?> 1534w" sizes="100vw" width="780" height="540" alt="<?php echo esc_attr($interview['role']); ?>" fetchpriority="high">
    </picture>
    <div class="p-interview__profile">
      <p class="p-interview__year"><?php echo esc_html($interview['year']); ?></p>
      <p class="p-interview__role"><?php echo esc_html($interview['role']); ?></p>
      <h1><?php if (isset($interview['heading_sp'])) : ?><span class="p-interview__copy-sp"><?php echo wp_kses($interview['heading_sp'], ['br' => []]); ?></span><span class="p-interview__copy-pc"><?php echo wp_kses($interview['heading'], ['br' => []]); ?></span><?php else : echo wp_kses($interview['heading'], ['br' => []]); endif; ?></h1>
    </div>
  </header>
  <div class="p-interview__body">
    <?php foreach ($interview['answers'] as $index => $answer) : ?>
      <section class="p-interview__section p-interview__section--<?php echo esc_attr($index + 1); ?>" aria-labelledby="interview-question-<?php echo esc_attr($index + 1); ?>">
        <?php if (isset($interview['photos'][$index])) : $photo = $interview['photos'][$index]; ?>
          <div class="p-interview__photo p-interview__photo--<?php echo esc_attr($photo); ?>">
            <picture>
              <source media="(min-width: 1024px)" srcset="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-' . $photo . '-pc@2x.png'); ?>" width="2000" height="1096">
              <source media="(min-width: 768px)" srcset="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-' . $photo . '-tab@2x.png'); ?>" width="1280" height="720">
              <img src="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-' . $photo . '-sp@2x.png'); ?>" srcset="<?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-' . $photo . '-sp@2x.png'); ?> 718w, <?php echo esc_url($compressed_image_uri . '/' . $interview_number . '-' . $photo . '-sp-4x-limit.png'); ?> 1436w" sizes="calc(100vw - 30px)" alt="<?php echo esc_attr($interview['photo_alts'][$index]); ?>" loading="lazy" decoding="async">
            </picture>
          </div>
        <?php endif; ?>
        <div class="p-interview__qa">
          <h2 id="interview-question-<?php echo esc_attr($index + 1); ?>"><?php if (isset($interview['questions_sp'][$index])) : ?><span class="p-interview__copy-sp"><?php echo wp_kses($interview['questions_sp'][$index], ['br' => []]); ?></span><span class="p-interview__copy-pc"><?php echo esc_html($interview['questions'][$index]); ?></span><?php else : echo esc_html($interview['questions'][$index]); endif; ?><img src="<?php echo esc_url($interview_image_uri . '/speech-pointer.svg'); ?>" alt="" aria-hidden="true" width="28.0719" height="20.0527"></h2>
          <p><?php if (isset($interview['answers_sp'][$index])) : ?><span class="p-interview__copy-sp"><?php echo esc_html($interview['answers_sp'][$index]); ?></span><span class="p-interview__copy-pc"><?php echo esc_html($answer); ?></span><?php else : echo esc_html($answer); endif; ?></p>
        </div>
      </section>
    <?php endforeach; ?>
  </div>
  <section class="p-interview__related" aria-labelledby="other-interviews">
    <h2 id="other-interviews">他の社員の声も見る</h2>
    <p class="p-interview__related-en">Employee Voices</p>
    <?php get_template_part('template-parts/recruit/voices', null, ['initial_index' => $interview['initial_index'], 'interview_images' => true]); ?>
  </section>
  <?php get_template_part('template-parts/recruit/entry', null, ['interview_images' => true]); ?>
</main>
<div class="p-page-recruit__entry-person-layer p-interview__entry-person-layer" aria-hidden="true">
  <span class="p-page-recruit__entry-person p-interview__compressed-person">
    <img src="<?php echo esc_url($compressed_image_uri . '/entry-person-transparent-source.png'); ?>" alt="" loading="lazy" decoding="async">
  </span>
</div>
<?php get_footer('company'); ?>
