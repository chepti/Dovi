<?php
if (!defined('ABSPATH')) {
    exit;
}
$fb_mail = memorial_opt('feedback_email');
$year = (int) date('Y');
$name = memorial_name();
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
      <?php foreach ($cols as $cid => $col) :
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
      <?php endforeach; ?>
    </div>

    <div class="legal" role="contentinfo">
      <div class="container legal__in">
        <span>© <?php echo $year; ?> · אתר הזיכרון של <?php echo esc_html($name); ?></span>
        <span class="legal__dot" aria-hidden="true">·</span>
        <a href="https://chepti.com" target="_blank" rel="noopener">חולמים תקשוב</a>
        <span class="legal__dot" aria-hidden="true">·</span>
        <button type="button" class="legal__btn" data-feedback-open>משוב</button>
      </div>
    </div>
  </div>
</footer>

<dialog class="dlg" id="feedback-dialog" aria-labelledby="fb-title">
  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form class="form dlg__form">
    <div class="dlg__head">
      <h2 id="fb-title">משוב על האתר</h2>
      <button class="dlg__close" type="button" data-dialog-close aria-label="סגירה"><?php echo memorial_icon('x'); ?></button>
    </div>
    <input type="hidden" name="action" value="memorial_feedback">
    <input type="hidden" name="page" value="<?php echo esc_url(home_url(add_query_arg([]))); ?>">
    <div class="hp" aria-hidden="true"><label>אל תמלאו שדה זה<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
    <p class="dlg__lead">מצאתם טעות, משהו שלא עובד או רעיון לשיפור? כתבו לנו.</p>
    <div class="field">
      <label for="fb-msg">ההודעה שלכם</label>
      <textarea id="fb-msg" name="message" rows="5" required minlength="3" maxlength="3000"></textarea>
    </div>
    <div class="field">
      <label for="fb-mail">מייל לחזרה (אופציונלי)</label>
      <input id="fb-mail" name="email" type="email" autocomplete="email">
    </div>
    <div class="form__actions">
      <button class="btn btn--sun" type="submit"><?php echo memorial_icon('send'); ?>שליחה</button>
      <?php if ($fb_mail) : ?>
        <a class="btn btn--ghost" href="mailto:<?php echo esc_attr($fb_mail); ?>?subject=<?php echo rawurlencode('משוב על אתר הזיכרון של ' . $name); ?>"><?php echo memorial_icon('mail'); ?>או במייל</a>
      <?php endif; ?>
    </div>
    <div class="form-msg" data-msg role="status" aria-live="polite" hidden></div>
  </form>
</dialog>
<?php wp_footer(); ?>
</body>
</html>
