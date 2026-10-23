<?php
/* Template Name: お問い合わせ（店舗ごと・業者様向け） */
get_header();
?>

<main id="page-contact" class="c-main p-contact">
    <?php ob_start(); ?>
<nav class="c-breadcrumb p-contact__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span>お問い合わせ</span>
    </nav>
<?php echo foods_schema_breadcrumb_html(ob_get_clean()); ?>
    <section class="p-contact__hero" aria-labelledby="contact-title">
        <div class="p-contact__inner">
            <h1 id="contact-title" class="p-contact__title">お問い合わせ</h1>
            <div class="p-contact__lead">
                <p>弊社へのご意見・ご要望・その他<br>お気づきの点がございましたら、<br>是非お知らせください。</p>
                <p>お客様の貴重なお声を、<br>より良い店づくりに生かして参ります。</p>
            </div>
            <p class="p-contact__note">※商品交換・返金等は直接、対象店舗にご連絡ください。</p>
        </div>
    </section>

    <section class="p-contact__caution" aria-labelledby="contact-caution-title">
        <div class="p-contact__inner">
            <div class="p-contact-caution">
                <h2 id="contact-caution-title" class="p-contact-caution__title">お問い合わせに関しましての注意点</h2>
                <ul class="p-contact-caution__list">
                    <li>お急ぎの案件は、直接対象店舗へ電話にて確認をお願いいたします。</li>
                    <li>Eメールの特性上、送信過程で内容欠落、送信遅延などの不具合が生じることがございます。<br>これらにつきましては当社といたしまして、一切の責任を負いかねます。</li>
                    <li>お問い合わせが重なるお時間帯など、ご連絡にお時間がかかる場合がございます。</li>
                    <li>内容によっては回答しかねる場合がございますのでご了承ください。</li>
                    <li>システム障害によりお答えできない場合がございます。誠に申し訳ございませんが、なにとぞご理解を賜りますようよろしくお願い申し上げます。</li>
                    <li>お問い合わせ送信後に受付メールが届かない場合は、「メールアドレスが誤っている」か「ドメイン指定受信等のメール受信機能を設定されている」場合がございます。「foods-selection.co.jp」からのメールが届くようにドメイン設定をお願いいたします。</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="p-contact__store" aria-labelledby="contact-store-title">
        <div class="p-contact__inner">
            <?php foods_cf7_render('contact', '/contact/confirm/', 'p-contact-form'); ?>
        </div>
    </section>
</main>

<?php
get_footer();
