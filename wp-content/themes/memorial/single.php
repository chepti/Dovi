<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
while (have_posts()) :
    the_post();
    $id = get_the_ID();
    $cats = get_the_category();
    $cat = $cats ? $cats[0] : null;
    $sib = [];
    if ($cat) {
        $sib = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 3, 'category' => $cat->term_id, 'post__not_in' => [$id], 'orderby' => 'rand']);
    }
    ?>
<article class="story story--post" style="--c:#4a5672">
  <header class="story__head">
    <div class="container container--narrow">
      <?php echo memorial_breadcrumbs_html(); ?>
      <?php if ($cat && memorial_page_exists('tributes')) : ?><div class="story__chips"><a class="chip" href="<?php echo esc_url(memorial_page_url('tributes') . '#' . $cat->slug); ?>"><?php echo esc_html($cat->name); ?></a></div><?php endif; ?>
      <h1 class="story__title"><?php the_title(); ?></h1>
      <p class="story__meta"><span class="by"><?php echo memorial_icon('calendar'); ?><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j בF Y')); ?></time></span></p>
    </div>
  </header>
  <div class="container container--narrow story__body">
    <?php if (has_post_thumbnail()) : ?>
      <figure class="story__figure"><?php the_post_thumbnail('large'); ?></figure>
    <?php endif; ?>
    <div class="prose prose--story"><?php the_content(); ?></div>
    <?php echo memorial_share_bar(get_permalink(), get_the_title() . ' | ' . memorial_name(), 'שתפו'); ?>
  </div>

  <?php if ($sib) : ?>
  <section class="section section--paper2">
    <div class="container">
      <?php echo memorial_section_head('עוד מילים', 'ממשיכים לקרוא', '', memorial_page_exists('tributes') ? ['url' => memorial_page_url('tributes'), 'label' => 'כל ההספדים'] : null); ?>
      <div class="grid grid--3">
        <?php foreach ($sib as $p) : ?>
          <article class="card card--tribute">
            <a class="card__link" href="<?php echo esc_url(get_permalink($p)); ?>">
              <span class="card__body">
                <h3 class="card__title"><?php echo esc_html(get_the_title($p)); ?></h3>
                <p class="card__excerpt"><?php echo esc_html(memorial_excerpt($p, 120)); ?></p>
                <span class="card__foot"><span class="card__more">לקריאה <?php echo memorial_icon('arrow-left'); ?></span></span>
              </span>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</article>
<?php echo memorial_continue(['tributes']);
endwhile;
get_footer();
