<?php
/**
 * Single vacancy template.
 *
 * Values are read from WordPress fields first and fall back to the approved
 * Gyotoku demo content. The fallback keeps the layout reviewable before the
 * production data source is connected.
 */

$job_detail_field = static function ($post_id, $field_name, $fallback = '') {
    $value = function_exists('get_field') ? get_field($field_name, $post_id) : get_post_meta($post_id, $field_name, true);
    return ($value !== null && $value !== false && $value !== '') ? $value : $fallback;
};

get_header('company');
?>

<main class="p-job-detail">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $job_id = get_the_ID();
        $theme_uri = get_template_directory_uri();
        $asset_uri = $theme_uri . '/img/page/single-recruit-part-time';
        $shop_name = $job_detail_field($job_id, 'shop_name', 'セレクション 行徳店');
        $job_type = $job_detail_field($job_id, 'job_type', 'チェッカー（レジ）');
        $salary = $job_detail_field($job_id, 'salary', '時給 1,180円～');
        $location = $job_detail_field($job_id, 'work_location_access', "セレクション 行徳店\n〒272-0132\n千葉県市川市湊新田1丁目6番8号\n東西線 行徳駅徒歩4分");
        $working_hours = $job_detail_field($job_id, 'working_hours', "① 8:00～12:00（4h）\n② 9:00～17:00（7h+休憩1h）\n週2日、1日4h～OKです。\n日・祝は+100円です。");
        $benefits = $job_detail_field($job_id, 'benefits', "交通費支給（1日500円まで）/制服支給/未経験可/研修制度あり/シフト相談可/社員雇用有/車・バイク通勤OK/高校生歓迎/大学生歓迎/主婦（夫）歓迎");
        $other = $job_detail_field($job_id, 'application_method', "セレクション 行徳店\n採用担当 ○○\nTEL: 000-0000-0000\n※ご応募はエントリーフォームにて\n　お願いします。");
        $lead = $job_detail_field($job_id, 'job_lead', '');
        $hero_url = has_post_thumbnail($job_id)
            ? get_the_post_thumbnail_url($job_id, 'full')
            : $asset_uri . '/job-register-hero.png';
        $entry_url = home_url('/job/entry/?job_id=' . $job_id);
        $detail_rows = [
            '職種' => $job_type,
            '給与' => $salary,
            '勤務地' => $location,
            '勤務時間' => $working_hours,
            '待遇/福利厚生' => $benefits,
            'その他' => $other,
        ];
        $steps = [
            ['応募フォームよりご応募', "エントリーフォームより\nご応募ください。", 'step-1.svg'],
            ['店舗担当者よりご連絡', "エントリーフォームよりご応募ください。\nもしくは店舗に\n直接お電話ください。", 'step-2.svg'],
            ['面談', "店舗にて面談を行います。\n履歴書をご持参ください。", 'step-3.svg'],
            ['採用', "採用の場合、2～3日以内に\n担当者から\nご連絡いたします。", 'step-4.svg'],
        ];
        ?>

        <nav class="p-job-detail__breadcrumb" aria-label="パンくずリスト">
            <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a><span>›</span>
            <a href="<?php echo esc_url(home_url('/recruit/')); ?>">採用情報</a><span>›</span>
            <a href="<?php echo esc_url(home_url('/job/')); ?>">パート・アルバイト募集</a><span>›</span>
            <a href="<?php echo esc_url(home_url('/job/search/')); ?>">検索結果</a><span>›</span>
            <span>行徳店 レジスタッフ募集</span>
        </nav>

        <article class="p-job-detail__article">
            <header class="p-job-detail__header">
                <h1><?php the_title(); ?></h1>
                <?php if ($lead) : ?>
                    <p><?php echo nl2br(esc_html($lead)); ?></p>
                <?php else : ?>
                    <p>お客様対応が好きな方歓迎！<br>レジでの会計対応を中心に、<br>明るく元気に接客していただくお仕事です。<br>未経験の方も、丁寧にサポートしますので<br>安心してご応募ください。</p>
                <?php endif; ?>
            </header>

            <figure class="p-job-detail__hero">
                <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($shop_name . ' ' . $job_type); ?>">
            </figure>

            <section class="p-job-detail__requirements" aria-labelledby="job-requirements-title">
                <h2 id="job-requirements-title" class="p-job-detail__visually-hidden">募集要項</h2>
                <dl>
                    <?php foreach ($detail_rows as $label => $value) : ?>
                        <div class="p-job-detail__row">
                            <dt>
                                <?php if ($label === '待遇/福利厚生') : ?>
                                    待遇/<br class="p-job-detail__mobile-break">福利厚生
                                <?php else : ?>
                                    <?php echo esc_html($label); ?>
                                <?php endif; ?>
                            </dt>
                            <dd><?php echo nl2br(esc_html((string) $value)); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </section>

            <a class="p-job-detail__entry-button" href="<?php echo esc_url($entry_url); ?>">エントリーフォーム</a>
        </article>

        <section class="p-job-detail__process" aria-labelledby="job-process-title">
            <picture class="p-job-detail__process-arrow">
                <source media="(min-width: 1020px)" srcset="<?php echo esc_url($asset_uri . '/process-arrow-pc.svg'); ?>">
                <img src="<?php echo esc_url($asset_uri . '/process-arrow.svg'); ?>" alt="">
            </picture>
            <h2 id="job-process-title">応募の流れ</h2>
            <p class="p-job-detail__process-subtitle">Application Process</p>
            <div class="p-job-detail__steps">
                <?php foreach ($steps as $index => $step) : ?>
                    <article class="p-job-detail__step p-job-detail__step--<?php echo esc_attr($index + 1); ?>">
                        <p class="p-job-detail__step-number"><span>STEP</span> <?php echo esc_html($index + 1); ?></p>
                        <img class="p-job-detail__step-line" src="<?php echo esc_url($asset_uri . '/step-line.svg'); ?>" alt="">
                        <img src="<?php echo esc_url($asset_uri . '/' . $step[2]); ?>" alt="">
                        <h3><?php echo esc_html($step[0]); ?></h3>
                        <p class="p-job-detail__step-copy"><?php echo nl2br(esc_html($step[1])); ?></p>
                        <?php if ($index === 0) : ?>
                            <a class="p-job-detail__step-button" href="<?php echo esc_url($entry_url); ?>">エントリーフォーム</a>
                        <?php endif; ?>
                    </article>
                    <?php if ($index < count($steps) - 1) : ?>
                        <img class="p-job-detail__step-arrow p-job-detail__step-arrow--<?php echo esc_attr($index + 1); ?>" src="<?php echo esc_url($asset_uri . '/step-arrow.svg'); ?>" alt="">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <a class="p-job-detail__entry-button p-job-detail__entry-button--bottom" href="<?php echo esc_url($entry_url); ?>">エントリーフォーム</a>
        </section>
    <?php endwhile; ?>
</main>

<?php get_footer('company'); ?>
