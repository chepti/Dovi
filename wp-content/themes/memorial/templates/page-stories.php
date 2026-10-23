<?php
/**
 * Template Name: סיפורים
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$chapters = memorial_chapters();
$values = memorial_values();
$order = array_flip(array_keys($chapters));
$stories = get_posts(['post_type' => 'story', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC']);
usort($stories, function ($a, $b) use ($order) {
    $ca = get_the_terms($a->ID, 'chapter');
    $cb = get_the_terms($b->ID, 'chapter');
    $ia = ($ca && !is_wp_error($ca)) ? ($order[array_values($ca)[0]->slug] ?? 99) : 99;
    $ib = ($cb && !is_wp_error($cb)) ? ($order[array_values($cb)[0]->slug] ?? 99) : 99;
    return $ia === $ib ? $a->ID <=> $b->ID : $ia <=> $ib;
});
$total = count($stories);
$short = memorial_short() ?: memorial_name();
$timeline = memorial_timeline_html();
$hp = memorial_head_photo();
?>
<section class="page-head<?php echo $hp['class']; ?>"<?php echo $hp['style']; ?>>
  <div class="container">
    <?php echo memorial_breadcrumbs_html(); ?>
    <p class="eyebrow">פרקי חיים</p>
    <h1 class="page-head__title">סיפורים על <?php echo esc_html($short); ?></h1>
    <p class="page-head__lede">חברים, משפחה ומכרים מספרים על רגעים קטנים. אפשר לעבור בין הפרקים, לחפש שם או מילה, או לתת לאתר להפתיע.</p>
    <p class="actions">
      <?php if (memorial_page_exists('share')) : ?><a class="btn btn--sun" href="<?php echo esc_url(memorial_page_url('share')); ?>"><?php echo memorial_icon('pen'); ?>ספרו לנו על <?php echo esc_html($short); ?></a><?php endif; ?>
      <a class="btn btn--ink" href="<?php echo esc_url(home_url('/random-story/')); ?>" rel="nofollow"><?php echo memorial_icon('shuffle'); ?>סיפור אקראי</a>
    </p>
  </div>
</section>

<?php if ($timeline) : ?>
<section class="section section--paper2" aria-labelledby="tl-h">
  <div class="container">
    <?php echo memorial_section_head('מאיפה מתחילים', 'ציר הזמן', 'כל תחנה מובילה אל הסיפורים של אותו פרק.'); ?>
    <?php echo $timeline; ?>
  </div>
</section>
<?php endif; ?>

<section class="section section--tight" id="stories">
  <div class="container">
    <?php echo memorial_section_head('', 'כל הסיפורים', 'סננו לפי פרק או ערך, או חפשו שם ומילה.'); ?>
    <div class="filters" data-filters>
      <div class="filters__search">
        <label class="vh" for="story-search">חיפוש סיפור לפי שם, כותב או מילה</label>
        <?php echo memorial_icon('search'); ?>
        <input id="story-search" type="search" placeholder="חיפוש לפי שם, כותב או מילה" autocomplete="off" data-filter-text>
      </div>
      <?php if ($chapters) : ?>
      <div class="filters__group" role="group" aria-label="פרקי חיים">
        <button type="button" class="chip chip--outline is-on" data-filter-chapter="" style="--c:#1f2b45">הכול <small><?php echo (int) $total; ?></small></button>
        <?php foreach ($chapters as $slug => $c) :
            $t = get_term_by('slug', $slug, 'chapter');
            if (!$t || !$t->count) {
                continue;
            } ?>
          <button type="button" class="chip chip--outline" data-filter-chapter="<?php echo esc_attr($slug); ?>" style="--c:<?php echo esc_attr($c['color']); ?>"><?php echo esc_html($c['name']); ?> <small><?php echo (int) $t->count; ?></small></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php if ($values) : ?>
      <div class="filters__group" role="group" aria-label="ערכים">
        <span class="filters__label">לפי ערך:</span>
        <?php foreach ($values as $slug => $v) : ?>
          <button type="button" class="chip chip--outline" data-filter-value="<?php echo esc_attr($slug); ?>" style="--c:<?php echo esc_attr($v['color']); ?>"><?php echo memorial_icon($v['icon']); ?><?php echo esc_html($v['name']); ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <p class="filters__status" role="status" aria-live="polite" data-filter-status><?php echo (int) $total; ?> סיפורים</p>
    </div>

    <div class="grid grid--3 stories-grid" data-grid>
      <?php foreach ($stories as $s) {
          echo memorial_story_card($s->ID, ['excerpt' => 140]);
      } ?>
    </div>
    <div class="empty" data-empty hidden>
      <p>לא נמצאו סיפורים שמתאימים לסינון. <button type="button" class="linkbtn" data-filter-reset>איפוס הסינון</button></p>
    </div>
  </div>
</section>

<?php if (memorial_page_exists('share')) : ?>
<section class="section section--paper2 tell tell--compact">
  <div class="container tell__strip">
    <div>
      <p class="eyebrow">חסר כאן משהו?</p>
      <h2 class="section__title">הוסיפו סיפור משלכם</h2>
      <p>כל רגע קטן שווה. המשפחה קוראת הכול ומפרסמת.</p>
    </div>
    <a class="btn btn--sun btn--lg" href="<?php echo esc_url(memorial_page_url('share')); ?>"><?php echo memorial_icon('pen'); ?>לכתיבת סיפור</a>
  </div>
</section>
<?php endif; ?>

<?php echo memorial_continue(['stories']);
get_footer();
