<?php
$recruit_nav_page_url = static function ($path) {
    $templates = [
        '/company/about/' => 'page-company-about.php',
        '/select/shop/' => 'page-select-shop.php',
        '/select/meat/' => 'page-select-meat.php',
        '/select/fish/' => 'page-select-fish.php',
        '/select/fruit-vegetables/' => 'page-select-vegetables-fruit.php',
        '/select/rice/' => 'page-select-rice.php',
        '/select/deli/' => 'page-select-deli.php',
        '/select/washoku-daily/' => 'page-select-washoku-daily.php',
        '/select/foods/' => 'page-select-foods.php',
        '/select/sweets/' => 'page-select-sweets.php',
        '/select/alcohol/' => 'page-select-alcohol.php',
    ];
    if (isset($templates[$path])) {
        $pages = get_posts(['post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => 1,
            'meta_key' => '_wp_page_template', 'meta_value' => $templates[$path]]);
        if ($pages) return get_permalink($pages[0]);
    }
    return home_url($path);
};
$recruit_nav_groups = [
 ['会社情報', '/company/', [['会社概要','/company/about/'],['採用情報（新卒・中途）','/recruit/'],['企業の方','/company/business/']]],
 ['チラシ・店舗情報', '/shop/', array_map(static fn($shop) => [$shop[0], '/shop/'.$shop[1].'/'], [
 ['行徳店','gyoutoku'],['三郷店','misato'],['八潮店','yashio'],['花野井店','hananoi'],['しいの木台店','shiinokidai'],['青葉台店','aobadai'],['西原店','nishihara'],['松戸店','matsudo'],['西新井店','nishiarai'],['西船橋店','nishifunabashi']])],
 ['セレクションのこだわり','/select/', array_map(static fn($item) => [$item[0].'のこだわり','/select/'.$item[1].'/'], [
 ['お店','shop'],['お肉','meat'],['お魚','fish'],['お野菜・果物','fruit-vegetables'],['お米','rice'],['お惣菜','deli'],['和日配','washoku-daily'],['加工食品','foods'],['お菓子','sweets'],['お酒','alcohol']])],
 ['レシピ','/recipe/',[]],
];
?>
<div class="p-recruit-navigation" data-recruit-navigation hidden>
  <button class="p-recruit-navigation__toggle" type="button" aria-label="採用ページのメニューを開く" aria-expanded="false" aria-controls="recruit-hero-navigation"><span></span><span></span><span></span><img src="<?php echo esc_url(get_template_directory_uri().'/img/page/page-recruit/svg/menu-close.svg'); ?>" alt="" width="29" height="29"></button>
  <nav class="p-recruit-navigation__panel" id="recruit-hero-navigation" aria-label="採用ページのナビゲーション" hidden inert>
    <div class="p-recruit-navigation__groups">
    <?php foreach ($recruit_nav_groups as [$title,$url,$items]) : ?>
      <section class="p-recruit-navigation__group">
        <h2><a href="<?php echo esc_url(home_url($url)); ?>"><?php echo esc_html($title); ?></a></h2>
        <?php if ($items) : ?><ul><?php foreach ($items as [$label,$path]) : ?><li><a href="<?php echo esc_url($recruit_nav_page_url($path)); ?>"><?php echo esc_html($label); ?></a></li><?php endforeach; ?></ul><?php endif; ?>
      </section>
    <?php endforeach; ?>
    </div>
    <div class="p-recruit-navigation__actions"><a href="<?php echo esc_url(home_url('/job/')); ?>">パート・アルバイト募集</a><a href="<?php echo esc_url(home_url('/company/business/contact/')); ?>">お問い合わせ</a></div>
  </nav>
</div>
