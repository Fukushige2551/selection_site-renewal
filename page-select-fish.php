<?php
/*
Template Name: セレクションのこだわり お魚
*/

$fish_news_query = new WP_Query([
    'post_type' => 'news',
    'post_status' => 'publish',
    'posts_per_page' => 2,
    'orderby' => [
        'date' => 'DESC',
        'ID' => 'DESC',
    ],
    'no_found_rows' => true,
    'tax_query' => [
        [
            'taxonomy' => 'news_commitment',
            'field' => 'slug',
            'terms' => ['fish'],
        ],
    ],
]);

$fish_news_category_priority = ['information', 'notice', 'campaign', 'shop-news', 'commitment', 'important'];
$fish_news_category_classes = [
    'information' => 'notice',
    'notice' => 'notice',
    'campaign' => 'campaign',
    'shop-news' => 'shop',
    'commitment' => 'commitment',
    'important' => 'important',
];

$fish_news_items = array_map(static function ($news_post) use ($fish_news_category_priority, $fish_news_category_classes) {
    $post_id = $news_post->ID;
    $categories = get_the_terms($post_id, 'news_category');
    $selected_category = null;

    if (!is_wp_error($categories) && $categories) {
        foreach ($fish_news_category_priority as $category_slug) {
            foreach ($categories as $category) {
                if ($category->slug === $category_slug) {
                    $selected_category = $category;
                    break 2;
                }
            }
        }

        if (!$selected_category) {
            $selected_category = reset($categories);
        }
    }

    $image_url = get_the_post_thumbnail_url($post_id, 'medium');
    $image_alt = '';

    if ($image_url) {
        $image_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
    }

    if (!$image_url && function_exists('get_field')) {
        foreach (['news_eyecatch_image', 'news_image', 'main_image', 'thumbnail', 'image'] as $field_name) {
            $image = get_field($field_name, $post_id);

            if (is_array($image) && !empty($image['url'])) {
                $image_url = $image['sizes']['medium'] ?? $image['url'];
                $image_alt = $image['alt'] ?? '';
                break;
            }

            if (is_numeric($image)) {
                $image_url = wp_get_attachment_image_url((int) $image, 'medium');
                $image_alt = get_post_meta((int) $image, '_wp_attachment_image_alt', true);
                break;
            }
        }
    }

    $category_slug = $selected_category ? $selected_category->slug : 'information';

    return [
        'title' => get_the_title($post_id),
        'url' => get_permalink($post_id),
        'date' => get_the_date('Y.m.d', $post_id),
        'datetime' => get_the_date('Y-m-d', $post_id),
        'image_url' => $image_url ?: get_template_directory_uri() . '/img/component/no-image.png',
        'image_alt' => $image_alt,
        'category_label' => $selected_category ? $selected_category->name : '',
        'category_class' => $fish_news_category_classes[$category_slug] ?? 'notice',
    ];
}, $fish_news_query->posts);

$fish_news_term = get_term_by('slug', 'fish', 'news_commitment');
$fish_news_archive_url = $fish_news_term ? get_term_link($fish_news_term) : get_post_type_archive_link('news');

if (is_wp_error($fish_news_archive_url)) {
    $fish_news_archive_url = get_post_type_archive_link('news');
}

get_header();
?>

