<?php $image_uri = get_template_directory_uri() . '/img/page/page-recruit'; ?>
  <div class="p-page-recruit__entry-group-layer" aria-hidden="true">
    <?php if (!empty($args['interview_images'])) : $group_uri = get_template_directory_uri() . '/img/page/page-recruit-interview/'; ?>
    <span class="p-page-recruit__entry-group p-interview__compressed-group">
      <img src="<?php echo esc_url($group_uri . 'compressed/entry-group-transparent-source.png'); ?>" alt="" loading="lazy" decoding="async">
    </span>
    <?php else : ?>
    <img class="p-page-recruit__entry-group" src="<?php echo esc_url($image_uri . '/img_entry-group-02.png'); ?>" alt="">
    <?php endif; ?>
  </div>

  <section id="recruit-requirements" class="p-page-recruit__entry">
    <div class="p-page-recruit__entry-word" aria-hidden="true">
      <img src="<?php echo esc_url($image_uri . '/svg/decoration_entry.svg'); ?>" alt="">
      <img src="<?php echo esc_url($image_uri . '/svg/text_entry-waiting.svg'); ?>" alt="">
    </div>
    <?php if (!empty($args['interview_images'])) : ?>
    <span class="p-interview__entry-tab-copy" aria-hidden="true">エントリー<br>お待ち<br>しています！</span>
    <?php endif; ?>
    <div class="p-page-recruit__entry-title"><span>ENTRY</span><p>募集要項、ご応募はこちらから</p></div>
    <div class="p-page-recruit__entry-actions">
      <a href="<?php echo esc_url(home_url('/entry/')); ?>">新卒採用<span aria-hidden="true">→</span></a>
      <a href="<?php echo esc_url(home_url('/career-entry/')); ?>">キャリア採用<span aria-hidden="true">→</span></a>
    </div>
    <div class="p-page-recruit__entry-review-wrap">
      <a class="p-page-recruit__entry-review-link c-pop" href="https://kai-z.net/companies/foods-selection/" target="_blank" rel="noopener noreferrer">
        <picture class="p-page-recruit__entry-review-banner">
          <source media="(max-width: 767px)" srcset="<?php echo esc_url($image_uri . '/svg/recruit-review-banner-sp.svg'); ?>">
          <img src="<?php echo esc_url($image_uri . '/svg/recruit-review-banner-pc.svg'); ?>" alt="口コミ・評判に公式回答するポータルメディア kai 匿名質問受付中">
        </picture>
      </a>
    </div>
    <?php if (empty($args['interview_images'])) : ?>
    <div class="p-page-recruit__entry-person-layer p-page-recruit__entry-person-layer--inside" aria-hidden="true">
      <img src="<?php echo esc_url($image_uri . '/img_entry-person.png'); ?>" alt="" class="p-page-recruit__entry-person">
    </div>
    <?php endif; ?>
  </section>
  <?php if (empty($args['interview_images'])) : ?>
  <div class="p-page-recruit__entry-person-tablet-layer" aria-hidden="true">
    <img src="<?php echo esc_url($image_uri . '/img_entry-person.png'); ?>" alt="" class="p-page-recruit__entry-person">
  </div>
  <?php endif; ?>
