<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$term = get_queried_object();
$is_chapter = $term->taxonomy === 'chapter';
$meta = $is_chapter ? memorial_chapter_meta($term) : memorial_value_meta($term);
$all = $is_chapter ? memorial_chapters() : memorial_values();
$desc = $term->description ?: $meta['desc'];
?>
<section class="page-head" style="--c:<?php echo esc_attr($meta['color']); ?>">
  <div class="container">
    <?php echo memorial_breadcrumbs_html(); ?>
    <p class="eyebrow"><?php echo $is_chapter ? 'פרק בחיים' : 'ערך'; ?></p>
    <h1 class="page-head__title"><span class="page-head__ic"><?php echo memorial_icon($meta['icon']); ?></span><?php echo esc_html($term->name); ?></h1>
    <?php if ($desc) : ?><p class="page-head__lede"><?php echo esc_html($desc); ?></p><?php endif; ?>
    <?php if (!$is_chapter && !empty($meta['quote'])) : ?>
      <blockquote class="pull"><?php echo esc_html($meta['quote']); ?><?php if (!empty($meta['who'])) : ?><cite><?php echo esc_html($meta['who']); ?></cite><?php endif; ?></blockquote>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="chipbar" aria-label="מעבר בין <?php echo $is_chapter ? 'פרקים' : 'ערכים'; ?>">
      <?php foreach ($all as $slug => $c) :
          $t = get_term_by('slug', $slug, $term->taxonomy);
          if (!$t || !$t->count) {
              continue;
          } ?>
        <a class="chip chip--outline<?php echo $slug === $term->slug ? ' is-on' : ''; ?>" style="--c:<?php echo esc_attr($c['color']); ?>" href="<?php echo esc_url(get_term_link($t)); ?>"<?php echo $slug === $term->slug ? ' aria-current="true"' : ''; ?>><?php echo esc_html($c['name']); ?> <small><?php echo (int) $t->count; ?></small></a>
      <?php endforeach; ?>
    </div>
    <?php if (have_posts()) : ?>
      <div class="grid grid--3">
        <?php while (have_posts()) :
            the_post();
            echo memorial_story_card(get_the_ID(), ['excerpt' => 150]);
        endwhile; ?>
      </div>
      <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => 'הקודם', 'next_text' => 'הבא']); ?>
    <?php else : ?>
      <p>עוד אין כאן סיפורים.<?php if (memorial_page_exists('share')) : ?> <a href="<?php echo esc_url(memorial_page_url('share')); ?>">אפשר להיות הראשונים לספר</a>.<?php endif; ?></p>
    <?php endif; ?>
  </div>
</section>

<?php
if ($is_chapter) {
    // תמונות מהאלבום שהוגדר לו אותו שם או מזהה (slug) כמו הפרק
    $photo_ids = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 36, 'fields' => 'ids', 'orderby' => 'menu_order ID', 'order' => 'ASC', 'tax_query' => [['taxonomy' => 'album', 'field' => 'slug', 'terms' => [$term->slug]]]]);
    if (count($photo_ids) >= 3) : ?>
<section class="section section--paper2 photostrip" aria-labelledby="ps-h">
  <div class="container">
    <?php echo memorial_section_head('מהאלבום', 'תמונות מהתקופה', '', memorial_page_exists('photos') ? ['url' => memorial_page_url('photos'), 'label' => 'לאלבום המלא'] : null); ?>
    <div class="strip" data-strip>
      <button type="button" class="strip__btn strip__btn--next" data-strip-next aria-label="התמונות הבאות"><?php echo memorial_icon('chevron-left'); ?></button>
      <button type="button" class="strip__btn strip__btn--prev" data-strip-prev aria-label="התמונות הקודמות"><?php echo memorial_icon('chevron-right'); ?></button>
      <?php echo memorial_gallery_html($photo_ids, ['class' => 'gallery--strip', 'label' => $term->name, 'captions' => false, 'size' => 'medium_large']); ?>
    </div>
  </div>
</section>
    <?php endif;
}
echo memorial_continue(['stories']);
get_footer();
