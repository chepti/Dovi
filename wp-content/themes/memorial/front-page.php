<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$name = memorial_name();
$short = memorial_short();
$honor = trim((string) memorial_opt('honorific'));
$hero_id = memorial_hero_image_id();
$lede = trim((string) memorial_opt('hero_lede'));
$roles = array_values(array_filter(array_map('trim', explode(',', (string) memorial_opt('hero_roles')))));
$by = (string) memorial_opt('birth_year');
$dd = (string) memorial_opt('death_date');
$dy = $dd ? substr($dd, 0, 4) : '';
$dates = ($by && $dy) ? $by . ' – ' . $dy : '';
$values = memorial_values();
$chapters = memorial_chapters();
$story_day = memorial_story_of_day();
$latest = get_posts(['post_type' => 'story', 'post_status' => 'publish', 'numberposts' => 3, 'orderby' => 'date', 'order' => 'DESC', 'exclude' => $story_day ? [$story_day] : []]);
$tributes = get_posts([
    'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 3, 'orderby' => 'date', 'order' => 'DESC',
    'meta_query' => [['key' => '_thumbnail_id', 'compare' => 'EXISTS']],
]);
$total_stories = (int) wp_count_posts('story')->publish;
$stories_url = memorial_page_url('stories');
?>
<section class="hero">
  <div class="container hero__grid">
    <div class="hero__text">
      <p class="eyebrow">אתר הזיכרון</p>
      <h1 class="hero__title"><?php echo esc_html($name); ?><?php if ($honor) : ?> <small><?php echo esc_html($honor); ?></small><?php endif; ?></h1>
      <?php if ($dates) : ?><p class="hero__dates"><?php echo esc_html($dates); ?></p><?php endif; ?>
      <?php if ($roles) : ?>
        <p class="hero__roles"><?php foreach ($roles as $i => $r) : ?><?php if ($i) : ?><i aria-hidden="true"></i><?php endif; ?><span><?php echo esc_html($r); ?></span><?php endforeach; ?></p>
      <?php endif; ?>
      <?php if ($lede) : ?><p class="hero__lede"><?php echo esc_html($lede); ?></p><?php endif; ?>
      <div class="hero__cta">
        <?php if (memorial_page_exists('stories')) : ?>
          <a class="btn btn--sun btn--lg" href="<?php echo esc_url($stories_url); ?>"><?php echo memorial_icon('book'); ?>קראו סיפורים<span class="btn__count"><?php echo (int) $total_stories; ?></span></a>
        <?php endif; ?>
        <?php if ($total_stories > 1) : ?>
          <a class="link-shuffle" href="<?php echo esc_url(home_url('/random-story/')); ?>" rel="nofollow"><?php echo memorial_icon('shuffle'); ?>סיפור אקראי</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="hero__art">
      <?php if ($hero_id) : ?>
        <div class="hero__figure"><?php echo wp_get_attachment_image($hero_id, 'large', false, ['alt' => $name, 'fetchpriority' => 'high', 'loading' => 'eager']); ?></div>
      <?php else : ?>
        <div class="hero__figure hero__figure--illus"><?php echo memorial_illus_bench(); ?></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($values) : ?>
<section class="section values" aria-labelledby="values-h">
  <div class="container">
    <?php echo memorial_section_head('מה היה חשוב', 'הערכים שמלווים את הסיפורים', 'כל ערך מוביל אל הסיפורים שמספרים עליו.'); ?>
    <div class="grid grid--4 values__grid">
      <?php foreach ($values as $slug => $v) :
          $link = get_term_link($slug, 'value');
          if (is_wp_error($link)) {
              continue;
          } ?>
        <a class="card card--value" href="<?php echo esc_url($link); ?>" style="--c:<?php echo esc_attr($v['color']); ?>">
          <span class="value__ic"><?php echo memorial_icon($v['icon']); ?></span>
          <span class="value__name"><?php echo esc_html($v['name']); ?></span>
          <?php if ($v['quote']) : ?><q class="value__quote"><?php echo esc_html($v['quote']); ?></q><?php endif; ?>
          <?php if ($v['who']) : ?><span class="value__who"><?php echo esc_html($v['who']); ?></span><?php endif; ?>
          <span class="value__go">הסיפורים על <?php echo esc_html($v['name']); ?> <?php echo memorial_icon('arrow-left'); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($story_day) :
    $sd = memorial_story_data($story_day);
    $sd_img = get_post_thumbnail_id($story_day);
    $sd_ch = $sd['chapters'] ? memorial_chapter_meta($sd['chapters'][0]) : null; ?>
