<?php
/**
 * Template Name: 採用情報：セレクションを知る
 */

$image_uri = get_template_directory_uri() . '/img/page/page-recruit-work';

$jobs = [
  ['食肉', 'プロの技で<br>“おいしさ”を<br>見せる。', 'job-meat.png', 'meat'],
  ['水産', '鮮度と見栄えが<br>勝負。', 'job-fish.png', 'fish'],
  ['青果', '季節の野菜や<br>果物を通じて旬を<br>届ける。', 'job-produce.png', 'produce'],
  ['惣菜', '手づくりの味で<br>毎日の食卓を<br>応援。', 'job-deli.png', 'deli'],
  ['グロサリー', '毎日の生活を<br>支える“定番商品”<br>を扱う。', 'job-grocery.png', 'grocery'],
  ['チェッカー', 'お客様と最も近い<br>「お店の顔」。', 'job-checker.png', 'checker'],
];

$job_modals = [
  'meat' => [
    'title' => '食肉部門',
    'lead' => 'プロの技で“おいしさ”をつくる',
    'image' => 'job-modal-meat.png',
    'image_alt' => '食肉部門で商品を加工するスタッフ',
    'description' => '精肉部門は、お客様の食卓に安全でおいしいお肉を届けるため、カット・パック・値付け・陳列などを行う部門です。肉の種類や部位によって扱い方や保存方法が異なるため、専門知識と丁寧な作業が求められます。',
    'tasks' => [
      '肉のカット、スライス、パック詰め',
      '値付け、ラベル貼り',
      '売場づくり（数字管理・陳列・装飾・POP作成）',
      '在庫管理・品質管理',
      'お客様からのご要望対応',
      'マネジメント業務',
    ],
    'rewards' => [
      '売り場の計画をたて量目を決め、計画通りにお肉が売れるときの達成感。',
      'スライサーの加工技術を褒められた時の嬉しさ。',
    ],
  ],
  'fish' => [
    'title' => '水産部門',
    'lead' => '鮮度と見栄えが勝負。',
    'image' => 'job-modal-fish.png',
    'image_alt' => '水産部門で商品を加工するスタッフ',
    'description' => '水産部門は、鮮魚や切り身、お刺身などを通して、海の恵みをお客様の食卓へ届ける部門です。魚の種類や鮮度に合わせた加工・調理・陳列を行い、安全でおいしい商品づくりを担います。',
    'tasks' => [
      '魚の加工（下処理、切り身、お刺身づくり）',
      'パック詰め、値付け、ラベル貼り',
      '売場づくり（数字管理・陳列・装飾・POP作成）',
      '在庫管理・鮮度、品質管理',
      'お客様対応',
      'マネジメント業務',
    ],
    'rewards' => [
      '技術が特に大事になります。捌き方ひとつで鮮度の良し悪しが大きく変わり販売に直結する。難しい部門ですが自分の技術をお客様に披露する機会が多く「綺麗なお造り！」や「お寿司美味しそう！」などの誉め言葉を受けるたびに励みになります。',
    ],
  ],
  'produce' => [
    'title' => '青果部門',
    'lead' => '季節の野菜や果物を通じて旬を届ける。',
    'image' => 'job-modal-produce.png',
    'image_alt' => '青果部門で野菜を加工するスタッフ',
    'description' => '青果部門は、野菜や果物を通して「新鮮さ」と「季節感」を伝える部門です。鮮度管理や美しい売場づくりを行い、毎日の食卓を支えます。',
    'tasks' => [
      '野菜・果物の加工、袋詰め',
      '値付け、ラベル貼り',
      '売場づくり（数字管理・陳列・装飾・量目調整）',
      '鮮度・在庫管理',
      'お客様対応',
      'マネジメント業務',
    ],
    'rewards' => [
      '売り場計画をたてそれがハマるとき。',
      '今日は葉物を多くしよう。この果物の売り場を拡げようなど商品知識、そして気候や季節を考えてつくるのが楽しいです。',
    ],
  ],
  'deli' => [
    'title' => '惣菜部門',
    'lead' => '手づくりの味で毎日の食卓を応援。',
    'image' => 'job-modal-deli.png',
    'image_alt' => '惣菜部門で商品を調理するスタッフ',
    'description' => '惣菜部門は、お弁当や揚げ物、煮物など、すぐに食べられる商品を提供する部門です。調理から売場づくりまで幅広く関わり、毎日の「助かる」「おいしい」を支えます。',
    'tasks' => [
      '惣菜・弁当の調理、盛り付け',
      'パック詰め、値付け',
      '売場づくり、品出し（数字管理・陳列・装飾・POP作成）',
      '衛生管理',
      '商品補充、製造数管理',
    ],
    'rewards' => [
      '「美味しそう！」や「これ、こないだ買って美味しかったの」と言って買って下さったとき。',
      'おいしいの一言が自分らの調理技術の腕や商品提案の成功に直結するのが嬉しいです。',
    ],
  ],
  'grocery' => [
    'title' => 'グロサリー部門',
    'lead' => '毎日の生活を支える“定番商品”を扱う。',
    'image' => 'job-modal-grocery.png',
    'image_alt' => 'グロサリー部門で商品を補充するスタッフ',
    'description' => 'グロサリー部門は、調味料・加工食品・お菓子・飲料など、日常に欠かせない商品を扱う部門です。売場管理を通して、お客様が買い物しやすい環境を整えます。',
    'tasks' => [
      '商品の品出し、補充',
      '売場づくり（数字管理・陳列・棚割り調整）',
      '発注、在庫管理',
      '売価変更、POP設置',
      '売場の整理整頓',
      'マネジメント業務',
    ],
    'rewards' => [
      '売り場づくりの工夫が売り上げに響いたと感じる時。',
      '発注・在庫管理をし利益をあげてく、数字と現場をみて判断し膨大な種類の商品を販売するのが難しいですが、楽しいです。',
    ],
  ],
  'checker' => [
    'title' => 'チェッカー部門',
    'lead' => 'お客様と最も近い「お店の顔」。',
    'image' => 'job-modal-checker.png',
    'image_alt' => 'チェッカー部門でお客様を接客するスタッフ',
    'description' => 'チェッカー部門は、レジ対応を通してお客様と直接関わる「お店の顔」となる部門です。正確で丁寧な対応が、お店全体の印象を左右します。',
    'tasks' => [
      'レジでの会計業務',
      '接客対応、声かけ',
      '袋詰めサポート',
      'レジ周りの整理整頓',
      'サービスカウンター業務（店舗により）',
    ],
    'rewards' => [
      '「ありがとう」「また来るね」と声をかけてもらえることが大きなやりがいです。',
      '接客スキルが自然と身につき、人と接する楽しさを実感できます。',
    ],
  ],
];

