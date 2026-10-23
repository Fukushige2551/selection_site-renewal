<?php
// The theme owns the approved title/description output. AIOSEO integration
// (SEO-01 and other head metadata) remains a separate configuration task.
add_filter('aioseo_disable', '__return_true');

/** Approved SEO-02–04 copy. Paths are relative to the WordPress site. */
function foods_seo_page_copy() {
    return [
        '' => ['', 'フーズマーケットセレクション公式サイト。千葉・東京・埼玉で展開する地域密着型スーパーマーケットです。チラシやお買い得情報、店舗一覧、バイヤーが全国から選び抜いたこだわりの商品をご紹介しています。'],
        'shop' => ['店舗一覧', '千葉・東京・埼玉に展開するフーズマーケットセレクションの店舗一覧です。お近くの店舗のチラシ、営業時間、所在地、アクセスなどをご確認いただけます。'],
        'news' => ['新着情報・キャンペーン', 'フーズマーケットセレクションからのお知らせをご紹介。店舗のお知らせやキャンペーン、季節の商品など、毎日のお買い物に役立つ情報をお届けします。'],
        'recipe' => ['レシピ', '毎日の献立づくりに役立つレシピをご紹介。食材やキーワードから料理を探し、材料、調理時間、作り方をご確認いただけます。'],
        'contact' => ['お問い合わせ', 'フーズマーケットセレクションへのお問い合わせはこちら。店舗や商品、サービスに関するご意見・ご質問をお問い合わせフォームからお寄せいただけます。'],
        'privacy' => ['プライバシーポリシー', '株式会社セレクションの個人情報の取り扱いについてご案内します。個人情報の利用目的、管理、第三者への提供などに関する方針をご確認いただけます。'],
        'contact/confirm' => ['お問い合わせ内容の確認', 'フーズマーケットセレクションへのお問い合わせ内容の確認ページです。ご入力いただいた内容をご確認のうえ、送信してください。'],
        'contact/thanks' => ['お問い合わせ受付完了', 'フーズマーケットセレクションへのお問い合わせを受け付けました。お問い合わせいただき、ありがとうございます。'],
        'select' => ['セレクションのこだわり', 'バイヤーが全国から選び抜いた商品と、セレクションのこだわりをご紹介。産地や生産者の思い、鮮度やおいしさを大切にする商品選びをお伝えします。'],
        'select/shop' => ['お店づくりのこだわり', 'フーズマーケットセレクションのお店づくりをご紹介。商品選びから売場づくりまで、毎日のお買い物で商品の魅力をお伝えするためのこだわりをご覧いただけます。'],
        'select/meat' => ['お肉のこだわり', 'セレクションが選ぶお肉のこだわりをご紹介。産地や生産者、育て方に目を向け、鮮度・品質・味を大切にした商品選びをお伝えします。'],
        'select/fish' => ['お魚のこだわり', 'セレクションが選ぶお魚・水産商品のこだわりをご紹介。産地や生産者、加工の工夫など、おいしさを支える商品の背景をお伝えします。'],
        'select/fruit-vegetables' => ['お野菜・果物のこだわり', 'セレクションが選ぶ野菜・果物のこだわりをご紹介。産地や生産者の思い、栽培や収穫の工夫など、おいしさにつながる取り組みをお伝えします。'],
        'select/rice' => ['お米のこだわり', 'セレクションが選ぶお米のこだわりをご紹介。生産者の思いや米づくり、精米への工夫など、毎日の食卓においしいお米を届ける背景をお伝えします。'],
        'select/deli' => ['お惣菜のこだわり', 'セレクションのお惣菜のこだわりをご紹介。素材や調味料、調理の工夫など、ご家庭の食卓においしさを届けるための取り組みをお伝えします。'],
        'select/washoku-daily' => ['和日配のこだわり', 'セレクションが選ぶ和日配商品のこだわりをご紹介。毎日の食卓になじむ商品の魅力と、つくり手が大切にしている思いや工夫をお伝えします。'],
        'select/foods' => ['加工食品のこだわり', 'セレクションが選ぶ加工食品のこだわりをご紹介。原材料や製法、生産者の思いなど、商品の味わいを支える背景をお伝えします。'],
        'select/sweets' => ['お菓子のこだわり', 'セレクションが選ぶお菓子のこだわりをご紹介。素材や製法、つくり手の思いなど、日々のおやつやひと息の時間を彩る商品の魅力をお伝えします。'],
        'select/alcohol' => ['お酒のこだわり', 'セレクションが選ぶお酒のこだわりをご紹介。産地やつくり手の思い、味わいの特徴など、食卓を彩るお酒の魅力をお伝えします。'],
        'company' => ['企業情報', '千葉・東京・埼玉でスーパーマーケットを展開する株式会社セレクションの企業情報です。会社概要や企業の方へのご案内、採用情報をご紹介します。'],
        'company/about' => ['会社概要', '株式会社セレクションの会社概要をご紹介。代表メッセージ、事業内容、事業所、会社沿革などをご覧いただけます。'],
        'company/business' => ['企業の方へ・お取引のご案内', 'セレクションとのお取引をお考えの企業様へ。商品・サービスのご提案や新規店舗の物件情報など、企業様向けのご案内をご覧いただけます。'],
        'company/business/contact' => ['企業様向けお問い合わせ', '株式会社セレクションへの企業様向けお問い合わせフォームです。商品のお取引、店舗物件情報、商品・サービスや広告・販促などのご提案を受け付けています。'],
        'recruit' => ['新卒・キャリア採用情報', '株式会社セレクションの新卒・キャリア採用情報です。会社の考え方や仕事内容、社員インタビュー、募集要項、エントリーについてご案内します。'],
        'company/work' => ['セレクションを知る・仕事と人づくり', 'セレクションが大切にする考え方や仕事、人づくりをご紹介。地域の暮らしを支える取り組みや、これからのスーパーマーケットへの思いをお伝えします。'],
        'entry' => ['新卒採用の募集要項・エントリー', '株式会社セレクションの新卒採用の募集要項をご案内。募集職種、勤務条件、待遇、選考について確認し、エントリーフォームからご応募いただけます。'],
        'career-entry' => ['キャリア採用の募集要項・エントリー', '株式会社セレクションのキャリア採用の募集要項をご案内。募集職種、応募資格、勤務条件、待遇などを確認し、エントリーフォームからご応募いただけます。'],
        'job' => ['パート・アルバイト採用情報', 'フーズマーケットセレクションのパート・アルバイト採用情報です。店舗や職種から求人を探し、仕事内容、勤務条件、応募方法をご確認いただけます。'],
        'job/search' => ['パート・アルバイト求人検索', 'フーズマーケットセレクションのパート・アルバイト求人を検索できます。勤務地や職種などの条件から、ご希望の求人情報をご確認ください。'],
        'recruit/entry' => ['パート・アルバイト応募フォーム', 'フーズマーケットセレクションのパート・アルバイト応募フォームです。応募する店舗・求人をご確認のうえ、必要事項をご入力ください。'],
    ];
}

