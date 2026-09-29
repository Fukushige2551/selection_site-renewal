<?php
/**
 * Template Name: 採用情報：新卒募集要項・エントリー
 */

$image_uri = get_template_directory_uri() . '/img/page/page-recruit-entry';

$requirements = [
  ['職種', '総合職（販売・バイヤー・商品管理）'],
  ['募集人数', '6 ~ 10名'],
  ['募集対象', '2026年3月、大学・大学院・専門・高専・短大卒業（修了）見込みの方<br>3年以内に大学・大学院・専門・高専・短大卒業（修了）された方'],
  ['初任給', '<span class="p-recruit-entry__dot-line">大卒：256,862円</span><span class="p-recruit-entry__dot-line">短大・専門卒：251,888円</span><span class="p-recruit-entry__dot-line">高卒：249,197円</span><small>※2025年度実績<span class="p-recruit-entry__regular-note">みなし残業30時間分含む</span>※短大・専門・高卒はみなし25時間分を含む<br>25時間・30時間を超える場合は別途支給</small>'],
  ['諸手当', '交通費支給'],
  ['昇給', '年1回'],
  ['賞与', '年2回（7月・12月）<br><small>※業務実績による</small>'],
  ['勤務時間', '7:00 ~ 16:15（シフト制による）'],
  ['休日休暇', 'シフトによる週休2日制<br><small>（1ヶ月単位の変形労働時間制による）</small><br>年間休日107日<br>夏季6連休制度・冬季6連休制度有り'],
  ['福利厚生', '健康保険・厚生年金・雇用保険・労災保険加入、資格取得支援制度有り<br><small>（スーパーマーケット検定・調理師免許・衛生管理者他）</small>'],
  ['研修制度', '制度あり<br>新人研修'],
  ['自己啓発支援', '制度あり<br><strong>【スーパーマーケット検定】</strong><span class="p-recruit-entry__dot-line">通信教育受講費用・講習費用・<br>テキスト費用は会社負担</span><span class="p-recruit-entry__dot-line">試験受験費用は初回のみ会社負担</span><br><strong>【その他】</strong><br>・食品表示管理士<br>・第二種衛生管理者<br>・調理師<span class="p-recruit-entry__detail-indent">（食肉・水産・惣菜に配属の社員対象）</span>・チェッカー技能検定<br>・チーズ検定<br>・ワインアドバイザー'],
];

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
  <nav class="p-recruit-entry__breadcrumb" aria-label="パンくずリスト">
    <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
    <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span aria-hidden="true">›</span>
    <span>募集要項・エントリー</span>
  </nav>

  <section class="p-recruit-entry__requirements" aria-label="新卒採用募集要項">
    <div class="p-recruit-entry__hero">
      <img src="<?php echo esc_url($image_uri . '/hero-new-graduate.png'); ?>" alt="売場で働くセレクションのスタッフ">
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

      <form class="p-recruit-entry__form" action="<?php echo esc_url(home_url('/recruit/entry/confirm/')); ?>" method="post" novalidate>
        <fieldset class="p-recruit-entry__type">
          <legend>応募区分 <em>*必須</em></legend>
          <div>
            <label><input type="radio" name="application_type" value="新卒" required checked><span>新卒</span></label>
            <label><input type="radio" name="application_type" value="インターン"><span>インターン</span></label>
            <label><input type="radio" name="application_type" value="キャリア"><span>キャリア</span></label>
          </div>
        </fieldset>

        <?php foreach ($fields as [$name, $label, $type, $autocomplete]) : ?>
          <label class="p-recruit-entry__field p-recruit-entry__field--<?php echo esc_attr(str_replace('_', '-', $name)); ?>">
            <span><?php echo esc_html($label); ?> <em>*必須</em></span>
            <input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($name); ?>" autocomplete="<?php echo esc_attr($autocomplete); ?>" required>
          </label>
        <?php endforeach; ?>

        <label class="p-recruit-entry__field">
          <span>その他内容 <em>*必須</em></span>
          <textarea name="message" required></textarea>
        </label>

        <label class="p-recruit-entry__privacy">
          <input type="checkbox" name="privacy_agree" value="1" required>
          <span><a href="<?php echo esc_url(home_url('/privacy/')); ?>">プライバシーポリシー</a>に同意して送信する</span>
        </label>

        <button class="p-recruit-entry__submit" type="submit">確認</button>
      </form>
    </div>
  </section>
</main>

<?php get_footer('company'); ?>