$career_stages = [
  [
    'period' => '1〜4年目',
    'roles' => [
      ['店舗担当者', 'まずは店舗担当として、売場づくりや商品管理、接客などの基本業務を経験します。日々の業務を通して、商品知識や接客スキル、店舗運営の基礎を身につけていきます。'],
    ],
  ],
  [
    'period' => '5〜9年目',
    'roles' => [
      ['マネージャー', '売場や部門を任されるマネージャーとして、スタッフの指導やシフト管理、売上管理を担当します。店舗全体を見渡しながら、より良い売場づくりやチーム運営に携わります。'],
      ['本部スタッフ', '現場経験を活かし、本部スタッフとして店舗運営を支えるポジションに進む道もあります。商品企画、販促、教育、運営サポートなど、店舗と本部をつなぐ役割を担います。'],
    ],
  ],
  [
    'period' => '10年目〜',
    'roles' => [
      ['店長', '店舗全体の責任者として、売上管理・人材育成・店舗運営を統括します。地域に愛される店舗づくりを目指し、経営視点での判断力が求められるポジションです。'],
      ['バイヤー', '商品の選定や仕入れを担当し、セレクションの「商品力」を支える重要な役割です。市場動向やお客様のニーズを捉え、魅力ある売場づくりに貢献します。'],
    ],
  ],
];

$qualification_levels = [
  ['ベーシック級', 'スーパーマーケット業務の基礎を習得'],
  ['マネージャー3級', '売場リーダー・主任を目指すための資格'],
  ['マネージャー2級', '店舗運営やチームマネジメントを担うレベル'],
  ['バイヤー級', '仕入れや商品企画に携わるための資格'],
];

