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
    ['store', '応募店舗', 'select', true, $entry_store_choices],
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
$entry_job = null;
$entry_blocked = false;
$entry_choices = [];
if ($is_entry) {
    $entry_job_id = isset($_GET['job_id']) && is_string($_GET['job_id'])
        && preg_match('/^[1-9][0-9]*$/D', $_GET['job_id'])
        ? (int) $_GET['job_id'] : 0;
    $entry_job = $entry_job_id ? get_post($entry_job_id) : null;
    if ($entry_job && ($entry_job->post_type !== 'recruit_part_time' || $entry_job->post_status !== 'publish')) {
        $entry_job = null;
    }
    $entry_choices = foods_job_application_choices($entry_job_id);
    $entry_blocked = (isset($_GET['job_id']) && (!$entry_job || !foods_job_can_apply($entry_job_id))) || !$entry_choices;
    foreach (['store', 'job'] as $entry_key) {
        $entry_value = $_GET[$entry_key] ?? '';
        $entry_defaults[$entry_key] = is_string($entry_value)
            ? sanitize_text_field(wp_unslash($entry_value))
            : '';
    }
}

get_header('company');
?>

<main class="p-business-form-page<?php echo $is_entry ? ' p-business-form-page--entry' : ' p-business-form-page--contact'; ?>">
    <?php ob_start(); ?>
<nav class="p-business-form-page__breadcrumb" aria-label="パンくずリスト">
        <?php if ($is_entry) : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/job/')); ?>">パートアルバイト募集</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/job/search/')); ?>">検索結果</a><span aria-hidden="true">›</span>
            <?php if ($entry_job) : ?>
                <a href="<?php echo esc_url(get_permalink($entry_job)); ?>"><?php echo esc_html(get_the_title($entry_job)); ?></a><span aria-hidden="true">›</span>
            <?php endif; ?>
            <span><?php echo esc_html($page_title); ?></span>
        <?php else : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/company/')); ?>">企業情報</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/company/business/')); ?>">企業の方へ</a><span aria-hidden="true">›</span>
            <span><?php echo esc_html($page_title); ?></span>
        <?php endif; ?>
    </nav>
<?php echo foods_schema_breadcrumb_html(ob_get_clean()); ?>

    <section class="p-business-form-page__content" aria-labelledby="business-form-title">
        <h1 id="business-form-title"><?php echo esc_html($page_title); ?></h1>
        <?php if ($is_entry && $entry_blocked) : ?>
            <p class="p-business-form-page__lead" role="status"><?php echo $entry_job && foods_job_is_closed($entry_job->ID) ? 'この求人は募集を終了しました。' : '現在応募を受け付けている求人がありません。'; ?></p>
            <a href="<?php echo esc_url(home_url('/job/search/')); ?>">募集中の求人を探す</a>
        <?php elseif ($is_entry) : ?>
            <p class="p-business-form-page__lead">各項目を入力の上、<br class="u-business-form-sp">「入力確認」ボタンを押してください。</p>
        <?php else : ?>
            <p class="p-business-form-page__lead">お取引希望の業者様は下記の問い合わせフォームよりお問合せください。<br>各項目を入力の上、<br class="u-business-form-sp">「入力確認」ボタンを押してください。</p>
        <?php endif; ?>

        <?php if (!$is_entry || !$entry_blocked) : ?>
            <?php foods_cf7_render($is_entry ? 'job' : 'business', $form_action, 'p-business-form'); ?>
        <?php endif; ?>
        <?php if ($is_entry && !$entry_blocked) : ?>
            <p>別の求人へ応募する場合は、<a href="<?php echo esc_url(home_url('/job/search/')); ?>">求人一覧</a>から選び直してください。</p>
            <script type="application/json" id="foods-job-choices"><?php echo wp_json_encode($entry_choices, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
            <script>
            (() => {
                const data = document.getElementById('foods-job-choices');
                const form = data.closest('section').querySelector('form.p-business-form');
                const store = form?.querySelector('select[name="store"]');
                const job = form?.querySelector('select[name="job"]');
                if (!store || !job) return;
                const choices = JSON.parse(data.textContent);
                const refresh = () => {
                    const selected = job.value;
                    job.replaceChildren(...(choices[store.value] || []).map(value => new Option(value, value)));
                    if ((choices[store.value] || []).includes(selected)) job.value = selected;
                };
                store.addEventListener('change', refresh);
                refresh();
            })();
            </script>
        <?php endif; ?>
    </section>
</main>

<?php get_footer('company'); ?>
