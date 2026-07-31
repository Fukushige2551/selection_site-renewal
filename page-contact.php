<?php
get_header();
?>

<main id="page-contact" class="c-main p-contact">
    <nav class="c-breadcrumb p-contact__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span>お問い合わせ</span>
    </nav>
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
            <form class="p-contact-form" action="<?php echo esc_url(home_url('/contact/confirm/')); ?>" method="post" novalidate>
            <fieldset class="p-contact-store">
                <legend id="contact-store-title" class="p-contact-store__title">
                    <span class="p-contact-store__title-text">【お問い合わせ先】</span>
                    <span class="p-contact-store__required">*必須</span>
                </legend>
                <p class="p-contact-store__guide">各項目を入力の上、<br>「入力確認」ボタンを押してください。</p>
                <div class="p-contact-store__options">
                    <div class="p-contact-store__row">
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="全店舗" checked><span>全店舗</span></label>
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="行徳店"><span>行徳店</span></label>
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="西船橋店"><span>西船橋店</span></label>
                    </div>
                    <div class="p-contact-store__row">
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="花野井店"><span>花野井店</span></label>
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="しいの木台店"><span>しいの木台店</span></label>
                    </div>
                    <div class="p-contact-store__row">
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="青葉台店"><span>青葉台店</span></label>
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="西原店"><span>西原店</span></label>
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="松戸店"><span>松戸店</span></label>
                    </div>
                    <div class="p-contact-store__row">
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="西新井店"><span>西新井店</span></label>
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="三郷店"><span>三郷店</span></label>
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="八潮店"><span>八潮店</span></label>
                    </div>
                    <div class="p-contact-store__row">
                        <label class="p-contact-store__option"><input type="radio" name="contact_store" value="業者様"><span>業者様</span></label>
                    </div>
                </div>
                <p class="p-contact-store__business-note">※お取引ご希望の業者様は <a href="<?php echo esc_url(home_url('/company/business/contact/')); ?>">こちら</a> よりお問い合わせください。</p>
            </fieldset>

            <div class="p-contact-fields">
                <div class="p-contact-fields__item">
                    <label class="p-contact-fields__label" for="your-name">お名前 <span>*必須</span></label>
                    <input id="your-name" class="p-contact-fields__control" type="text" name="your_name" autocomplete="name" required>
                </div>
                <div class="p-contact-fields__item">
                    <label class="p-contact-fields__label" for="your-kana">お名前（フリガナ）</label>
                    <input id="your-kana" class="p-contact-fields__control" type="text" name="your_kana" autocomplete="off">
                </div>
                <div class="p-contact-fields__item">
                    <label class="p-contact-fields__label" for="your-email">メールアドレス <span>*必須</span></label>
                    <input id="your-email" class="p-contact-fields__control" type="email" name="your_email" autocomplete="email" required>
                </div>
                <div class="p-contact-fields__item">
                    <label class="p-contact-fields__label" for="your-email-confirm">メールアドレス（確認） <span>*必須</span></label>
                    <input id="your-email-confirm" class="p-contact-fields__control" type="email" name="your_email_confirm" autocomplete="email" required>
                </div>
                <div class="p-contact-fields__item">
                    <label class="p-contact-fields__label" for="your-tel">電話番号</label>
                    <input id="your-tel" class="p-contact-fields__control" type="tel" name="your_tel" autocomplete="tel">
                </div>
                <div class="p-contact-fields__item">
                    <label class="p-contact-fields__label" for="your-message">お問い合わせ内容 <span>*必須</span></label>
                    <textarea id="your-message" class="p-contact-fields__control p-contact-fields__control--textarea" name="message" required></textarea>
                </div>
            </div>

            <div class="p-contact-privacy">
                <label class="p-contact-privacy__label">
                    <input class="p-contact-privacy__checkbox" type="checkbox" name="privacy_agree" value="1" required>
                    <span class="p-contact-privacy__text"><a href="<?php echo esc_url(home_url('/privacy/')); ?>">プライバシーポリシー</a>に同意して送信する</span>
                </label>
            </div>

            <div class="p-contact-actions">
                <button class="c-btn c-btn--common--green--large p-contact-actions__button" type="submit">確認</button>
            </div>
            </form>
        </div>
    </section>
</main>

<?php
get_footer();
