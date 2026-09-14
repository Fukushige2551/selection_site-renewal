<?php
/**
 * 企業ページ共通フッター
 * 未設定のリンク先は、設定されるまでリンクとして出力しない。
 */
$footer_destinations = apply_filters('foods_company_footer_destinations', [
    'company_profile' => '', 'recruit' => '', 'business' => '',
    'app' => '', 'cgc' => '', 'youtube' => '', 'instagram' => '', 'facebook' => '',
]);
// 店舗一覧
$footer_shops = [
    ['行徳店', 'gyoutoku'], ['三郷店', 'misato'], ['八潮店', 'yashio'],
    ['西原店', 'nishihara'], ['西船橋店', 'nishifunabashi'],
    ['花野井店', 'hananoi'], ['しいの木台店', 'shiinokidai'], ['青葉台店', 'aobadai'],
    ['松戸店', 'matsudo'], ['西新井店', 'nishiarai'],
];
// 商品・売場の一覧
$footer_departments = [
    ['お店づくり', '/select/'], ['お野菜・果物', '/select-vegetables-fruit/'],
    ['お肉', '/select-meat/'], ['お魚', '/select-fish/'], ['お菓子', ''], ['お米', '/select-rice/'],
    ['乳製品', ''], ['和日配', ''], ['お惣菜', '/select-deli/'], ['加工食品', ''], ['お酒', ''],
];
// リンク先の有無に応じて要素を出力する。
$footer_link = static function ($label, $url, $class = '') {
    if ($url) {
        echo '<a class="' . esc_attr($class) . '" href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
    } else {
        echo '<span class="' . esc_attr($class) . '">' . esc_html($label) . '</span>';
    }
};
?>
<!-- 企業ページ共通フッター 開始 -->
<footer class="l-company-footer">
    <div class="l-company-footer__inner">
        <a class="l-company-footer__logo" href="<?php echo esc_url(home_url('/')); ?>">
            <picture>
                <source srcset="<?php echo esc_url(get_template_directory_uri() . '/img/footer/footer_logo-pc.webp'); ?>" type="image/webp">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/footer/footer_logo-pc.png'); ?>" alt="FOODS MARKET Selection" width="200" height="100" loading="lazy">
            </picture>
        </a>

        <!-- フッターナビゲーション 開始 -->
        <nav class="l-company-footer__nav" aria-label="フッターナビゲーション">
            <div class="l-company-footer__column">
                <details class="l-company-footer__group l-company-footer__group--shops">
                    <summary><a href="<?php echo esc_url(get_post_type_archive_link('shop')); ?>">チラシ・店舗情報</a><span class="l-company-footer__plus" aria-hidden="true">+</span></summary>
                    <ul class="l-company-footer__list l-company-footer__list--shops">
                        <?php foreach ($footer_shops as [$name, $slug]) : ?>
                            <li><?php $footer_link($name, home_url('/shop/' . $slug . '/')); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </details>
                <div class="l-company-footer__row l-company-footer__row--news"><?php $footer_link('新着情報', get_post_type_archive_link('news')); ?></div>
                <div class="l-company-footer__row l-company-footer__row--recruit"><?php $footer_link('パート・アルバイト募集', get_post_type_archive_link('recruit_part_time')); ?></div>
                <div class="l-company-footer__row l-company-footer__row--recipe"><?php $footer_link('レシピ', get_post_type_archive_link('recipe')); ?></div>
                <div class="l-company-footer__row l-company-footer__row--online"><?php $footer_link('オンラインショップ', 'https://foods-selection.shops.jp/'); ?></div>
            </div>

            <div class="l-company-footer__column">
                <details class="l-company-footer__group l-company-footer__group--select" data-footer-departments>
                    <summary><a href="<?php echo esc_url(home_url('/select/')); ?>">セレクションのこだわり</a><span class="l-company-footer__plus" aria-hidden="true">+</span></summary>
                    <ul class="l-company-footer__list l-company-footer__list--departments">
                        <?php foreach ($footer_departments as [$name, $path]) : ?>
                            <li><?php $footer_link($name, $path ? home_url($path) : ''); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </details>
            </div>

            <div class="l-company-footer__column">
                <details class="l-company-footer__group l-company-footer__group--company">
                    <summary><a href="<?php echo esc_url(home_url('/company/')); ?>">会社情報</a><span class="l-company-footer__plus" aria-hidden="true">+</span></summary>
                    <ul class="l-company-footer__list l-company-footer__list--company">
                        <li><?php $footer_link('会社概要', $footer_destinations['company_profile']); ?></li>
                        <li><?php $footer_link('採用情報（新卒・中途）', $footer_destinations['recruit']); ?></li>
                        <li><?php $footer_link('企業の方', $footer_destinations['business']); ?></li>
                    </ul>
                </details>
                <div class="l-company-footer__row l-company-footer__row--contact"><?php $footer_link('お問い合わせ', home_url('/contact/')); ?></div>
                <div class="l-company-footer__row l-company-footer__row--privacy"><?php $footer_link('プライバシーポリシー', home_url('/privacy/')); ?></div>
            </div>
        </nav>
        <!-- フッターナビゲーション 終了 -->

        <!-- バナー一覧 -->
        <div class="l-company-footer__banners">
            <?php foreach (['app' => ['selection-app', 'セレクション アプリ ダウンロード'], 'cgc' => ['cgc-colab', 'セレクションはCGCの加盟店です']] as $key => [$file, $alt]) : ?>
                <?php if ($footer_destinations[$key]) : ?><a class="l-company-footer__banner" href="<?php echo esc_url($footer_destinations[$key]); ?>"><?php else : ?><div class="l-company-footer__banner"><?php endif; ?>
                    <picture>
                        <source srcset="<?php echo esc_url(get_template_directory_uri() . '/img/footer/footer_banner-' . $file . '.webp'); ?>" type="image/webp">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/img/footer/footer_banner-' . $file . '.jpg'); ?>" alt="<?php echo esc_attr($alt); ?>" width="<?php echo $key === 'app' ? 722 : 720; ?>" height="<?php echo $key === 'app' ? 257 : 254; ?>" loading="lazy">
                    </picture>
                <?php if ($footer_destinations[$key]) : ?></a><?php else : ?></div><?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ソーシャルリンク -->
    <ul class="l-company-footer__socials">
        <?php foreach (['youtube' => 'YouTube', 'instagram' => 'Instagram', 'facebook' => 'Facebook'] as $key => $label) : ?>
            <li class="l-company-footer__social l-company-footer__social--<?php echo esc_attr($key); ?>">
                <?php if ($footer_destinations[$key]) : ?><a href="<?php echo esc_url($footer_destinations[$key]); ?>" aria-label="<?php echo esc_attr($label); ?>"><?php endif; ?>
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/img/component/svg/icon_' . $key . '.svg'); ?>" alt="<?php echo esc_attr($label); ?>" width="40" height="40" loading="lazy">
                <?php if ($footer_destinations[$key]) : ?></a><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <!-- 会社情報 -->
    <div class="l-company-footer__company">
        <p>株式会社セレクション</p>
        <address>千葉県市川市湊新田1丁目6番8号</address>
    </div>
    <p class="l-company-footer__copyright">© <?php echo esc_html(wp_date('Y')); ?> FOODS MARKET Selection co,ltd.</p>
    <!-- ページ上部へ戻るボタン -->
    <button class="c-btn c-btn--scroll-top" type="button" aria-label="ページトップへ戻る"></button>
</footer>
<!-- 企業ページ共通フッター 終了 -->
<?php
wp_enqueue_script('foods-company-footer', get_template_directory_uri() . '/src/js/footer-company.js', [], filemtime(get_template_directory() . '/src/js/footer-company.js'), true);
wp_footer();
?>
</body>
</html>
