<?php
/**
 * Template Name: הספדים והנצחה
 * מציג את פוסטי האתר לפי הקטגוריות שלהם (למשל: הספדים, הנצחה, בתקשורת).
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$cats = get_categories(['hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC']);
$seen = [];
$short = memorial_short() ?: memorial_name();
$hp = memorial_head_photo('--c:#4a5672;');
?>
<section class="page-head<?php echo $hp['class']; ?>"<?php echo $hp['style']; ?>>
  <div class="container">
    <?php echo memorial_breadcrumbs_html(); ?>
    <p class="eyebrow">זכרו ברוך</p>
    <h1 class="page-head__title">הספדים והנצחה</h1>
    <p class="page-head__lede">מילים שנאמרו על <?php echo esc_html($short); ?>, הודעות וכל מה שהוקם לזכרו.</p>
    <?php if (count($cats) > 1) : ?>
    <p class="chipbar">
      <?php foreach ($cats as $c) : ?><a class="chip chip--outline" style="--c:#4a5672" href="#<?php echo esc_attr($c->slug); ?>"><?php echo esc_html($c->name); ?></a><?php endforeach; ?>
    </p>
    <?php endif; ?>
  </div>
</section>

<?php foreach ($cats as $i => $c) :
    $posts = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'cat' => $c->term_id, 'orderby' => 'date', 'order' => 'ASC', 'post__not_in' => $seen ?: [0]]);
    if (!$posts) {
        continue;
    } ?>
<section class="section<?php echo $i % 2 ? ' section--paper2' : ''; ?>" id="<?php echo esc_attr($c->slug); ?>" aria-labelledby="h-<?php echo esc_attr($c->slug); ?>">
  <div class="container">
    <?php echo memorial_section_head('', $c->name, $c->description); ?>
    <div class="grid grid--3">
      <?php foreach ($posts as $p) :
          $seen[] = $p->ID;
          $thumb = get_post_thumbnail_id($p);
          $link_only = memorial_link_only_url($p); ?>
        <article class="card card--tribute<?php echo $thumb ? ' has-img' : ''; ?>">
          <a class="card__link" href="<?php echo esc_url(get_permalink($p)); ?>">
            <?php if ($thumb) : ?><span class="card__media"><?php echo wp_get_attachment_image($thumb, 'medium_large', false, ['loading' => 'lazy', 'alt' => '']); ?></span><?php endif; ?>
            <span class="card__body">
              <?php if ($link_only) : ?><span class="chip chip--sm chip--mono"><?php echo memorial_icon('external'); ?><?php echo esc_html(memorial_domain_label($link_only)); ?></span><?php endif; ?>
              <h3 class="card__title"><?php echo esc_html(get_the_title($p)); ?></h3>
              <?php if (!$link_only) : ?><p class="card__excerpt"><?php echo esc_html(memorial_excerpt($p, 130)); ?></p><?php endif; ?>
              <span class="card__foot"><span class="card__more">לקריאה <?php echo memorial_icon('arrow-left'); ?></span></span>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php echo memorial_continue(['tributes']);
get_footer();
