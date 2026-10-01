<?php
$is_entry = isset($business_form_type) && $business_form_type === 'entry';
$page_title = $is_entry ? 'エントリーフォーム' : 'お問い合わせ';
$form_action = $is_entry ? '/company/business/entry/confirm/' : '/company/business/contact/confirm/';

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

$entry_fields = [
    ['store', '応募店舗', 'select', false, ['行徳店', '西船橋店', '花野井店', 'しいの木台店', '青葉台店', '西原店', '松戸店', '西新井店', '三郷店', '八潮店']],
    ['job', '応募職種', 'select', true, ['チェッカー（レジ）', '水産', '青果', '惣菜', '食肉', 'グロサリー']],
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

get_header('company');
?>

<main class="p-business-form-page">
    <nav class="p-business-form-page__breadcrumb" aria-label="パンくずリスト">
        <?php if ($is_entry) : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(get_post_type_archive_link('recruit_part_time')); ?>">パートアルバイト募集</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(get_post_type_archive_link('recruit_part_time')); ?>">検索結果</a><span aria-hidden="true">›</span>
            <span><?php echo esc_html($page_title); ?></span>
        <?php else : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/company/')); ?>">企業情報</a><span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(home_url('/company/business/')); ?>">生産者・お取引先の方へ</a><span aria-hidden="true">›</span>
            <span><?php echo esc_html($page_title); ?></span>
        <?php endif; ?>
    </nav>

    <section class="p-business-form-page__content" aria-labelledby="business-form-title">
        <h1 id="business-form-title"><?php echo esc_html($page_title); ?></h1>
        <?php if ($is_entry) : ?>
            <p class="p-business-form-page__lead">各項目を入力の上、<br class="u-business-form-sp">「入力確認」ボタンを押してください。</p>
        <?php else : ?>
            <p class="p-business-form-page__lead">お取引希望の業者様は下記の問い合わせ<br class="u-business-form-sp">フォームよりお問合せください。<br>各項目を入力の上、<br class="u-business-form-sp">「入力確認」ボタンを押してください。</p>
        <?php endif; ?>

        <form class="p-business-form" action="<?php echo esc_url(home_url($form_action)); ?>" method="post" novalidate>
            <?php foreach ($fields as $field) :
                [$name, $label, $type, $required] = $field;
                $extra = $field[4] ?? null;
                $modifier = $field[5] ?? '';
                $is_select = $type === 'select';
                $is_textarea = str_starts_with($type, 'textarea');
            ?>
                <label class="p-business-form__field<?php echo $modifier ? ' p-business-form__field--' . esc_attr($modifier) : ''; ?>">
                    <span class="p-business-form__label"><?php echo esc_html($label); ?><?php if ($required) : ?> <em>*必須</em><?php endif; ?></span>
                    <?php if ($is_select) : ?>
                        <span class="p-business-form__select-wrap">
                            <select name="<?php echo esc_attr($name); ?>" <?php echo $required ? 'required' : ''; ?>>
                                <option value="" selected disabled hidden></option>
                                <?php foreach ($extra as $option) : ?><option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option><?php endforeach; ?>
                            </select>
                        </span>
                    <?php elseif ($is_textarea) : ?>
                        <textarea class="<?php echo $type === 'textarea-small' ? 'is-small' : ''; ?>" name="<?php echo esc_attr($name); ?>" <?php echo $required ? 'required' : ''; ?>></textarea>
                    <?php else : ?>
                        <input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($name); ?>" autocomplete="<?php echo esc_attr(is_string($extra) ? $extra : 'off'); ?>" <?php echo $required ? 'required' : ''; ?>>
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>

            <label class="p-business-form__privacy">
                <input type="checkbox" name="privacy_agree" value="1" required>
                <span><a href="<?php echo esc_url(home_url('/privacy/')); ?>">プライバシーポリシー</a>に同意して送信する</span>
            </label>
            <button class="p-business-form__submit" type="submit">確認</button>
        </form>
    </section>
</main>

<?php get_footer('company'); ?>
