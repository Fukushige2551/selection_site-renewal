<?php
get_header();

function foods_single_shop_get_field_value($name, $post_id) {
    if (function_exists('get_field')) {
        return get_field($name, $post_id);
    }

    return get_post_meta($post_id, $name, true);
}

function foods_single_shop_format_post($post, $field_names = []) {
    $post_id = $post instanceof WP_Post ? $post->ID : (int) $post;
    $post_object = get_post($post_id);

    if (!$post_object) {
        return null;
    }

    $fields = [];
    foreach ($field_names as $field_name) {
        $fields[$field_name] = foods_single_shop_get_field_value($field_name, $post_id);
    }

    return [
        'id' => $post_id,
        'title' => get_the_title($post_id),
        'slug' => $post_object->post_name,
        'permalink' => get_permalink($post_id),
        'status' => $post_object->post_status,
        'date' => get_the_date('c', $post_id),
        'excerpt' => get_the_excerpt($post_id),
        'content' => apply_filters('the_content', $post_object->post_content),
        'thumbnail' => get_the_post_thumbnail_url($post_id, 'full'),
        'fields' => $fields,
    ];
}

function foods_single_shop_get_term_data($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);

    if (!$terms || is_wp_error($terms)) {
        return [];
    }

    return array_map(function ($term) {
        return [
            'id' => (int) $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'taxonomy' => $term->taxonomy,
        ];
    }, $terms);
}

function foods_single_shop_get_related_term_ids($taxonomy, $term_name, $slugs) {
    $term_ids = [];

    foreach (array_unique($slugs) as $slug) {
        $term = get_term_by('slug', $slug, $taxonomy);

        if ($term && !is_wp_error($term)) {
            $term_ids[] = (int) $term->term_id;
        }
    }

    $term = get_term_by('name', $term_name, $taxonomy);
    if ($term && !is_wp_error($term)) {
        $term_ids[] = (int) $term->term_id;
    }

    return array_values(array_unique($term_ids));
}

function foods_single_shop_get_legacy_shop_slugs($shop_slug) {
    $legacy_slugs = [
        'gyoutoku' => ['gyotoku'],
        'nishifunabashi' => ['nishi-funabashi'],
    ];

    return $legacy_slugs[$shop_slug] ?? [];
}

function foods_single_shop_parse_ymd($date) {
    if (!$date) {
        return null;
    }

    $date = preg_replace('/[^0-9]/', '', (string) $date);
    if (strlen($date) !== 8) {
        return null;
    }

    $parsed = DateTimeImmutable::createFromFormat('!Ymd', $date);

    return $parsed ?: null;
}

function foods_single_shop_get_weekday($date) {
    $parsed = foods_single_shop_parse_ymd($date);

    if (!$parsed) {
        return '';
    }

    $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

    return $weekdays[(int) $parsed->format('w')];
}

function foods_single_shop_format_ymd($date, $with_weekday = true) {
    $parsed = foods_single_shop_parse_ymd($date);

    if (!$parsed) {
        return '';
    }

    $formatted = $parsed->format('n月j日');

    if ($with_weekday) {
        $formatted .= '(' . foods_single_shop_get_weekday($date) . ')';
    }

    return $formatted;
}

function foods_single_shop_format_ymd_html($date, $prefix = '') {
    $parsed = foods_single_shop_parse_ymd($date);

    if (!$parsed) {
        return '';
    }

    return '<span class="p-single-shop__deal-date-main">' . esc_html($prefix . $parsed->format('n月j日')) . '</span>'
        . '<span class="p-single-shop__deal-date-weekday">(' . esc_html(foods_single_shop_get_weekday($date)) . ')</span>';
}

function foods_single_shop_format_period($start_date, $end_date) {
    $start_parsed = foods_single_shop_parse_ymd($start_date);
    $end_parsed = foods_single_shop_parse_ymd($end_date);

    if ($start_parsed && $end_parsed) {
        if ($start_parsed->format('Ymd') === $end_parsed->format('Ymd')) {
            return foods_single_shop_format_ymd($start_date);
        }

        return foods_single_shop_format_ymd($start_date) . ' -' . foods_single_shop_format_ymd($end_date);
    }

    if ($start_parsed) {
        return foods_single_shop_format_ymd($start_date);
    }

    if ($end_parsed) {
        return foods_single_shop_format_ymd($end_date);
    }

    return '';
}

function foods_single_shop_format_period_html($start_date, $end_date) {
    $start_parsed = foods_single_shop_parse_ymd($start_date);
    $end_parsed = foods_single_shop_parse_ymd($end_date);

    if ($start_parsed && $end_parsed) {
        if ($start_parsed->format('Ymd') === $end_parsed->format('Ymd')) {
            return foods_single_shop_format_ymd_html($start_date);
        }

        return foods_single_shop_format_ymd_html($start_date)
            . '<span class="p-single-shop__deal-date-space"> </span>'
            . foods_single_shop_format_ymd_html($end_date, '-');
    }

    if ($start_parsed) {
        return foods_single_shop_format_ymd_html($start_date);
    }

    if ($end_parsed) {
        return foods_single_shop_format_ymd_html($end_date);
    }

    return '';
}function foods_single_shop_is_date_active($start_date, $end_date, $today) {
    $start = foods_single_shop_parse_ymd($start_date);
    $end = foods_single_shop_parse_ymd($end_date);

    if ($start && $today < $start) {
        return false;
    }

    if ($end && $today > $end) {
        return false;
    }

    return true;
}