<section class="section section--paper2 featured" aria-labelledby="featured-h">
  <div class="container">
    <?php echo memorial_section_head('סיפור היום', 'רגע קטן שכדאי לזכור', '', memorial_page_exists('stories') ? ['url' => $stories_url, 'label' => 'כל הסיפורים'] : null); ?>
    <div class="featured__grid">
      <article class="featured__main" style="--c:<?php echo esc_attr($sd_ch ? $sd_ch['color'] : '#e8743b'); ?>">
        <?php if ($sd_img) : ?><a class="featured__img" href="<?php echo esc_url(get_permalink($story_day)); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_get_attachment_image($sd_img, 'large', false, ['loading' => 'lazy', 'alt' => '']); ?></a><?php endif; ?>
        <div class="featured__body">
          <?php if ($sd_ch) : ?><span class="chip"><?php echo esc_html($sd_ch['name']); ?></span><?php endif; ?>
          <h3 class="featured__title"><a href="<?php echo esc_url(get_permalink($story_day)); ?>"><?php echo esc_html(get_the_title($story_day)); ?></a></h3>
          <blockquote class="featured__quote"><?php echo esc_html(memorial_excerpt($story_day, 330)); ?></blockquote>
          <p class="featured__by"><?php echo memorial_byline($sd); ?></p>
          <p class="featured__actions">
            <a class="btn btn--sun" href="<?php echo esc_url(get_permalink($story_day)); ?>">לסיפור המלא<?php echo memorial_icon('arrow-left'); ?></a>
            <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/random-story/')); ?>" rel="nofollow"><?php echo memorial_icon('shuffle'); ?>סיפור אחר</a>
          </p>
        </div>
      </article>
      <?php if ($latest) : ?>
      <div class="featured__side">
        <h3 class="featured__side-title">חדש באתר</h3>
        <div class="stack">
          <?php foreach ($latest as $s) {
              echo memorial_story_card($s->ID, ['excerpt' => 90, 'image' => false, 'class' => 'card--compact']);
          } ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
$chapter_rows = [];
foreach ($chapters as $slug => $c) {
    $term = get_term_by('slug', $slug, 'chapter');
    if ($term && $term->count) {
        $chapter_rows[] = [$c, $term];
    }
}
if ($chapter_rows) : ?>
<section class="section chapters" aria-labelledby="chapters-h">
  <div class="container">
    <?php echo memorial_section_head('מסע בין פרקי החיים', 'פרקים בחיים של ' . ($short ?: $name), 'בחרו פרק, וקראו מה מספרים מי שהיו שם.'); ?>
    <ol class="path">
      <?php foreach ($chapter_rows as list($c, $term)) : ?>
        <li style="--c:<?php echo esc_attr($c['color']); ?>">
          <a href="<?php echo esc_url(get_term_link($term)); ?>"<?php echo $c['desc'] ? ' data-tip="' . esc_attr($c['desc']) . '"' : ''; ?>>
            <span class="path__n"><?php echo memorial_icon($c['icon']); ?></span>
            <span class="path__name"><?php echo esc_html($c['name']); ?></span>
            <span class="path__count"><?php echo (int) $term->count; ?> סיפורים</span>
          </a>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<?php
$album_ids = memorial_album_photo_ids(8);
if (count($album_ids) >= 3) : ?>
<section class="section family" aria-labelledby="family-h">
  <div class="container">
    <?php echo memorial_section_head('מהאלבום', 'תמונות מהחיים', '', memorial_page_exists('photos') ? ['url' => memorial_page_url('photos'), 'label' => 'לאלבום המלא'] : null); ?>
    <?php echo memorial_gallery_html($album_ids, ['class' => 'gallery--big']); ?>
  </div>
</section>
<?php endif; ?>

<?php
$places = get_posts(['post_type' => 'bench', 'post_status' => 'publish', 'numberposts' => 6, 'orderby' => 'meta_value_num', 'meta_key' => 'bench_order', 'order' => 'ASC']);
if ($places) :
    $p_img = get_post_thumbnail_id($places[0]);
    if (!$p_img) {
        $pg = memorial_gallery_ids($places[0]->ID, 'bench_gallery');
        $p_img = $pg ? $pg[0] : 0;
    } ?>
