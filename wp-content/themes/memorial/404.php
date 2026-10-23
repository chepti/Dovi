<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header(); ?>
<section class="section notfound">
  <div class="container container--narrow center">
    <div class="notfound__art"><?php echo memorial_illus_bench(); ?></div>
    <p class="eyebrow">404</p>
    <h1 class="section__title">העמוד שחיפשתם לא נמצא</h1>
    <p>אולי הקישור ישן, אולי הוקלד בטעות. אפשר לנסות לחפש, או להמשיך מאחד המקומות האלה.</p>
    <form class="searchform" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <label class="vh" for="s404">חיפוש באתר</label>
      <input id="s404" type="search" name="s" placeholder="חיפוש סיפור, שם או מקום" value="<?php echo esc_attr(get_search_query()); ?>">
      <button class="btn btn--sun" type="submit"><?php echo memorial_icon('search'); ?>חיפוש</button>
    </form>
    <p class="actions center">
      <a class="btn btn--ink" href="<?php echo esc_url(home_url('/random-story/')); ?>" rel="nofollow"><?php echo memorial_icon('shuffle'); ?>סיפור אקראי</a>
      <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/')); ?>">לדף הבית</a>
    </p>
  </div>
</section>
<?php echo memorial_continue([]);
get_footer();
