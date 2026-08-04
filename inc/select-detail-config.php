<?php

/**
 * セレクションのこだわり詳細ページで使用するページ別設定。
 *
 * レイアウトは共通テンプレート側で管理し、このファイルには
 * ページごとに変わる文章、画像、ニュース分類だけを定義する。
 *
 * @return array<string, array<string, mixed>>
 */
function foods_get_select_detail_configs() {
    return [
        'meat' => [
            'page_slug' => 'select-meat',
            'modifier' => 'meat',
            'title' => 'お肉のこだわり',
            'asset_base' => 'img/page/page-select-meat',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '毎日の食卓から特別な日の一皿まで。<br>産地や鮮度、部位ごとの特長を見極め、<br>「美味しさ」と「安心」の両立にこだわった<br>お肉をお届けしています。',
                'image' => 'svg/img_meatTop.svg',
                'image_alt' => '家族で食事を楽しむ様子',
            ],
            'message' => [
                'bubble_sp' => 'svg/img_message-sp.svg',
                'bubble_pc' => 'svg/img_message-pc.svg',
                'buyer_image' => 'svg/img_message-buyer.svg',
                'background_pc' => 'svg/message_section_bg-pc.svg',
                'text' => '毎日の使いやすさと、週末のごちそう感。<br>その両方を叶える“肉の売場”。<br>セレクションは、部位の特徴を活かした<br>カットと提案で、<br>料理がきれいに決まる売場づくりを<br>徹底しています。<br>おいしさの土台は、鮮度と素材の安心。<br>私たちは、飼育や原材料まで目を向けた<br>肉を選び、必要以上の添加物に頼らない<br>無添加ハム・ウィンナーなども<br>揃えています。<br>さらに、北総豚をはじめ<br>生産者とつながる取り組みで、<br>味わいと品質を安定して届けます。<br>そして最後は“料理目線”。<br>「炒める」「煮る」「揚げる」<br>「焼く」など<br>用途が一目で伝わるカット、<br>味付け・下ごしらえの提案まで<br>セットにして、<br>迷わず選べるように。<br>平日は時短でおいしく、<br>週末はしっかりごちそうに。<br>毎日の食卓を、お肉から支えます。',
                'text_pc' => '毎日の使いやすさと、週末のごちそう感。<br>その両方を叶える“肉の売場”。<br>セレクションは、部位の特徴を活かしたカットと提案で、<br>料理がきれいに決まる売場づくりを徹底しています。<br>おいしさの土台は、鮮度と素材の安心。<br>私たちは、飼育や原材料まで目を向けた<br>肉を選び、必要以上の添加物に頼らない<br>無添加ハム・ウィンナーなども揃えています。<br>さらに、北総豚をはじめ生産者とつながる取り組みで、<br>味わいと品質を安定して届けます。<br>そして最後は“料理目線”。<br>「炒める」「煮る」「揚げる」「焼く」など<br>用途が一目で伝わるカット、<br>味付け・下ごしらえの提案までセットにして、迷わず選べるように。<br>平日は時短でおいしく、週末はしっかりごちそうに。<br>毎日の食卓を、お肉から支えます。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => 'svg/meat_about_title.svg',
                'background' => 'brick.png',
                'wave_sp_top' => 'svg/upper_wave.svg',
                'wave_sp_bottom' => 'svg/bottom_wave.svg',
                'wave_pc_top' => 'svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => 'svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [
                    'title_sp' => ['src' => 'svg/meat_about_deco1.svg', 'alt' => 'ファーム作業人'],
                    'title_pc' => ['src' => 'svg/meat_about_deco1-pc.svg', 'alt' => 'ファーム作業人'],
                ],
                'sections' => [
                    [
                        'title' => '美味北総豚<br><span>代表生産者</span> 井上農場',
                        'main_image' => ['src' => 'meat_about_img1.png', 'webp' => 'webp/meat_about_img1.webp', 'alt' => '美味北総豚を育てる井上農場'],
                        'text' => '豚肉の味は、品種と飼育環境が大きく影響されるといわれています。「美味北総豚」は千葉県九十九里の緑豊かで温暖な気候の北総大地の 中で日々の健康管理、飼料や飼育環境に目を配りストレスを極力かけず大切に育てられています。',
                        'sub_title' => '豊かな自然と丁寧な飼育から生まれる千葉が誇るブランド豚',
                        'secondary_image' => ['src' => 'meat_about_img2.png', 'webp' => 'webp/meat_about_img2.webp', 'alt' => '美味北総豚'],
                        'secondary_text' => '肉質はきめ細かくやわらかく、脂にはしつこさがなく、口に入れた瞬間に広がる自然な甘みと旨味が特長のおすすめの豚肉です。店舗から近い産地より産地直結でほぼ毎日仕入れているので鮮度も自信をもっておすすめします。',
                        'gallery' => [
                            ['src' => 'meat_about_img3.png', 'webp' => 'webp/meat_about_img3.webp', 'alt' => '美味北総豚の生産風景'],
                            ['src' => 'meat_about_img4.png', 'webp' => 'webp/meat_about_img4.webp', 'alt' => '美味北総豚の生産風景'],
                        ],
                        'decorations' => [
                            ['key' => 'decoration2', 'src' => 'svg/meat_about_deco2.svg', 'alt' => 'お肉のこだわり', 'placement' => 'content'],
                            ['key' => 'decoration7', 'src' => 'svg/meat_about_deco7.svg', 'alt' => 'ファーム作業人', 'placement' => 'gallery_after'],
                        ],
                    ],
                    [
                        'title' => '毎日たべらるリーズナブルな国産牛肉<br>かみむらファーム かみむら牛',
                        'main_image' => ['src' => 'meat_about_img5.png', 'webp' => 'webp/meat_about_img5.webp', 'alt' => 'かみむら牛'],
                        'text' => 'かみむらファームでは、牛のエサづくりから製造・加工まで一貫して行い、ニーズに応える技術を集結。『一頭一頭、完璧なトレーサビリティ』を掲げ、カミチクファームの飼料用米・イネを発酵飼料に加工して肥育する「玄米黒牛」など、あっさり口どけの良い脂を目指すオリジナルブランド牛を展開。安全・安心で毎日食卓へ届けています。',
                        'gallery' => [
                            ['src' => 'meat_about_img6.png', 'webp' => 'webp/meat_about_img6.webp', 'alt' => 'かみむら牛の生産風景'],
                            ['src' => 'meat_about_img7.png', 'webp' => 'webp/meat_about_img7.webp', 'alt' => 'かみむら牛の生産風景'],
                        ],
                        'decorations' => [
                            ['key' => 'decoration3', 'src' => 'svg/meat_about_deco3.svg', 'alt' => 'お肉のこだわり', 'placement' => 'content'],
                            ['key' => 'decoration4', 'src' => 'svg/meat_about_deco4.svg', 'alt' => 'お肉のこだわり', 'placement' => 'gallery_before'],
                        ],
                    ],
                    [
                        'title' => '素材だけで、ここまで旨い <br>The Better Table ホワイトスモーク',
                        'main_image' => ['src' => 'meat_about_img8.png', 'webp' => 'webp/meat_about_img8.webp', 'alt' => 'The Better Table ホワイトスモーク'],
                        'text' => 'かみむらファームでは、牛のエサづくりから製造・加工まで一貫して行い、ニーズに応える技術を集結。『一頭一頭、完璧なトレーサビリティ』を掲げ、カミチクファームの飼料用米・イネを発酵飼料に加工して肥育する「玄米黒牛」など、あっさり口どけの良い脂を目指すオリジナルブランド牛を展開。安全・安心で毎日食卓へ届けています。',
                        'gallery' => [
                            ['src' => 'meat_about_img9.png', 'webp' => 'webp/meat_about_img9.webp', 'alt' => 'ホワイトスモークの商品'],
                            ['src' => 'meat_about_img10.png', 'webp' => 'webp/meat_about_img10.webp', 'alt' => 'ホワイトスモークの商品'],
                            ['src' => 'meat_about_img11.png', 'webp' => 'webp/meat_about_img11.webp', 'alt' => 'ホワイトスモークの商品'],
                            ['src' => 'meat_about_img12.png', 'webp' => 'webp/meat_about_img12.webp', 'alt' => 'ホワイトスモークの商品'],
                        ],
                        'decorations' => [
                            ['key' => 'decoration5', 'src' => 'svg/meat_about_deco5.svg', 'alt' => 'お肉のこだわり', 'placement' => 'gallery_before'],
                        ],
                    ],
                ],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'meat',
                'title' => 'お肉のこだわりをもっと見る',
                'archive_label' => 'お肉のこだわり一覧はこちら',
                'decoration' => 'svg/meat_about_deco6.svg',
                'background_sp' => 'meat_news_bgSP.png',
                'background_pc' => 'meat_news_bgPC.png',
            ],
        ],
    ];
}