<section class="section benches-teaser" aria-labelledby="benches-h">
  <div class="container benches-teaser__grid">
    <div class="benches-teaser__img">
      <?php echo $p_img ? wp_get_attachment_image($p_img, 'large', false, ['loading' => 'lazy', 'alt' => get_the_title($places[0])]) : memorial_illus_bench(); ?>
    </div>
    <div class="benches-teaser__text">
      <p class="eyebrow">מקומות הנצחה</p>
      <h2 id="benches-h" class="section__title">מקומות שנעשו לזכר <?php echo esc_html($short ?: $name); ?></h2>
      <p>ספסלים, נטיעות ופינות זיכרון, על המפה ובשטח. אפשר לבקר, לעצור רגע, ולשלוח לנו תמונה מהביקור.</p>
      <ul class="benches-teaser__list">
        <?php foreach ($places as $b) : ?>
          <li><a href="<?php echo esc_url(get_permalink($b)); ?>"><?php echo memorial_icon('map-pin'); ?><?php echo esc_html(get_the_title($b)); ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php if (memorial_page_exists('places')) : ?>
        <p class="actions">
          <a class="btn btn--sun" href="<?php echo esc_url(memorial_page_url('places')); ?>"><?php echo memorial_icon('map-pin'); ?>המפה והמקומות</a>
          <a class="btn btn--ghost" href="<?php echo esc_url(memorial_page_url('places')); ?>#visit"><?php echo memorial_icon('camera'); ?>ביקרתם? שלחו תמונה</a>
        </p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
$creations = get_posts(['post_type' => 'creation', 'post_status' => 'publish', 'numberposts' => 6, 'orderby' => 'menu_order ID', 'order' => 'ASC', 'meta_query' => [['key' => '_thumbnail_id', 'compare' => 'EXISTS']]]);
$creation_ids = array_filter(array_map('get_post_thumbnail_id', $creations));
if (count($creation_ids) >= 3) : ?>
<section class="section section--ink creations" aria-labelledby="creations-h">
  <div class="container">
    <?php echo memorial_section_head('יצירות', 'מה שנוצר בידיים ובלב', '', memorial_page_exists('creations') ? ['url' => memorial_page_url('creations'), 'label' => 'לכל היצירות'] : null); ?>
    <?php echo memorial_gallery_html($creation_ids, ['class' => 'gallery--big', 'captions' => false]); ?>
  </div>
</section>
<?php endif; ?>

<?php if ($tributes) : ?>
<section class="section section--paper2 tributes-teaser" aria-labelledby="tributes-h">
  <div class="container">
    <?php echo memorial_section_head('הספדים והנצחה', 'מילים שנאמרו', '', memorial_page_exists('tributes') ? ['url' => memorial_page_url('tributes'), 'label' => 'לכל ההספדים'] : null); ?>
    <div class="grid grid--3">
      <?php foreach ($tributes as $t) : ?>
        <article class="card card--tribute">
          <a class="card__link" href="<?php echo esc_url(get_permalink($t)); ?>">
            <span class="card__media"><?php echo get_the_post_thumbnail($t, 'medium_large', ['loading' => 'lazy', 'alt' => '']); ?></span>
            <span class="card__body">
              <h3 class="card__title"><?php echo esc_html(get_the_title($t)); ?></h3>
              <p class="card__excerpt"><?php echo esc_html(memorial_excerpt($t, 110)); ?></p>
              <span class="card__foot"><span class="card__more">לקריאה <?php echo memorial_icon('arrow-left'); ?></span></span>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section tell" id="tell" aria-labelledby="tell-h">
  <div class="container tell__grid">
    <div class="tell__text">
      <p class="eyebrow">יש לכם סיפור?</p>
      <h2 id="tell-h" class="section__title">ספרו לנו על <?php echo esc_html($short ?: $name); ?></h2>
      <p>אנחנו אוספים סיפורים על רגעי חיים קטנים: משהו שנאמר, שנבנה, שצויר, ששאלו. גם משפט אחד חשוב. המשפחה קוראת כל סיפור ומפרסמת אותו כאן, כדי שגם מי שלא הכיר יוכל להכיר.</p>
      <div class="tell__art"><?php echo memorial_illus_bench(); ?></div>
    </div>
    <div class="tell__form card card--form">
      <?php echo memorial_story_form(['id' => 'story-home']); ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
