<?php
$interviews = foods_get_recruit_interviews();
$initial_index = isset($args['initial_index']) ? (int) $args['initial_index'] : 1;
$image_uri = get_template_directory_uri() . '/img/page/page-recruit';
// Carousel order follows article numbers; visual variant IDs remain stable.
$card_order = ['01', '02', '03'];
$card_variants = ['01' => 0, '02' => 2, '03' => 1];
?>
<div class="p-page-recruit__voice-slider" data-recruit-slider data-initial-index="<?php echo esc_attr($initial_index); ?>">
  <div class="p-page-recruit__voice-track">
    <?php foreach ($card_order as $index => $number) : $person = $interviews[$number]; ?>
      <article class="p-page-recruit__voice-card<?php echo $index === $initial_index ? ' is-active' : ''; ?>" data-slide="<?php echo esc_attr($card_variants[$number]); ?>" data-interview-number="<?php echo esc_attr($number); ?>">
        <a class="p-page-recruit__voice-link" href="<?php echo esc_url(foods_get_recruit_interview_url($number)); ?>" aria-label="<?php echo esc_attr($person['role'] . 'のインタビューを読む'); ?>" draggable="false">
          <div class="p-page-recruit__voice-photo">
            <img class="p-page-recruit__voice-background" src="<?php echo esc_url($image_uri . '/' . $person['card_background']); ?>" alt="" draggable="false">
            <img class="p-page-recruit__voice-person" src="<?php echo esc_url($image_uri . '/' . $person['card_person']); ?>" alt="" draggable="false">
          </div>
          <div class="p-page-recruit__voice-body">
            <h3><?php echo wp_kses($person['card_heading'], ['br' => []]); ?></h3>
            <div class="p-page-recruit__voice-meta"><span><?php echo esc_html($person['card_year']); ?></span><strong><?php echo wp_kses($person['card_role'], ['br' => []]); ?></strong></div>
          </div>
        </a>
      </article>
    <?php endforeach; ?>
  </div>
</div>
<div class="p-page-recruit__voice-dots" role="group" aria-label="先輩社員の紹介を切り替える">
  <?php foreach (['01', '02', '03'] as $number) : $index = array_search($number, $card_order, true); ?>
    <button type="button" data-interview-number="<?php echo esc_attr($number); ?>" data-slide-index="<?php echo esc_attr($index); ?>"<?php echo $index === $initial_index ? ' class="is-active"' : ''; ?> aria-label="<?php echo esc_attr($interviews[$number]['role'] . 'のカードを表示'); ?>" aria-pressed="<?php echo $index === $initial_index ? 'true' : 'false'; ?>"><?php echo esc_html($number); ?></button>
  <?php endforeach; ?>
</div>