/**
 * ページ種別を指定して、こだわり詳細ページの設定を取得する。
 *
 * @param string $key meat、fish、rice などのページ種別。
 * @return array<string, mixed>|null
 */
function foods_get_select_detail_config($key) {
    $configs = foods_get_select_detail_configs();

    return isset($configs[$key]) ? $configs[$key] : null;
}

/**
 * WordPress固定ページのスラッグから設定を取得する。
 *
 * @param string $page_slug 固定ページのスラッグ。
 * @return array<string, mixed>|null
 */
function foods_get_select_detail_config_by_page_slug($page_slug) {
    foreach (foods_get_select_detail_configs() as $config) {
        if (isset($config['page_slug']) && $config['page_slug'] === $page_slug) {
            return $config;
        }
    }

    return null;
}

/**
 * ページ設定内の相対パスをテーマ内画像URLへ変換する。
 *
 * @param array<string, mixed> $config ページ別設定。
 * @param string               $relative_path asset_baseからの相対パス。
 * @return string
 */
function foods_get_select_detail_asset_url($config, $relative_path) {
    $base = isset($config['asset_base']) ? trim((string) $config['asset_base'], '/') : '';
    $path = trim($relative_path, '/');

    return get_template_directory_uri() . '/' . $base . '/' . $path;
}