$schedule = [
  ['8:00', '出勤・朝礼', '当日の特売品や発注状況を確認し、<br>担当ごとに作業を分担します。', 'schedule-morning.png', 'morning'],
  ['8:15', '加工・パック詰め', '牛・豚・鶏などの肉を部位ごとに<br>切り分け、スライスやパック詰めを<br>行います。<br>鮮度を保ちながらスピーディに<br>作業します。', 'schedule-processing.png', 'processing'],
  ['9:00', '値付け・売場づくり', 'ラベルを貼り、<br>ショーケースに美しく陳列。<br>季節感や特売に合わせた演出も<br>工夫します。', 'schedule-display.png', 'display'],
  ['12:00', '昼休憩', 'チームで交代しながら休憩。<br>午後の作業に備えてリフレッシュ。', '', ''],
  ['13:00', '補充・発注業務', '午前中に売れた商品の補充や、<br>翌日の発注作業。<br>売れ行きを見ながら在庫を<br>調整します。', 'schedule-ordering.png', 'ordering'],
  ['15:00', 'お客様対応・整理', 'ご要望に応じた<br>カットや量り売りに対応。<br>バックヤードの清掃・整理も<br>行います。', 'schedule-customer.png', 'customer'],
  ['17:00', '売場整理・退勤', '残り商品の確認と在庫整理を行い、<br>翌日の準備をして退勤します。', '', ''],
];

get_header('company');
?>

