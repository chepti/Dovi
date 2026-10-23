<?php
if (!defined('ABSPATH')) {
    exit;
}
$about = trim((string) memorial_opt('footer_about'));
$cols = [
    'f-explore' => ['גלו', [memorial_page_link('stories'), memorial_page_link('about'), memorial_page_link('photos'), memorial_page_link('tributes')]],
    'f-made' => ['מקומות ויצירה', [memorial_page_link('places'), memorial_page_link('creations'), ['url' => home_url('/random-story/'), 'label' => 'סיפור אקראי', 'nofollow' => true]]],
    'f-info' => ['מידע', [memorial_page_link('accessibility'), memorial_page_link('privacy'), ['url' => home_url('/wp-sitemap.xml'), 'label' => 'מפת אתר']]],
];
?>
</main>
<footer class="site-footer">
  <div class="sunrise" aria-hidden="true"></div>
  <div class="footer__body">
    <div class="container footer__grid">
      <section class="footer__about" aria-labelledby="f-about">
        <h2 id="f-about" class="footer__brand"><?php echo esc_html(memorial_title_name()); ?></h2>
        <?php if ($about) : ?><p><?php echo esc_html($about); ?></p><?php endif; ?>
        <?php if (memorial_page_exists('share')) : ?>
          <p class="footer__cta"><a class="btn btn--sun btn--sm" href="<?php echo esc_url(memorial_page_url('share')); ?>"><?php echo memorial_icon('pen'); ?>שתפו סיפור או תמונה</a></p>
        <?php endif; ?>
      </section>
      <?php if (has_nav_menu('footer')) : ?>
        <nav class="footer__col" aria-label="תפריט תחתון">
          <?php wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'depth' => 1]); ?>
        </nav>
      <?php else :
      foreach ($cols as $cid => $col) :
          $links = array_filter($col[1]);
          if (!$links) {
              continue;
          } ?>
        <nav class="footer__col" aria-labelledby="<?php echo esc_attr($cid); ?>">
          <h2 id="<?php echo esc_attr($cid); ?>"><?php echo esc_html($col[0]); ?></h2>
          <ul>
            <?php foreach ($links as $l) : ?>
              <li><a href="<?php echo esc_url($l['url']); ?>"<?php echo !empty($l['nofollow']) ? ' rel="nofollow"' : ''; ?>><?php echo esc_html($l['label']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </nav>
      <?php endforeach;
      endif; ?>
    </div>

    <div class="legal" role="contentinfo">
      <div class="container legal__in">
        <span><?php echo memorial_footer_credit(); ?></span>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