<main id="page-select-fish" class="p-page-select-fish c-main">
    <nav class="c-breadcrumb" aria-label="パンくずリスト">
        <a class="c-breadcrumb__link" href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <img class="c-breadcrumb__arrow" src="<?php echo esc_url(get_template_directory_uri() . '/img/component/svg/icon_breadcrumb.svg'); ?>" alt="" aria-hidden="true">
        <a class="c-breadcrumb__link" href="<?php echo esc_url(home_url('/select/')); ?>">セレクションのこだわり</a>
        <img class="c-breadcrumb__arrow" src="<?php echo esc_url(get_template_directory_uri() . '/img/component/svg/icon_breadcrumb.svg'); ?>" alt="" aria-hidden="true">
        <span class="c-breadcrumb__current" aria-current="page">お魚</span>
    </nav>

    <section class="p-page-select-fish__intro" aria-labelledby="fish-intro-title">
        <h1 id="fish-intro-title" class="p-page-select-fish__intro-title">お魚のこだわり</h1>
        <div class="p-page-select-fish__intro-description">
            <p>旬や鮮度はもちろん、仕入れから売り場に並ぶまでのスピードを大切に。</p>
            <p>その日いちばん美味しい状態でお魚をお届けするための工夫を積み重ねています。</p>
        </div>
        <img
            class="p-page-select-fish__intro-image"
            src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/fishery.svg'); ?>"
            width="363"
            height="239"
            alt="漁港で新鮮な魚を仕分けする様子"
        >
    </section>

    <section class="p-page-select-fish__buyer" aria-labelledby="buyer-message-title">
        <img
            class="p-page-select-fish__buyer-bg"
            src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/buyer-message-bg.svg'); ?>"
            alt=""
            aria-hidden="true"
        >
        <div class="p-page-select-fish__buyer-heading">
            <img
                class="p-page-select-fish__buyer-balloon"
                src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/buyer-message-balloon.svg'); ?>"
                alt=""
                aria-hidden="true"
            >
            <h2 id="buyer-message-title" class="p-page-select-fish__buyer-title">バイヤーメッセージ</h2>
            <img
                class="p-page-select-fish__buyer-decoration"
                src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/buyer-message-decoration.svg'); ?>"
                alt=""
                aria-hidden="true"
            >
            <p class="p-page-select-fish__buyer-catch">旬を逃さず、<br>旨さを届ける</p>
        </div>
        <img
            class="p-page-select-fish__buyer-manager"
            src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/buyer-manager.png'); ?>"
            width="288"
            height="284"
            alt="鮮魚バイヤー"
        >
        <p class="p-page-select-fish__buyer-message">
            毎鮮度はもちろん、<br>
            産地や漁法、脂の乗りまで<br>
            “目で見て選ぶ”。<br>
            セレクションのお魚は、<br>
            全国の産直に加え、<br>
            地元・千葉県の漁港から届く鮮度の良い<br>
            「地魚」を中心に買付を行っています。<br>
            季節ごとに変わる<br>
            “旬のいちばん”を逃さず、<br>
            切身・刺身・焼魚まで、<br>
            今日いちばんおいしい形で<br>
            食卓に届けます。<br>
            魚は「鮮度」だけでなく、<br>
            今の状態に合った食べ方でおいしさが<br>
            決まります。<br>
            私たちは、脂の乗り・身質・サイズを見て<br>
            売場に出し、刺身、焼き、煮付けなど<br>
            おすすめの食べ方も一緒にご案内します。<br>
            売場では旬の打ち出しも強め、<br>
            迷わず選べるように。<br>
            迷ったら気軽に声をかけてください。
        </p>
    </section>

    <section class="p-page-select-fish__field" aria-labelledby="field-title">
        <header class="p-page-select-fish__field-header">
            <img
                class="p-page-select-fish__field-pickup"
                src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-pickup.svg'); ?>"
                alt="PICK UP"
            >
            <div class="p-page-select-fish__field-title-wrap">
                <img
                    class="p-page-select-fish__field-title-bg"
                    src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-title-balloon.svg'); ?>"
                    alt=""
                    aria-hidden="true"
                >
                <h2 id="field-title" class="p-page-select-fish__field-title">こだわりの現場から</h2>
            </div>
        </header>

        <article class="p-page-select-fish__field-article">
            <h3 class="p-page-select-fish__field-shop">
                <span>高知県宿毛市</span>
                <small>株式会社</small> 勇進
            </h3>
            <img
                class="p-page-select-fish__field-main-image"
                src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-yellowtail-fisher.png'); ?>"
                width="672"
                height="420"
                alt="高知県宿毛市でブリを育てる生産者"
            >
            <p class="p-page-select-fish__field-lead">
                高知県の西の端に位置する宿毛湾。雄大な自然に囲まれた豊かな海域で、丹精込めて育てた自慢の逸品。それが「荒木さん家のブリ」。海とともに生きる荒木さんたちが、真心を込めてお届けします。
            </p>
            <h4 class="p-page-select-fish__field-heading">愛情をかけて育てる！自慢の逸品<br>「荒木さん家のブリ」</h4>
            <img
                class="p-page-select-fish__field-main-image"
                src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-yellowtail-sashimi.png'); ?>"
                width="672"
                height="420"
                alt="荒木さん家のブリの刺身"
            >
            <div class="p-page-select-fish__field-copy">
                <p>「荒木さん家のブリ」は名前の通り、1軒の生産者（荒木さん）が養殖から加工、販売まで一貫して行っているのですが、その全てにこだわりを持っています。</p>
                <p>宿毛湾は黒潮と豊後水道が交わり、栄養豊富な松田川が流れ込む肥沃な海域。まさに日本有数の飼育環境です。尚且つ、恵まれた海域で大型いけすを使っています。潮通しの環境が良く、水深50mの海で頻繁に餌をあげられるので成長が早く、ゴンゴン泳ぐので身も引き締まっています。</p>
                <p>餌はカタクチイワシなどの良質な魚粉にきびしぼりを配合したオリジナル飼料を使用。この飼料を用いることでブリ本来の旨味成分が増し、刺身で、焼いても、煮ても「美味しいブリ」が育ちます。</p>
            </div>
            <div class="p-page-select-fish__field-gallery p-page-select-fish__field-gallery--three">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-yellowtail-farm.png'); ?>" width="320" height="196" alt="ブリの養殖場">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-yellowtail-processing.png'); ?>" width="320" height="196" alt="水産加工の様子">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-yellowtail-packing.png'); ?>" width="320" height="196" alt="ブリを包装する様子">
            </div>
        </article>

        <hr class="p-page-select-fish__field-divider">

        <article class="p-page-select-fish__field-article">
            <h3 class="p-page-select-fish__field-shop">
                <span>千葉県鴨川市</span>
                島津商店 千産千消「房州ひじき」
            </h3>
            <img
                class="p-page-select-fish__field-main-image"
                src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-hijiki-processing.png'); ?>"
                width="672"
                height="420"
                alt="房州ひじきを加工する様子"
            >
            <p class="p-page-select-fish__field-lead">
                嶋津商店の「房州ひじき」は千葉県南部の沿岸の磯で採れたものを、新鮮なうちに、じっくりと炊き上げてあります。ふっくらとしたやわらかさと、磯の香りをお楽しみください。
            </p>
            <h4 class="p-page-select-fish__field-heading">旬の時期だけに採れる上質ひじき<br>産地指定で届ける千葉の味</h4>
            <img
                class="p-page-select-fish__field-main-image"
                src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-hijiki.png'); ?>"
                width="672"
                height="420"
                alt="房州ひじき"
            >
            <div class="p-page-select-fish__field-copy">
                <p>ひじきは成長が進むと芽の部分が開き柔らかくなるため、芽の閉まっている2月～3月中旬に良質のひじきが採れる勝浦地区、鴨川地区の原料を指定して入札しております。</p>
                <p>浜で入札されたひじきはすぐに嶋津商店の加工場に運ばれ、洗浄⇒釜蒸し⇒乾燥⇒選別、異物検査の順で加工されます。原料の鮮度がよく、水揚げ後すぐにボイルされるため、ひじきの風味や味わいが格段に違います。</p>
                <p>千葉のひじきは生産量が多いにもかかわらず、県内での消費がほとんど。お土産や道の駅などでは他県からの旅行者に大変評判がよく「ひじきだけ買いに来たわ……」という方もいらっしゃるほどです。</p>
                <p>セレクションのひじきは水揚げからしっかり履歴の管理された安全、安心のひじき、味わい豊富なひじきです。煮物以外にも、サラダや味噌汁、てんぷらもおいしく食べ方も豊富です。ぜひご賞味ください。</p>
            </div>
            <div class="p-page-select-fish__field-gallery">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-hijiki-drying.png'); ?>" width="320" height="196" alt="ひじきを乾燥させる様子">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-hijiki-dish.png'); ?>" width="320" height="196" alt="ひじきを使った料理">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-hijiki-product.png'); ?>" width="320" height="196" alt="房州ひじきの商品">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/field-hijiki-producer.png'); ?>" width="320" height="196" alt="房州ひじきの生産者">
            </div>
        </article>
    </section>

    <section class="p-page-select-fish__more" aria-labelledby="fish-more-title">
        <h2 id="fish-more-title" class="p-page-select-fish__more-title">お魚のこだわりを<br>もっと見る</h2>

        <?php if ($fish_news_items) : ?>
            <div class="p-page-select-fish__more-list">
                <?php foreach ($fish_news_items as $fish_news_item) : ?>
                    <a class="p-page-select-fish__more-card" href="<?php echo esc_url($fish_news_item['url']); ?>">
                        <div class="p-page-select-fish__more-card-header">
                            <time datetime="<?php echo esc_attr($fish_news_item['datetime']); ?>"><?php echo esc_html($fish_news_item['date']); ?></time>
                            <?php if ($fish_news_item['category_label']) : ?>
                                <span class="p-page-select-fish__more-category p-page-select-fish__more-category--<?php echo esc_attr($fish_news_item['category_class']); ?>"><?php echo esc_html($fish_news_item['category_label']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="p-page-select-fish__more-card-body">
                            <img src="<?php echo esc_url($fish_news_item['image_url']); ?>" alt="<?php echo esc_attr($fish_news_item['image_alt'] ?: $fish_news_item['title']); ?>">
                            <p><?php echo esc_html($fish_news_item['title']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a class="p-page-select-fish__more-commitment-link" href="<?php echo esc_url($fish_news_archive_url); ?>">お魚のこだわり一覧</a>
        <a class="p-page-select-fish__more-back" href="<?php echo esc_url(home_url('/select/')); ?>">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/img/page/page-select-fish/more-back-button.png'); ?>" alt="">
            <span>一覧へ戻る</span>
        </a>
    </section>
</main>

<?php get_footer(); ?>
