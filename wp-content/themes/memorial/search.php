<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header(); ?>
<section class="page-head page-head--plain">
  <div class="container container--narrow">
    <?php echo memorial_breadcrumbs_html(); ?>
    <h1 class="page-head__title">חיפוש: <?php echo esc_html(get_search_query()); ?></h1>
    <form class="searchform" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <label class="vh" for="ssearch">חיפוש באתר</label>
      <input id="ssearch" type="search" name="s" placeholder="חיפוש סיפור, שם או מקום" value="<?php echo esc_attr(get_search_query()); ?>">
      <button class="btn btn--sun" type="submit"><?php echo memorial_icon('search'); ?>חיפוש</button>
    </form>
  </div>
</section>
<section class="section">
  <div class="container">
    <?php if (have_posts()) : ?>
      <p class="muted"><?php echo (int) $wp_query->found_posts; ?> תוצאות</p>
      <div class="grid grid--3">
        <?php while (have_posts()) :
            the_post();
            if (get_post_type() === 'story') {
                echo memorial_story_card(get_the_ID(), ['excerpt' => 140]);
            } elseif (get_post_type() === 'bench') {
                echo memorial_bench_card(get_the_ID());
            } else { ?>
              <article class="card card--tribute">
                <a class="card__link" href="<?php the_permalink(); ?>">
                  <span class="card__body">
                    <h2 class="card__title"><?php the_title(); ?></h2>
                    <p class="card__excerpt"><?php echo esc_html(memorial_excerpt(get_the_ID(), 130)); ?></p>
                  </span>
                </a>
              </article>
            <?php }
        endwhile; ?>
      </div>
      <?php the_posts_pagination(['prev_text' => 'הקודם', 'next_text' => 'הבא']); ?>
    <?php else : ?>
      <div class="center">
        <p>לא מצאנו תוצאות. אפשר לנסות מילה אחרת, או לעבור בין הסיפורים לפי פרקי חיים.</p>
        <?php if (memorial_page_exists('stories')) : ?><p class="actions center"><a class="btn btn--sun" href="<?php echo esc_url(memorial_page_url('stories')); ?>">לכל הסיפורים</a></p><?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php get_footer();
