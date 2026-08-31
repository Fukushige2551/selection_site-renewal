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
        'vegetables-fruit' => [
            'page_slug' => 'select-vegetables-fruit',
            'modifier' => 'vegetables-fruit',
            'title' => 'お野菜・果物のこだわり',
            'asset_base' => 'img/page/page-select-vegetables-fruit',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '季節の移ろいを感じられる、<br>みずみずしい美味しさを。<br>産地や育て方に目を向け、<br>鮮度・味・安心感に<br>こだわった野菜とくだものを揃えています。',
                'image' => 'svg/hero.svg',
                'image_alt' => '畑で野菜を収穫する様子',
            ],
            'message' => [
                'bubble_sp' => 'svg/buyer-message-balloon-sp.svg',
                'bubble_pc' => 'svg/buyer-message-balloon-pc.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'corner_decoration' => 'svg/seedlings.svg',
                'background_pc' => '@theme/img/page/page-select-detail/svg/message_section_bg-pc.svg',
                'text' => '味は、畑で決まる。<br>だから、私たちは“仕入れ”に<br>妥協しません。<br>セレクションの青果は、安心・安全を大前提に、おいしさで選び抜く。<br>産地は「有名だから」ではなく、<br>その季節にいちばん旨い場所を選ぶ。<br>そして、生産者とつながることで、<br>顔が見える品質と<br>ぶれない基準を手に入れる。<br>市場任せでは届かない旬がある。<br>セレクションは、独自の産直ルートで<br>“季節の主役”を先回りし、<br>売場で旬がひと目で伝わる<br>提案をつくります。<br>さらに入荷後も勝負どころ。<br>温度・水分・陳列の細部まで<br>鮮度管理を行い、<br>今日も、明日も、ちゃんとおいしい――<br>ご家庭でいちばんおいしくなる状態で<br>お渡しします。<br>野菜は料理で選ぶ。<br>果物は食べる日で選ぶ。<br>サラダ、炒め物、煮込み――<br>用途に合う品種と使い方。<br>追熟や保存のコツまで、<br>売場で一緒にご案内します。<br>今日のおすすめ、ぜひ聞いてください。<br>いちばんおいしいタイミングまで<br>ご提案します。',
                'text_pc' => '味は、畑で決まる。<br>だから、私たちは“仕入れ”に妥協しません。<br>セレクションの青果は、安心・安全を大前提に、おいしさで選び抜く。<br>産地は「有名だから」ではなく、その季節にいちばん旨い場所を選ぶ。<br>そして、生産者とつながることで、顔が見える品質と<br>ぶれない基準を手に入れる。<br>市場任せでは届かない旬がある。<br>セレクションは、独自の産直ルートで“季節の主役”を先回りし、<br>売場で旬がひと目で伝わる提案をつくります。<br>さらに入荷後も勝負どころ。<br>温度・水分・陳列の細部まで鮮度管理を行い、<br>今日も、明日も、ちゃんとおいしい――<br>ご家庭でいちばんおいしくなる状態でお渡しします。<br>野菜は料理で選ぶ。果物は食べる日で選ぶ。<br>サラダ、炒め物、煮込み――用途に合う品種と使い方。<br>追熟や保存のコツまで、売場で一緒にご案内します。<br>今日のおすすめ、ぜひ聞いてください。<br>いちばんおいしいタイミングまでご提案します。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'sections' => [
                    [
                        'title' => '群馬発祥。<br>”感動農業”を信念に<br>品質にこだわった野菜をお届け<br>野菜くらぶ',
                        'main_image' => [
                            'src' => 'vegetable-club.png',
                            'alt' => '野菜くらぶの社屋',
                        ],
                        'text_blocks' => [
                            '野菜くらぶでは「適地適作」を大切にし、作物ごと、時期ごとに最適な産地で野菜づくりを行っています。土づくりから栽培方法まで、自社独自基準で丁寧に管理。化学肥料や農薬に極力頼らず、環境に配慮した農業で野菜本来の味を引き出します。',
                            '収穫後の管理にも気を遣い、例えばレタスであれば早朝に収穫したものを真空冷却機で急速冷却し、長期間シャキシャキの鮮度が維持するよう徹底的な品質管理を行っています。',
                            '毎日食べるものだからこそ、品質には妥協しません。',
                            '安心・安全な野菜を、新鮮なまま食卓へお届けします。',
                        ],
                        'gallery' => [
                            ['src' => 'vegetable-farmers.png', 'alt' => '野菜くらぶの生産者たち'],
                            ['src' => 'vegetable-sprout.png', 'alt' => '畑で育つ野菜の芽'],
                            ['src' => 'lettuce-field.png', 'alt' => '畑で育つレタス'],
                        ],
                        'gallery_variant' => 'three',
                        'decorations' => [
                            [
                                'key' => 'rice-planting',
                                'src' => 'svg/rice-planting.svg',
                                'src_pc' => 'svg/rice-planting-pc.svg',
                                'alt' => '',
                                'placement' => 'content',
                            ],
                            [
                                'key' => 'cabbage-farmer',
                                'src' => 'svg/cabbage-farmer.svg',
                                'alt' => '',
                                'placement' => 'content',
                            ],
                            [
                                'key' => 'truck-farmer',
                                'src' => 'svg/truck-farmer.svg',
                                'src_pc' => 'svg/truck-farmer-pc.svg',
                                'alt' => '',
                                'placement' => 'gallery_after',
                            ],
                        ],
                    ],
                    [
                        'location' => '熊本県河内のみかん農家',
                        'title' => 'ウシジマ青果',
                        'main_image' => [
                            'src' => 'ushijima-farmer.png',
                            'alt' => 'ウシジマ青果の生産者とみかん',
                        ],
                        'text_blocks' => [
                            '熊本県河内地方。有明海を臨む段々畑は、「太陽の光」「石垣の照り返し」「海からの照り返し」の”３つの太陽”の恵みを最大限に吸収できる最高の立地。',
                            'ウシジマ青果のこだわりは”自然農法”。農薬や化学肥料には極力頼らず、水分のバランス調整や高度な選定技術を駆使し、より自然に近い環境で樹に適度なストレスを与えることでみかん本来のおいしさを引き出します。',
                        ],
                        'sub_title' => '40年間、みかんと向き合い続けてきた<br>”職人のみかん”をお届け',
                        'secondary_image' => [
                            'src' => 'cut-mikan.png',
                            'alt' => '切ったみかん',
                        ],
                        'secondary_text_blocks' => [
                            '収穫したみかんは自社光センサーで厳しく選定。甘いだけでなくコクのある、糖酸バランスの良い美味しいみかんだけを出荷します。',
                            'みかんだけではなく、冬から春にかけてはポンカンや不知火など、その時一番美味しい柑橘をお届けします。',
                            'セレクションが惚れ込んだ”職人の味”を是非ご堪能ください。',
                        ],
                        'gallery' => [
                            ['src' => 'mikan-field.png', 'alt' => '熊本県河内地方のみかん畑'],
                            ['src' => 'mikan-tree.png', 'alt' => '木に実るみかん'],
                            ['src' => 'mikan-packing.png', 'alt' => 'みかんの選果と梱包作業'],
                        ],
                        'gallery_variant' => 'three',
                        'decorations' => [
                            [
                                'key' => 'mikan',
                                'src' => 'svg/mikan.svg',
                                'alt' => '',
                                'placement' => 'main_image',
                            ],
                            [
                                'key' => 'mikan-harvest',
                                'src' => 'svg/mikan-harvest.svg',
                                'alt' => '',
                                'placement' => 'main_image',
                            ],
                            [
                                'key' => 'mikan-farmer',
                                'src' => 'svg/mikan-farmer.svg',
                                'src_pc' => 'svg/mikan-farmer-pc.svg',
                                'alt' => '',
                                'placement' => 'gallery_after',
                            ],
                        ],
                    ],
                ],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'vegetables',
                'title' => 'お野菜・果物のこだわりを<br>もっと見る',
                'archive_label' => 'お野菜・果物のこだわり一覧',
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
                'wrap_decorations' => [
                    [
                        'key' => 'cabbage',
                        'src' => 'svg/cabbage.svg',
                        'alt' => '',
                    ],
                    [
                        'key' => 'apple',
                        'src' => 'svg/apple.svg',
                        'alt' => '',
                    ],
                ],
            ],
        ],
        'foods' => [
            'page_slug' => 'select-foods',
            'modifier' => 'foods',
            'title' => '加工食品のこだわり',
            'asset_base' => 'img/page/page-select-foods',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '料理の味を支える、なくてはならない存在。<br>原材料や製法にこだわった商品を中心に、<br>毎日の料理が楽しくなる食品を厳選しています。',
                'image' => '加工.svg',
                'image_alt' => '加工食品をつくる現場',
            ],
            'message' => [
                'bubble_sp' => '吹き出しsp.svg',
                'bubble_pc' => '吹き出しpc.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'corner_decoration' => 'スプーン.svg',
                'text' => '料理の決め手になる調味料から、<br>コーヒーなどの嗜好品まで。<br>セレクションの加工食品は、<br>お客様の食卓が「ちゃんとおいしく整う」品ぞろえを大切にしています。<br>世界の一流メーカーの定番はもちろん、<br>日本の小さな町で丁寧につくられた<br>こだわりの商品まで、<br>使う場面が想像できるものを<br>選び抜きました。<br>毎日の料理がラクになる“基本の一本”、<br>いつもの味が変わる“ひとさじ”、<br>手土産にもなる“ちょっと良いもの”。<br>定番は切らさず、<br>季節や話題で新しい出会いもつくる。<br>迷ったら、料理や好みに合わせて<br>おすすめします。',
                'text_pc' => '料理の決め手になる調味料から、コーヒーなどの嗜好品まで。<br>セレクションの加工食品は、<br>お客様の食卓が「ちゃんとおいしく整う」品ぞろえを大切にしています。<br>世界の一流メーカーの定番はもちろん、<br>日本の小さな町で丁寧につくられた<br>こだわりの商品まで、使う場面が想像できるものを選び抜きました。<br>毎日の料理がラクになる“基本の一本”、<br>いつもの味が変わる“ひとさじ”、<br>手土産にもなる“ちょっと良いもの”。<br>定番は切らさず、季節や話題で新しい出会いもつくる。<br>迷ったら、料理や好みに合わせておすすめします。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'sections' => [
                    [
                        'location' => '自家焙煎珈琲専門店',
                        'title' => 'イデカフェ',
                        'main_image' => ['src' => '珈琲豆.png', 'alt' => '自家焙煎珈琲専門店 イデカフェの珈琲豆'],
                        'text_blocks' => [
                            '世界各国のコーヒー農園から、厳選を重ねて品質の高いアラビカ種の生豆を仕入れています。生産地や豆の状態を丁寧に見極めながら、確かな品質の原料を選び抜いています。',
                        ],
                        'sub_title' => '焙煎マイスターが仕上げる<br>鮮度にこだわった自家焙煎コーヒー',
                        'secondary_image' => ['src' => '撮影予定1.png', 'alt' => 'セレクションオリジナルコーヒー'],
                        'secondary_text_blocks' => [
                            'コーヒーは鮮度が命！マイスター資格をもつ焙煎師が、気温や豆の状態に合わせて、最適な火入れで香り高く焙煎。パッキングまでの全行程を、自社で一括管理することで、安全で高品質なコーヒーを、新鮮な状態でお届けしています。「毎日飲むからこそ本当に美味しいコーヒーを」「１杯のコーヒーが生活を彩る名脇役であるために」そんな願いを込めた、自家焙煎専門店イデカフェのこだわりの１杯をお楽しみ下さい。',
                        ],
                        'gallery' => [
                            [
                                'src' => 'イデカフェのこだわり.png',
                                'src_sp' => 'イデカフェのこだわりsp.png',
                                'alt' => '品質、焙煎技術、鮮度にこだわるイデカフェ',
                            ],
                        ],
                        'decorations' => [
                            ['key' => 'coffee-staff', 'src' => 'スタッフ.svg', 'alt' => '', 'placement' => 'content'],
                            ['key' => 'coffee-beans', 'src' => '珈琲豆デザイン.svg', 'alt' => '', 'placement' => 'content'],
                            ['key' => 'coffee-shop', 'src' => '珈琲売店.svg', 'src_sp' => '珈琲売店sp.svg', 'alt' => '', 'placement' => 'gallery_after'],
                        ],
                    ],
                    [
                        'title' => '大分県日田<br>川津食品',
                        'main_image' => ['src' => '川津スタッフ.png', 'alt' => '川津食品のスタッフ'],
                        'text_blocks' => [
                            '川津食品は、柚子こしょうを世に広めた先駆けとして知られています。今から60数年前――「わさびのように、刺身に合う新しい薬味は作れないだろうか？」そのひとつの問いから、柚子・唐辛子・塩を絶妙に掛け合わせた、新しい薬味「柚子こしょう」が誕生しました。',
                        ],
                        'sub_title' => '柚子こしょうを広めた先駆け。<br>川津食品が守り続ける本物の味',
                        'secondary_image' => ['src' => '撮影予定2.png', 'alt' => '川津食品の柚子こしょう'],
                        'secondary_text_blocks' => [
                            '素材はシンプル。だからこそ、ごまかしはききません。香り高い柚子の風味、唐辛子の切れ味、塩のうまみ。そのすべてが調和した、凛とした味わい。',
                            '誕生当時の製法は、今もなお変わることなく受け継がれています。',
                            '時代が移り変わっても、守り続ける“昔ながらの本物の味”。それが、川津食品の柚子こしょうです。',
                        ],
                        'gallery' => [
                            ['src' => 'ゆずこしょう.png', 'alt' => '柚子こしょう'],
                            ['src' => '加工場.png', 'alt' => '柚子を加工する川津食品の工場'],
                        ],
                        'decorations' => [
                            ['key' => 'yuzu-processing', 'src' => 'ゆず加工.svg', 'alt' => '', 'placement' => 'content'],
                            ['key' => 'yuzu', 'src' => 'ゆず.svg', 'alt' => '', 'placement' => 'content'],
                            ['key' => 'yuzu-machine', 'src' => 'ゆずこしょう加工機.svg', 'alt' => '', 'placement' => 'gallery_before'],
                        ],
                    ],
                ],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'foods',
                'title' => '加工食品のこだわりを<br>もっと見る',
                'archive_label' => '加工食品のこだわり一覧',
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
                'wrap_decorations' => [
                    ['key' => 'yuzu-kosho', 'src' => '柚子胡椒@2x 2.svg', 'alt' => ''],
                ],
            ],
        ],
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
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'background_pc' => '@theme/img/page/page-select-detail/svg/message_section_bg-pc.svg',
                'text' => '毎日の使いやすさと、週末のごちそう感。<br>その両方を叶える“肉の売場”。<br>セレクションは、部位の特徴を活かした<br>カットと提案で、<br>料理がきれいに決まる売場づくりを<br>徹底しています。<br>おいしさの土台は、鮮度と素材の安心。<br>私たちは、飼育や原材料まで目を向けた<br>肉を選び、必要以上の添加物に頼らない<br>無添加ハム・ウィンナーなども<br>揃えています。<br>さらに、北総豚をはじめ<br>生産者とつながる取り組みで、<br>味わいと品質を安定して届けます。<br>そして最後は“料理目線”。<br>「炒める」「煮る」「揚げる」<br>「焼く」など<br>用途が一目で伝わるカット、<br>味付け・下ごしらえの提案まで<br>セットにして、<br>迷わず選べるように。<br>平日は時短でおいしく、<br>週末はしっかりごちそうに。<br>毎日の食卓を、お肉から支えます。',
                'text_pc' => '毎日の使いやすさと、週末のごちそう感。<br>その両方を叶える“肉の売場”。<br>セレクションは、部位の特徴を活かしたカットと提案で、<br>料理がきれいに決まる売場づくりを徹底しています。<br>おいしさの土台は、鮮度と素材の安心。<br>私たちは、飼育や原材料まで目を向けた<br>肉を選び、必要以上の添加物に頼らない<br>無添加ハム・ウィンナーなども揃えています。<br>さらに、北総豚をはじめ生産者とつながる取り組みで、<br>味わいと品質を安定して届けます。<br>そして最後は“料理目線”。<br>「炒める」「煮る」「揚げる」「焼く」など<br>用途が一目で伝わるカット、<br>味付け・下ごしらえの提案までセットにして、迷わず選べるように。<br>平日は時短でおいしく、週末はしっかりごちそうに。<br>毎日の食卓を、お肉から支えます。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
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
						'gallery_variant' => 'two',
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
						'gallery_variant' => 'two',
                        'decorations' => [
                            ['key' => 'decoration3', 'src' => 'svg/meat_about_deco3.svg', 'alt' => 'お肉のこだわり', 'placement' => 'content'],
                            ['key' => 'decoration4', 'src' => 'svg/meat_about_deco4.svg', 'alt' => 'お肉のこだわり', 'placement' => 'gallery_before'],
                            ['key' => 'kamimura-cow', 'src' => 'svg/kamimura-cow.svg', 'alt' => '', 'placement' => 'gallery_before'],
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
                'title' => 'お肉のこだわりを<br>もっと見る',
                'archive_label' => 'お肉のこだわり一覧はこちら',
                'decoration' => 'svg/meat_about_deco6.svg',
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
            ],
        ],
        'rice' => [
            'page_slug' => 'select-rice',
            'modifier' => 'rice',
            'title' => 'お米のこだわり',
            'asset_base' => 'img/page/page-select-rice',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '毎日食べるものだからこそ、<br>妥協しない美味しさを。<br>産地や品種、精米状態までこだわり、<br>ご家庭の食卓に合うお米を厳選しています。',
                'image' => 'svg/rice-planting.svg',
                'image_alt' => '田植えをする生産者の様子',
            ],
            'message' => [
                'bubble_sp' => 'svg/buyer-message-sp.svg',
                'bubble_pc' => 'svg/buyer-message.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'corner_decoration' => 'svg/swallows.svg',
                'background_pc' => '@theme/img/page/page-select-detail/svg/message_section_bg-pc.svg',
                'text' => '日本の食卓の主役だから、味で選ぶ。<br>産地と品種、精米の状態までこだわり、<br>ご家庭の食卓に<br>“いちばん合う一杯”を揃えました。<br>粒立ち、香り、甘み、粘り――<br>同じ銘柄でも年産や精米具合で<br>食味は変わります。<br>セレクションは<br>「炊き上がりの満足感」を基準に、<br>毎日のごはんがちゃんとおいしくなる<br>お米だけを選び抜きます。<br>千葉は米どころ。その強みを活かし、<br>生産者直送米を中心に、<br>全国の銘柄米まで幅広く厳選。<br>つくり手のこだわりが見えるお米、<br>食味が安定したお米を軸に、<br>日々の食卓から“ここぞ”のごはんまで<br>対応できる品揃えにしています。<br>さらに、入荷後の品質にも<br>手を抜きません。<br>保管環境や鮮度に目を配り、<br>「いつ炊いてもおいしい」状態で<br>お渡しすることを大切にしています。<br>白米としての旨みはもちろん、<br>カレー・丼・おにぎり・お弁当など、<br>料理に合わせた選び方もご案内します。<br>セレクションが選ぶ「食味」の違いを、<br>ぜひお召し上がりください。',
                'text_pc' => '日本の食卓の主役だから、味で選ぶ。<br>産地と品種、精米の状態までこだわり、ご家庭の食卓に<br>“いちばん合う一杯”を揃えました。<br>粒立ち、香り、甘み、粘り――<br>同じ銘柄でも年産や精米具合で食味は変わります。<br>セレクションは<br>「炊き上がりの満足感」を基準に、毎日のごはんがちゃんとおいしくなる<br>お米だけを選び抜きます。<br>千葉は米どころ。その強みを活かし、生産者直送米を中心に、<br>全国の銘柄米まで幅広く厳選。<br>つくり手のこだわりが見えるお米、食味が安定したお米を軸に、<br>日々の食卓から“ここぞ”のごはんまで対応できる品揃えにしています。<br>さらに、入荷後の品質にも手を抜きません。<br>保管環境や鮮度に目を配り、「いつ炊いてもおいしい」状態で<br>お渡しすることを大切にしています。<br>白米としての旨みはもちろん、カレー・丼・おにぎり・お弁当など、<br>料理に合わせた選び方もご案内します。<br>セレクションが選ぶ「食味」の違いを、<br>ぜひお召し上がりください。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'sections' => [
                    [
                        'location' => '千葉県山武市',
                        'title' => '房の黄金米 米のたけやま',
                        'main_image' => ['src' => 'rice-cooked.png', 'alt' => '炊きたての房の黄金米'],
                        'text_blocks' => [
                            '「自然の力で、自然のままに」たけやまの圃場を訪れた方は、一帯に草が生い茂る水田の様子に驚くかもしれません。たけやまの水田からは、生育ムラがなく健康な稲が育ち、水が循環するため、稲が雑草の影響を受けることもありません。水田の除草も最低限に抑え、切磋琢磨するいのちの営みのなかでこそ、お米本来の美味しさが引き出されると考えるからです。',
                            'こうした取り組みが、安心・安全なお米づくりの基礎を支えています。',
                        ],
                        'secondary_image' => ['src' => 'rice-farming.png', 'alt' => '稲の生育を見守る生産者'],
                        'secondary_text_blocks' => [
                            'たけやまが誇る「房の黄金米」をはじめ、ふさこがね・ふさおとめ・こしひかりなど、多彩な品種を栽培し、それぞれの特長である甘み・旨み・もっちり食感を引き出す工程に徹底的にこだわっています。',
                        ],
                        'gallery' => [
                            ['src' => 'tractor.png', 'alt' => '田植えを行うトラクター'],
                            ['src' => 'rice-grains.png', 'alt' => '収穫されたお米'],
                            ['src' => 'rice-product.png', 'alt' => '房の黄金米の商品'],
                            ['src' => 'award.png', 'alt' => '受賞した米のたけやまの生産者'],
                        ],
                        'gallery_variant' => 'four',
                        'decorations' => [
                            ['key' => 'rice-and-farmer', 'src' => 'svg/rice-and-farmer.svg', 'alt' => '', 'placement' => 'content'],
                            ['key' => 'rice-plant', 'src' => 'svg/rice-plant.svg', 'alt' => '', 'placement' => 'content'],
                        ],
                    ],
                    [
                        'location' => '新潟県南魚沼市',
                        'title' => '細矢農園',
                        'main_image' => ['src' => 'koshihikari.png', 'alt' => '細矢農園のコシヒカリ'],
                        'text_blocks' => [
                            '魚沼地区の中でも、しおざわ地区は、米づくりに適した自然環境・気候に恵まれています。稲の実りの時期は、昼間は暑く、夜は肌寒いといったように、昼夜の寒暖の差がお米に美味しさを生み出しています。',
                        ],
                        'sub_title' => '実りの時期の寒暖差によって雑味のない甘いお米…',
                        'secondary_image' => ['src' => 'rice-field.png', 'alt' => '南魚沼市の田んぼ'],
                        'secondary_text_blocks' => [
                            '産地は、米どころ魚沼しおざわ産100％ですので、お米と同様に恵まれた自然環境の中、おいしいもち米が、栽培できます。栽培方法も農薬・化学肥料をできるだけ使わない減農薬栽培（県認証）です。製造過程でも無添加で製造していますので安心してお召し上がりいただけます。こしひかりは、もとから甘み、粘りと旨みの三拍子そろったお米ですが、細矢農園さんのお米はさらに豊かな香りも感じることができ、美味しさがぎゅっと詰まっています。',
                        ],
                        'gallery' => [
                            ['src' => 'cultivation.png', 'alt' => '実った稲穂'],
                            ['src' => 'farmer.png', 'alt' => '田んぼで作業する生産者'],
                        ],
						'gallery_variant' => 'two',
                        'decorations' => [
                            ['key' => 'rice-bales-and-farmer', 'src' => 'svg/rice-bales-and-farmer.svg', 'alt' => '', 'placement' => 'main_image'],
                            ['key' => 'sun-drying', 'src' => 'svg/sun-drying.svg', 'alt' => '', 'placement' => 'secondary_image'],
                        ],
                    ],
                ],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'rice',
                'title' => 'お米のこだわりを<br>もっと見る',
                'archive_label' => 'お米のこだわり一覧',
                'wrap_decorations' => [
                    ['key' => 'rice-design', 'src' => 'svg/rice-design.svg', 'alt' => ''],
                ],
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
            ],
        ],
        'deli' => [
            'page_slug' => 'select-deli',
            'modifier' => 'deli',
            'title' => 'お惣菜のこだわり',
            'asset_base' => 'img/page/page-select-deli',
            'theme' => [
                'message_background' => '#fff8df',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '忙しい日にも、ほっとできる美味しさを。<br>店内調理や素材選びにこだわり、<br>手づくり感と安心感を大切にした<br>お惣菜をご用意しています。',
                'image' => 'svg/deli-counter.svg',
                'image_alt' => 'お惣菜売場の様子',
            ],
            'message' => [
                'bubble_sp' => 'svg/buyer-message-balloon-sp.svg',
                'bubble_pc' => 'svg/buyer-message-balloon-pc.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'text' => 'セレクションの惣菜は、<br>店内のこだわり食材を活かした<br>「自慢の惣菜」を中心に、<br>素材と味に自信のある<br>ラインアップを揃えています。<br>毎日の食卓にもう一品、<br>忙しい日でもちゃんとおいしい――<br>そんな場面で頼れる惣菜売場を<br>目指しています。<br>素材の旬や季節行事、<br>行楽などの生活シーンに<br>合わせて売場の提案を更新し、<br>主菜・副菜の組み合わせまで<br>選びやすく整えています。<br>迷ったら、今日のおすすめを気軽に<br>聞いてください。',
                'text_pc' => 'セレクションの惣菜は、<br>店内のこだわり食材を活かした「自慢の惣菜」を中心に、<br>素材と味に自信のあるラインアップを揃えています。<br>毎日の食卓にもう一品、<br>忙しい日でもちゃんとおいしい――<br>そんな場面で頼れる惣菜売場を目指しています。<br>素材の旬や季節行事、行楽などの生活シーンに合わせて売場の提案を更新し、<br>主菜・副菜の組み合わせまで選びやすく整えています。<br>迷ったら、今日のおすすめを気軽に聞いてください。',
                'corner_decoration' => 'svg/deli-cookware.svg',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'sections' => [[
                    'title' => '店内で生地から。焼きたてを、<br class="u-deli-title-break-sp-tab">そのまま食卓へ<br>セレクションオリジナルピザ',
                    'main_image' => ['src' => '@theme/img/page/page-select-rice/rice-cooked.png', 'alt' => '炊きたての房の黄金米'],
                    'text_blocks' => [
                        '店内で生地から仕込み、毎日焼き上げるセレクションオリジナルピザ。',
                        '一枚ずつ直径30cmになるように伸ばし、400℃のピザ窯で高温・短時間で焼き上げることで、香ばしい焼き目と中はもちもちのナポリ風食感に仕上げています。種類は10種類以上。定番から気分で選べるラインアップで、家族の食卓にも週末のごちそうにもぴったりです。',
                    ],
                    'gallery' => [
                        ['src' => '@theme/img/page/page-select-rice/tractor.png', 'alt' => '田植えを行うトラクター'],
                        ['src' => '@theme/img/page/page-select-rice/rice-grains.png', 'alt' => '収穫されたお米'],
                        ['src' => '@theme/img/page/page-select-rice/rice-product.png', 'alt' => '房の黄金米の商品'],
                        ['src' => '@theme/img/page/page-select-rice/award.png', 'alt' => '受賞した米のたけやまの生産者'],
                    ],
                    'gallery_variant' => 'four',
                    'decorations' => [
                        [
                            'key' => 'staff',
                            'src' => 'svg/deli-staff.svg',
                            'alt' => '',
                            'placement' => 'content',
                        ],
                        [
                            'key' => 'bread-making',
                            'src' => 'svg/deli-bread-making.svg',
                            'alt' => '',
                            'placement' => 'gallery_before',
                        ],
                    ],
                ]],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'deli',
                'title' => 'お惣菜のこだわりを<br>もっと見る',
                'archive_label' => 'お惣菜のこだわり一覧',
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
                'wrap_decorations' => [
                    ['key' => 'karaage', 'src' => 'svg/deli-karaage.svg', 'alt' => ''],
                    ['key' => 'nikujaga', 'src' => 'svg/deli-nikujaga.svg', 'alt' => ''],
                ],
            ],
        ],
        'washoku-daily' => [
            'page_slug' => 'select-washoku-daily',
            'modifier' => 'washoku-daily',
            'title' => '和日配のこだわり',
            'asset_base' => 'img/page/page-select-washoku-daily',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '毎日使うものだからこそ、品質と使いやすさを大切に。<br>日々の暮らしに寄り添う、<br>安心して選び続けられる商品を取り揃えています。',
                'lead_pc' => '毎日使うものだからこそ、品質と使いやすさを大切に。<br>日々の暮らしに寄り添う、<br>安心して選び続けられる商品を取り揃えています。',
                'image' => 'svg/product-sorting.svg',
                'image_alt' => '和日配の商品を仕分けする様子',
            ],
            'message' => [
                'bubble_sp' => 'svg/buyer-message-balloon-sp.svg',
                'bubble_pc' => 'svg/buyer-message-balloon-pc.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'text' => '毎日の食卓に、ちゃんと“本物”を。<br>セレクションの和日配は、<br>素材とつくり手で<br>選び抜き、定番まで抜かりなく<br>揃える売場です。<br>梅干しは、<br>紀州・和歌山県田辺の産直梅干しを軸に、<br>塩・紫蘇・熟成まで<br>「味の芯」があるものを。<br>納豆は、昔ながらの<br>手作り製法にこだわり、<br>大豆の産地や品種、蒸し加減、<br>発酵の温度管理まで――<br>原料と造りかたが語れるものを<br>厳選しています。<br>さらに、毎日手に取るものほど<br>差が出るから。<br>豆腐・油揚げ・こんにゃく・漬物なども、<br>いつもの料理が一段おいしくなる<br>基準で揃えています。<br>もちろん、日々の食卓で活躍する<br>定番もしっかり。<br>忙しい日も、いつもの朝も、<br>「これがあれば安心」という<br>使いやすさと、<br>毎日食べても飽きないおいしさを<br>大切にしています。<br>セレクションの和日配で、<br>“違いがわかるおいしさ”をぜひ。',
                'text_pc' => '毎日の食卓に、ちゃんと“本物”を。<br>セレクションの和日配は、素材とつくり手で<br>選び抜き、定番まで抜かりなく揃える売場です。<br>梅干しは、紀州・和歌山県田辺の産直梅干しを軸に、<br>塩・紫蘇・熟成まで「味の芯」があるものを。<br>納豆は、昔ながらの手作り製法にこだわり、<br>大豆の産地や品種、蒸し加減、発酵の温度管理まで――<br>原料と造りかたが語れるものを厳選しています。<br>さらに、毎日手に取るものほど差が出るから。<br>豆腐・油揚げ・こんにゃく・漬物なども、<br>いつもの料理が一段おいしくなる基準で揃えています。<br>もちろん、日々の食卓で活躍する定番もしっかり。<br>忙しい日も、いつもの朝も、「これがあれば安心」という<br>使いやすさと、毎日食べても飽きないおいしさを大切にしています。<br>セレクションの和日配で、“違いがわかるおいしさ”をぜひ。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'sections' => [[
                    'title' => '世界が認めた、梅の郷に息づく伝統。<br>濱田農園 株式会社濱田',
                    'main_image' => ['src' => 'farmer.png', 'alt' => '梅農園の生産者'],
                    'text_blocks' => [
                        '江戸の昔からつづく、梅の郷・和歌山県田辺市石神。株式会社濱田の自社農園も、その一角に位置しています。',
                        '標高250mの土地は痩せ地でありながらも日照時間が長く、海から吹く黒潮の風が多くのミネラルを運んできてくれるので、梅の栽培に適しています。独自の栽培方法である「香壌栽培」を確立させ、完熟梅だけを梅干し、梅酒造りに使用しています。',
                        '自社農園を構え、土作りからの製品作りまで管理しつつ、品質を高いレベルで安定させる。これが濱田のこだわりです。',
                    ],
                    'sub_title' => '自然の恵みと人の技が融合した、<br>最高の梅干を作るための農園。',
                    'secondary_image' => ['src' => 'plums.png', 'alt' => '農園で実る梅', 'background_decoration' => true],
                    'secondary_text_blocks' => ['梅を通じた「おいしさ」と「楽しさ」を提供しつづけます。'],
                    'gallery' => [
                        ['src' => 'umeboshi.png', 'alt' => '濱田農園の梅干し'],
                        ['src' => 'farmers.png', 'alt' => '梅を収穫する農家の皆さん'],
                    ],
                    'gallery_variant' => 'two',
                    'decorations' => [],
                ]],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'washoku-daily',
                'title' => '和日配のこだわりを<br>もっと見る',
                'title_pc' => '和日配のこだわり<br>をもっと見る',
                'archive_label' => '和日配のこだわり一覧',
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
            ],
        ],
        'sweets' => [
            'page_slug' => 'select-sweets',
            'modifier' => 'sweets',
            'title' => 'お菓子のこだわり',
            'asset_base' => 'img/page/page-select-sweets',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => 'ちょっとしたご褒美や、家族の団らんの時間に。<br>定番から話題の商品まで、選ぶ楽しさと美味しさを感じられる<br>お菓子を揃えています。',
                'image' => '製造工場.svg',
                'image_alt' => 'お菓子の製造工場',
            ],
            'message' => [
                'bubble_sp' => 'buyer-message-balloon-sp.svg',
                'bubble_pc' => 'buyer-message-balloon-pc.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'text' => 'お菓子は、暮らしの気分を整える“<br>毎日のごほうび”。<br>だから、選び方に理由がある。<br>セレクションの菓子売場は、<br>おいしさの納得感と選びやすさを<br>大切にしています。<br>家族の団らん、仕事の合間、<br>お茶の時間、手土産――<br>日常のシーンに寄り添いながら、<br>定番からこだわりまで<br>“ちゃんと選べる”品揃えを整えました。<br>せんべいは、米の旨みと<br>焼きの香ばしさ、食感まで。<br>素材や製法にこだわった<br>“ちゃんと旨い”一枚を中心に、<br>堅焼・醤油・海苔・甘辛など、<br>好みに合わせて選べるラインアップに。<br>地域の銘菓は、<br>その土地の個性が感じられる味わいを。<br>自宅用はもちろん、<br>ちょっとした手土産にも<br>「間違いない」品を揃えています。<br>お子さま向けは、<br>体にやさしい視点を大切に。<br>素材選びや甘さの設計、<br>食べやすさに配慮した<br>お菓子も充実させ、<br>毎日でも手に取りやすい<br>コーナーに仕上げました。<br>そしてチョコレートは、世界の厳選品を。<br>産地やカカオの個性、<br>香りと余韻まで楽しめる<br>“ごほうびの一粒”から、<br>毎日の一枚まで幅広く。<br>季節のイベントや贈り物にも、<br>気分で選べる楽しさを揃えています。<br>定番で整える日も、少し贅沢したい日も。<br>セレクションのお菓子で、<br>“選ぶ時間まで楽しい”ひとときをどうぞ。',
                'text_pc' => 'お菓子は、暮らしの気分を整える“毎日のごほうび”。<br>だから、選び方に理由がある。<br>セレクションの菓子売場は、おいしさの納得感と選びやすさを大切にしています。<br>家族の団らん、仕事の合間、<br>お茶の時間、手土産――日常のシーンに寄り添いながら、<br>定番からこだわりまで“ちゃんと選べる”品揃えを整えました。<br>せんべいは、米の旨みと焼きの香ばしさ、食感まで。<br>素材や製法にこだわった“ちゃんと旨い”一枚を中心に、<br>堅焼・醤油・海苔・甘辛など、好みに合わせて選べるラインアップに。<br>地域の銘菓は、その土地の個性が感じられる味わいを。<br>自宅用はもちろん、ちょっとした手土産にも「間違いない」品を揃えています。<br>お子さま向けは、体にやさしい視点を大切に。<br>素材選びや甘さの設計、食べやすさに配慮した<br>お菓子も充実させ、毎日でも手に取りやすい<br>コーナーに仕上げました。<br>そしてチョコレートは、世界の厳選品を。<br>産地やカカオの個性、香りと余韻まで楽しめる<br>“ごほうびの一粒”から、毎日の一枚まで幅広く。<br>季節のイベントや贈り物にも、気分で選べる楽しさを揃えています。<br>定番で整える日も、少し贅沢したい日も。<br>セレクションのお菓子で、“選ぶ時間まで楽しい”ひとときをどうぞ。',
                'foreground_decorations' => [
                    ['key' => 'potato', 'src' => 'ジャガイモ.svg', 'alt' => ''],
                    ['key' => 'cacao', 'src' => 'カカオ.svg', 'alt' => ''],
                ],
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'sections' => [
                    [
                        'location' => '福井県の日本でも数少ない<br>チョコレート専門メーカー',
                        'location_pc' => '福井県の日本でも数少ないチョコレート専門メーカー',
                        'title' => '横井チョコレート',
                        'main_image' => ['src' => '横井チョコレート.png', 'alt' => '横井チョコレートの商品'],
                        'text_blocks' => ['福井県の横井チョコレートは添加物はなるべく使用しないなど身体に良い素材を厳選し、誰もが安心して美味しく食べられるチョコレート作りを目指しています。'],
                        'sub_title' => '上質な”クーベルチュールチョコレート”を使用',
                        'secondary_image' => ['src' => 'クーベルチュールチョコレート.png', 'alt' => 'クーベルチュールチョコレートの商品'],
                        'secondary_text_blocks' => ['横井チョコレートで主に使用するのは、総カカオ分35％以上、ココアバター31%以上といった厳しい国際基準を満たす「クーベルチュールチョコレート」。産地もガーナ産に指定した特注品です。またアーモンドは使用するたびに焙煎するなど、すべての素材に手抜きなし。「日本一の材料で」と、素材にはこだわり尽くしています。'],
                        'gallery' => [
                            ['src' => '販売.png', 'alt' => 'チョコレートの販売風景'],
                            ['src' => 'チョコとパン.png', 'alt' => 'チョコレートとパン'],
                        ],
                        'gallery_variant' => 'two',
                        'decorations' => [
                            ['key' => 'cacao-beans', 'src' => 'カカオ豆.svg', 'alt' => '', 'placement' => 'main_image'],
                            ['key' => 'patissier', 'src' => 'パティシエ.svg', 'alt' => '', 'placement' => 'main_image'],
                        ],
                    ],
                    [
						'location' => '安心な北海道の原料に<br>こだわったお菓子作り',
						'location_pc' => '安心な北海道の原料にこだわったお菓子作り',
						'title' => 'ノースカラーズ',
                        'main_image' => ['src' => 'ノースカラーズ.png', 'alt' => 'ノースカラーズの北海道の風景'],
                        'text_blocks' => ['純国産シリーズは化学添加物や合成着色料を使わず、厳選された自然素材を使用。素材の美味しさと自然の魅力を最大限引き出す商品づくり。特に北海道産の原材料を活かし、子どもから大人まで安心して食べられるお菓子で、健康と笑顔を届けることを目指しています。'],
                        'sub_title' => '北海道の恵みと自然素材から生まれる、やさしくて安心なお菓子。',
                        'gallery' => [
                            [
                                'src' => 'ノースカラーズの思い.png',
                                'alt' => 'ノースカラーズの想い',
                                'modifier' => 'north-colors-thought',
                            ],
                        ],
                        'decorations' => [
                            ['key' => 'cook', 'src' => 'コック.svg', 'alt' => '', 'placement' => 'main_image'],
                            ['key' => 'yuzu', 'src' => 'ゆず.svg', 'alt' => '', 'placement' => 'main_image'],
                        ],
                    ],
                ],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'snacks',
                'title' => 'お菓子のこだわりを<br>もっと見る',
                'archive_label' => 'お菓子のこだわり一覧',
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
                'foreground_decorations' => [
                    ['key' => 'baumkuchen', 'src' => 'バウムクーヘン@2x 2.svg', 'alt' => ''],
                    ['key' => 'forklift', 'src' => 'フォークリフト.svg', 'alt' => '', 'wrap_anchor_only' => true],
                ],
            ],
        ],
        'alcohol' => [
            'page_slug' => 'select-alcohol',
            'modifier' => 'alcohol',
            'title' => 'お酒のこだわり',
            'asset_base' => 'img/page/page-select-alcohol',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '日常の一杯から特別な日の乾杯まで。<br>味わい・造り手・料理との相性に目を向け、<br>シーンに合わせて選べるお酒をご提案しています。',
                'image' => 'お酒造り.svg',
                'image_alt' => '酒蔵でお酒を造る職人たち',
            ],
            'message' => [
                'bubble_sp' => '吹き出しsp.svg',
                'bubble_pc' => '吹き出しpc.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'text' => '蔵元とつながる。<br>だから、酒がまっすぐ旨い。<br>セレクションの酒売場は、スーパーマーケットでは珍しい「蔵元特約店」。<br>惚れ込んだ地酒蔵と正規に取り組み、清酒・焼酎を正規価格でお届けします。<br>“安さで選ぶ酒”ではなく、“旨い理由がある酒”を並べる。<br>だから、品揃えが他店と違う。<br>ワインも同じ。<br>フランスの生産者・蔵元直送を軸に、世界中から料理で映える一本を厳選。<br>肉・魚・チーズ・惣菜――<br>食材が揃うセレクションだからこそ、「今日の献立に合うワイン」が選べます。<br>さらに、ウイスキー、ジンなどのスピリッツも“飲み方まで楽しくなる”ラインアップで。<br>毎日の一杯から、週末のごほうびまで。<br>選ぶ時間ごと、うまい酒をどうぞ。',
                'text_pc' => '蔵元とつながる。<br>だから、酒がまっすぐ旨い。<br>セレクションの酒売場は、スーパーマーケットでは珍しい「蔵元特約店」。<br>惚れ込んだ地酒蔵と正規に取り組み、清酒・焼酎を正規価格でお届けします。<br>“安さで選ぶ酒”ではなく、“旨い理由がある酒”を並べる。<br>だから、品揃えが他店と違う。<br>ワインも同じ。<br>フランスの生産者・蔵元直送を軸に、世界中から料理で映える一本を厳選。<br>肉・魚・チーズ・惣菜――<br>食材が揃うセレクションだからこそ、「今日の献立に合うワイン」が選べます。<br>さらに、ウイスキー、ジンなどの<br>スピリッツも“飲み方まで楽しくなる”ラインアップで。<br>毎日の一杯から、週末のごほうびまで。<br>選ぶ時間ごと、うまい酒をどうぞ。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'sections' => [
                    [
                        'location' => '特約焼酎蔵元',
                        'title' => '田村合名会社',
                        'main_image' => ['src' => '焼酎蔵元.png', 'alt' => '田村合名会社の焼酎蔵'],
                        'text_blocks' => [
                            '鹿児島県指宿市山川。薩摩焼酎を造り続ける田村合名会社は、昔ながらの甕仕込みで丁寧に焼酎を育てています。手仕事を大切にしながら、芋の個性をまっすぐ引き出します。',
                        ],
                        'sub_title' => '明治創業　百年以上の歴史が育てる、<br>自然の手造り芋焼酎「純黒」。',
                        'secondary_image' => ['src' => '純黒.png', 'alt' => '開聞岳とさつま芋畑、純黒をはじめとする焼酎'],
                        'secondary_text_blocks' => [
                            '代表銘柄「純黒」は、まろやかでコクがあり、料理を引き立てる味わい。土地の恵みと蔵人の技が息づく一本を、ぜひお楽しみください。',
                        ],
                        'gallery' => [],
                    ],
                    [
                        'location' => 'フランス直輸入の自然派ワイナリー',
                        'title' => 'ドメーヌ・ド・パスキエ',
                        'main_image' => ['src' => 'ブドウ園.png', 'alt' => 'ドメーヌ・ド・パスキエのブドウ園'],
                        'text_blocks' => [
                            'フランスの豊かな土地で育ったぶどうを使い、自然な味わいを大切にしたワイン造りを続けています。料理に寄り添い、日々の食卓を豊かにするワインです。',
                        ],
                        'sub_title' => '1935年創業。守りと攻めのワイン村で育まれる、<br>濃くて穏やかな南仏ローヌのワイン。',
                        'secondary_image' => ['src' => 'スイーツとワイン.png', 'alt' => 'チョコレートのスイーツと赤ワイン'],
                        'secondary_text_blocks' => [
                            '上品な果実味とやわらかな余韻。料理はもちろん、デザートとのペアリングにもおすすめです。セレクションでは、食卓のシーンまで思い浮かべながら一本一本を選んでいます。',
                        ],
                        'gallery' => [
                            ['src' => '試飲準備.png', 'alt' => 'ワインの試飲準備'],
                            ['src' => '注がれるワイン.png', 'alt' => 'グラスに注がれる赤ワイン'],
                        ],
                        'gallery_variant' => 'two',
                    ],
                ],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'alcohol',
                'title' => 'こだわりのお酒を<br>もっと見る',
                'archive_label' => 'お酒のこだわり一覧',
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
            ],
        ],
        'fish' => [
            'page_slug' => 'select-fish',
            'modifier' => 'fish',
            'title' => 'お魚のこだわり',
            'asset_base' => 'img/page/page-select-fish',
            'theme' => [
                'message_background' => '#e7f9f5',
            ],
            'breadcrumb' => [
                'parent_label' => 'セレクションのこだわり',
                'parent_url' => '/select/',
            ],
            'hero' => [
                'lead' => '旬や鮮度はもちろん、<br>仕入れから売り場に並ぶまでの<br>スピードを大切に。<br>その日いちばん美味しい状態で<br>お魚をお届けするための工夫を<br>積み重ねています。',
                'lead_pc' => '旬や鮮度はもちろん、仕入れから売り場に並ぶまでのスピードを大切に。<br>その日いちばん美味しい状態でお魚をお届けするための<br>工夫を積み重ねています。',
                'image' => 'svg/fishery.svg',
                'image_alt' => '漁港で新鮮な魚を仕分けする様子',
            ],
            'message' => [
                'bubble_sp' => 'svg/buyer-message-balloon-sp.svg',
                'bubble_pc' => 'svg/buyer-message-balloon-pc.svg',
                'buyer_image' => '@theme/img/page/page-select-detail/svg/img_message-buyer.svg',
                'background_pc' => '@theme/img/page/page-select-detail/svg/message_section_bg-pc.svg',
                'text' => '鮮度はもちろん、<br>産地や漁法、脂の乗りまで<br>“目で見て選ぶ”。<br>セレクションのお魚は、<br>全国の産直に加え、<br>地元・千葉県の漁港から届く鮮度の良い<br>「地魚」を中心に買付を行っています。<br>季節ごとに変わる<br>“旬のいちばん”を逃さず、<br>切身・刺身・焼魚まで、<br>今日いちばんおいしい形で<br>食卓に届けます。<br>魚は「鮮度」だけでなく、<br>今の状態に合った食べ方でおいしさが<br>決まります。<br>私たちは、脂の乗り・身質・サイズを見て<br>売場に出し、刺身、焼き、煮付けなど<br>おすすめの食べ方も一緒にご案内します。<br>売場では旬の打ち出しも強め、<br>迷わず選べるように。<br>迷ったら気軽に声をかけてください。',
                'text_pc' => '鮮度はもちろん、産地や漁法、脂の乗りまで<br>“目で見て選ぶ”。<br>セレクションのお魚は、全国の産直に加え、<br>地元・千葉県の漁港から届く鮮度の良い<br>「地魚」を中心に買付を行っています。<br>季節ごとに変わる“旬のいちばん”を逃さず、<br>切身・刺身・焼魚まで、<br>今日いちばんおいしい形で食卓に届けます。<br>魚は「鮮度」だけでなく、<br>今の状態に合った食べ方でおいしさが決まります。<br>私たちは、脂の乗り・身質・サイズを見て<br>売場に出し、刺身、焼き、煮付けなど<br>おすすめの食べ方も一緒にご案内します。<br>売場では旬の打ち出しも強め、迷わず選べるように。<br>迷ったら気軽に声をかけてください。',
            ],
            'about' => [
                'title' => 'こだわりの現場から',
                'title_background' => '@theme/img/page/page-select-detail/svg/meat_about_title.svg',
                'background' => '@theme/img/page/page-select-detail/brick.png',
                'wave_sp_top' => '@theme/img/page/page-select-detail/svg/upper_wave.svg',
                'wave_sp_bottom' => '@theme/img/page/page-select-detail/svg/about-wave-bottom-sp.svg',
                'wave_pc_top' => '@theme/img/page/page-select-detail/svg/meat_about_wave-top-pc.svg',
                'wave_pc_bottom' => '@theme/img/page/page-select-detail/svg/meat_about_wave-bottom-pc.svg',
                'decorations' => [],
                'background_decorations' => [
                    ['key' => 'wave', 'src' => 'svg/wave.svg', 'alt' => ''],
                    ['key' => 'fisherman', 'src' => 'svg/fisherman.svg', 'alt' => ''],
                    ['key' => 'cat-and-worker', 'src' => 'svg/cat-and-worker.svg', 'alt' => ''],
                    ['key' => 'calico-cat', 'src' => 'svg/cat.svg', 'alt' => 'ぶち猫'],
                    ['key' => 'worker', 'src' => 'svg/worker.svg', 'alt' => ''],
                    ['key' => 'worker-and-cat-2', 'src' => 'svg/worker-and-cat-2.svg', 'alt' => '', 'anchored_only' => true],
                ],
                'sections' => [
                    [
                        'location' => '高知県宿毛市',
                        'title' => '<small>株式会社</small> 勇進',
                        'main_image' => ['src' => 'field-yellowtail-fisher.png', 'alt' => '高知県宿毛市でブリを育てる生産者'],
                        'text_blocks' => [
                            '高知県の西の端に位置する宿毛湾。雄大な自然に囲まれた豊かな海域で、丹精込めて育てた自慢の逸品。それが「荒木さん家のブリ」。海とともに生きる荒木さんたちが、真心を込めてお届けします。',
                        ],
                        'sub_title' => '愛情をかけて育てる！自慢の逸品<br>「荒木さん家のブリ」',
                        'secondary_image' => [
                            'src' => 'field-yellowtail-sashimi.png',
                            'alt' => '荒木さん家のブリの刺身',
                            'decoration_anchors' => ['worker-and-cat-2'],
                        ],
                        'secondary_text_blocks' => [
                            '「荒木さん家のブリ」は名前の通り、1軒の生産者（荒木さん）が養殖から加工、販売まで一貫して行っているのですが、その全てにこだわりを持っています。',
                            '宿毛湾は黒潮と豊後水道が交わり、栄養豊富な松田川が流れ込む肥沃な海域。まさに日本有数の飼育環境です。尚且つ、恵まれた海域で大型いけすを使っています。潮通しの環境が良く、水深50mの海で頻繁に餌をあげられるので成長が早く、ゴンゴン泳ぐので身も引き締まっています。',
                            '餌はカタクチイワシなどの良質な魚粉にきびしぼりを配合したオリジナル飼料を使用。この飼料を用いることでブリ本来の旨味成分が増し、刺身で、焼いても、煮ても「美味しいブリ」が育ちます。',
                        ],
                        'gallery' => [
                            ['src' => 'field-yellowtail-farm.png', 'alt' => 'ブリの養殖場'],
                            ['src' => 'field-yellowtail-processing.png', 'alt' => '水産加工の様子'],
                            [
                                'src' => 'field-yellowtail-packing.png',
                                'alt' => 'ブリを包装する様子',
                                'decoration_anchors' => ['cat-and-worker', 'calico-cat'],
                            ],
                        ],
                        'gallery_variant' => 'three',
                        'decorations' => [],
                    ],
                    [
                        'location' => '千葉県鴨川市',
                        'title' => '島津商店 千産千消「房州ひじき」',
                        'main_image' => [
                            'src' => 'field-hijiki-processing.png',
                            'alt' => '房州ひじきを加工する様子',
                        ],
                        'text_blocks' => [
                            '嶋津商店の「房州ひじき」は千葉県南部の沿岸の磯で採れたものを、新鮮なうちに、じっくりと炊き上げてあります。ふっくらとしたやわらかさと、磯の香りをお楽しみください。',
                        ],
                        'sub_title' => '旬の時期だけに採れる上質ひじき<br>産地指定で届ける千葉の味',
                        'secondary_image' => [
                            'src' => 'field-hijiki.png',
                            'alt' => '房州ひじき',
                            'decoration_anchors' => ['worker'],
                        ],
                        'secondary_text_blocks' => [
                            'ひじきは成長が進むと芽の部分が開き柔らかくなるため、芽の閉まっている2月～3月中旬に良質のひじきが採れる勝浦地区、鴨川地区の原料を指定して入札しております。',
                            '浜で入札されたひじきはすぐに嶋津商店の加工場に運ばれ、洗浄⇒釜蒸し⇒乾燥⇒選別、異物検査の順で加工されます。原料の鮮度がよく、水揚げ後すぐにボイルされるため、ひじきの風味や味わいが格段に違います。',
                            '千葉のひじきは生産量が多いにもかかわらず、県内での消費がほとんど。お土産や道の駅などでは他県からの旅行者に大変評判がよく「ひじきだけ買いに来たわ……」という方もいらっしゃるほどです。',
                            'セレクションのひじきは水揚げからしっかり履歴の管理された安全、安心のひじき、味わい豊富なひじきです。煮物以外にも、サラダや味噌汁、てんぷらもおいしく食べ方も豊富です。ぜひご賞味ください。',
                        ],
                        'gallery' => [
                            ['src' => 'field-hijiki-drying.png', 'alt' => 'ひじきを乾燥させる様子'],
                            ['src' => 'field-hijiki-dish.png', 'alt' => 'ひじきを使った料理'],
                            ['src' => 'field-hijiki-product.png', 'alt' => '房州ひじきの商品'],
                            ['src' => 'field-hijiki-producer.png', 'alt' => '房州ひじきの生産者'],
                        ],
                        'gallery_variant' => 'four',
                        'decorations' => [],
                    ],
                ],
            ],
            'news' => [
                'taxonomy' => 'news_commitment',
                'term' => 'fish',
                'title' => 'お魚のこだわりを<br>もっと見る',
                'archive_label' => 'お魚のこだわり一覧',
                'foreground_decorations' => [
                    ['key' => 'fish', 'src' => 'svg/fish.svg', 'alt' => ''],
                ],
                'background_sp' => '@theme/img/page/page-select-detail/meat_news_bgSP.png',
                'background_pc' => '@theme/img/page/page-select-detail/meat_news_bgPC.png',
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

    if (0 === strpos($path, '@theme/')) {
        return get_template_directory_uri() . '/' . substr($path, 7);
    }

    return get_template_directory_uri() . '/' . $base . '/' . $path;
}
