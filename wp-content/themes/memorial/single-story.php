<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
while (have_posts()) :
    the_post();
    $id = get_the_ID();
    $d = memorial_story_data($id);
    $ch = $d['chapters'] ? memorial_chapter_meta($d['chapters'][0]) : null;
    $color = $ch ? $ch['color'] : '#4a5672';
    $gallery = memorial_gallery_ids($id, 'story_gallery');
    list($prev, $next) = memorial_story_neighbors($id);
    $title = get_the_title();
    $short = memorial_short();
    ?>
<article class="story" style="--c:<?php echo esc_attr($color); ?>">
  <header class="story__head">
    <div class="container container--narrow">
      <?php echo memorial_breadcrumbs_html(); ?>
      <div class="story__chips">
        <?php foreach ($d['chapters'] as $t) :
            $m = memorial_chapter_meta($t); ?>
          <a class="chip" style="--c:<?php echo esc_attr($m['color']); ?>" href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo memorial_icon($m['icon']); ?><?php echo esc_html($t->name); ?></a>
        <?php endforeach; ?>
      </div>
      <h1 class="story__title"><?php echo esc_html($title); ?></h1>
      <p class="story__meta">
        <?php echo memorial_byline($d); ?>
      </p>
    </div>
  </header>

  <div class="container container--narrow story__body">
    <?php if (has_post_thumbnail()) : ?>
      <figure class="story__figure">
        <?php the_post_thumbnail('large', ['alt' => $title]); ?>
        <?php if ($cap = get_the_post_thumbnail_caption()) : ?><figcaption><?php echo esc_html($cap); ?></figcaption><?php endif; ?>
      </figure>
    <?php endif; ?>

    <div class="prose prose--story"><?php the_content(); ?></div>

  <?php if ($prev || $next) : ?>
  <nav class="story__nav" aria-label="סיפורים נוספים בפרק">
    <?php if ($prev) : ?>
      <a class="story__navcard story__navcard--prev" href="<?php echo esc_url(get_permalink($prev)); ?>" rel="prev"><span class="navcard__dir"><?php echo memorial_icon('chevron-right'); ?>הקודם<?php echo $ch ? ' ב' . esc_html($ch['name']) : ''; ?></span><strong><?php echo esc_html(get_the_title($prev)); ?></strong></a>
    <?php endif; ?>
    <?php if ($next) : ?>
      <a class="story__navcard story__navcard--next" href="<?php echo esc_url(get_permalink($next)); ?>" rel="next"><span class="navcard__dir">הבא<?php echo $ch ? ' ב' . esc_html($ch['name']) : ''; ?><?php echo memorial_icon('chevron-left'); ?></span><strong><?php echo esc_html(get_the_title($next)); ?></strong></a>
    <?php endif; ?>
  </nav>
  <?php endif; ?>

    <?php if ($gallery) : ?>
      <div class="story__gallery"><?php echo memorial_gallery_html($gallery, ['class' => 'gallery--small', 'label' => $title]); ?></div>
    <?php endif; ?>

    <?php if ($d['source'] || $d['related']) : ?>
      <div class="story__links">
        <?php if ($d['related'] && get_post_status($d['related']) === 'publish') : ?>
          <a class="btn btn--ghost" href="<?php echo esc_url(get_permalink($d['related'])); ?>"><?php echo memorial_icon('book'); ?>להספד המלא</a>
        <?php endif; ?>
        <?php if ($d['source']) : ?>
          <a class="btn btn--ghost" href="<?php echo esc_url($d['source']); ?>" target="_blank" rel="noopener noreferrer"><?php echo memorial_icon('external'); ?>למקור</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ($d['values']) : ?>
      <p class="story__values">
        <?php foreach ($d['values'] as $v) :
            $vm = memorial_value_meta($v); ?>
          <a class="chip chip--outline" style="--c:<?php echo esc_attr($vm['color']); ?>" href="<?php echo esc_url(get_term_link($v)); ?>"><?php echo memorial_icon($vm['icon']); ?><?php echo esc_html($v->name); ?></a>
        <?php endforeach; ?>
      </p>
    <?php endif; ?>

    <?php echo memorial_share_bar(get_permalink(), $title . ' | סיפור על ' . memorial_name(), 'שיתוף'); ?>
  </div>

  <?php
  // עוד סיפורים באותו ערך (או באותו פרק)
  $more_args = ['post_type' => 'story', 'post_status' => 'publish', 'numberposts' => 3, 'post__not_in' => [$id], 'orderby' => 'rand'];
  if ($d['values']) {
      $more_args['tax_query'] = [['taxonomy' => 'value', 'field' => 'term_id', 'terms' => $d['values'][0]->term_id]];
      $more_label = 'עוד סיפורים על ' . $d['values'][0]->name;
  } elseif ($d['chapters']) {
      $more_args['tax_query'] = [['taxonomy' => 'chapter', 'field' => 'term_id', 'terms' => $d['chapters'][0]->term_id]];
      $more_label = 'עוד מפרק ' . $d['chapters'][0]->name;
  } else {
      $more_label = 'עוד סיפורים';
  }
  $more = get_posts($more_args);
  if ($more) : ?>
  <section class="section section--paper2 story__more" aria-labelledby="more-h">
    <div class="container">
      <?php echo memorial_section_head('ממשיכים לקרוא', $more_label, '', memorial_page_exists('stories') ? ['url' => memorial_page_url('stories'), 'label' => 'כל הסיפורים'] : null); ?>
      <div class="grid grid--3">
        <?php foreach ($more as $m) {
            echo memorial_story_card($m->ID, ['excerpt' => 120]);
        } ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (memorial_page_exists('share')) : ?>
  <section class="section tell tell--compact" aria-labelledby="tell-h">
    <div class="container tell__strip">
      <div>
        <p class="eyebrow">הכרתם?</p>
        <h2 id="tell-h" class="section__title">גם לכם יש סיפור על <?php echo esc_html($short ?: memorial_name()); ?></h2>
        <p>משפט אחד, תמונה, זיכרון קטן. המשפחה קוראת הכול ומפרסמת באתר.</p>
      </div>
      <a class="btn btn--sun btn--lg" href="<?php echo esc_url(memorial_page_url('share')); ?>"><?php echo memorial_icon('pen'); ?>ספרו לנו</a>
    </div>
  </section>
  <?php endif; ?>
</article>

<?php echo memorial_continue(['stories']); ?>
<?php endwhile;
get_footer();
