<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
while (have_posts()) :
    the_post(); ?>
<section class="page-head page-head--plain">
  <div class="container container--narrow">
    <?php echo memorial_breadcrumbs_html(); ?>
    <h1 class="page-head__title"><?php the_title(); ?></h1>
  </div>
</section>
<section class="section">
  <div class="container container--narrow">
    <div class="prose"><?php the_content(); ?></div>
  </div>
</section>
<?php echo memorial_continue([]);
endwhile;
get_footer();