function foods_single_shop_get_image_data($image, $size = 'full') {
    if (is_array($image)) {
        if (array_keys($image) === range(0, count($image) - 1)) {
            $first_image = $image[0] ?? '';
            $image = is_array($first_image) ? ($first_image['image'] ?? $first_image) : $first_image;
        }
        if (is_array($image)) {
            $image = $image['ID'] ?? $image['id'] ?? $image['url'] ?? '';
        }
    }

    if (is_numeric($image) && (int) $image > 0) {
        return [
            'id' => (int) $image,
            'url' => wp_get_attachment_image_url((int) $image, $size),
            'alt' => get_post_meta((int) $image, '_wp_attachment_image_alt', true),
        ];
    }

    if (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {
        return [
            'id' => 0,
            'url' => $image,
            'alt' => '',
        ];
    }

    return null;
}

function foods_single_shop_get_flyer_images($fields) {
    $images = [];

    if (!empty($fields['flyer_images']) && is_array($fields['flyer_images'])) {
        foreach ($fields['flyer_images'] as $row) {
            $image = is_array($row) ? ($row['image'] ?? '') : $row;
            $image_data = foods_single_shop_get_image_data($image);

            if ($image_data && !empty($image_data['url'])) {
                $images[] = $image_data;
            }
        }
    }

    $legacy_image = foods_single_shop_get_image_data($fields['flyer_image'] ?? '');
    if (!$images && $legacy_image && !empty($legacy_image['url'])) {
        $images[] = $legacy_image;
    }

    return $images;
}

function foods_single_shop_get_first_field_value($post_id, $field_names) {
    foreach ($field_names as $field_name) {
        $value = foods_single_shop_get_field_value($field_name, $post_id);

        if ($value !== '' && $value !== null && $value !== false && $value !== []) {
            return $value;
        }
    }

    return '';
}

function foods_single_shop_format_multiline_text($value) {
    $text = preg_replace('/<br\s*\/?>/i', "\n", (string) $value);
    $text = str_replace(['\\r\\n', '\\n', "\r\n", "\r"], "\n", $text);
    $lines = array_values(array_filter(array_map('trim', explode("\n", $text)), 'strlen'));
    $text = implode("\n", $lines);

    if ($text === '') {
        return '';
    }

    return nl2br(esc_html($text));
}

function foods_single_shop_format_important_notices($notice_field) {
    if (!$notice_field) {
        return [];
    }

    if (is_string($notice_field)) {
        return [[
            'title' => '',
            'body' => $notice_field,
            'url' => '',
        ]];
    }

    if (!is_array($notice_field)) {
        return [];
    }

    $is_list = array_keys($notice_field) === range(0, count($notice_field) - 1);
    $rows = $is_list ? $notice_field : [$notice_field];

    return array_values(array_filter(array_map(function ($row) {
        if (is_string($row)) {
            return [
                'title' => '',
                'body' => $row,
                'url' => '',
            ];
        }

        if (!is_array($row)) {
            return null;
        }

        $title = $row['title'] ?? $row['important_notice_title'] ?? $row['notice_title'] ?? '';
        $body = $row['body'] ?? $row['text'] ?? $row['important_notice_body'] ?? $row['important_notice_text'] ?? $row['notice_body'] ?? $row['notice_text'] ?? '';
        $url = $row['url'] ?? $row['link'] ?? $row['important_notice_url'] ?? $row['notice_url'] ?? '';

        if (!$title && !$body && !$url) {
            return null;
        }

        return [
            'title' => $title,
            'body' => $body,
            'url' => $url,
        ];
    }, $rows)));
}

function foods_single_shop_render_important_notice($important_notices) {
    if (!$important_notices) {
        ?>
        <section class="p-single-shop__important" aria-labelledby="single-shop-important-title">
            <h2 id="single-shop-important-title"><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/single-shop/important.svg'); ?>" alt="" aria-hidden="true">重要なお知らせ</h2>
            <article class="p-single-shop__important-item">
                <div class="p-single-shop__important-body">
                    <p>重要なお知らせはありません。</p>
                </div>
            </article>
        </section>
        <?php
        return;
    }
    ?>
    <section class="p-single-shop__important" aria-labelledby="single-shop-important-title">
        <h2 id="single-shop-important-title"><img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/single-shop/important.svg'); ?>" alt="" aria-hidden="true">重要なお知らせ</h2>
        <?php foreach ($important_notices as $notice) : ?>
            <article class="p-single-shop__important-item">
                <?php if (!empty($notice['title'])) : ?>
                    <h3><?php echo esc_html($notice['title']); ?></h3>
                <?php endif; ?>
                <?php if (!empty($notice['body'])) : ?>
                    <div class="p-single-shop__important-body">
                        <?php echo wp_kses_post(wpautop($notice['body'])); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($notice['url'])) : ?>
                    <p>詳しくは <a href="<?php echo esc_url($notice['url']); ?>" target="_blank" rel="noopener noreferrer">こちら</a></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
    <?php
}

function foods_single_shop_get_embed_html($html) {
    if (!$html || !is_string($html)) {
        return '';
    }

    return wp_kses($html, [
        'iframe' => [
            'src' => true,
            'width' => true,
            'height' => true,
            'style' => true,
            'allow' => true,
            'allowfullscreen' => true,
            'loading' => true,
            'referrerpolicy' => true,
            'title' => true,
            'aria-label' => true,
            'frameborder' => true,
        ],
        'div' => [
            'class' => true,
            'style' => true,
        ],
        'p' => [
            'class' => true,
        ],
        'a' => [
            'href' => true,
            'target' => true,
            'rel' => true,
        ],
        'br' => [],
    ]);
}

function foods_single_shop_get_text_field_html($value) {
    if ($value === '' || $value === null || $value === false) {
        return '';
    }

    return nl2br(esc_html((string) $value));
}

function foods_single_shop_flatten_checkbox_values($value) {
    if ($value === '' || $value === null || $value === false || $value === []) {
        return [];
    }

    if (!is_array($value)) {
        return [(string) $value];
    }

    $values = [];
    foreach ($value as $item) {
        if (is_array($item)) {
            foreach (['value', 'label', 'name'] as $key) {
                if (isset($item[$key]) && $item[$key] !== '') {
                    $values[] = (string) $item[$key];
                }
            }
            continue;
        }

        if ($item !== '' && $item !== null && $item !== false) {
            $values[] = (string) $item;
        }
    }

    return $values;
}

function foods_single_shop_normalize_choice_key($value) {
    $value = strtolower((string) $value);
    return preg_replace('/[\s　_\-・\/（）()]+/u', '', $value);
}

function foods_single_shop_get_selected_choice_keys($post_id, $field_names) {
    $selected_values = foods_single_shop_flatten_checkbox_values(
        foods_single_shop_get_first_field_value($post_id, $field_names)
    );

    return array_values(array_unique(array_filter(array_map(
        'foods_single_shop_normalize_choice_key',
        $selected_values
    ))));
}

function foods_single_shop_choice_is_selected($choice, $selected_keys) {
    if (!$selected_keys) {
        return false;
    }

    $aliases = array_merge(
        [$choice['key'] ?? '', $choice['label'] ?? ''],
        $choice['aliases'] ?? []
    );

    foreach ($aliases as $alias) {
        if (in_array(foods_single_shop_normalize_choice_key($alias), $selected_keys, true)) {
            return true;
        }
    }

    return false;
}

function foods_single_shop_get_news_image_data($post_id, $size = 'large') {
    if (has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        return [
            'url' => get_the_post_thumbnail_url($post_id, $size),
            'alt' => get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
        ];
    }

    $image = foods_single_shop_get_first_field_value($post_id, [
        'news_eyecatch_image',
        'news_image',
        'main_image',
        'thumbnail',
        'image',
    ]);

    return foods_single_shop_get_image_data($image, $size);
}

$shop_id = get_the_ID();
$shop_slug = get_post_field('post_name', $shop_id);
$shop_title = get_the_title($shop_id);

$shop_data = foods_single_shop_format_post($shop_id, [
    'business_hours',
    'business_hours_note',
    'postal_code',
    'address',
    'tel',
    'fax',
    'parking',
    'access',
    'congestion_url',
    'shop_order',
    'important_notice',
    'important_notices',
    'shop_important_notice',
    'shop_important_notices',
    'shop_image',
    'shop_images',
    'main_image',
    'main_photo',
    'access_map_embed_html',
    'access_map_html',
    'map_embed_html',
    'google_map_embed_html',
    'google_map_url',
    'payment_methods',
    'payment_method',
    'shop_payment_methods',
    'accepted_payments',
    'cashless_payment',
    'cashless_payments',
    '決済方法',
    'services',
    'service',
    'shop_services',
    'available_services',
    'handling_services',
    'handled_services',
    '取り扱いサービス',
    'facilities',
    'manager_message',
    'manager_name',
    'manager_image',
]);
$shop_data['terms'] = [
    'shop_prefecture' => foods_single_shop_get_term_data($shop_id, 'shop_prefecture'),
    'shop_status' => foods_single_shop_get_term_data($shop_id, 'shop_status'),
];

$shop_prefecture_term_ids = array_map(function ($term) {
    return (int) $term['id'];
}, $shop_data['terms']['shop_prefecture']);

$same_prefecture_shop_posts = [];
if ($shop_prefecture_term_ids) {
    $same_prefecture_shop_posts = get_posts([
        'post_type' => 'shop',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'post__not_in' => [$shop_id],
        'meta_key' => 'shop_order',
        'orderby' => [
            'meta_value_num' => 'ASC',
            'title' => 'ASC',
        ],
        'tax_query' => [
            [
                'taxonomy' => 'shop_prefecture',
                'field' => 'term_id',
                'terms' => $shop_prefecture_term_ids,
            ],
        ],
    ]);
}

$same_prefecture_shops_data = array_values(array_filter(array_map(function ($shop) {
    $data = foods_single_shop_format_post($shop, [
        'business_hours',
        'business_hours_note',
        'postal_code',
        'address',
        'tel',
        'shop_order',
    ]);

    if (!$data) {
        return null;
    }

    $data['terms'] = [
        'shop_prefecture' => foods_single_shop_get_term_data($shop->ID, 'shop_prefecture'),
        'shop_status' => foods_single_shop_get_term_data($shop->ID, 'shop_status'),
    ];

    return $data;
}, $same_prefecture_shop_posts)));

