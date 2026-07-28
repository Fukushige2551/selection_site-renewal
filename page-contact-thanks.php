<?php
/*
Template Name: お問い合わせ完了
*/

get_header();
?>

<main id="page-contact-thanks" class="c-main p-contact-thanks">
    <nav class="c-breadcrumb p-contact-thanks__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span>お問い合わせ完了</span>
    </nav>

    <section class="p-contact-thanks__content" aria-labelledby="contact-thanks-title">
        <div class="p-contact-thanks__message">
            <h1 id="contact-thanks-title" class="p-contact-thanks__title">お問い合わせ<br>ありがとうございます</h1>
            <p class="p-contact-thanks__text">送信が完了しました</p>
        </div>
        <p class="p-contact-thanks__lead">
            お問い合わせいただきありがとうございます。<br>
            内容を確認のうえ、担当者よりご連絡いたします。<br>
            <span class="p-contact-thanks__lead-sp">受付メールが届かない場合は、<br>
                メールアドレスの入力内容や<br>
                受信設定をご確認ください。</span><span class="p-contact-thanks__lead-desktop">受付メールが届かない場合は、メールアドレスの入力内容や受信設定をご確認ください。</span>
        </p>
        <a class="p-contact-thanks__button" href="<?php echo esc_url(home_url('/')); ?>">TOPページへ</a>
    </section>
</main>

<?php
get_footer();