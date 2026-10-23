<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header(); ?>
<section class="page-head page-head--plain">
  <div class="container container--narrow">
    <h1 class="page-head__title"><?php echo is_home() ? 'הספדים והנצחה' : esc_html(get_the_archive_title()); ?></h1>
  </div>
</section>
<section class="section">
  <div class="container">
    <?php if (have_posts()) : ?>
      <div class="grid grid--3">
        <?php while (have_posts()) :
            the_post(); ?>
          <article class="card card--tribute">
            <a class="card__link" href="<?php the_permalink(); ?>">
              <?php if (has_post_thumbnail()) : ?><span class="card__media"><?php the_post_thumbnail('medium_large', ['alt' => '']); ?></span><?php endif; ?>
              <span class="card__body">
                <h2 class="card__title"><?php the_title(); ?></h2>
                <p class="card__excerpt"><?php echo esc_html(memorial_excerpt(get_the_ID(), 120)); ?></p>
              </span>
            </a>
          </article>
        <?php endwhile; ?>
      </div>
      <?php the_posts_pagination(['prev_text' => 'הקודם', 'next_text' => 'הבא']); ?>
    <?php else : ?>
      <p>לא נמצא תוכן.</p>
    <?php endif; ?>
  </div>
</section>
<?php get_footer();
