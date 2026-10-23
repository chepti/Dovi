<?php
if (!defined('ABSPATH')) {
    exit;
}
$name = memorial_name();
$short = memorial_short();
$honor = trim((string) memorial_opt('honorific'));
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#content">דילוג לתוכן</a>
<?php echo memorial_yahrzeit_banner(); ?>
<header class="site-header">
  <div class="container header__in">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($name . ($honor ? ' ' . $honor : '') . ', לדף הבית'); ?>">
      <span class="brand__mark"><?php echo memorial_icon('flame'); ?></span>
      <span class="brand__text"><strong><?php echo esc_html($name); ?></strong><small><?php echo esc_html(($honor ? $honor . ' · ' : '') . 'אתר הזיכרון'); ?></small></span>
    </a>
    <nav class="nav" id="nav" aria-label="תפריט ראשי">
      <?php
      if (has_nav_menu('primary')) {
          wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav__list', 'depth' => 1]);
      } else {
          $items = [['stories', 'סיפורים'], ['about', ''], ['places', ''], ['creations', ''], ['photos', ''], ['tributes', '']];
          echo '<ul class="nav__list">';
          $cur = is_page() ? urldecode(get_post_field('post_name', get_queried_object_id())) : '';
          foreach ($items as $it) {
              $link = memorial_page_link($it[0], $it[1]);
              if (!$link) {
                  continue;
              }
              $active = ($cur === $it[0]) || ($it[0] === 'stories' && (is_singular('story') || is_tax(['chapter', 'value']))) || ($it[0] === 'places' && is_singular('bench')) || ($it[0] === 'tributes' && is_singular('post'));
              echo '<li><a href="' . esc_url($link['url']) . '"' . ($active ? ' aria-current="page"' : '') . '>' . esc_html($link['label']) . '</a></li>';
          }
          echo '</ul>';
      }
      ?>
      <?php if (memorial_page_exists('share')) : ?>
        <a class="btn btn--sun btn--sm nav__cta" href="<?php echo esc_url(memorial_page_url('share')); ?>"><?php echo memorial_icon('pen'); ?>ספרו על <?php echo esc_html($short ?: 'האדם'); ?></a>
      <?php endif; ?>
    </nav>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav"><?php echo memorial_icon('menu', 'nav-toggle__open'); ?><?php echo memorial_icon('x', 'nav-toggle__close'); ?><span class="vh">תפריט</span></button>
  </div>
  <div class="sunrise" aria-hidden="true"></div>
</header>
<main id="content" tabindex="-1">
