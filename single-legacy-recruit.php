<?php
/** Original recruitment announcements that are not part-time vacancy records. */
get_header('company');
?>
<main>
    <?php while (have_posts()) : the_post(); ?>
        <article class="l-container">
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer('company'); ?>