$flyer_store_term_ids = foods_single_shop_get_related_term_ids(
    'flyer_store',
    $shop_title,
    array_merge([$shop_slug], foods_single_shop_get_legacy_shop_slugs($shop_slug))
);

$important_notice_field = foods_single_shop_get_first_field_value($shop_id, [
    'important_notice',
    'important_notices',
    'shop_important_notice',
    'shop_important_notices',
]);
$important_notices = foods_single_shop_format_important_notices($important_notice_field);
$shop_image_field = foods_single_shop_get_first_field_value($shop_id, [
    'shop_image',
    'shop_images',
    'main_image',
    'main_photo',
]);
$shop_image_data = foods_single_shop_get_image_data($shop_image_field, 'large');
$shop_image_url = $shop_image_data['url'] ?? ($shop_data['thumbnail'] ?: get_template_directory_uri() . '/img/component/no-image.png');
$shop_image_alt = $shop_image_data['alt'] ?? '';
$access_map_embed_html = foods_single_shop_get_embed_html(foods_single_shop_get_first_field_value($shop_id, [
    'google_map_url',
    'access_map_embed_html',
    'access_map_html',
    'map_embed_html',
    'google_map_embed_html',
]));

$selected_payment_keys = foods_single_shop_get_selected_choice_keys($shop_id, [
    'payment_methods',
    'payment_method',
    'shop_payment_methods',
    'accepted_payments',
    'cashless_payment',
    'cashless_payments',
    '決済方法',
]);
$selected_service_keys = foods_single_shop_get_selected_choice_keys($shop_id, [
    'services',
    'service',
    'shop_services',
    'available_services',
    'handling_services',
    'handled_services',
    '取り扱いサービス',
    'facilities',
]);

$payment_groups = [
    [
        'title' => 'クレジットカード',
        'items' => [
            ['key' => 'visa', 'label' => 'VISA', 'icon' => 'visa.svg', 'aliases' => ['VISA']],
            ['key' => 'mastercard', 'label' => 'Mastercard', 'icon' => 'mastercard.svg', 'aliases' => ['Mastercard', 'MasterCard', 'マスターカード']],
            ['key' => 'jcb', 'label' => 'JCB', 'icon' => 'jcb.svg', 'aliases' => ['JCB']],
            ['key' => 'american_express', 'label' => 'American Express', 'icon' => 'american_express.svg', 'aliases' => ['American Express', 'AMEX', 'アメリカン・エキスプレス']],
            ['key' => 'diners_club_international', 'label' => 'Diners Club International', 'icon' => 'diners_club_international.svg', 'aliases' => ['Diners Club International', 'Diners Club', 'ダイナースクラブ']],
            ['key' => 'discover', 'label' => 'Discover', 'icon' => 'discover.svg', 'aliases' => ['Discover']],
        ],
    ],
    [
        'title' => '二次元コード決済',
        'class' => 'p-single-shop__payment-icons--qr',
        'items' => [
            ['key' => 'paypay', 'label' => 'PayPay', 'icon' => 'paypay.svg', 'aliases' => ['PayPay']],
            ['key' => 'rakuten_pay', 'label' => '楽天ペイ', 'icon' => 'rakuten_pay.svg', 'aliases' => ['楽天ペイ', '楽天Pay', 'Rakuten Pay']],
            ['key' => 'd_pay', 'label' => 'd払い', 'icon' => 'd_pay.svg', 'aliases' => ['d払い', 'd払', 'd pay', 'd払い決済', 'dbarai', 'd_barai', 'd-barai', 'd-payment']],
            ['key' => 'au_pay', 'label' => 'au PAY', 'icon' => 'au_pay.svg', 'aliases' => ['au PAY', 'aupay']],
            ['key' => 'merukari_pay', 'label' => 'メルペイ', 'icon' => 'merukari_pay.svg', 'aliases' => ['メルペイ', 'merpay', 'メルカリペイ']],
        ],
    ],
    [
        'title' => '各種電子マネー',
        'items' => [
            ['key' => 'cogca', 'label' => 'CoGCa', 'icon' => 'cogca.svg', 'aliases' => ['CoGCa']],
            ['key' => 'quicpay', 'label' => 'QUICPay', 'icon' => 'quic_pay.svg', 'aliases' => ['QUICPay', 'QUIC Pay']],
            ['key' => 'id', 'label' => 'iD', 'icon' => 'id_pay.svg', 'aliases' => ['iD', 'iD払い']],
        ],
    ],
    [
        'title' => '交通系ICカード',
        'items' => [
            ['key' => 'kitaca', 'label' => 'Kitaca', 'icon' => 'kitaca.svg', 'aliases' => ['Kitaca']],
            ['key' => 'suica', 'label' => 'Suica', 'icon' => 'suica.svg', 'aliases' => ['Suica']],
            ['key' => 'pasmo', 'label' => 'PASMO', 'icon' => 'pasmo.svg', 'aliases' => ['PASMO']],
            ['key' => 'tolca', 'label' => 'tolCa', 'icon' => 'tolca.svg', 'aliases' => ['tolCa', 'TOICA']],
            ['key' => 'manaca', 'label' => 'manaca', 'icon' => 'manaca.svg', 'aliases' => ['manaca']],
            ['key' => 'icoca', 'label' => 'ICOCA', 'icon' => 'icoca.svg', 'aliases' => ['ICOCA']],
            ['key' => 'sugoca', 'label' => 'SUGOCA', 'icon' => 'sugoca.svg', 'aliases' => ['SUGOCA']],
            ['key' => 'nimoca', 'label' => 'nimoca', 'icon' => 'nimoca.svg', 'aliases' => ['nimoca']],
            ['key' => 'hayakaken', 'label' => 'はやかけん', 'icon' => 'hayakaken.svg', 'aliases' => ['はやかけん', 'Hayakaken']],
            ['key' => 'pitapa', 'label' => 'PiTaPa', 'icon' => 'pitapa.svg', 'aliases' => ['PiTaPa']],
        ],
    ],
];
$payment_groups = array_values(array_filter(array_map(function ($group) use ($selected_payment_keys) {
    $group['items'] = array_values(array_filter($group['items'], function ($item) use ($selected_payment_keys) {
        return foods_single_shop_choice_is_selected($item, $selected_payment_keys);
    }));

    return $group['items'] ? $group : null;
}, $payment_groups)));

$service_items = [
    ['key' => 'parking', 'label' => '駐車場', 'icon' => 'parking.svg', 'aliases' => ['駐車場', 'P']],
    ['key' => 'wheelchair-accessible', 'label' => '車いす設備', 'icon' => 'wheelchair.svg', 'aliases' => ['wheelchair', '車いす設備', '車椅子設備', '車いす', '車椅子']],
    ['key' => 'atm', 'label' => 'ATM隣接', 'icon' => 'atm.svg', 'aliases' => ['ATM隣接', 'ATM']],
    ['key' => 'credit-card', 'label' => 'クレジットカード', 'icon' => 'creditcard.svg', 'aliases' => ['creditcard', 'クレジットカード', 'クレジット']],
    ['key' => 'electronic-payment', 'label' => '各種電子決済', 'icon' => 'qr.svg', 'aliases' => ['qr', '各種電子決済', '電子決済', 'QR', 'QR決済']],
    ['key' => 'aed', 'label' => 'AED', 'icon' => 'aed.svg', 'aliases' => ['AED']],
    ['key' => 'courier-reception', 'label' => '宅急便受付<br>(持込不可)', 'icon' => 'delivery.svg', 'aliases' => ['delivery', '宅急便受付', '宅急便受付（持込不可）', '宅急便', '配送']],
    ['key' => 'rice-polishing-machine', 'label' => '精米機', 'icon' => 'rice_mill.svg', 'aliases' => ['rice_mill', '精米機']],
];
$service_items = array_values(array_filter($service_items, function ($item) use ($selected_service_keys) {
    return foods_single_shop_choice_is_selected($item, $selected_service_keys);
}));