<main id="page-recruit-work" class="p-page-recruit-work">
  <nav class="p-page-recruit-work__breadcrumb" aria-label="パンくずリスト">
    <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
    <span aria-hidden="true">›</span>
    <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a>
    <span aria-hidden="true">›</span>
    <span>セレクションを知る</span>
  </nav>

  <div class="p-page-recruit-work__hero">
    <img src="<?php echo esc_url($image_uri . '/hero-super-resolution.webp'); ?>" alt="セレクションの青果売場" fetchpriority="high">
  </div>

  <section class="p-page-recruit-work__introduction">
    <header class="p-page-recruit-work__heading">
      <h1>セレクションを知る</h1>
      <p>Learn about the selection</p>
    </header>
    <h2>地域の暮らしを支え、<br>未来を変えていく。<br>それが、<br>私たちセレクションです。</h2>
    <p>千葉県、埼玉県、<br>そして近年は東京都エリアにも<br>出店を広げてます。<br>独自の仕入れルートで「低価格で高品質」な<br>商品ラインナップ実現。<br>また本社のある千葉県エリアを中心に<br>「地産地消」にも力をいれる<br>地域に密着したスーパーマーケットです！</p>
  </section>

  <section class="p-page-recruit-work__about">
    <article class="p-page-recruit-work__vision p-page-recruit-work__white-card">
      <div class="p-page-recruit-work__card-decoration"><img src="<?php echo esc_url($image_uri . '/vision-sprout.png'); ?>" alt="芽吹く若葉"></div>
      <h2 class="p-page-recruit-work__label">VISION</h2>
      <h3>“これからの<br>スーパーマーケット”を、<br>本気で考える。</h3>
      <div class="p-page-recruit-work__body-copy">
        <p>スーパーマーケットを取り巻く環境は、いま大きく変わっています。</p>
        <p>人口減少、少子高齢化、共働き世帯の増加、物価高騰、環境問題、食品ロス、人手不足。<br>地域社会が抱える課題は、年々複雑になっています。</p>
        <p>だからこそ、これからのスーパーマーケットには、単に商品を販売するだけではない役割が求められていると、私たちは考えています。</p>
        <p>地域の食を守ること。<br>地域の暮らしを支えること。<br>地域で働く人を育てること。<br>そして、地域社会の未来に貢献すること。</p>
        <p>セレクションは、“地域の暮らしに最も近い会社”として、これからの時代に必要とされる新しいスーパーマーケットを目指しています。</p>
      </div>
    </article>

    <div class="p-page-recruit-work__mission p-page-recruit-work__white-card">
      <div class="p-page-recruit-work__card-decoration"><img src="<?php echo esc_url($image_uri . '/mission-market.png'); ?>" alt="セレクションの売場"></div>
      <h2 class="p-page-recruit-work__label">MISSION</h2>

      <article class="p-page-recruit-work__mission-item">
        <h3>「価格」だけではなく、<br>「価値」を届ける。</h3>
        <div class="p-page-recruit-work__body-copy">
          <p>私たちは、価格だけを追い続ける競争に未来はないと考えています。</p>
          <p>もちろん、毎日の暮らしを支えるうえで“値頃感”は大切です。<br>しかし、本当に地域に必要とされる会社になるためには、それだけでは足りません。</p>
          <p>鮮度。品質。安心感。便利さ。提案力。そして、食べることの楽しさ。</p>
          <p>セレクションは、商品を通じて“食卓そのもの”を豊かにする会社でありたいと考えています。</p>
          <p>毎日来ても新しい発見があること。<br>旬を感じられること。<br>忙しい日々の中でも、少し気持ちが明るくなること。</p>
          <p>私たちは、“安いから行く店”ではなく、<br>「セレクションだから行きたい」と思っていただける存在を目指しています。</p>
        </div>
      </article>

      <article class="p-page-recruit-work__mission-item">
        <h3>地域密着とは、<br>「地域課題」に<br>向き合うこと。</h3>
        <div class="p-page-recruit-work__body-copy">
          <p>セレクションは、地域密着型スーパーマーケットとして歩んできました。</p>
          <p>私たちが考える地域密着とは、単に地域に店舗があることではありません。<br>その地域の暮らしを理解し、その地域が抱える課題に向き合い続けることだと考えています。</p>
          <p>高齢化による買い物環境の変化。<br>地域産業の衰退。<br>働き手不足。<br>食生活の変化。</p>
          <p>スーパーマーケットは、地域の暮らしに欠かせない“生活インフラ”です。</p>
          <p>だからこそ私たちは、地域の生産者とのつながりを大切にし、地域で働く人を育て、地域の食文化を守りながら、<br class="p-page-recruit-work__desktop-break">地域社会に必要とされる存在であり続けたいと考えています。</p>
        </div>
      </article>

      <article class="p-page-recruit-work__mission-item">
        <h3>食品ロスを減らすことは、<br>未来を守ること。</h3>
        <div class="p-page-recruit-work__body-copy">
          <p>日本では、まだ食べられる食品が大量に廃棄されています。</p>
          <p>この課題に対して、スーパーマーケットが果たすべき役割は決して小さくありません。</p>
          <p>セレクションでは、食品ロスを単なるコストの問題ではなく、<br class="p-page-recruit-work__desktop-break">“社会全体で向き合うべき課題”として捉えています。</p>
          <p>必要な量を、必要な分だけ届けること。<br>食材をできる限り無駄なく活かすこと。<br>捨てる前提ではなく、活かし切る発想へ変えていくこと。</p>
          <p>販売データや需要予測を活用しながら、発注精度や製造量の改善、小容量商品の展開、規格外商品の活用<br class="p-page-recruit-work__desktop-break">など、さまざまな取り組みを進めています。</p>
          <p>食品を大切にすることは、生産者を守り、環境を守り、地域の未来を守ることにつながる。<br>私たちは、そう考えています。</p>
        </div>
      </article>

      <article class="p-page-recruit-work__mission-item">
        <h3>持続可能な社会へ。</h3>
        <div class="p-page-recruit-work__body-copy">
          <p>気候変動や資源問題は、食や暮らしにも大きな影響を与えています。</p>
          <p>セレクションでは、地域に根差す企業として、環境負荷低減への取り組みを進めています。</p>
          <p>地産地消による輸送距離の削減。<br>包材使用量の見直し。<br>省エネルギー化。<br>食品ロス削減。<br>リサイクル推進。</p>
          <p>一つひとつは決して派手なことではありません。<br>しかし、未来の地域社会を守るためには、日々の積み重ねこそが大切だと考えています。</p>
          <p>私たちは、これからも持続可能な社会に向けて、できることを真剣に考え続けていきます。</p>
        </div>
      </article>

      <article class="p-page-recruit-work__mission-item">
        <h3>人を育てることが、<br>未来をつくる。</h3>
        <div class="p-page-recruit-work__body-copy">
          <p>どれだけ時代が変わっても、最後に価値を生み出すのは「人」です。</p>
          <p>だからセレクションは、人材育成をとても大切にしています。</p>
          <p>年齢や勤続年数ではなく、“できる仕事”を正しく評価する。<br>挑戦する人にチャンスを与える。<br>社員も、パートナー社員も、外国籍スタッフも、一人ひとりが成長できる環境をつくる。</p>
          <p>変化の時代だからこそ、人の成長が会社の未来をつくると考えています。</p>
          <p>私たちは、“作業をする人”ではなく、地域の食卓を支える人を育てていきたいと考えています。</p>
        </div>
      </article>

      <article class="p-page-recruit-work__mission-item">
        <h3>変化を恐れず、未来へ。</h3>
        <div class="p-page-recruit-work__body-copy">
          <p>スーパーマーケット業界はいま、大きな転換期を迎えています。</p>
          <p>従来のやり方だけでは、地域に必要とされ続けることはできません。</p>
          <p>だからこそ、セレクションは変化を恐れません。</p>
          <p>地域のお客様の暮らしを見つめ、社会の変化に向き合いながら、<br class="p-page-recruit-work__desktop-break">新しいスーパーマーケットのあり方に挑戦し続けます。</p>
          <p>地域の食卓を支え、地域社会を支え、未来をつくる存在へ。</p>
          <p>セレクションは、これからも挑戦を続けていきます。</p>
        </div>
      </article>
    </div>

    <article class="p-page-recruit-work__culture p-page-recruit-work__white-card">
      <div class="p-page-recruit-work__card-decoration"><img src="<?php echo esc_url($image_uri . '/organization-team.png'); ?>" alt="売場で働くスタッフ"></div>
      <h2 class="p-page-recruit-work__label">組織風土</h2>
      <ol>
        <li><span>01</span>社員一人ひとりが役割を明確にし、責任感を持ち、共有できる目標に活き活きと<br class="p-page-recruit-work__desktop-break">向かう一枚岩の組織風土</li>
        <li><span>02</span>社員一人ひとりが喜びを感じ、満足し、笑顔あふれる組織風土</li>
      </ol>
    </article>

    <article class="p-page-recruit-work__business p-page-recruit-work__white-card">
      <div class="p-page-recruit-work__card-decoration"><img src="<?php echo esc_url($image_uri . '/organization-store.png'); ?>" alt="セレクションの店舗"></div>
      <h2 class="p-page-recruit-work__label">事業</h2>
      <p>令和5年3月<br>売上高：　173億2700万円<br>営業利益：1.56％（2.7億円）<br>店舗数：　10店舗</p>
    </article>

    <a class="p-page-recruit-work__company-link" href="<?php echo esc_url(home_url('/company/')); ?>">
      <span>会社概要</span>
      <img class="p-page-recruit-work__company-link-line" src="<?php echo esc_url($image_uri . '/company-link-line.svg'); ?>" alt="">
      <img class="p-page-recruit-work__company-link-chevron" src="<?php echo esc_url($image_uri . '/company-link-chevron.svg'); ?>" alt="">
    </a>
  </section>

  <section class="p-page-recruit-work__combination">
    <div class="p-page-recruit-work__combination-card">
      <img class="p-page-recruit-work__combination-main" src="<?php echo esc_url($image_uri . '/combination-main.png'); ?>" alt="売場で商品を選ぶお客様">
      <div class="p-page-recruit-work__combination-panel">
        <div class="p-page-recruit-work__combination-grid" aria-hidden="true">
          <span class="p-page-recruit-work__combination-photo p-page-recruit-work__combination-photo--pork"><img src="<?php echo esc_url($image_uri . '/combination-pork.png'); ?>" alt=""></span>
          <span class="p-page-recruit-work__combination-cross">×</span>
          <span class="p-page-recruit-work__combination-photo p-page-recruit-work__combination-photo--kimchi"><img src="<?php echo esc_url($image_uri . '/combination-kimchi.png'); ?>" alt=""></span>
          <span class="p-page-recruit-work__combination-photo p-page-recruit-work__combination-photo--cheese"><img src="<?php echo esc_url($image_uri . '/combination-cheese.png'); ?>" alt=""></span>
          <span class="p-page-recruit-work__combination-cross">×</span>
          <span class="p-page-recruit-work__combination-photo p-page-recruit-work__combination-photo--wine"><img src="<?php echo esc_url($image_uri . '/combination-wine.png'); ?>" alt=""></span>
          <span class="p-page-recruit-work__combination-photo p-page-recruit-work__combination-photo--braised-pork"><img src="<?php echo esc_url($image_uri . '/combination-braised-pork.png'); ?>" alt=""></span>
          <span class="p-page-recruit-work__combination-cross">×</span>
          <span class="p-page-recruit-work__combination-photo p-page-recruit-work__combination-photo--eggs"><img src="<?php echo esc_url($image_uri . '/combination-eggs.png'); ?>" alt=""></span>
        </div>
        <h2>考えてみてください<br>思わず買っちゃう組み合わせ</h2>
        <p class="p-page-recruit-work__mobile-copy">豚肉のとなりにキムチが置いてあれば、<br>「今夜は豚キムチにしようかしら？」<br>なんて思ってしまうのが、<br>主婦ゴコロというもの。<br>そんな「思わず買っちゃう」<br>組み合わせづくりに力を入れている私たち</p>
        <p class="p-page-recruit-work__mobile-copy">あなたのアイデアで、<br>お客様の「買いたいキモチ」を<br>ひと押ししてください。</p>
        <p class="p-page-recruit-work__desktop-copy">豚肉のとなりにキムチが置いてあれば、<br class="p-page-recruit-work__desktop-break">「今夜は豚キムチにしようかしら？」<br class="p-page-recruit-work__desktop-break">なんて思ってしまうのが、主婦ゴコロというもの。<br class="p-page-recruit-work__desktop-break">そんな「思わず買っちゃう」組み合わせづくりに力を入れている私たち</p>
        <p class="p-page-recruit-work__desktop-copy">あなたのアイデアで、<br class="p-page-recruit-work__desktop-break">お客様の「買いたいキモチ」をひと押ししてください。</p>
      </div>
    </div>
  </section>

  <section class="p-page-recruit-work__jobs">
    <header class="p-page-recruit-work__heading p-page-recruit-work__heading--light">
      <h2>仕事内容</h2>
      <p>job description</p>
    </header>
    <picture>
      <source media="(max-width: 767px)" srcset="<?php echo esc_url($image_uri . '/jobs-main-sp.webp'); ?>" type="image/webp">
      <img class="p-page-recruit-work__jobs-main" src="<?php echo esc_url($image_uri . '/jobs-main.png'); ?>" alt="セレクションの各部門で働くスタッフ">
    </picture>
    <div class="p-page-recruit-work__job-list">
      <?php foreach ($jobs as [$title, $description, $image, $slug]) : ?>
        <article class="p-page-recruit-work__job-card">
          <div class="p-page-recruit-work__job-photo"><img class="p-page-recruit-work__job-photo-image p-page-recruit-work__job-photo-image--<?php echo esc_attr($slug); ?>" src="<?php echo esc_url($image_uri . '/' . $image); ?>" alt="<?php echo esc_attr($title . '部門'); ?>" loading="lazy"></div>
          <div class="p-page-recruit-work__job-copy"><h3><?php echo esc_html($title); ?></h3><p><?php echo wp_kses($description, ['br' => []]); ?></p></div>
          <img class="p-page-recruit-work__job-arrow" src="<?php echo esc_url($image_uri . '/job-arrow.svg'); ?>" alt="">
          <?php if (isset($job_modals[$slug])) : ?>
            <button class="p-page-recruit-work__job-trigger" type="button" data-job-modal-open="<?php echo esc_attr($slug); ?>" aria-haspopup="dialog" aria-controls="job-modal-<?php echo esc_attr($slug); ?>">
              <span><?php echo esc_html($title); ?>部門の詳細を見る</span>
            </button>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="p-page-recruit-work__career">
    <header class="p-page-recruit-work__heading">
      <h2>キャリアアップ</h2>
      <p>job description</p>
    </header>
    <h3>若いうちから成長し、<br>責任ある仕事に挑戦できる<br>環境です。</h3>
    <p class="p-page-recruit-work__career-lead">入社後1〜4年で基礎を固め、<br>5〜9年目にはマネージャーとして店舗を牽引。<br>10年以降にはバイヤーとして<br>商品開発や仕入れに携わるなど、<br>長期的にキャリアを築ける環境です。</p>
    <div class="p-page-recruit-work__career-chart">
      <img src="<?php echo esc_url($image_uri . '/career-chart.svg'); ?>" alt="店舗担当者からマネージャー、店長、バイヤーへ進むキャリアの流れ" loading="lazy">
    </div>
    <div class="p-page-recruit-work__career-stages">
      <?php foreach ($career_stages as $stage) : ?>
        <section class="p-page-recruit-work__career-stage">
          <h3><span><?php echo esc_html($stage['period']); ?></span></h3>
          <?php foreach ($stage['roles'] as [$role, $description]) : ?>
            <article><h4><?php echo esc_html($role); ?></h4><p><?php echo esc_html($description); ?></p></article>
          <?php endforeach; ?>
        </section>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="p-page-recruit-work__training">
    <header class="p-page-recruit-work__heading p-page-recruit-work__heading--light">
      <h2>研修・資格制度</h2>
      <p>Training and Certification Systems</p>
    </header>
    <p class="p-page-recruit-work__training-lead p-page-recruit-work__mobile-copy">当社のような小規模スーパーでは、<br>一人ひとりの役割が大きく、<br>若手のうちから売場やチームを<br>任されるチャンスがあります。<br>入社後は段階的な研修と資格制度を通じて、<br>スーパーマーケットの運営に必要な<br>知識・スキルをしっかりと<br>身につけることができます。</p>
    <p class="p-page-recruit-work__training-lead p-page-recruit-work__desktop-copy p-page-recruit-work__desktop-lines"><span>当社のような小規模スーパーでは、</span><br><span>一人ひとりの役割が大きく、若手のうちから売場やチームを</span><br><span>任されるチャンスがあります。</span><br><span>入社後は段階的な研修と資格制度を通じて、</span><br><span>スーパーマーケットの運営に必要な知識・スキルを</span><br><span>しっかりと身につけることができます。</span></p>

    <article class="p-page-recruit-work__training-card">
      <img src="<?php echo esc_url($image_uri . '/training-main.png'); ?>" alt="研修を受ける社員" loading="lazy">
      <div class="p-page-recruit-work__training-card-body">
        <h3>新人研修</h3>
        <section><h4>入社前研修</h4><p>社会人としての基本マナーやルール、コミュニケーションの基礎、スーパーマーケット業務の基本<br>知識を学びます。<br>実際の売場づくりや商品チェックなど、現場に立つ準備を整えるプログラムです。</p></section>
        <section><h4>入社後研修</h4><p>配属後も、定期的にフォロー研修を実施しています。<br>入社から約3〜6か月間は、日々の業務を通じてスーパーマーケットの基本動作を身につけ、先輩の<br>サポートを受けながら自立を目指します。<br>研修では、チーフやマネージャーを目指すための知識や考え方も学びます。</p></section>
      </div>
    </article>

    <article class="p-page-recruit-work__qualification-card">
      <img src="<?php echo esc_url($image_uri . '/qualification-main.png'); ?>" alt="タブレットを使って学ぶ社員" loading="lazy">
      <div class="p-page-recruit-work__qualification-body">
        <h3>自己啓発支援・資格制度</h3>
        <p class="p-page-recruit-work__mobile-copy">スタッフ一人ひとりの成長を<br>後押しするため、<br>スキルに応じた社内資格制度を<br>設けています。<br>経験年数やスキルレベルに合わせて<br>ステップアップが可能です。</p>
        <p class="p-page-recruit-work__desktop-copy p-page-recruit-work__desktop-lines"><span>スタッフ一人ひとりの成長を後押しするため、スキルに応じた社内</span><br><span>資格制度を設けています。経験年数やスキルレベルに合わせてステ</span><br><span>ップアップが可能です。</span></p>
        <ol>
          <?php foreach ($qualification_levels as [$level, $description]) : ?>
            <li><strong><?php echo esc_html($level); ?></strong><span><?php echo esc_html($description); ?></span></li>
          <?php endforeach; ?>
        </ol>
        <p class="p-page-recruit-work__mobile-copy">これらの資格を段階的に<br>取得することで、<br>数年で店舗運営やバイヤー業務に<br>携わるスキルを<br>身につけることができます。<br>また、食品衛生責任者や調理師などの<br>外部資格取得も<br>会社がサポートしています。</p>
        <p class="p-page-recruit-work__desktop-copy p-page-recruit-work__desktop-lines"><span>これらの資格を段階的に取得することで、</span><br><span>数年で店舗運営やバイヤー業務に携わるスキルを身につけることができます。</span><br><span>また、食品衛生責任者や調理師などの外部資格取得も会社がサポートしています。</span></p>
      </div>
    </article>
  </section>

  <section class="p-page-recruit-work__daily">
    <header class="p-page-recruit-work__heading">
      <h2>1日の流れ</h2>
      <p>Daily Schedule</p>
    </header>
    <h3>プロの技とチームワークで<br>“おいしさ”を届ける一日。</h3>
    <div class="p-page-recruit-work__daily-intro"><img src="<?php echo esc_url($image_uri . '/daily-intro.png'); ?>" alt="食肉部門のスタッフ" loading="lazy"></div>
    <p class="p-page-recruit-work__daily-lead p-page-recruit-work__mobile-copy">「お店のスタッフって、<br>どんな一日を過ごしているんだろう？」<br>そんな疑問にお答えするために、<br>今回は食肉部門スタッフの一日を例に、<br>仕事の流れをご紹介します。<br>朝から夕方まで、<br>チームで協力しながらおいしさを<br>届ける一日をのぞいてみましょう！</p>
    <p class="p-page-recruit-work__daily-lead p-page-recruit-work__desktop-copy p-page-recruit-work__desktop-lines"><span>「お店のスタッフって、どんな一日を過ごしているんだろう？」</span><br><span>そんな疑問にお答えするために、</span><br><span>今回は食肉部門スタッフの一日を例に、</span><br><span>仕事の流れをご紹介します。</span><br><span>朝から夕方まで、チームで協力しながらおいしさを</span><br><span>届ける一日をのぞいてみましょう！</span></p>

    <div class="p-page-recruit-work__schedule">
      <?php foreach ($schedule as [$time, $title, $description, $image, $slug]) : ?>
        <article class="p-page-recruit-work__schedule-item<?php echo $image ? '' : ' p-page-recruit-work__schedule-item--compact'; ?>">
          <div class="p-page-recruit-work__schedule-copy">
            <img src="<?php echo esc_url($image_uri . '/schedule-clock.svg'); ?>" alt="">
            <div><h3><time><?php echo esc_html($time); ?></time><span><?php echo esc_html($title); ?></span></h3><p><?php echo wp_kses($description, ['br' => []]); ?></p></div>
          </div>
          <?php if ($image) : ?><div class="p-page-recruit-work__schedule-photo"><img class="p-page-recruit-work__schedule-photo-image p-page-recruit-work__schedule-photo-image--<?php echo esc_attr($slug); ?>" src="<?php echo esc_url($image_uri . '/' . $image); ?>" alt="" loading="lazy"></div><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php foreach ($job_modals as $slug => $modal) : ?>
  <div class="p-page-recruit-work__job-modal" data-job-modal="<?php echo esc_attr($slug); ?>" hidden>
    <div class="p-page-recruit-work__job-modal-overlay" data-job-modal-close></div>
    <section class="p-page-recruit-work__job-modal-dialog" id="job-modal-<?php echo esc_attr($slug); ?>" role="dialog" aria-modal="true" aria-labelledby="job-modal-<?php echo esc_attr($slug); ?>-title" tabindex="-1">
      <button class="p-page-recruit-work__job-modal-icon-close" type="button" data-job-modal-close aria-label="閉じる">
        <img src="<?php echo esc_url($image_uri . '/job-modal-close.svg'); ?>" alt="">
      </button>
      <h2 class="p-page-recruit-work__job-modal-title" id="job-modal-<?php echo esc_attr($slug); ?>-title"><?php echo esc_html($modal['title']); ?></h2>
      <div class="p-page-recruit-work__job-modal-content">
        <p class="p-page-recruit-work__job-modal-lead p-page-recruit-work__job-modal-lead--<?php echo esc_attr($slug); ?>"><?php echo esc_html($modal['lead']); ?></p>
        <div class="p-page-recruit-work__job-modal-photo p-page-recruit-work__job-modal-photo--<?php echo esc_attr($slug); ?>">
          <img src="<?php echo esc_url($image_uri . '/' . $modal['image']); ?>" alt="<?php echo esc_attr($modal['image_alt']); ?>">
        </div>
        <div class="p-page-recruit-work__job-modal-section p-page-recruit-work__job-modal-section--description">
          <p><?php echo esc_html($modal['description']); ?></p>
        </div>
        <div class="p-page-recruit-work__job-modal-section">
          <h3>【主な仕事内容】</h3>
          <ul>
            <?php foreach ($modal['tasks'] as $task) : ?>
              <li>・<?php echo esc_html($task); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="p-page-recruit-work__job-modal-section">
          <h3>【やりがい】</h3>
          <?php foreach ($modal['rewards'] as $reward) : ?>
            <p><?php echo esc_html($reward); ?></p>
          <?php endforeach; ?>
        </div>
        <button class="p-page-recruit-work__job-modal-text-close" type="button" data-job-modal-close>× 閉じる</button>
      </div>
    </section>
  </div>
<?php endforeach; ?>

<?php get_footer('company'); ?>
