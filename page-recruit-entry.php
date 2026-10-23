<?php
/**
 * Template Name: 採用情報：新卒募集要項・エントリー
 */

$image_uri = get_template_directory_uri() . '/img/page/page-recruit-entry';
$is_career_entry = basename((string) get_page_template()) === 'page-recruit-career-entry.php';

$requirements = foods_recruit_requirements(get_queried_object_id());
foods_schema_recruit_page(get_queried_object_id(), $requirements);
$confirmed_rows = foods_job_schema_public_rows(foods_job_schema_values(get_queried_object_id()));
$requirements = array_merge($requirements, array_map(
    static fn($label, $value) => [$label, esc_html($value)],
    array_keys($confirmed_rows),
    array_values($confirmed_rows)
));
$hero_filename = $is_career_entry ? 'hero-career.png' : 'hero-new-graduate.png';
$form_action = foods_application_config($is_career_entry ? 'career' : 'graduate')['path'] . 'confirm/';
$requirements_label = $is_career_entry ? 'キャリア採用募集要項' : '新卒採用募集要項';

$fields = [
  ['name', 'お名前', 'text', 'name'],
  ['name_kana', 'お名前（カナ）', 'text', 'off'],
  ['postal_code', '郵便番号', 'text', 'postal-code'],
  ['address_1', '住所1', 'text', 'address-line1'],
  ['address_2', '住所2（建物名）', 'text', 'address-line2'],
  ['phone', '電話番号', 'tel', 'tel'],
  ['birth_date', '生年月日', 'text', 'bday'],
  ['education', '最終学歴', 'text', 'off'],
  ['email', 'メールアドレス', 'email', 'email'],
  ['email_confirm', 'メールアドレス（確認）', 'email', 'email'],
];

get_header('company');
?>

<main id="page-recruit-entry" class="p-recruit-entry">
  <?php ob_start(); ?>
<nav class="p-recruit-entry__breadcrumb" aria-label="パンくずリスト">
    <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
    <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span aria-hidden="true">›</span>
    <span>募集要項・エントリー</span>
  </nav>
<?php echo foods_schema_breadcrumb_html(ob_get_clean()); ?>

  <section class="p-recruit-entry__requirements" aria-label="<?php echo esc_attr($requirements_label); ?>">
    <div class="p-recruit-entry__hero">
      <img src="<?php echo esc_url($image_uri . '/' . $hero_filename); ?>" alt="売場で働くセレクションのスタッフ">
    </div>

    <dl class="p-recruit-entry__table">
      <?php foreach ($requirements as [$label, $value]) : ?>
        <div class="p-recruit-entry__row">
          <dt><?php echo esc_html($label); ?></dt>
          <dd><?php echo wp_kses($value, ['br' => [], 'small' => [], 'strong' => [], 'span' => ['class' => []]]); ?></dd>
        </div>
      <?php endforeach; ?>
    </dl>
  </section>

  <section class="p-recruit-entry__application" aria-labelledby="recruit-entry-form-title">
    <picture class="p-recruit-entry__application-bg" aria-hidden="true">
      <source media="(min-width: 768px)" srcset="<?php echo esc_url($image_uri . '/form-bg-pc.svg'); ?>">
      <img src="<?php echo esc_url($image_uri . '/form-bg-sp.svg'); ?>" alt="">
    </picture>
    <div class="p-recruit-entry__application-inner">
      <picture class="p-recruit-entry__application-arrow" aria-hidden="true">
        <source media="(min-width: 768px)" srcset="<?php echo esc_url($image_uri . '/form-arrow-pc.svg'); ?>">
        <img src="<?php echo esc_url($image_uri . '/form-arrow-sp.svg'); ?>" alt="">
      </picture>
      <div class="p-recruit-entry__application-guide">
        <h1 id="recruit-entry-form-title">ご応募は以下のフォームから</h1>
        <p>各項目を入力の上、<br class="u-sp-only">「入力確認」ボタンを押してください。</p>
      </div>

      <?php foods_cf7_render($is_career_entry ? 'career' : 'graduate', $form_action, 'p-recruit-entry__form'); ?>
    </div>
  </section>
</main>

<?php get_footer('company'); ?>