$shop_news_category_term = get_term_by('slug', 'shop-news', 'news_category');
$shop_news_shop_term_ids = foods_single_shop_get_related_term_ids(
    'news_shop_category',
    $shop_title,
    array_merge([$shop_slug], foods_single_shop_get_legacy_shop_slugs($shop_slug))
);
$shop_news_posts = [];

if ($shop_news_category_term && !is_wp_error($shop_news_category_term) && $shop_news_shop_term_ids) {
    $shop_news_posts = get_posts([
        'post_type' => 'news',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'news_category',
                'field' => 'term_id',
                'terms' => [(int) $shop_news_category_term->term_id],
            ],
            [
                'taxonomy' => 'news_shop_category',
                'field' => 'term_id',
                'terms' => $shop_news_shop_term_ids,
            ],
        ],
    ]);
}

$shop_news_items = array_values(array_map(function ($news_post) {
    $image = foods_single_shop_get_news_image_data($news_post->ID, 'large');

    return [
        'id' => $news_post->ID,
        'title' => get_the_title($news_post->ID),
        'permalink' => get_permalink($news_post->ID),
        'date' => get_the_date('Y.m.d', $news_post->ID),
        'datetime' => get_the_date('Y-m-d', $news_post->ID),
        'image' => $image,
    ];
}, $shop_news_posts));

$flyer_query_args = [
    'post_type' => 'flyer',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
];

if ($flyer_store_term_ids) {
    $flyer_query_args['tax_query'] = [
        [
            'taxonomy' => 'flyer_store',
            'field' => 'term_id',
            'terms' => $flyer_store_term_ids,
        ],
    ];
} else {
    $flyer_query_args['post__in'] = [0];
}

$flyer_posts = get_posts($flyer_query_args);

$flyers_data = array_values(array_filter(array_map(function ($flyer) {
    $data = foods_single_shop_format_post($flyer, [
        'flyer_image',
        'flyer_images',
        'publish_start_date',
        'publish_end_date',
        'flyer_start_date',
        'flyer_end_date',
        'bargain_items',
    ]);

    if (!$data) {
        return null;
    }

    $data['terms'] = [
        'flyer_store' => foods_single_shop_get_term_data($flyer->ID, 'flyer_store'),
    ];

    return $data;
}, $flyer_posts)));

$today = foods_single_shop_parse_ymd(current_time('Ymd'));
$active_flyers_data = array_values(array_filter($flyers_data, function ($flyer) use ($today) {
    $fields = $flyer['fields'] ?? [];
    $start_date = $fields['publish_start_date'] ?: ($fields['flyer_start_date'] ?? '');
    $end_date = $fields['publish_end_date'] ?: ($fields['flyer_end_date'] ?? '');

    return foods_single_shop_is_date_active($start_date, $end_date, $today);
}));

usort($active_flyers_data, function ($a, $b) {
    $a_fields = $a['fields'] ?? [];
    $b_fields = $b['fields'] ?? [];
    $a_end = $a_fields['publish_end_date'] ?: ($a_fields['flyer_end_date'] ?? '99991231');
    $b_end = $b_fields['publish_end_date'] ?: ($b_fields['flyer_end_date'] ?? '99991231');

    return strcmp((string) $a_end, (string) $b_end);
});

$current_flyer = $active_flyers_data[0] ?? null;
$current_flyer_fields = $current_flyer['fields'] ?? [];
$current_flyer_images = $current_flyer ? foods_single_shop_get_flyer_images($current_flyer_fields) : [];
$current_bargain_items = $current_flyer && !empty($current_flyer_fields['bargain_items']) && is_array($current_flyer_fields['bargain_items'])
    ? $current_flyer_fields['bargain_items']
    : [];

$recruit_search_posts = get_posts([
    'post_type' => 'recruit_part_time',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
    's' => $shop_title,
]);

$recruit_location_posts = get_posts([
    'post_type' => 'recruit_part_time',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
        [
            'key' => 'work_location_access',
            'value' => $shop_title,
            'compare' => 'LIKE',
        ],
    ],
]);

$recruit_posts_by_id = [];
foreach (array_merge($recruit_search_posts, $recruit_location_posts) as $recruit_post) {
    $recruit_posts_by_id[$recruit_post->ID] = $recruit_post;
}
$recruit_posts = array_values($recruit_posts_by_id);

$recruit_data = array_values(array_filter(array_map(function ($recruit) {
    return foods_single_shop_format_post($recruit, [
        'job_type',
        'salary',
        'work_location_access',
        'working_hours',
        'benefits',
        'qualification',
        'commuting',
        'application_method',
    ]);
}, $recruit_posts)));

$single_shop_data = [
    'shop' => [
        'current' => $shop_data,
        'samePrefecture' => $same_prefecture_shops_data,
    ],
    'flyers' => $flyers_data,
    'recruitPartTime' => $recruit_data,
];

$theme_uri = get_template_directory_uri();
$placeholder_image = $theme_uri . '/img/component/no-image.png';
$manager_message = trim((string) ($shop_data['fields']['manager_message'] ?? ''));
if ($manager_message === '') {
    $manager_message = 'セレクション行徳店店長の阿部です。地域密着型スーパーマーケットで「鮮度・味・低価格」を重視した営業をさせて頂いてます。惣菜コーナーにおいても手作りのお弁当・煮物・揚げたてのご提供もさせて頂いております。地域のお客様あっての店舗運営をしております。従業員一同心よりお待ちしております。ぜひご来店下さい。';
}
$manager_name = trim((string) ($shop_data['fields']['manager_name'] ?? ''));
$manager_image_data = foods_single_shop_get_image_data($shop_data['fields']['manager_image'] ?? '', 'large');
$manager_image_url = $manager_image_data['url'] ?? $placeholder_image;
$manager_image_alt = $manager_image_data['alt'] ?? '';
$recruit_image = $theme_uri . '/img/page/single-shop/recruit_banner.svg';
$visible_recruit_data = array_slice($recruit_data, 0, 10);
$recruit_page_count = (int) ceil(count($visible_recruit_data) / 2);
$recruit_archive_url = get_post_type_archive_link('recruit_part_time') ?: home_url('/recruit/');
?>

