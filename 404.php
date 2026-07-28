<?php
get_header();
?>

<main id="page-404" class="c-main p-404">
    <nav class="c-breadcrumb p-404__breadcrumb" aria-label="パンくず">
        <a href="<?php echo esc_url(home_url('/')); ?>">TOP</a>
        <span class="c-breadcrumb__separator" aria-hidden="true"></span>
        <span>404</span>
    </nav>

    <section class="p-404__content" aria-labelledby="page-404-title">
        <div class="p-404__message">
            <h1 id="page-404-title" class="p-404__title">404 Not Found</h1>
            <p class="p-404__text">ページが見つかりません。</p>
        </div>
        <p class="p-404__lead">
            お探しのページは削除されたか、<br>
            URLが変更された可能性があります。<br>
            <span class="p-404__lead-sp">お手数ですが、<br>
                トップページから目的のページを<br>
                お探しください。</span><span class="p-404__lead-desktop">お手数ですが、トップページから目的のページをお探しください。</span>
        </p>
        <a class="p-404__button" href="<?php echo esc_url(home_url('/')); ?>">TOPページへ</a>
    </section>
</main>

<?php
get_footer();
?>