<?php
get_header();

$prefectures = get_terms([
    'taxonomy'   => 'shop_prefecture',
    'hide_empty' => true,
]);

if (is_wp_error($prefectures)) {
    $prefectures = [];
}

$prefecture_order = [
    '北海道',
    '青森県',
    '岩手県',
    '宮城県',
    '秋田県',
    '山形県',
    '福島県',
    '茨城県',
    '栃木県',
    '群馬県',
    '千葉県',
    '東京都',
    '埼玉県',
    '神奈川県',
    '新潟県',
    '富山県',
    '石川県',
    '福井県',
    '山梨県',
    '長野県',
    '岐阜県',
    '静岡県',
    '愛知県',
    '三重県',
    '滋賀県',
    '京都府',
    '大阪府',
    '兵庫県',
    '奈良県',
    '和歌山県',
    '鳥取県',
    '島根県',
    '岡山県',
    '広島県',
    '山口県',
    '徳島県',
    '香川県',
    '愛媛県',
    '高知県',
    '福岡県',
    '佐賀県',
    '長崎県',
    '熊本県',
    '大分県',
    '宮崎県',
    '鹿児島県',
    '沖縄県',
];

$prefecture_order_map = array_flip($prefecture_order);

usort($prefectures, function ($a, $b) use ($prefecture_order_map) {
    $a_order = $prefecture_order_map[$a->name] ?? PHP_INT_MAX;
    $b_order = $prefecture_order_map[$b->name] ?? PHP_INT_MAX;

    return $a_order <=> $b_order;
});
?>

<main id="page-shop" class="c-main p-page-shop">
    <section class="c-bg p-page-shop__bg--top">
        <!-- タイトル start -->
        <h1 class="c-section__title">チラシ・店舗情報一覧</h1>
        <p class="c-section__title--sub">Flyers and shop information</p>
        <!-- タイトル end -->

        <?php if ($prefectures) : ?>
            <!-- 都道府県セクションへスクロール start -->
            <div class="p-page-shop__scroll">
                <?php foreach ($prefectures as $prefecture) : ?>
                    <a
                        href="#prefecture-<?php echo esc_attr($prefecture->slug); ?>"
                        class="c-btn p-page-shop__scroll__link"
                    >
                        <?php echo esc_html(preg_replace('/[都道府県]$/u', '', $prefecture->name)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <!-- 都道府県セクションへスクロール end -->
        <?php endif; ?>
    </section>

    <?php foreach ($prefectures as $prefecture) : ?>
        <?php
        $shops = get_posts([
            'post_type'      => 'shop',
            'posts_per_page' => -1,
            'meta_key'       => 'shop_order',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'tax_query'      => [
                [
                    'taxonomy' => 'shop_prefecture',
                    'field'    => 'term_id',
                    'terms'    => $prefecture->term_id,
                ],
            ],
        ]);
        ?>
        <section
            class="p-page-shop__section"
            id="prefecture-<?php echo esc_attr($prefecture->slug); ?>"
        >
            <h2 class="c-section__title"><?php echo esc_html($prefecture->name); ?></h2>
            <p class="c-section__title--sub"><?php echo esc_html(ucwords(str_replace('-', ' ', $prefecture->slug))); ?></p>

            <ul class="c-shop-card-list">
                <?php foreach ($shops as $shop) : ?>
                    <?php
                    $shop_id        = $shop->ID;
                    $business_hours = get_field('business_hours', $shop_id);
                    $business_hours_note = get_field('business_hours_note', $shop_id);
                    $postal_code    = get_field('postal_code', $shop_id);
                    $address        = get_field('address', $shop_id);
                    $tel            = get_field('tel', $shop_id);
                    ?>
                    <li class="c-shop-card">
                        <p class="c-shop-card__name"><?php echo esc_html(get_the_title($shop_id)); ?></p>
                        <dl class="c-shop-card__detail">
                            <div class="c-shop-card__detail__item">
                                <dt class="c-shop-card__detail__title">営業時間</dt>
                                <dd class="c-shop-card__detail__content">
                                    <time class="c-shop-card__detail__time">
                                        <span class="c-shop-card__detail__time__main">
                                            <?php echo esc_html($business_hours); ?>
                                        </span>
                                        <span class="c-shop-card__detail__time__note">
                                            <?php echo esc_html($business_hours_note); ?>
                                        </span>
                                    </time>
                                </dd>
                            </div>
                            <div class="c-shop-card__detail__item">
                                <dt class="c-shop-card__detail__title">住所</dt>
                                <dd class="c-shop-card__detail__content">
                                    <address class="c-shop-card__detail__address">
                                        <span class="c-shop-card__detail__address__zip">
                                            〒<?php echo esc_html($postal_code); ?>
                                        </span><br>
                                        <span class="c-shop-card__detail__address__place">
                                            <?php echo esc_html($address); ?>
                                        </span>
                                    </address>
                                </dd>
                            </div>
                            <div class="c-shop-card__detail__item">
                                <dt class="c-shop-card__detail__title">電話番号</dt>
                                <dd class="c-shop-card__detail__content">
                                    <a
                                        class="c-shop-card__detail__tel"
                                        href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $tel)); ?>"
                                    >
                                        <?php echo esc_html($tel); ?>
                                    </a>
                                </dd>
                            </div>
                        </dl>

                        <div class="c-shop-card__link--wrapper">
                            <a
                                class="c-shop-card__link u-bg--green"
                                href="<?php echo esc_url(get_permalink($shop_id)); ?>"
                            >
                                <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M12.6994 0H5.65984L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                                    <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                                    <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                                    <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                                </svg>
                                <span class="c-shop-card__link__text">店舗チラシ</span>
                            </a>
                            <a class="c-shop-card__link u-bg--blue" href="">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61 11.95 8.8 11.67 8.13 11.21C6.96 11.71 6.24 12.59 5.81 13.37C5.23 14.4 5.68 15.87 6.69 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                                    <path d="M10.49 10.98C12.21 10.98 13.6 9.59 13.6 7.87V7.13C13.6 5.41 12.21 4.02 10.49 4.02C8.77 4.02 7.38 5.41 7.38 7.13V7.87C7.38 9.59 8.77 10.98 10.49 10.98Z" fill="white"/>
                                    <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76 17.92 3.08C13.82 -1.02 7.16 -1.02 3.07 3.07C-1.02 7.16 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83 4.53 4.53C7.82 1.24 13.16 1.24 16.46 4.53C19.75 7.82 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                                </svg>
                                <span class="c-shop-card__link__text">求人</span>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endforeach; ?>
</main>

<?php get_footer(); ?>
