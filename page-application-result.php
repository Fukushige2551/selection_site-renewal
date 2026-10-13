<?php
$kind = get_query_var('foods_application_kind');
$config = foods_application_config($kind);
$is_confirm = get_query_var('foods_application_step') === 'confirm';
if ($is_confirm) {
    foods_application_check_request($kind);
    $values = foods_application_values($kind);
    $token = bin2hex(random_bytes(16));
    set_transient('foods_application_' . $token, $kind, 30 * MINUTE_IN_SECONDS);
}
get_header('company');
?>
<main class="c-main p-business-form-page">
    <nav class="c-breadcrumb p-business-form-page__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
        <a href="<?php echo esc_url(home_url($config['path'])); ?>"><?php echo esc_html($config['title']); ?></a><span aria-hidden="true">›</span>
        <span><?php echo $is_confirm ? '応募内容の確認' : '応募完了'; ?></span>
    </nav>
    <section class="p-business-form-page__content">
        <h1><?php echo $is_confirm ? '応募内容の確認' : 'ご応募ありがとうございます'; ?></h1>
        <?php if ($is_confirm) : ?>
            <p class="p-business-form-page__lead">内容をご確認のうえ、「送信」ボタンを押してください。</p>
            <form class="p-business-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <?php wp_nonce_field('foods_application_' . $kind, 'foods_application_nonce'); ?>
                <input type="hidden" name="action" value="foods_application_send">
                <input type="hidden" name="foods_application_kind" value="<?php echo esc_attr($kind); ?>">
                <input type="hidden" name="foods_application_token" value="<?php echo esc_attr($token); ?>">
                <?php foreach ($values as $key => $value) : ?>
                    <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>">
                <?php endforeach; ?>
                <?php foreach (foods_cf7_labels($kind) as $key => $label) : ?>
                    <div class="p-business-form__field"><span class="p-business-form__label"><?php echo esc_html($label); ?></span><div><?php echo nl2br(esc_html($values[$key] !== '' ? $values[$key] : '－')); ?></div></div>
                <?php endforeach; ?>
                <button class="p-business-form__submit" type="submit">送信</button>
            </form>
        <?php else : ?>
            <p class="p-business-form-page__lead">ご応募を受け付けました。<br>内容を確認のうえ、担当者よりご連絡いたします。</p>
            <?php if (isset($_GET['receipt']) && $_GET['receipt'] === 'failed') : ?><p role="alert">ご応募は受け付けましたが、受付メールを送信できませんでした。</p><?php endif; ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">TOPへ戻る</a>
        <?php endif; ?>
    </section>
</main>
<?php get_footer('company'); ?>