<main id="single-shop" class="c-main p-single-shop">
    <script
        type="application/json"
        id="js-single-shop-data"
    ><?php echo wp_json_encode($single_shop_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

    <nav class="p-single-shop__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span aria-hidden="true">></span>
        <a href="<?php echo esc_url(home_url('/shop/')); ?>">チラシ・店舗情報一覧</a>
        <span aria-hidden="true">></span>
        <span><?php echo esc_html($shop_title ?: '行徳店'); ?></span>
    </nav>

    <section class="p-single-shop__hero" aria-labelledby="single-shop-title">
        <h1 id="single-shop-title" class="p-single-shop__title"><?php echo esc_html($shop_title ?: '行徳店'); ?></h1>
        <p class="p-single-shop__title-sub">gotoku</p>
    </section>

    <?php if ($current_flyer) : ?>
        <section class="p-single-shop__flyer p-single-shop__section" aria-labelledby="single-shop-flyer-title">
            <h2 id="single-shop-flyer-title" class="p-single-shop__heading p-single-shop__heading--accent">
                <span class="p-single-shop__heading-chevron p-single-shop__heading-chevron--left" aria-hidden="true"></span> チラシ情報 <span class="p-single-shop__heading-chevron p-single-shop__heading-chevron--right" aria-hidden="true"></span>
            </h2>
            <p class="p-single-shop__lead"><?php echo esc_html($current_flyer['title']); ?></p>
            <?php
            $flyer_period = foods_single_shop_format_period(
                $current_flyer_fields['flyer_start_date'] ?? '',
                $current_flyer_fields['flyer_end_date'] ?? ''
            );
            ?>
            <?php if ($flyer_period) : ?>
                <p class="p-single-shop__period"><?php echo esc_html($flyer_period); ?></p>
            <?php endif; ?>
            <?php if ($current_flyer_images) : ?>
                <div class="p-single-shop__flyer-list">
                    <?php foreach ($current_flyer_images as $index => $flyer_image) : ?>
                        <a href="<?php echo esc_url($flyer_image['url']); ?>" class="p-single-shop__flyer-card" aria-label="チラシを拡大して見る">
                            <img src="<?php echo esc_url($flyer_image['url']); ?>" alt="<?php echo esc_attr($flyer_image['alt'] ?: $current_flyer['title'] . ' チラシ' . ($index + 1)); ?>">
                            <span class="p-single-shop__zoom" aria-hidden="true"></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if ($current_flyer && $current_bargain_items) : ?>
        <section class="p-single-shop__deals p-single-shop__section" aria-labelledby="single-shop-deals-title">
            <h2 id="single-shop-deals-title" class="p-single-shop__deals-title">今日のお買い得品</h2>
            <div class="p-single-shop__deal-decorations" aria-hidden="true">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--scallion" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/scallion.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--apple" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/apple.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--banana" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/banana.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--prepared-food" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/prepared_food.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--soy-sauce" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/soy_sauce.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--cart" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/cart.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--meat" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/meat.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--rice" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/rice.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--napa-cabbage" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/napa_cabbage.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--cabbage" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/cabbage-laptop.svg'); ?>" alt="">
                <img class="p-single-shop__deal-decoration p-single-shop__deal-decoration--chocolate" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/chocolate-laptop.svg'); ?>" alt="">
            </div>
            <div class="p-front-page__special-offers js-single-shop-deal-list">
                <?php foreach ($current_bargain_items as $item_index => $item) : ?>
                    <?php
                    $item_image = foods_single_shop_get_image_data($item['item_image'] ?? '', 'medium');
                    $item_image_url = $item_image && !empty($item_image['url']) ? $item_image['url'] : $placeholder_image;
                    $item_name = $item['item_name'] ?? '';
                    $item_period = foods_single_shop_format_period_html($item['item_start_date'] ?? '', $item['item_end_date'] ?? '');
                    ?>
                    <article class="p-front-page__special-offers__item js-single-shop-deal-card<?php echo $item_index === 0 ? ' is-good-deal' : ''; ?><?php echo !empty($item['label_1']) ? ' is-special' : ''; ?>" data-deal-index="<?php echo esc_attr($item_index); ?>"<?php echo !empty($item['label_1']) ? ' data-label="' . esc_attr($item['label_1']) . '"' : ''; ?><?php echo $item_index >= 2 ? ' hidden' : ''; ?>>
                        <?php if ($item_period) : ?>
                            <p class="p-front-page__special-offers__date"><?php echo $item_period; ?></p>
                        <?php endif; ?>
                        <div class="p-front-page__special-offers__item__details--wrapper<?php echo !empty($item['label_2']) ? ' is--limited--qupon' : ''; ?>"<?php echo !empty($item['label_2']) ? ' data-coupon="' . esc_attr($item['label_2']) . '"' : ''; ?>>
                            <picture class="p-front-page__special-offers__item__img">
                                <img src="<?php echo esc_url($item_image_url); ?>" alt="<?php echo esc_attr($item_image['alt'] ?? $item_name ?: '商品画像'); ?>">
                            </picture>
                            <div class="p-front-page__special-offers__item__details">
                                <div class="p-front-page__special-offers__item__details__title">
                                    <?php if (!empty($item['item_origin'])) : ?>
                                        <span class="p-front-page__special-offers__item__details__title__origin"><?php echo esc_html($item['item_origin']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item_name) : ?>
                                        <span class="p-front-page__special-offers__item__details__title__highlight"><?php echo esc_html($item_name); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['item_quantity'])) : ?>
                                        <span class="p-front-page__special-offers__item__details__title__note"><?php echo esc_html($item['item_quantity']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-front-page__special-offers__item__details__price">
                                <?php if (isset($item['base_price']) && $item['base_price'] !== '') : ?>
                                    <span class="p-front-page__special-offers__item__details__price__label">本体</span>
                                    <span class="p-front-page__special-offers__item__details__price__value"><?php echo esc_html(number_format((float) $item['base_price'])); ?></span><span class="p-front-page__special-offers__item__details__price__unit">円</span>
                                <?php endif; ?>
                                <?php if (isset($item['tax_price']) && $item['tax_price'] !== '') : ?>
                                    <p class="p-front-page__special-offers__item__details__tax">(税込 <?php echo esc_html(number_format((float) $item['tax_price'], 2)); ?>円)</p>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($item['note'])) : ?>
                            <p class="p-front-page__special-offers__item--note"><?php echo esc_html($item['note']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if (count($current_bargain_items) > 1) : ?>
                <div class="p-single-shop__deal-pager">
                    <button class="c-pop c-slider--btn c-slider--btn--left p-single-shop__deal-pager-button js-single-shop-deal-prev" type="button" aria-label="前のお買い得品へ">
                        <img class="c-slider--btn__img p-single-shop__deal-pager-img" src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                        <svg class="c-slider--btn__img" width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M23.4573 7.22216C23.4075 7.06927 23.3578 6.92366 23.3081 6.77077L23.4573 6.77077L23.4573 6.47228C23.6064 6.42131 23.7485 6.37035 23.8977 6.31939C24.1108 6.13009 24.1535 5.7952 24.189 5.4239C24.3382 5.37293 24.4802 5.32197 24.6294 5.27101C24.807 5.69327 25.013 5.93353 25.0699 6.47228C24.3666 6.64701 24.2245 7.13479 23.4644 7.22216L23.4573 7.22216ZM26.377 5.27101L26.377 5.4239L27.2508 5.4239L27.2508 5.57679L27.4 5.57679C27.3644 5.93353 27.336 5.94809 27.2508 6.17378L26.6683 6.17378L26.6683 6.02089C26.3841 5.8316 26.3912 5.78063 25.9365 5.72239C25.8868 5.47486 25.8371 5.22004 25.7874 4.97251L25.6382 4.97251C25.5174 4.47016 25.2688 4.31727 24.7644 4.22263C24.7502 3.96053 24.6152 3.6766 24.6152 3.62564L24.7644 3.62564L24.7644 3.17425L25.4961 3.17425L25.4961 3.32714C25.7376 3.50915 26.377 4.25903 26.5191 4.5284L27.3929 4.5284C27.265 5.06716 26.9737 5.2346 26.3699 5.27829L26.377 5.27101ZM27.8404 3.48003L27.8404 3.32714L27.9896 3.32714L27.9896 3.48003L28.1388 3.48003L28.1388 3.63291C27.9896 3.68388 27.8475 3.73484 27.6983 3.7858C27.7836 3.48731 27.677 3.65476 27.8475 3.48731L27.8404 3.48003ZM26.3059 27.9276L26.3059 28.0805L26.1568 28.0805C26.107 28.2334 26.0573 28.379 26.0076 28.5319C25.3753 28.4955 25.5103 28.4373 25.1338 28.2334L25.1338 27.9349L26.3059 27.9349L26.3059 27.9276ZM23.9687 28.0805L23.237 28.0805C23.1873 27.9276 23.1376 27.782 23.0879 27.6292L23.237 27.6292C23.2868 27.4763 23.3365 27.3307 23.3862 27.1778L24.1179 27.1778C24.1179 27.6073 24.0682 27.8184 23.9687 28.0733L23.9687 28.0805ZM25.1338 26.7337C25.0841 26.5808 25.0343 26.4352 24.9846 26.2823C24.8852 26.2313 24.7928 26.1804 24.6934 26.1294L24.6934 25.9765L24.1108 25.9765L24.1108 25.8236L23.9616 25.8236C23.8622 25.5251 23.7698 25.2266 23.6704 24.9281C23.9261 24.8116 24.2245 24.6879 24.4021 24.4767C25.1409 24.5204 25.2475 24.8044 25.7163 25.0737C25.7021 25.8527 25.5316 26.1367 25.4251 26.7191L25.1338 26.7191L25.1338 26.7337ZM24.5513 22.5401L24.7005 22.5401L24.7005 22.3873C25.077 22.4091 25.2119 22.4018 25.4322 22.5401L25.4322 22.693L25.8726 22.693C25.8726 22.693 26.0218 22.9114 26.1639 22.9915C26.1283 23.4138 26.107 23.5376 25.8726 23.7414C25.7092 23.9089 25.8726 23.8069 25.5813 23.8943C25.4819 23.5958 25.3895 23.2973 25.2901 22.9988L24.5584 22.9988L24.5584 22.5474L24.5513 22.5401ZM28.494 23.2099C28.4442 23.0571 28.3945 22.9114 28.3448 22.7586L28.1956 22.7586L28.1956 22.6057L28.3448 22.6057L28.3448 22.4528C28.8065 22.5693 28.8847 22.6493 28.9273 23.2027L28.4869 23.2027L28.494 23.2099ZM27.1797 22.4601C27.2721 22.096 27.336 22.1324 27.471 21.8631L27.9114 21.8631L27.9114 22.3145C27.6699 22.3654 27.4213 22.4164 27.1797 22.4673L27.1797 22.4601ZM26.8885 22.5401L26.8885 22.2416L27.1797 22.2416L27.1797 22.5401L26.8885 22.5401Z" fill="#BEBBBB"/>
                        </svg>
                    </button>
                    <div class="p-single-shop__slider-dots p-single-shop__slider-dots--deals js-single-shop-deal-dots" aria-label="お買い得品のページ送り">
                        <?php foreach ($current_bargain_items as $item_index => $item) : ?>
                            <button
                                type="button"
                                class="<?php echo $item_index === 0 ? 'is-active' : ''; ?>"
                                data-deal-index="<?php echo esc_attr($item_index); ?>"
                                aria-label="<?php echo esc_attr(($item_index + 1) . '番目の商品から表示'); ?>"
                                aria-pressed="<?php echo $item_index === 0 ? 'true' : 'false'; ?>"
                            ></button>
                        <?php endforeach; ?>
                    </div>
                    <button class="c-pop c-slider--btn c-slider--btn--right p-single-shop__deal-pager-button js-single-shop-deal-next" type="button" aria-label="次のお買い得品へ">
                        <img class="c-slider--btn__img p-single-shop__deal-pager-img" src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                        <svg class="c-slider--btn__img" width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.54134 7.68812C6.59107 7.52537 6.64079 7.37037 6.69052 7.20762L6.54134 7.20762L6.54134 6.88986C6.39216 6.83561 6.25008 6.78136 6.10089 6.72711C5.88778 6.5256 5.84515 6.1691 5.80963 5.77384C5.66045 5.71959 5.51837 5.66534 5.36919 5.61109C5.19159 6.0606 4.98557 6.31635 4.92874 6.88986C5.63203 7.07586 5.77411 7.59512 6.53423 7.68812L6.54134 7.68812ZM3.62161 5.61109L3.62161 5.77384L2.74783 5.77384L2.74783 5.9366L2.59864 5.9366C2.63416 6.31635 2.66258 6.33185 2.74783 6.57211L3.33035 6.57211L3.33035 6.40935C3.61451 6.20785 3.6074 6.1536 4.06206 6.0916C4.11179 5.82809 4.16151 5.55684 4.21124 5.29334L4.36042 5.29334C4.48119 4.75858 4.72983 4.59583 5.23421 4.49508C5.24842 4.21607 5.38339 3.91382 5.38339 3.85957L5.23421 3.85957L5.23421 3.37906L4.5025 3.37906L4.5025 3.54181C4.26097 3.73556 3.62161 4.53383 3.47953 4.82058L2.60575 4.82058C2.73362 5.39409 3.02488 5.57234 3.62872 5.61884L3.62161 5.61109ZM2.1582 3.70456L2.1582 3.54181L2.00902 3.54181L2.00902 3.70456L1.85983 3.70456L1.85983 3.86732C2.00902 3.92157 2.15109 3.97582 2.30028 4.03007C2.21503 3.71231 2.32159 3.89056 2.15109 3.71231L2.1582 3.70456ZM3.69265 29.7295L3.69265 29.8922L3.84184 29.8922C3.89157 30.055 3.94129 30.21 3.99102 30.3727C4.62327 30.334 4.4883 30.272 4.86481 30.055L4.86481 29.7372L3.69265 29.7372L3.69265 29.7295ZM6.02986 29.8922L6.76156 29.8922C6.81129 29.7295 6.86102 29.5744 6.91075 29.4117L6.76156 29.4117C6.71184 29.2489 6.66211 29.0939 6.61238 28.9312L5.88067 28.9312C5.88067 29.3884 5.9304 29.6132 6.02986 29.8845L6.02986 29.8922ZM4.86481 28.4584C4.91453 28.2957 4.96426 28.1407 5.01399 27.9779C5.11345 27.9237 5.2058 27.8694 5.30525 27.8152L5.30525 27.6524L5.88778 27.6524L5.88778 27.4897L6.03696 27.4897C6.13641 27.1719 6.22877 26.8542 6.32822 26.5364C6.07248 26.4124 5.77411 26.2806 5.59651 26.0559C4.8577 26.1024 4.75114 26.4047 4.28228 26.6914C4.29649 27.5207 4.46699 27.8229 4.57355 28.4429L4.86481 28.4429L4.86481 28.4584ZM5.44733 23.9944L5.29815 23.9944L5.29815 23.8316C4.92164 23.8549 4.78666 23.8471 4.56644 23.9944L4.56644 24.1571L4.126 24.1571C4.126 24.1571 3.97681 24.3896 3.83473 24.4749C3.87025 24.9244 3.89156 25.0561 4.126 25.2731C4.28939 25.4514 4.126 25.3429 4.41726 25.4359C4.51671 25.1181 4.60906 24.8004 4.70852 24.4826L5.44023 24.4826L5.44023 24.0021L5.44733 23.9944ZM1.50464 24.7074C1.55436 24.5446 1.60409 24.3896 1.65382 24.2269L1.803 24.2269L1.803 24.0641L1.65382 24.0641L1.65382 23.9014C1.19206 24.0254 1.11392 24.1106 1.07129 24.6996L1.51174 24.6996L1.50464 24.7074ZM2.81887 23.9091C2.72652 23.5216 2.66258 23.5604 2.52761 23.2736L2.08716 23.2736L2.08716 23.7541C2.32869 23.8084 2.57733 23.8626 2.81887 23.9169L2.81887 23.9091ZM3.11013 23.9944L3.11013 23.6766L2.81887 23.6766L2.81887 23.9944L3.11013 23.9944Z" fill="#BEBBBB"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
            <?php foods_single_shop_render_important_notice($important_notices); ?>
        </section>
    <?php endif; ?>

    <?php if ((!$current_flyer || !$current_bargain_items) && $important_notices) : ?>
        <div class="p-single-shop__section p-single-shop__important-section">
            <?php foods_single_shop_render_important_notice($important_notices); ?>
        </div>
    <?php endif; ?>

    <section class="p-single-shop__info p-single-shop__section" aria-labelledby="single-shop-info-title">
        <h2 id="single-shop-info-title" class="p-single-shop__heading">基本情報</h2>
        <img class="p-single-shop__main-photo" src="<?php echo esc_url($shop_image_url); ?>" alt="<?php echo esc_attr($shop_image_alt ?: ($shop_title ?: '店舗') . ' 外観'); ?>">
        <dl class="p-single-shop__info-list">
            <div>
                <dt>営業時間</dt>
                <dd>
                    <?php echo foods_single_shop_get_text_field_html($shop_data['fields']['business_hours'] ?? ''); ?>
                    <?php if (!empty($shop_data['fields']['business_hours_note'])) : ?>
                        <br><span><?php echo foods_single_shop_get_text_field_html($shop_data['fields']['business_hours_note']); ?></span>
                    <?php endif; ?>
                </dd>
            </div>
            <div>
                <dt>住所</dt>
                <dd>
                    <?php if (!empty($shop_data['fields']['postal_code'])) : ?>
                        〒<?php echo esc_html($shop_data['fields']['postal_code']); ?><br>
                    <?php endif; ?>
                    <?php echo foods_single_shop_get_text_field_html($shop_data['fields']['address'] ?? ''); ?>
                </dd>
            </div>
            <div>
                <dt>電話番号</dt>
                <dd><?php echo foods_single_shop_get_text_field_html($shop_data['fields']['tel'] ?? ''); ?></dd>
            </div>
            <div>
                <dt>FAX</dt>
                <dd><?php echo foods_single_shop_get_text_field_html($shop_data['fields']['fax'] ?? ''); ?></dd>
            </div>
            <div>
                <dt>駐車場</dt>
                <dd><?php echo foods_single_shop_get_text_field_html($shop_data['fields']['parking'] ?? ''); ?></dd>
            </div>
        </dl>
        <?php if ($access_map_embed_html) : ?>
            <div class="p-single-shop__map p-single-shop__map--embed">
                <?php echo $access_map_embed_html; ?>
            </div>
        <?php else : ?>
            <div class="p-single-shop__map" role="img" aria-label="店舗周辺地図の仮表示"></div>
        <?php endif; ?>
        <dl class="p-single-shop__info-list p-single-shop__info-list--compact">
            <div>
                <dt>アクセス</dt>
                <dd><?php echo foods_single_shop_get_text_field_html($shop_data['fields']['access'] ?? ''); ?></dd>
            </div>
            <div>
                <dt>混雑状況</dt>
                <dd>
                    <?php if (!empty($shop_data['fields']['congestion_url'])) : ?>
                        <a href="<?php echo esc_url($shop_data['fields']['congestion_url']); ?>" target="_blank" rel="noopener noreferrer">現在の混雑状況はこちら</a>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>
    </section>

    <?php if ($payment_groups) : ?>
        <section class="p-single-shop__payment p-single-shop__section" aria-labelledby="single-shop-payment-title">
            <h2 id="single-shop-payment-title" class="p-single-shop__bracket-heading">決済方法</h2>
            <?php foreach ($payment_groups as $payment_group) : ?>
                <div class="p-single-shop__payment-group">
                    <h3><?php echo esc_html($payment_group['title']); ?></h3>
                    <ul class="p-single-shop__payment-icons <?php echo esc_attr($payment_group['class'] ?? ''); ?>">
                        <?php foreach ($payment_group['items'] as $payment_item) : ?>
                            <li><img src="<?php echo esc_url($theme_uri . '/img/page/single-shop/' . $payment_item['icon']); ?>" alt="<?php echo esc_attr($payment_item['label']); ?>"></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <?php if ($service_items) : ?>
        <section class="p-single-shop__services p-single-shop__section" aria-labelledby="single-shop-services-title">
            <h2 id="single-shop-services-title" class="p-single-shop__bracket-heading">施設・サービス</h2>
            <ul class="p-single-shop__service-grid">
                <?php foreach ($service_items as $service_item) : ?>
                    <li>
                        <span><img src="<?php echo esc_url($theme_uri . '/img/page/single-shop/' . $service_item['icon']); ?>" alt=""></span>
                        <?php echo wp_kses($service_item['label'], ['br' => []]); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ($shop_news_items) : ?>
        <section class="p-single-shop__news p-single-shop__section" aria-labelledby="single-shop-news-title">
            <h2 id="single-shop-news-title" class="p-single-shop__heading">店舗からのお知らせ</h2>
            <img class="p-single-shop__news-staff" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/shop_news_staff.svg'); ?>" alt="" aria-hidden="true">
            <div class="p-single-shop__news-list">
                <?php foreach ($shop_news_items as $news_index => $news_item) : ?>
                    <?php
                    $news_image_url = $news_item['image']['url'] ?? $placeholder_image;
                    $news_image_alt = $news_item['image']['alt'] ?? '';
                    ?>
                    <div class="c-campaign-content-wrapper p-single-shop__news-campaign js-single-shop-news-card" <?php echo $news_index === 0 ? '' : 'hidden'; ?>>
                        <div class="c-campaign-content">
                            <a href="<?php echo esc_url($news_item['permalink']); ?>">
                                <picture>
                                    <img class="c-pop c-campaign-content__img" src="<?php echo esc_url($news_image_url); ?>" alt="<?php echo esc_attr($news_image_alt ?: $news_item['title']); ?>">
                                </picture>
                                <time class="c-campaign-content__time" datetime="<?php echo esc_attr($news_item['datetime']); ?>"><?php echo esc_html($news_item['date']); ?></time>
                            </a>
                        </div>
                        <p class="c-campaign-content-caption"><?php echo esc_html($shop_title ?: 'セレクション'); ?><br><?php echo esc_html($news_item['title']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($shop_news_items) > 1) : ?>
                <button class="c-pop c-slider--btn c-slider--btn--left p-single-shop__news-pager-button js-single-shop-news-prev" type="button" aria-label="前のお知らせへ">
                    <img class="c-slider--btn__img" src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-left.svg'); ?>" alt="">
                </button>
                <button class="c-pop c-slider--btn c-slider--btn--right p-single-shop__news-pager-button js-single-shop-news-next" type="button" aria-label="次のお知らせへ">
                    <img class="c-slider--btn__img" src="<?php echo esc_url($theme_uri . '/img/component/svg/btn_arrow-right.svg'); ?>" alt="">
                </button>
                <div class="p-single-shop__slider-dots js-single-shop-news-dots" aria-label="店舗からのお知らせのページ送り">
                    <?php foreach ($shop_news_items as $news_index => $news_item) : ?>
                        <button
                            type="button"
                            class="<?php echo $news_index === 0 ? 'is-active' : ''; ?>"
                            data-news-index="<?php echo esc_attr($news_index); ?>"
                            aria-label="<?php echo esc_attr(($news_index + 1) . '件目のお知らせを表示'); ?>"
                            aria-pressed="<?php echo $news_index === 0 ? 'true' : 'false'; ?>"
                        ></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="p-single-shop__message p-single-shop__section" aria-labelledby="single-shop-message-title">
        <h2 id="single-shop-message-title" class="p-single-shop__heading">店長あいさつ</h2>
        <div class="p-single-shop__message-body">
            <?php echo wp_kses_post(wpautop($manager_message)); ?>
        </div>
        <?php if ($manager_name) : ?>
            <p class="p-single-shop__manager"><?php echo esc_html(($shop_title ?: '店舗') . ' 店長 ' . $manager_name); ?></p>
        <?php endif; ?>
        <div class="p-single-shop__manager-photo">
            <img class="p-single-shop__manager-photo-image" src="<?php echo esc_url($manager_image_url); ?>" alt="<?php echo esc_attr($manager_image_alt ?: ($shop_title ?: '店舗') . ' 店長' . ($manager_name ? ' ' . $manager_name : '')); ?>">
            <img class="p-single-shop__manager-photo-frame" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/owner_greeting_frame.svg'); ?>" alt="" aria-hidden="true">
            <img class="p-single-shop__manager-photo-decoration p-single-shop__manager-photo-decoration--default" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/owner_greeting_decoration.png'); ?>" alt="" aria-hidden="true">
            <img class="p-single-shop__manager-photo-decoration p-single-shop__manager-photo-decoration--tablet" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/owner_greeting_decoration-tablet.svg'); ?>" alt="" aria-hidden="true">
            <img class="p-single-shop__manager-photo-bush" src="<?php echo esc_url($theme_uri . '/img/page/single-shop/owner_greeting_bush.svg'); ?>" alt="" aria-hidden="true">
        </div>
    </section>

    <section class="p-single-shop__recruit p-single-shop__section" aria-labelledby="single-shop-recruit-title">
        <h2 id="single-shop-recruit-title" class="p-single-shop__heading">パートアルバイト<br>求人情報</h2>
        <?php if ($visible_recruit_data) : ?>
            <div class="p-single-shop__recruit-list">
                <?php foreach ($visible_recruit_data as $recruit_index => $recruit_item) : ?>
                    <?php
                    $recruit_fields = $recruit_item['fields'] ?? [];
                    $recruit_title = trim((string) ($recruit_fields['job_type'] ?? '')) ?: ($recruit_item['title'] ?? '');
                    $recruit_salary = foods_single_shop_format_multiline_text($recruit_fields['salary'] ?? '');
                    $recruit_hours = foods_single_shop_format_multiline_text($recruit_fields['working_hours'] ?? '');
                    $recruit_note = foods_single_shop_format_multiline_text($recruit_fields['benefits'] ?? '');
                    $recruit_link = $recruit_item['permalink'] ?? '#';
                    ?>
                    <article class="p-single-shop__recruit-card js-single-shop-recruit-card" <?php echo $recruit_index < 2 ? '' : 'hidden'; ?>>
                        <h3><?php echo esc_html($recruit_title); ?></h3>
                        <?php if ($recruit_salary) : ?>
                            <p><strong>給与：</strong><?php echo $recruit_salary; ?></p>
                        <?php endif; ?>
                        <?php if ($recruit_hours) : ?>
                            <p><strong>勤務時間：</strong><br><?php echo $recruit_hours; ?></p>
                        <?php endif; ?>
                        <?php if ($recruit_note) : ?>
                            <p><?php echo $recruit_note; ?></p>
                        <?php endif; ?>
                        <a href="<?php echo esc_url($recruit_link); ?>">応募する</a>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($recruit_page_count > 1) : ?>
                <div class="p-single-shop__slider-dots js-single-shop-recruit-dots" aria-label="パートアルバイト求人情報のページ送り">
                    <?php for ($recruit_page_index = 0; $recruit_page_index < $recruit_page_count; $recruit_page_index++) : ?>
                        <button
                            type="button"
                            class="<?php echo $recruit_page_index === 0 ? 'is-active' : ''; ?>"
                            data-recruit-page="<?php echo esc_attr($recruit_page_index); ?>"
                            aria-label="<?php echo esc_attr(($recruit_page_index + 1) . 'ページ目の求人を表示'); ?>"
                            aria-pressed="<?php echo $recruit_page_index === 0 ? 'true' : 'false'; ?>"
                        ></button>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            <a class="p-single-shop__more" href="<?php echo esc_url($recruit_archive_url); ?>">もっと見る</a>
        <?php endif; ?>
        <img class="p-single-shop__recruit-banner" src="<?php echo esc_url($recruit_image); ?>" alt="採用募集">
    </section>

    <?php $nearby_shops = $same_prefecture_shops_data; ?>
    <?php $nearby_page_count = (int) ceil(count($nearby_shops) / 3); ?>
    <?php if ($nearby_shops) : ?>
    <section class="p-single-shop__nearby p-single-shop__section" aria-labelledby="single-shop-nearby-title">
        <h2 id="single-shop-nearby-title" class="p-single-shop__heading">近隣の店舗を見る</h2>
        <ul class="c-shop-card-list">
            <?php foreach ($nearby_shops as $nearby_index => $nearby_shop) : ?>
                <?php
                $nearby_fields = $nearby_shop['fields'] ?? [];
                $nearby_postal_code = preg_replace('/^〒\s*/u', '', (string) ($nearby_fields['postal_code'] ?? ''));
                $nearby_tel = (string) ($nearby_fields['tel'] ?? '');
                $nearby_permalink = (string) ($nearby_shop['permalink'] ?? '');
                ?>
                <li class="c-shop-card js-single-shop-nearby-card"<?php echo $nearby_index < 3 ? '' : ' hidden'; ?>>
                    <p class="c-shop-card__name"><?php echo esc_html($nearby_shop['title'] ?? ''); ?></p>
                    <dl class="c-shop-card__detail">
                        <div class="c-shop-card__detail__item">
                            <dt class="c-shop-card__detail__title">営業時間</dt>
                            <dd class="c-shop-card__detail__content">
                                <time class="c-shop-card__detail__time">
                                    <span class="c-shop-card__detail__time__main">
                                        <?php echo esc_html($nearby_fields['business_hours'] ?? ''); ?>
                                    </span>
                                    <span class="c-shop-card__detail__time__note">
                                        <?php echo esc_html($nearby_fields['business_hours_note'] ?? ''); ?>
                                    </span>
                                </time>
                            </dd>
                        </div>
                        <div class="c-shop-card__detail__item">
                            <dt class="c-shop-card__detail__title">住所</dt>
                            <dd class="c-shop-card__detail__content">
                                <address class="c-shop-card__detail__address">
                                    <?php if ($nearby_postal_code !== '') : ?>
                                        <span class="c-shop-card__detail__address__zip">
                                            〒<?php echo esc_html($nearby_postal_code); ?>
                                        </span><br>
                                    <?php endif; ?>
                                    <span class="c-shop-card__detail__address__place">
                                        <?php echo esc_html($nearby_fields['address'] ?? ''); ?>
                                    </span>
                                </address>
                            </dd>
                        </div>
                        <div class="c-shop-card__detail__item">
                            <dt class="c-shop-card__detail__title">電話番号</dt>
                            <dd class="c-shop-card__detail__content">
                                <a
                                    class="c-shop-card__detail__tel"
                                    href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $nearby_tel)); ?>"
                                >
                                    <?php echo esc_html($nearby_tel); ?>
                                </a>
                            </dd>
                        </div>
                    </dl>

                    <div class="c-shop-card__link--wrapper">
                        <a
                            class="c-shop-card__link u-bg--green"
                            href="<?php echo esc_url($nearby_permalink); ?>"
                        >
                            <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M12.6994 0H5.65984L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                                <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                                <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                                <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                            </svg>
                            <span class="c-shop-card__link__text">店舗チラシ</span>
                        </a>
                        <a class="c-shop-card__link u-bg--blue" href="<?php echo esc_url($nearby_permalink . '#single-shop-recruit-title'); ?>">
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
        <?php if ($nearby_page_count > 1) : ?>
            <div class="p-single-shop__slider-dots js-single-shop-nearby-dots" aria-label="近隣店舗のページ送り">
                <?php for ($nearby_page_index = 0; $nearby_page_index < $nearby_page_count; $nearby_page_index++) : ?>
                    <button
                        type="button"
                        class="<?php echo $nearby_page_index === 0 ? 'is-active' : ''; ?>"
                        data-nearby-page="<?php echo esc_attr($nearby_page_index); ?>"
                        aria-label="<?php echo esc_attr(($nearby_page_index + 1) . 'ページ目の近隣店舗を表示'); ?>"
                        aria-pressed="<?php echo $nearby_page_index === 0 ? 'true' : 'false'; ?>"
                    ></button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
        <a class="p-single-shop__more" href="<?php echo esc_url(home_url('/shop/')); ?>">店舗一覧</a>
    </section>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
