<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
while (have_posts()) :
    the_post();
    $id = get_the_ID();
    $title = get_the_title();
    $region = get_post_meta($id, 'bench_region', true);
    $lat = get_post_meta($id, 'bench_lat', true);
    $lng = get_post_meta($id, 'bench_lng', true);
    $approx = get_post_meta($id, 'bench_approx', true) === '1';
    $dirs = get_post_meta($id, 'bench_directions', true);
    $gallery = memorial_gallery_ids($id, 'bench_gallery');
    $thumb = get_post_thumbnail_id();
    $hero = $thumb ?: ($gallery ? $gallery[0] : 0);
    $visits = get_posts(['post_type' => 'visit', 'post_status' => 'publish', 'numberposts' => 40, 'meta_key' => 'visit_bench', 'meta_value' => $id, 'orderby' => 'date', 'order' => 'DESC']);
    $visit_ids = [];
    foreach ($visits as $v) {
        if ($t = get_post_thumbnail_id($v)) {
            $visit_ids[] = $t;
        }
    }
    $others = get_posts(['post_type' => 'bench', 'post_status' => 'publish', 'numberposts' => 3, 'post__not_in' => [$id], 'orderby' => 'rand']);
    ?>
<article class="bench">
  <header class="page-head page-head--bench" style="--c:#7b4a9e">
    <div class="container">
      <?php echo memorial_breadcrumbs_html(); ?>
      <?php if ($region) : ?><p class="eyebrow"><?php echo memorial_icon('map-pin'); ?> <?php echo esc_html($region); ?></p><?php endif; ?>
      <h1 class="page-head__title"><?php echo esc_html($title); ?></h1>
      <p class="page-head__lede"><?php echo esc_html(memorial_excerpt($id, 170)); ?></p>
    </div>
  </header>

  <div class="container bench__layout">
    <div class="bench__main">
      <?php if ($hero) : ?>
        <figure class="bench__hero"><?php echo wp_get_attachment_image($hero, 'large', false, ['alt' => $title, 'loading' => 'eager', 'fetchpriority' => 'high']); ?></figure>
      <?php endif; ?>
      <div class="prose prose--story"><?php the_content(); ?></div>
      <?php if ($vid = get_post_meta($id, 'bench_video', true)) :
          $vsrc = 'https://www.facebook.com/plugins/video.php?show_text=false&t=0&href=' . rawurlencode($vid); ?>
        <div class="vframe" data-vframe data-title="<?php echo esc_attr('סרטון: ' . $title); ?>" data-src="<?php echo esc_url($vsrc); ?>">
          <button type="button" class="vframe__btn"><?php echo memorial_icon('play'); ?><span>לצפייה בסרטון</span><small>הסרטון נטען מפייסבוק רק אחרי הלחיצה</small></button>
          <noscript><a href="<?php echo esc_url($vid); ?>" target="_blank" rel="noopener noreferrer">לצפייה בסרטון בפייסבוק</a></noscript>
        </div>
      <?php endif; ?>
      <?php if ($gallery) : ?>
        <h2 class="subhead">תמונות</h2>
        <?php echo memorial_gallery_html($gallery, ['label' => $title]); ?>
      <?php endif; ?>
      <?php if ($visit_ids) : ?>
        <h2 class="subhead">מבקרים שלחו</h2>
        <?php echo memorial_gallery_html($visit_ids, ['label' => 'ביקור ב' . $title, 'class' => 'gallery--small']); ?>
      <?php endif; ?>
    </div>

    <aside class="bench__side" aria-label="מידע על המקום">
      <?php if ($lat && $lng) : ?>
        <div class="map" id="bench-map" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>" data-approx="<?php echo $approx ? '1' : '0'; ?>" data-title="<?php echo esc_attr($title); ?>" role="img" aria-label="מפה: מיקום <?php echo esc_attr($title); ?>"></div>
        <p class="map__links">
          <a class="btn btn--ink btn--sm" href="<?php echo esc_url('https://www.google.com/maps/search/?api=1&query=' . rawurlencode($lat . ',' . $lng)); ?>" target="_blank" rel="noopener noreferrer"><?php echo memorial_icon('map-pin'); ?>מפות גוגל</a>
          <a class="btn btn--ghost btn--sm" href="<?php echo esc_url('https://waze.com/ul?ll=' . rawurlencode($lat . ',' . $lng) . '&navigate=yes'); ?>" target="_blank" rel="noopener noreferrer"><?php echo memorial_icon('navigation'); ?>Waze</a>
        </p>
      <?php endif; ?>
      <?php if ($dirs) : ?>
        <div class="card card--note">
          <h2 class="card__h">איך מגיעים</h2>
          <p><?php echo nl2br(esc_html($dirs)); ?></p>
        </div>
      <?php endif; ?>
      <div class="card card--note card--cta">
        <h2 class="card__h">ביקרתם במקום הזה?</h2>
        <p>המשפחה תשמח לקבל ד״ש. שלחו תמונה, והיא תופיע כאן.</p>
        <a class="btn btn--sun btn--sm" href="#visit"><?php echo memorial_icon('camera'); ?>שליחת תמונה</a>
      </div>
      <?php echo memorial_share_bar(get_permalink(), $title . ' | מקום הנצחה לזכר ' . memorial_name(), 'שתפו'); ?>
    </aside>
  </div>

  <section class="section section--paper2" id="visit" aria-labelledby="visit-h">
    <div class="container container--narrow">
      <p class="eyebrow">ביקרתם?</p>
      <h2 id="visit-h" class="section__title">שלחו לנו תמונה מהמקום</h2>
      <div class="card card--form"><?php echo memorial_visit_form($id, 'visit-bench'); ?></div>
    </div>
  </section>

  <?php if ($others) : ?>
  <section class="section">
    <div class="container">
      <?php echo memorial_section_head('עוד מקומות', 'ממשיכים בדרך', '', memorial_page_exists('places') ? ['url' => memorial_page_url('places'), 'label' => 'לכל המקומות'] : null); ?>
      <div class="grid grid--3"><?php foreach ($others as $o) {
          echo memorial_bench_card($o->ID);
      } ?></div>
    </div>
  </section>
  <?php endif; ?>
</article>
<?php echo memorial_continue(['places']);
endwhile;
get_footer();
