<?php
$is_confirm = get_query_var('foods_business_step') === 'confirm';
if ($is_confirm) {
    foods_business_contact_check_request();
    $values = foods_business_contact_values();
    $token = bin2hex(random_bytes(16));
    set_transient('foods_business_' . $token, true, 30 * MINUTE_IN_SECONDS);
}
get_header('company');
?>
<main class="c-main p-business-form-page<?php echo $is_confirm ? ' p-business-form-page--confirm' : ' p-business-form-page--thanks'; ?>">
    <nav class="c-breadcrumb p-business-form-page__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
        <a href="<?php echo esc_url(home_url('/company/business/contact/')); ?>">お問い合わせ</a><span aria-hidden="true">›</span>
        <span><?php echo $is_confirm ? 'お問い合わせ内容の確認' : 'お問い合わせ完了'; ?></span>
    </nav>
    <section class="p-business-form-page__content">
        <h1><?php echo $is_confirm ? 'お問い合わせ内容の確認' : 'お問い合わせありがとうございます'; ?></h1>
        <?php if ($is_confirm) : ?>
            <p class="p-business-form-page__lead">内容をご確認のうえ、<br>「送信」ボタンを押してください。</p>
            <form class="p-business-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <?php wp_nonce_field('foods_business_contact', 'foods_business_nonce'); ?>
                <input type="hidden" name="action" value="foods_business_contact_send">
                <input type="hidden" name="foods_business_token" value="<?php echo esc_attr($token); ?>">
                <?php foreach ($values as $key => $value) : ?>
                    <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>">
                <?php endforeach; ?>
                <dl class="p-business-confirm__list">
                    <?php foreach (foods_cf7_labels('business') as $key => $label) : ?>
                        <div class="p-business-confirm__row">
                            <dt class="p-business-confirm__label"><?php echo esc_html(str_replace(['住所1', '住所2'], ['住所１', '住所２'], $label)); ?></dt>
                            <dd class="p-business-confirm__value"><?php echo nl2br(esc_html($values[$key] !== '' ? $values[$key] : '－')); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
                <button class="p-business-form__submit" type="submit">送信</button>
            </form>
        <?php else : ?>
            <p class="p-business-form-page__lead">お問い合わせを受け付けました。<br>内容を確認のうえ、担当者よりご連絡いたします。</p>
            <?php if (isset($_GET['receipt']) && $_GET['receipt'] === 'failed') : ?>
                <p role="alert">お問い合わせは受け付けましたが、受付メールを送信できませんでした。</p>
            <?php endif; ?>
            <a class="p-business-thanks__home" href="<?php echo esc_url(home_url('/')); ?>">TOPへ戻る</a>
        <?php endif; ?>
    </section>
</main>
<?php get_footer('company'); ?>
