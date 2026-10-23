<?php
/**
 * Template Name: יצירות
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$media = get_terms(['taxonomy' => 'medium', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC']);
$short = memorial_short() ?: memorial_name();
?>
<section class="page-head" style="--c:#7b4a9e">
  <div class="container">
    <?php echo memorial_breadcrumbs_html(); ?>
    <p class="eyebrow">יצירות</p>
    <h1 class="page-head__title">מה שיצר <?php echo esc_html($short); ?></h1>
    <p class="page-head__lede">ציורים, מתקנים, כתבים ויצירות מכל סוג: דברים שנעשו מתוך סקרנות, ותמיד כדי לתת למישהו משהו.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <?php if (is_wp_error($media) || !$media) : ?>
      <p>היצירות יתווספו בקרוב.</p>
    <?php else : ?>
      <div class="filters" data-filters data-mode="sections">
        <div class="filters__group" role="group" aria-label="סוג יצירה">
          <button type="button" class="chip chip--outline is-on" data-filter-section="" style="--c:#1f2b45">הכול</button>
          <?php foreach ($media as $m) : ?>
            <button type="button" class="chip chip--outline" data-filter-section="<?php echo esc_attr($m->slug); ?>" style="--c:#7b4a9e"><?php echo esc_html($m->name); ?> <small><?php echo (int) $m->count; ?></small></button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php foreach ($media as $m) :
          $items = get_posts([
              'post_type' => 'creation', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order ID', 'order' => 'ASC',
              'tax_query' => [['taxonomy' => 'medium', 'field' => 'term_id', 'terms' => $m->term_id]],
          ]);
          if (!$items) {
              continue;
          } ?>
        <section class="create-section" data-section="<?php echo esc_attr($m->slug); ?>" aria-labelledby="m-<?php echo esc_attr($m->slug); ?>">
          <h2 id="m-<?php echo esc_attr($m->slug); ?>" class="subhead"><?php echo esc_html($m->name); ?></h2>
          <?php if ($m->description) : ?><p class="muted"><?php echo esc_html($m->description); ?></p><?php endif; ?>
          <ul class="gallery gallery--create" data-lightbox role="list">
            <?php foreach ($items as $it) :
                $tid = get_post_thumbnail_id($it);
                if (!$tid) {
                    continue;
                }
                $full = wp_get_attachment_image_src($tid, 'full');
                if ($full && $full[1] > 2400) {
                    $full = wp_get_attachment_image_src($tid, 'large');
                }
                if (!$full) {
                    continue;
                }
                $cap = trim(get_the_title($it) . ($it->post_excerpt ? ': ' . $it->post_excerpt : ''));
                $alt = get_post_meta($it->ID, 'creation_alt', true) ?: get_the_title($it);
                $year = get_post_meta($it->ID, 'creation_year', true); ?>
              <li class="gallery__item">
                <a class="g-item" href="<?php echo esc_url($full[0]); ?>" data-caption="<?php echo esc_attr($cap . ($year ? ' (' . $year . ')' : '')); ?>" data-alt="<?php echo esc_attr($alt); ?>" aria-label="<?php echo esc_attr('הגדלת יצירה: ' . get_the_title($it)); ?>">
                  <?php echo wp_get_attachment_image($tid, 'medium_large', false, ['alt' => $alt, 'loading' => 'lazy', 'sizes' => '(max-width:600px) 46vw, (max-width:1000px) 31vw, 280px']); ?>
                  <span class="g-cap"><?php echo esc_html(get_the_title($it)); ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
<?php echo memorial_continue(['creations']);
get_footer();
