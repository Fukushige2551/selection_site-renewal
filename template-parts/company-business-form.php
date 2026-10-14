<?php
$is_entry = isset($business_form_type) && $business_form_type === 'entry';
$page_title = $is_entry ? 'エントリーフォーム' : 'お問い合わせ';
$form_action = $is_entry ? '/job/entry/confirm/' : '/company/business/contact/confirm/';

$contact_fields = [
    ['inquiry_type', 'お問い合わせ内容', 'select', true, ['お取引について', '商品について', 'その他']],
    ['company_name', '企業名', 'text', true],
    ['department', '部署名', 'text', false],
    ['postal_code', '郵便番号', 'text', true, 'postal-code', 'postal'],
    ['address_1', '住所1', 'text', true, 'address-line1'],
    ['address_2', '住所2（建物名）', 'text', false, 'address-line2'],
    ['phone', '電話番号', 'tel', true, 'tel'],
    ['fax', 'FAX番号', 'tel', false],
    ['contact_name', '担当者名', 'text', true, 'name'],
    ['contact_name_kana', '担当者名（フリガナ）', 'text', false],
    ['email', 'メールアドレス', 'email', true, 'email'],
    ['email_confirm', 'メールアドレス（確認）', 'email', true, 'email'],
    ['main_products', '主力商品', 'textarea-small', false],
    ['site_url', 'サイトURL', 'url', false, 'url'],
    ['message', 'お問い合わせ内容', 'textarea', true],
];

$entry_store_choices = $is_entry ? array_values(foods_get_job_shop_choices()) : [];

$entry_fields = [
    ['store', '応募店舗', 'select', false, $entry_store_choices],
    ['job', '応募職種', 'select', true, array_values(foods_get_job_occupation_choices())],
    ['name', 'お名前', 'text', true, 'name'],
    ['name_kana', 'お名前（カナ）', 'text', false],
    ['phone', '電話番号', 'tel', true, 'tel'],
    ['age', '年齢', 'number', true],
    ['email', 'メールアドレス', 'email', true, 'email'],
    ['email_confirm', 'メールアドレス（確認）', 'email', true, 'email'],
    ['contact_time', '都合の良い連絡時間帯', 'select', false, ['いつでも', '午前', '午後', '夕方以降']],
    ['message', 'その他', 'textarea', false],
];

$fields = $is_entry ? $entry_fields : $contact_fields;
$entry_defaults = [];
if ($is_entry) {
    foreach (['store', 'job'] as $entry_key) {
        $entry_value = $_GET[$entry_key] ?? '';
        $entry_defaults[$entry_key] = is_string($entry_value)
            ? sanitize_text_field(wp_unslash($entry_value))
            : '';
    }
}

get_header('company');
?>

<main class="p-business-form-page<?php echo $is_entry ? '' : ' p-business-form-page--contact'; ?>">
    <nav class="p-business-form-page__breadcrumb" aria-label="パンくずリスト">
        <?php if ($is_entry) : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/job/')); ?>">パートアルバイト募集</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/job/search/')); ?>">検索結果</a><span aria-hidden="true">›</span>
            <span><?php echo esc_html($page_title); ?></span>
        <?php else : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/company/')); ?>">企業情報</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/company/business/')); ?>">企業の方へ</a><span aria-hidden="true">›</span>
            <span><?php echo esc_html($page_title); ?></span>
        <?php endif; ?>
    </nav>

    <section class="p-business-form-page__content" aria-labelledby="business-form-title">
        <h1 id="business-form-title"><?php echo esc_html($page_title); ?></h1>
        <?php if ($is_entry) : ?>
            <p class="p-business-form-page__lead">各項目を入力の上、<br class="u-business-form-sp">「入力確認」ボタンを押してください。</p>
        <?php else : ?>
            <p class="p-business-form-page__lead">お取引希望の業者様は下記の問い合わせフォームよりお問合せください。<br>各項目を入力の上、<br class="u-business-form-sp">「入力確認」ボタンを押してください。</p>
        <?php endif; ?>

        <?php foods_cf7_render($is_entry ? 'job' : 'business', $form_action, 'p-business-form'); ?>
    </section>
</main>

<?php get_footer('company'); ?>
