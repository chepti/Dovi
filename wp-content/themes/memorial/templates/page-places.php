<?php
/**
 * Template Name: מקומות הנצחה
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$places = get_posts(['post_type' => 'bench', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'meta_value_num', 'meta_key' => 'bench_order', 'order' => 'ASC']);
$visits = get_posts(['post_type' => 'visit', 'post_status' => 'publish', 'numberposts' => 24, 'orderby' => 'date', 'order' => 'DESC']);
$visit_ids = [];
foreach ($visits as $v) {
    if ($t = get_post_thumbnail_id($v)) {
        $visit_ids[] = $t;
    }
}
$pinned = count(memorial_map_points());
$short = memorial_short() ?: memorial_name();
$hp = memorial_head_photo('--c:#7b4a9e;');
?>
<section class="page-head page-head--bench<?php echo $hp['class']; ?>"<?php echo $hp['style']; ?>>
  <div class="container">
    <?php echo memorial_breadcrumbs_html(); ?>
    <p class="eyebrow">מקומות הנצחה</p>
    <h1 class="page-head__title">מקומות שנעשו לזכר <?php echo esc_html($short); ?></h1>
    <p class="page-head__lede">ספסלים, נטיעות ופינות זיכרון. כל מקום הוא הזמנה לעצור רגע, להסתכל, ולזכור.</p>
    <p class="actions">
      <a class="btn btn--sun" href="#visit"><?php echo memorial_icon('camera'); ?>ביקרתם? שלחו תמונה</a>
      <?php if ($pinned >= 2) : ?><a class="btn btn--ghost" href="#map"><?php echo memorial_icon('map-pin'); ?>למפה</a><?php endif; ?>
    </p>
  </div>
</section>

<?php if ($pinned >= 2) : ?>
<section class="section section--tight" id="map" aria-labelledby="map-h">
  <div class="container">
    <h2 id="map-h" class="vh">מפת מקומות ההנצחה</h2>
    <div class="map map--big" id="benches-map" role="region" aria-label="מפה אינטראקטיבית של מקומות ההנצחה"></div>
    <noscript><p class="muted">המפה דורשת JavaScript. רשימת המקומות מופיעה למטה.</p></noscript>
  </div>
</section>
<?php endif; ?>

<section class="section" aria-labelledby="list-h">
  <div class="container">
    <?php echo memorial_section_head('המקומות', 'לאן אפשר ללכת', 'כל כרטיס מוביל לעמוד עם סיפור, תמונות ופרטי הגעה.'); ?>
    <?php if ($places) : ?>
    <div class="grid grid--3">
      <?php foreach ($places as $b) {
          echo memorial_bench_card($b->ID);
      } ?>
    </div>
    <?php else : ?>
      <p>המקומות יתווספו בקרוב.</p>
    <?php endif; ?>
  </div>
</section>

<?php if ($visit_ids) : ?>
<section class="section section--paper2" aria-labelledby="visitors-h">
  <div class="container">
    <?php echo memorial_section_head('מבקרים שלחו', 'ד״ש מהמקומות', 'תמונות שנשלחו אלינו ממי שביקרו.'); ?>
    <?php echo memorial_gallery_html($visit_ids, ['label' => 'ביקור במקום הנצחה', 'class' => 'gallery--small']); ?>
  </div>
</section>
<?php endif; ?>

<section class="section" id="visit" aria-labelledby="visit-h">
  <div class="container container--narrow">
    <p class="eyebrow">ביקרתם?</p>
    <h2 id="visit-h" class="section__title">שלחו לנו תמונה מהמקום</h2>
    <p>המשפחה תשמח לקבל ד״ש. תמונה אחת, כמה מילים, והיא תופיע כאן אחרי אישור.</p>
    <div class="card card--form"><?php echo memorial_visit_form(0, 'visit-page'); ?></div>
  </div>
</section>
<?php echo memorial_continue(['places']);
get_footer();
