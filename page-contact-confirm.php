<?php
/*
Template Name: お問い合わせ確認
*/

get_header();

if (!function_exists('foods_contact_confirm_value')) {
    function foods_contact_confirm_value($key) {
        $value = filter_input(INPUT_POST, $key, FILTER_UNSAFE_RAW);

        if (is_array($value)) {
            $value = implode('、', array_map('sanitize_text_field', $value));
        }

        return is_string($value) ? trim(wp_unslash($value)) : '';
    }
}

if (!function_exists('foods_contact_confirm_display')) {
    function foods_contact_confirm_display($key) {
        $value = foods_contact_confirm_value($key);

        if ($value === '') {
            return '－';
        }

        return nl2br(esc_html($value));
    }
}

$contact_confirm_fields = [
    'contact_store' => 'お問い合わせ先',
    'your_name' => 'お名前',
    'your_kana' => 'お名前（フリガナ）',
    'your_email' => 'メールアドレス',
    'your_email_confirm' => 'メールアドレス（確認）',
    'your_tel' => '電話番号',
    'message' => 'お問い合わせ内容',
];
?>

<main id="page-contact-confirm" class="c-main p-contact p-contact--confirm">
    <nav class="c-breadcrumb p-contact__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span>お問い合わせ内容の確認</span>
    </nav>

    <section class="p-contact__hero" aria-labelledby="contact-confirm-title">
        <div class="p-contact__inner">
            <h1 id="contact-confirm-title" class="p-contact__title">お問い合わせ内容の確認</h1>
        </div>
    </section>

    <section class="p-contact__store p-contact-confirm" aria-labelledby="contact-confirm-title">
        <div class="p-contact__inner">
            <p class="p-contact-confirm__lead">以下のお問い合わせ内容をご確認ください。<br>内容にお間違いがなければ「送信」ボタンを押してください。</p>

            <dl class="p-contact-confirm__list">
                <?php foreach ($contact_confirm_fields as $key => $label) : ?>
                    <div class="p-contact-confirm__item">
                        <dt class="p-contact-confirm__label"><?php echo esc_html($label); ?></dt>
                        <dd class="p-contact-confirm__value"><?php echo foods_contact_confirm_display($key); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <form class="p-contact-confirm__form" action="<?php echo esc_url(home_url('/contact/thanks/')); ?>" method="post">
                <?php foreach ($contact_confirm_fields as $key => $label) : ?>
                    <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr(foods_contact_confirm_value($key)); ?>">
                <?php endforeach; ?>

                <div class="p-contact-actions">
                    <button class="c-btn c-btn--common--green--large p-contact-actions__button" type="submit">送信</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php
get_footer();