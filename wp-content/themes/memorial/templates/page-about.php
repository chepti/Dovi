<?php
/**
 * Template Name: אודות
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
while (have_posts()) :
    the_post();
    $values = memorial_values();
    $hero_id = memorial_hero_image_id();
    $name = memorial_name();
    $short = memorial_short() ?: $name;
    $timeline = memorial_timeline_html();
    $hp = memorial_head_photo();
    ?>
<section class="page-head page-head--about<?php echo $hp['class']; ?>"<?php echo $hp['style']; ?>>
  <div class="container about__head">
    <div>
      <?php echo memorial_breadcrumbs_html(); ?>
      <p class="eyebrow">מי היה</p>
      <h1 class="page-head__title"><?php echo esc_html(get_the_title()); ?></h1>
      <?php if ($lede = trim((string) memorial_opt('hero_lede'))) : ?><p class="page-head__lede"><?php echo esc_html($lede); ?></p><?php endif; ?>
      <p class="actions">
        <?php if (memorial_page_exists('stories')) : ?><a class="btn btn--sun" href="<?php echo esc_url(memorial_page_url('stories')); ?>"><?php echo memorial_icon('book'); ?>סיפורים על <?php echo esc_html($short); ?></a><?php endif; ?>
        <?php if (memorial_page_exists('photos')) : ?><a class="btn btn--ghost" href="<?php echo esc_url(memorial_page_url('photos')); ?>"><?php echo memorial_icon('image'); ?>אלבום תמונות</a><?php endif; ?>
      </p>
    </div>
    <?php if ($hero_id) : ?>
    <figure class="about__portrait">
      <?php echo wp_get_attachment_image($hero_id, 'large', false, ['alt' => $name, 'loading' => 'eager']); ?>
    </figure>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container container--narrow">
    <div class="prose prose--story prose--about"><?php the_content(); ?></div>
  </div>
</section>

<?php if ($timeline) : ?>
<section class="section section--paper2" aria-labelledby="tl-h">
  <div class="container">
    <?php echo memorial_section_head('דרכו', 'ציר זמן', 'כל תחנה מובילה אל הסיפורים של אותו פרק.'); ?>
    <?php echo $timeline; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($values) : ?>
<section class="section section--tight" aria-labelledby="vals-h">
  <div class="container container--narrow">
    <h2 id="vals-h" class="subhead">לקרוא סיפורים לפי ערך</h2>
    <p class="story__values">
      <?php foreach ($values as $slug => $v) :
          $link = get_term_link($slug, 'value');
          if (is_wp_error($link)) {
              continue;
          } ?>
        <a class="chip chip--outline" style="--c:<?php echo esc_attr($v['color']); ?>" href="<?php echo esc_url($link); ?>"><?php echo memorial_icon($v['icon']); ?><?php echo esc_html($v['name']); ?></a>
      <?php endforeach; ?>
    </p>
  </div>
</section>
<?php endif; ?>
<?php echo memorial_continue(['about']);
endwhile;
get_footer();
