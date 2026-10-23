<?php
/**
 * Template Name: שליחת סיפור או תמונה
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$short = memorial_short() ?: memorial_name();
$hp = memorial_head_photo();
?>
<section class="page-head<?php echo $hp['class']; ?>"<?php echo $hp['style']; ?>>
  <div class="container">
    <?php echo memorial_breadcrumbs_html(); ?>
    <p class="eyebrow">אנחנו אוספים סיפורים</p>
    <h1 class="page-head__title">ספרו לנו על <?php echo esc_html($short); ?></h1>
    <p class="page-head__lede">על רגעי חיים קטנים: משהו שנאמר, שנעשה, שנבנה, שצויר, ששאלו. גם משפט אחד חשוב. המשפחה קוראת כל סיפור ומפרסמת אותו באתר.</p>
  </div>
</section>

<section class="section">
  <div class="container share-page">
    <div class="share-page__form card card--form">
      <?php echo memorial_story_form(['id' => 'story-page']); ?>
    </div>
    <aside class="share-page__side">
      <div class="card card--note">
        <h2 class="card__h"><?php echo memorial_icon('lightbulb'); ?>לא יודעים איך להתחיל?</h2>
        <ul class="ticks">
          <li>איפה הכרתם, ומה הייתה הפעם הראשונה?</li>
          <li>משפט או בדיחה שנשארו איתכם.</li>
          <li>משהו שנבנה, צויר או תוקן בשבילכם.</li>
          <li>רגע שבו הרגשתם שבאמת הקשיבו לכם.</li>
          <li>מה למדתם, גם בלי שידעו.</li>
        </ul>
      </div>
      <?php if (memorial_page_exists('places')) : ?>
      <div class="card card--note">
        <h2 class="card__h"><?php echo memorial_icon('camera'); ?>ביקרתם במקום הנצחה?</h2>
        <p>אפשר לשלוח תמונה מהמקום, והיא תעלה לגלריית המבקרים.</p>
        <a class="btn btn--ghost btn--sm" href="<?php echo esc_url(memorial_page_url('places')); ?>#visit">לשליחת תמונה מהמקום</a>
      </div>
      <?php endif; ?>
      <div class="card card--note">
        <h2 class="card__h"><?php echo memorial_icon('heart'); ?>מה קורה אחרי השליחה?</h2>
        <p>הסיפור מגיע למשפחה במייל, נקרא, ואם צריך, מתוקן בעדינות ומתפרסם. לא נפרסם בלי אישור שלכם.</p>
      </div>
    </aside>
  </div>
</section>
<?php echo memorial_continue(['stories'], [['url' => home_url('/random-story/'), 'icon' => 'shuffle', 'title' => 'קראו סיפור אקראי', 'blurb' => 'כך נראים רגעים קטנים שאחרים כתבו.']]);
get_footer();