function foods_seo_text($value) {
    return is_scalar($value) ? trim(preg_replace('/\s+/u', ' ', html_entity_decode(wp_strip_all_tags(strip_shortcodes((string) $value)), ENT_QUOTES, 'UTF-8'))) : '';
}

/** Approved individual copy is keyed by stable content slugs, not database IDs. */
function foods_seo_article_copy() {
    return [
        'news:hp-renewal' => ['ホームページリニューアルのお知らせ', 'フーズマーケットセレクションのホームページがリニューアルしました。チラシ、店舗情報、おすすめ商品やキャンペーンなど、お買い物に役立つ情報をご紹介します。'],
        'news:beaujolais-2026' => ['2026年ヌーヴォ・新酒のご案内', 'フランスから届く2026年の新酒をご紹介。ピノノワール ヌーヴォとボジョレー・ヌーヴォの商品情報、ご予約についてご案内します。'],
        'news:christmas-cake-2026' => ['クリスマスケーキご予約のご案内', 'セレクションのクリスマスケーキをご紹介。家族や大切な人とのクリスマスを彩るケーキの商品情報、ご予約についてご案内します。'],
        'news:osechi' => ['おせち料理ご予約のご案内', 'セレクションのおせち料理をご紹介。新しい年の食卓を彩るおせちの商品情報や、各店でのご予約についてご案内します。'],
        'recipe:kurashiru-0331c75b-ff6e-489e-b0bd-24f85e130555' => ['ほくほく 醤油バターで長芋のステーキ｜レシピ', '長芋をバターとニンニクで焼き、しょうゆベースのたれを絡める副菜です。「ほくほく 醤油バターで長芋のステーキ」の材料、分量、作り方をご紹介します。'],
        'recipe:kurashiru-95c4deed-e934-424a-b480-916458fcc51c' => ['さっぱりシャキシャキ ゆず大根｜レシピ', '拍子切りの大根を、ゆずの果汁と皮を使った甘酢に漬けるレシピです。「さっぱりシャキシャキ ゆず大根」の材料、分量、作り方をご紹介します。'],
        'recipe:kurashiru-6722ed59-25f8-44f4-b127-1f6483033a5f' => ['バター香る 秋鮭としめじの炊き込みごはん｜レシピ', '鮭としめじを米と炊き、仕上げにバターを混ぜる炊き込みごはんです。材料、分量、調理手順をご紹介します。'],
        'recipe:kurashiru-cc497c48-2264-4f0c-9860-27c6a3df6e08' => ['サッパリおいしい！鶏むね肉の甘辛みぞれ煮｜レシピ', '焼いた鶏むね肉を、大根おろしと酢入りの甘辛だれで煮る料理です。「鶏むね肉の甘辛みぞれ煮」の材料、分量、作り方をご紹介します。'],
        'recipe:kurashiru-e3d7ed0f-b292-4cc3-99b3-c3dd5827d254' => ['簡単うまうま 白菜と豚バラのミルフィーユ鍋｜レシピ', '白菜と薄切りの豚バラ肉を重ね、和風だしと生姜で煮る鍋料理です。材料、分量、調理手順をご紹介します。'],
        'recipe:kurashiru-94fea917-25fd-4e72-858f-8b830e568738' => ['炊飯器で鶏肉と大根のとろとろ煮｜レシピ', '手羽元と大根を炊飯器で煮るレシピです。「炊飯器で鶏肉と大根のとろとろ煮」の材料、分量、調理手順をご紹介します。'],
    ];
}

