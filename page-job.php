<?php
/**
 * Template Name: パート・アルバイト募集
 */

$job_stores = ['行徳店', '西船橋店', '花野井店', 'しいの木台店', '青葉台店', '西原店', '松戸店', '西新井店', '三郷店', '八潮店'];
$job_categories = ['どこでも可（レジ）', '品出し（グロサリー）', 'チェッカー', 'お惣菜', 'ベーカリー', '水産', '寿司', '食肉', '青果'];

get_header('company');
?>

<main class="p-job">
    <nav class="p-job__breadcrumb" aria-label="パンくずリスト">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span aria-hidden="true">›</span>
        <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span aria-hidden="true">›</span>
        <span>パートアルバイト募集</span>
    </nav>

    <section class="p-job__hero" aria-labelledby="job-title">
        <div class="p-job__hero-image" aria-hidden="true"></div>
        <div class="p-job__hero-heading">
            <p>食卓にいつも笑顔を</p>
            <h1 id="job-title">地域の皆さまの<br>“おいしい毎日”を<br>一緒に支えませんか？</h1>
        </div>
        <p class="p-job__hero-copy">地域のお客様に笑顔をお届けする、<br>スーパーマーケットで一緒に働きませんか？<br>レジや品出し、惣菜づくりなど、お仕事はいろいろ。<br>未経験の方でも、先輩スタッフが丁寧に<br>サポートしますので安心してスタートできます。</p>
    </section>

    <section class="p-job-search" aria-labelledby="job-search-title">
        <h2 id="job-search-title">求人を探す</h2>
        <form action="<?php echo esc_url(home_url('/job/search/')); ?>" method="get">
            <div class="p-job-search__group">
                <h3>フリーワードで探す</h3>
                <input type="search" name="keyword" placeholder="店舗名、駅名、住所など">
            </div>

            <fieldset class="p-job-search__group">
                <legend>勤務地から探す</legend>
                <div class="p-job-search__options">
                    <?php foreach ($job_stores as $store) : ?>
                        <label><input type="checkbox" name="store[]" value="<?php echo esc_attr($store); ?>"><span><?php echo esc_html($store); ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset class="p-job-search__group">
                <legend>職種から探す</legend>
                <div class="p-job-search__options p-job-search__options--jobs">
                    <?php foreach ($job_categories as $category) : ?>
                        <label><input type="checkbox" name="job[]" value="<?php echo esc_attr($category); ?>"><span><?php echo esc_html($category); ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <button type="submit">検索</button>
        </form>
    </section>
</main>

<?php get_footer('company'); ?>
