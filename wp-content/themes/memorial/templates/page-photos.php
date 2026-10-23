<?php
/**
 * Template Name: אלבום תמונות
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$albums = get_terms(['taxonomy' => 'album', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC']);
$short = memorial_short() ?: memorial_name();
$hp = memorial_head_photo('--c:#e9a825;');
?>
<section class="page-head<?php echo $hp['class']; ?>"<?php echo $hp['style']; ?>>
  <div class="container">
    <?php echo memorial_breadcrumbs_html(); ?>
    <p class="eyebrow">אלבום</p>
    <h1 class="page-head__title">תמונות מהחיים של <?php echo esc_html($short); ?></h1>
    <p class="page-head__lede">לחצו על תמונה כדי להגדיל.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <?php if (is_wp_error($albums) || !$albums) : ?>
      <p>התמונות יתווספו בקרוב.</p>
    <?php else : ?>
      <div class="filters" data-filters data-mode="sections">
        <div class="filters__group" role="group" aria-label="אלבומים">
          <button type="button" class="chip chip--outline is-on" data-filter-section="" style="--c:#1f2b45">הכול</button>
          <?php foreach ($albums as $a) : ?>
            <button type="button" class="chip chip--outline" data-filter-section="<?php echo esc_attr($a->slug); ?>" style="--c:#e9a825"><?php echo esc_html($a->name); ?> <small><?php echo (int) $a->count; ?></small></button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php foreach ($albums as $a) :
          $ids = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'menu_order ID', 'order' => 'ASC', 'tax_query' => [['taxonomy' => 'album', 'field' => 'term_id', 'terms' => $a->term_id]]]);
          if (!$ids) {
              continue;
          } ?>
        <section class="create-section" data-section="<?php echo esc_attr($a->slug); ?>" aria-labelledby="al-<?php echo esc_attr($a->slug); ?>">
          <h2 id="al-<?php echo esc_attr($a->slug); ?>" class="subhead"><?php echo esc_html($a->name); ?></h2>
          <?php if ($a->description) : ?><p class="muted"><?php echo esc_html($a->description); ?></p><?php endif; ?>
          <?php echo memorial_gallery_html($ids, ['label' => $a->name]); ?>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php if (memorial_page_exists('share')) : ?>
<section class="section section--paper2 tell tell--compact">
  <div class="container tell__strip">
    <div>
      <p class="eyebrow">יש לכם תמונה?</p>
      <h2 class="section__title">שלחו לנו עוד תמונות מהחיים</h2>
      <p>אפשר לצרף תמונות לסיפור, והמשפחה תוסיף אותן לאלבום.</p>
    </div>
    <a class="btn btn--sun btn--lg" href="<?php echo esc_url(memorial_page_url('share')); ?>"><?php echo memorial_icon('camera'); ?>שליחת סיפור ותמונות</a>
  </div>
</section>
<?php endif; ?>
<?php echo memorial_continue(['photos']);
get_footer();