function foods_seo_interview_copy() {
    return [
        '01' => ['社員インタビュー｜店舗運営部 店長 Sさん', 'セレクションの店舗運営部で店長を務めるSさんのインタビュー。売場づくりの工夫や仕事のやりがい、働く仲間との関わりについてご紹介します。'],
        '02' => ['社員インタビュー｜チェッカーチーム 本部トレーナー Kさん', 'セレクションのチェッカーチームで本部トレーナーを務めるKさんのインタビュー。仕事内容や入社のきっかけ、仕事のやりがいについてご紹介します。'],
        '03' => ['社員インタビュー｜店舗運営部 部長 Kさん', 'セレクションの店舗運営部で部長を務めるKさんのインタビュー。お客様の期待に応える店づくりや、人とのつながりを大切にする仕事への思いをご紹介します。'],
    ];
}

/** Resolve from the queried object, never from user-supplied URL parameters. */
function foods_seo_current_copy() {
    $pages = foods_seo_page_copy();
    if (is_404()) {
        return ['ページが見つかりません', 'お探しのページは見つかりませんでした。フーズマーケットセレクションのトップページやメニューから、ご希望の情報をお探しください。'];
    }
    if (is_front_page()) {
        return $pages[''];
    }
    foreach (['shop', 'news', 'recipe'] as $type) {
        if (is_post_type_archive($type)) {
            return $pages[$type];
        }
    }
    $post = get_queried_object();
    if (!$post instanceof WP_Post) {
        return null;
    }
    $id = $post->ID;
    $title = foods_seo_text(get_the_title($id));
    if (is_singular('shop')) {
        $name = foods_get_shop_short_name($title);
        $address = foods_seo_text(get_post_meta($id, 'address', true));
        preg_match('/(東京都|北海道|(?:京都|大阪)府|.{2,3}県)(.+?[市区町村])/u', $address, $location);
        $prefix = isset($location[0]) ? $location[0] . 'の' : '';
        return [$name . 'のチラシ・店舗情報', $prefix . 'フーズマーケットセレクション' . $name . 'の店舗情報です。チラシ、営業時間、所在地、電話番号、アクセスなどをご確認いただけます。'];
    }
    if (is_singular('recruit_part_time')) {
        $shop = foods_seo_text(get_post_meta($id, 'shop_name', true));
        $role = foods_seo_text(get_post_meta($id, 'job_type', true));
        $role = strtr($role, ['チェッカー（レジ）、品出し' => 'レジ・品出し', '品出しパートナー募集' => '品出し', 'クリ－ン部' => 'クリーン部', '販売部（チェッカー・品出し他）' => '販売部〈レジ・品出しなど〉', '食品品出し(レジ作業含む)' => '食品品出し〈レジ作業含む〉', '加工品（品出し）' => '加工品〈品出し〉', 'チェッカー（レジ）' => 'チェッカー〈レジ〉']);
        if ($shop === '' || $role === '') {
            return null; // Missing source fields require editorial review.
        }
        return [$shop . '｜' . $role . 'の求人・募集要項', $shop . 'の' . $role . 'の求人情報です。仕事内容、給与、勤務時間、勤務地、待遇、応募方法など、募集要項をご確認いただけます。'];
    }
    if (is_singular(['news', 'recipe'])) {
        $approved = foods_seo_article_copy();
        $key = $post->post_type . ':' . $post->post_name;
        if (isset($approved[$key])) {
            return $approved[$key];
        }
        $summary = $post->post_type === 'recipe' ? get_post_meta($id, 'recipe_summary', true) : get_the_excerpt($post);
        return [$title . ($post->post_type === 'recipe' ? '｜レシピ' : ''), foods_seo_text($summary)];
    }
    if (is_page()) {
        $template = get_page_template_slug($id);
        if ($template === 'page-recruit-interview.php') {
            $number = foods_get_recruit_interview_number($post);
            $interviews = foods_get_recruit_interviews();
            $interview = $interviews[$number] ?? null;
            $approved = foods_seo_interview_copy();
            if (isset($approved[$number])) {
                return $approved[$number];
            }
            if ($interview) {
                return ['社員インタビュー｜' . foods_seo_text($interview['role'] ?? ''), foods_seo_text(implode(' ', $interview['answers'] ?? []))];
            }
            // Additional ordinary interview pages use their own published copy.
            return ['社員インタビュー｜' . $title, foods_seo_text($post->post_excerpt ?: $post->post_content)];
        }
        if (get_query_var('foods_job_search')) {
            return $pages['job/search'];
        }
        if (get_query_var('foods_job_entry')) {
            return $pages['recruit/entry'];
        }
        if ($template === 'page-shop.php') {
            return $pages['shop'];
        }
        $path = get_page_uri($id);
        return $pages[$path] ?? null;
    }
    return null;
}

function foods_confirmed_seo_title($title) {
    $copy = foods_seo_current_copy();
    $site = 'フーズマーケットセレクション｜千葉・東京・埼玉のスーパーマーケット';
    return $copy === null ? $title : ($copy[0] === '' ? $site : $copy[0] . '｜' . $site);
}
add_filter('pre_get_document_title', 'foods_confirmed_seo_title');

function foods_confirmed_seo_head() {
    $copy = foods_seo_current_copy();
    if ($copy !== null && $copy[1] !== '') {
        $description = $copy[1];
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    // Reuse the legacy image; its actual format is PNG despite the .ico name.
    // Let WordPress own icon output if a site icon is configured in the future.
    if (!has_site_icon()) {
        echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url(get_theme_file_uri('/img/favicon.png')) . '">' . "\n";
    }
}
add_action('wp_head', 'foods_confirmed_seo_head');
