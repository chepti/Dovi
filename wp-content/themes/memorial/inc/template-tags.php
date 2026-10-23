<?php
if (!defined('ABSPATH')) {
    exit;
}

/** נתוני סיפור */
function memorial_story_data($id)
{
    $terms = get_the_terms($id, 'chapter');
    $vals = get_the_terms($id, 'value');
    return [
        'author'   => get_post_meta($id, 'story_author', true),
        'relation' => get_post_meta($id, 'story_relation', true),
        'source'   => get_post_meta($id, 'story_source', true),
        'related'  => (int) get_post_meta($id, 'story_related', true),
        'chapters' => ($terms && !is_wp_error($terms)) ? array_values($terms) : [],
        'values'   => ($vals && !is_wp_error($vals)) ? array_values($vals) : [],
    ];
}

function memorial_byline($d, $with_icon = true)
{
    if (!$d['author'] && !$d['relation']) {
        return '';
    }
    $s = $d['author'] ? '<strong>' . esc_html($d['author']) . '</strong>' : '';
    if ($d['relation']) {
        $s .= ($s ? ' <span class="by__rel">· ' . esc_html($d['relation']) . '</span>' : esc_html($d['relation']));
    }
    return '<span class="by">' . ($with_icon ? memorial_icon('user') : '') . $s . '</span>';
}

/** כרטיס סיפור */
function memorial_story_card($id, $opts = [])
{
    $opts = wp_parse_args($opts, ['excerpt' => 150, 'image' => true, 'class' => '', 'tag' => 'article']);
    $d = memorial_story_data($id);
    $ch = $d['chapters'] ? memorial_chapter_meta($d['chapters'][0]) : null;
    $color = $ch ? $ch['color'] : '#4a5672';
    $thumb = $opts['image'] ? get_post_thumbnail_id($id) : 0;
    $chap_slugs = implode(' ', wp_list_pluck($d['chapters'], 'slug'));
    $val_slugs = implode(' ', wp_list_pluck($d['values'], 'slug'));
    $text = mb_strtolower(get_the_title($id) . ' ' . $d['author'] . ' ' . memorial_excerpt($id, 400));
    $tag = in_array($opts['tag'], ['article', 'div', 'li'], true) ? $opts['tag'] : 'article';
    ob_start(); ?>
<<?php echo $tag; ?> class="card card--story <?php echo esc_attr($opts['class']); ?><?php echo $thumb ? ' has-img' : ''; ?>" style="--c:<?php echo esc_attr($color); ?>" data-chapters="<?php echo esc_attr($chap_slugs); ?>" data-values="<?php echo esc_attr($val_slugs); ?>" data-text="<?php echo esc_attr($text); ?>">
  <a class="card__link" href="<?php echo esc_url(get_permalink($id)); ?>">
    <?php if ($thumb) : ?>
      <span class="card__media"><?php echo wp_get_attachment_image($thumb, 'medium_large', false, ['loading' => 'lazy', 'sizes' => '(max-width:700px) 92vw, 380px']); ?></span>
    <?php else : ?>
      <span class="card__mark" aria-hidden="true"><?php echo memorial_icon('quote'); ?></span>
    <?php endif; ?>
    <span class="card__body">
      <?php if ($ch) : ?><span class="chip chip--sm"><?php echo esc_html($ch['name']); ?></span><?php endif; ?>
      <h3 class="card__title"><?php echo esc_html(get_the_title($id)); ?></h3>
      <?php if ($opts['excerpt']) : ?><p class="card__excerpt"><?php echo esc_html(memorial_excerpt($id, $opts['excerpt'])); ?></p><?php endif; ?>
      <span class="card__foot"><?php echo memorial_byline($d); ?><span class="card__more">קראו עוד <?php echo memorial_icon('arrow-left'); ?></span></span>
    </span>
  </a>
</<?php echo $tag; ?>>
<?php
    return ob_get_clean();
}

/** סיפור היום: מתחלף כל יום, מתוך סיפורים בעלי אורך סביר */
function memorial_story_of_day()
{
    $ids = get_posts(['post_type' => 'story', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids']);
    $good = [];
    foreach ($ids as $id) {
        if (mb_strlen(wp_strip_all_tags(get_post_field('post_content', $id))) >= 220) {
            $good[] = $id;
        }
    }
    if (!$good) {
        return 0;
    }
    $day = (int) floor((time() + 2 * HOUR_IN_SECONDS) / DAY_IN_SECONDS);
    return $good[$day % count($good)];
}

/** סיפור קודם/הבא בתוך הפרק */
function memorial_story_neighbors($id)
{
    $d = memorial_story_data($id);
    if (!$d['chapters']) {
        return [0, 0];
    }
    $ids = get_posts([
        'post_type' => 'story', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids',
        'tax_query' => [['taxonomy' => 'chapter', 'field' => 'term_id', 'terms' => $d['chapters'][0]->term_id]],
    ]);
    $pos = array_search($id, $ids, true);
    if ($pos === false || count($ids) < 2) {
        return [0, 0];
    }
    $n = count($ids);
    return [$ids[($pos - 1 + $n) % $n], $ids[($pos + 1) % $n]];
}

/** ציר הזמן (מוגדר בהגדרות האתר). ריק אם לא הוגדר */
function memorial_timeline_html()
{
    $items = memorial_timeline_items();
    if (!$items) {
        return '';
    }
    ob_start();
    echo '<ol class="timeline">';
    foreach ($items as $row) {
        $term = $row['chapter'] ? get_term_by('slug', $row['chapter'], 'chapter') : null;
        $meta = $term ? memorial_chapter_meta($term) : ['color' => '#3b4a6b'];
        echo '<li style="--c:' . esc_attr($meta['color']) . '"' . ($row['text'] ? ' data-tip="' . esc_attr($row['text']) . '"' : '') . '><span class="timeline__when">' . esc_html($row['when']) . '</span><div class="timeline__what"><h3>' . esc_html($row['title']) . '</h3>';
        if ($row['text']) {
            echo '<p>' . esc_html($row['text']) . '</p>';
        }
        if ($term && $term->count) {
            $link = get_term_link($term);
            if (!is_wp_error($link)) {
                echo '<a href="' . esc_url($link) . '">סיפורים בפרק ' . esc_html($term->name) . ' ' . memorial_icon('arrow-left') . '</a>';
            }
        }
        echo '</div></li>';
    }
    echo '</ol>';
    return ob_get_clean();
}

/** סגנון רקע של תמונת נוף לראש עמוד (התמונה מוגדרת בהגדרות האתר) */
function memorial_head_photo($extra = '')
{
    $id = (int) memorial_opt('header_image');
    $src = $id ? wp_get_attachment_image_src($id, 'large') : null;
    if (!$src) {
        return ['class' => '', 'style' => $extra ? ' style="' . esc_attr($extra) . '"' : ''];
    }
    return ['class' => ' page-head--photo', 'style' => ' style="' . esc_attr($extra) . '--hero-img:url(' . esc_url($src[0]) . ')"'];
}

/** כרטיס מקום הנצחה */
function memorial_bench_card($id)
{
    $img = get_post_thumbnail_id($id);
    $g = memorial_gallery_ids($id, 'bench_gallery');
    if (!$img && $g) {
        $img = $g[0];
    }
    $region = get_post_meta($id, 'bench_region', true);
    ob_start(); ?>
<article class="card card--bench">
  <a class="card__link" href="<?php echo esc_url(get_permalink($id)); ?>">
    <span class="card__media"><?php echo $img ? wp_get_attachment_image($img, 'medium_large', false, ['loading' => 'lazy', 'sizes' => '(max-width:700px) 92vw, 380px']) : memorial_illus_bench(); ?></span>
    <span class="card__body">
      <?php if ($region) : ?><span class="chip chip--sm chip--mono"><?php echo memorial_icon('map-pin'); ?><?php echo esc_html($region); ?></span><?php endif; ?>
      <h3 class="card__title"><?php echo esc_html(get_the_title($id)); ?></h3>
      <p class="card__excerpt"><?php echo esc_html(memorial_excerpt($id, 110)); ?></p>
      <span class="card__foot"><span class="card__more">לעמוד המקום <?php echo memorial_icon('arrow-left'); ?></span></span>
    </span>
  </a>
</article>
<?php
    return ob_get_clean();
}

/** תמונות אלבום אחרונות (מכל האלבומים), למקטע בדף הבית */
function memorial_album_photo_ids($limit = 8, $album_slugs = [])
{
    $args = ['post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => $limit, 'fields' => 'ids', 'orderby' => 'date', 'order' => 'DESC'];
    $all = get_terms(['taxonomy' => 'album', 'hide_empty' => true, 'fields' => 'ids']);
    if (is_wp_error($all) || !$all) {
        return [];
    }
    $args['tax_query'] = [['taxonomy' => 'album', 'field' => $album_slugs ? 'slug' : 'term_id', 'terms' => $album_slugs ?: $all]];
    return get_posts($args);
}

/** גלריה עם lightbox */
function memorial_gallery_html($ids, $args = [])
{
    $args = wp_parse_args($args, ['size' => 'large', 'class' => '', 'captions' => true, 'label' => '', 'sizes' => '(max-width:700px) 60vw, (max-width:1100px) 40vw, 420px', 'fallback_caption' => '']);
    $ids = array_values(array_unique(array_filter(array_map('intval', (array) $ids))));
    if (!$ids) {
        return '';
    }
    ob_start();
    echo '<ul class="gallery ' . esc_attr($args['class']) . '" data-lightbox role="list">';
    foreach ($ids as $i => $id) {
        $full = wp_get_attachment_image_src($id, 'full');
        if (!$full) {
            continue;
        }
        if ($full[1] > 2400) {
            $full = wp_get_attachment_image_src($id, 'large') ?: $full;
        }
        $cap = wp_get_attachment_caption($id) ?: $args['fallback_caption'];
        $alt = trim((string) get_post_meta($id, '_wp_attachment_image_alt', true)) ?: $cap;
        if (!$alt) {
            $alt = ($args['label'] ? $args['label'] . ' – תמונה ' . ($i + 1) : 'תמונה מאתר הזיכרון של ' . memorial_name());
        }
        $img = wp_get_attachment_image($id, $args['size'], false, ['alt' => $alt, 'loading' => $i < 4 ? 'eager' : 'lazy', 'sizes' => $args['sizes'], 'decoding' => 'async']);
        $ratio = ($full[2] ? max(0.45, min(3.2, $full[1] / $full[2])) : 1.5);
        echo '<li class="gallery__item" style="--r:' . round($ratio, 3) . '"><a class="g-item" href="' . esc_url($full[0]) . '" data-caption="' . esc_attr($cap) . '" data-alt="' . esc_attr($alt) . '" aria-label="' . esc_attr('הגדלת תמונה: ' . $alt) . '">' . $img;
        if ($args['captions'] && $cap) {
            echo '<span class="g-cap">' . esc_html($cap) . '</span>';
        }
        echo '</a></li>';
    }
    echo '</ul>';
    return ob_get_clean();
}

/** פירורי לחם */
function memorial_breadcrumbs_html()
{
    if (!function_exists('memorial_get_breadcrumbs')) {
        return '';
    }
    $items = memorial_get_breadcrumbs();
    if (count($items) < 2) {
        return '';
    }
    $out = '<nav class="crumbs" aria-label="מיקום באתר"><ol>';
    foreach ($items as $i => $it) {
        $last = $i === count($items) - 1;
        $out .= '<li>' . ($it['url'] && !$last ? '<a href="' . esc_url($it['url']) . '">' . esc_html($it['label']) . '</a>' : '<span' . ($last ? ' aria-current="page"' : '') . '>' . esc_html($it['label']) . '</span>') . '</li>';
    }
    return $out . '</ol></nav>';
}

/** כפתורי שיתוף */
function memorial_share_bar($url, $title, $label = 'שיתוף')
{
    $wa = 'https://wa.me/?text=' . rawurlencode($title . "\n" . $url);
    ob_start(); ?>
<div class="share" data-share data-url="<?php echo esc_url($url); ?>" data-title="<?php echo esc_attr($title); ?>">
  <span class="share__label"><?php echo esc_html($label); ?></span>
  <a class="btn btn--sm btn--wa" href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener noreferrer"><?php echo memorial_icon('message'); ?>וואטסאפ</a>
  <button type="button" class="btn btn--sm btn--ghost" data-copy><?php echo memorial_icon('link'); ?><span>העתקת קישור</span></button>
  <button type="button" class="btn btn--sm btn--ghost" data-native-share hidden><?php echo memorial_icon('share'); ?>עוד אפשרויות</button>
</div>
<?php
    return ob_get_clean();
}

/** כותרת סקשן */
function memorial_section_head($eyebrow, $title, $sub = '', $link = null, $level = 2)
{
    ob_start(); ?>
<header class="section__head">
  <div>
    <?php if ($eyebrow) : ?><p class="eyebrow"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
    <h<?php echo (int) $level; ?> class="section__title"><?php echo esc_html($title); ?></h<?php echo (int) $level; ?>>
    <?php if ($sub) : ?><p class="section__sub"><?php echo esc_html($sub); ?></p><?php endif; ?>
  </div>
  <?php if ($link) : ?><a class="btn btn--ghost btn--sm" href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['label']); ?><?php echo memorial_icon('arrow-left'); ?></a><?php endif; ?>
</header>
<?php
    return ob_get_clean();
}

/** באנר אזכרה (מוצג שלושה שבועות לפני ועד שבוע אחרי) */
function memorial_yahrzeit_banner()
{
    if (!function_exists('memorial_yahrzeit')) {
        return '';
    }
    $y = memorial_yahrzeit();
    if (!$y['next'] || $y['days_until'] === null) {
        return '';
    }
    $d = $y['days_until'];
    $tz = new DateTimeZone('Asia/Jerusalem');
    $prev = false;
    if ($d > 21) {
        // האזכרה הייתה לפני פחות משבוע?
        $last = clone $y['next'];
        $last->modify('-1 year');
        $today = new DateTime('today', $tz);
        $since = (int) $last->diff($today)->format('%r%a');
        if ($since < 0 || $since > 7) {
            return '';
        }
        $prev = true;
    }
    $years = max(1, (int) $y['years_count']);
    if ($prev) {
        $years = max(1, $years - 1);
    }
    $word = [1 => 'שנה', 2 => 'שנתיים', 3 => 'שלוש שנים', 4 => 'ארבע שנים', 5 => 'חמש שנים', 6 => 'שש שנים', 7 => 'שבע שנים', 8 => 'שמונה שנים', 9 => 'תשע שנים', 10 => 'עשר שנים'][$years] ?? ($years . ' שנים');
    $phrase = memorial_opt('passing_phrase') . ' ' . memorial_short();
    if ($prev) {
        $msg = 'עברו ' . $word . ' ' . $phrase;
    } elseif ($d === 0) {
        $msg = 'היום, ' . $y['heb_label'] . ', ' . $word . ' ' . $phrase;
    } elseif ($d === 1) {
        $msg = 'מחר, ' . $y['heb_label'] . ', ' . $word . ' ' . $phrase;
    } else {
        $msg = 'בעוד ' . $d . ' ימים, ' . $y['heb_label'] . ', ' . $word . ' ' . $phrase;
    }
    $note = memorial_opt('yahrzeit_note');
    ob_start(); ?>
<aside class="yz" aria-label="יום הזיכרון של <?php echo esc_attr(memorial_short()); ?>">
  <div class="container yz__in">
    <?php echo memorial_icon('flame', 'yz__ic'); ?>
    <p class="yz__text"><strong><?php echo esc_html($msg); ?></strong><?php if ($note) : ?> <span><?php echo esc_html($note); ?></span><?php endif; ?></p>
    <?php if (memorial_page_exists('share')) : ?><a class="btn btn--sm btn--paper" href="<?php echo esc_url(memorial_page_url('share')); ?>">שתפו זיכרון<?php echo memorial_icon('arrow-left'); ?></a><?php endif; ?>
  </div>
</aside>
<?php
    return ob_get_clean();
}

/** כרטיסי "ממשיכים לגלות" בסוף כל עמוד */
function memorial_continue($exclude = [], $extra = [])
{
    $cat = memorial_catalog();
    foreach ((array) $exclude as $k) {
        unset($cat[$k]);
    }
    $keys = array_keys($cat);
    $seed = is_singular() ? get_the_ID() : (int) date('z');
    $pick = [];
    $n = count($keys);
    for ($i = 0; $i < 3 && $n; $i++) {
        $pick[] = $keys[($seed + $i * 2) % $n];
    }
    $pick = array_values(array_unique($pick));
    $pick = array_slice($pick, 0, max(0, 3 - count($extra)));
    if (!$pick && !$extra) {
        return '';
    }
    ob_start(); ?>
<section class="continue" aria-labelledby="continue-h">
  <div class="container">
    <p class="eyebrow">ממשיכים לגלות</p>
    <h2 id="continue-h" class="section__title">עוד על <?php echo esc_html(memorial_short() ?: 'האתר'); ?></h2>
    <div class="grid grid--3">
      <?php foreach ($extra as $e) : ?>
        <a class="card card--go" href="<?php echo esc_url($e['url']); ?>">
          <span class="go__ic"><?php echo memorial_icon($e['icon'] ?? 'shuffle'); ?></span>
          <span class="go__t"><?php echo esc_html($e['title']); ?></span>
          <span class="go__b"><?php echo esc_html($e['blurb']); ?></span>
          <span class="go__arrow"><?php echo memorial_icon('arrow-left'); ?></span>
        </a>
      <?php endforeach; ?>
      <?php foreach ($pick as $k) :
          $c = $cat[$k]; ?>
        <a class="card card--go" href="<?php echo esc_url(memorial_page_url($c['slug'])); ?>">
          <span class="go__ic"><?php echo memorial_icon($c['icon']); ?></span>
          <span class="go__t"><?php echo esc_html($c['title']); ?></span>
          <span class="go__b"><?php echo esc_html($c['blurb']); ?></span>
          <span class="go__arrow"><?php echo memorial_icon('arrow-left'); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="continue__random"><a class="btn btn--ink" href="<?php echo esc_url(home_url('/random-story/')); ?>" rel="nofollow"><?php echo memorial_icon('shuffle'); ?>סיפור אקראי</a></p>
  </div>
</section>
<?php
    return ob_get_clean();
}

/** הודעת הצלחה/שגיאה של טפסים (גם ללא JS) */
function memorial_form_notice($kind)
{
    $ok = isset($_GET['memorial_sent']) && sanitize_key(wp_unslash($_GET['memorial_sent'])) === $kind;
    $err = isset($_GET['memorial_err']) ? sanitize_text_field(wp_unslash(urldecode($_GET['memorial_err']))) : '';
    $msgs = ['story' => 'תודה רבה. הסיפור התקבל, והמשפחה תקרא אותו ותפרסם אותו בקרוב.', 'visit' => 'תודה! התמונה התקבלה והמשפחה תשמח לראות אותה.'];
    if ($ok) {
        return '<div class="form-msg form-msg--ok" id="sent" role="status">' . memorial_icon('check') . esc_html($msgs[$kind] ?? 'תודה!') . '</div>';
    }
    if ($err) {
        return '<div class="form-msg form-msg--err" id="form-error" role="alert">' . esc_html($err) . '</div>';
    }
    return '';
}

/** טופס שליחת סיפור */
function memorial_story_form($args = [])
{
    $args = wp_parse_args($args, ['id' => 'story-form', 'title' => '']);
    $id = esc_attr($args['id']);
    $short = memorial_short();
    ob_start(); ?>
<form class="form" id="<?php echo $id; ?>" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" data-ajax-form>
  <input type="hidden" name="action" value="memorial_story">
  <div class="hp" aria-hidden="true"><label>אל תמלאו שדה זה<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
  <?php echo memorial_form_notice('story'); ?>
  <div class="form__row">
    <div class="field">
      <label for="<?php echo $id; ?>-name">השם שלך <span class="req" aria-hidden="true">*</span></label>
      <input id="<?php echo $id; ?>-name" name="name" type="text" required maxlength="80" autocomplete="name">
    </div>
    <div class="field">
      <label for="<?php echo $id; ?>-rel">איך הכרת את <?php echo esc_html($short); ?>?</label>
      <input id="<?php echo $id; ?>-rel" name="relation" type="text" maxlength="80" list="<?php echo $id; ?>-rels" placeholder="למשל: אח, חבר מהלימודים, שכן">
      <datalist id="<?php echo $id; ?>-rels"><option value="בן משפחה"><option value="חבר"><option value="חבר מהלימודים"><option value="חבר מהשירות"><option value="שכן"><option value="עמית לעבודה"><option value="חניך"><option value="מורה"></datalist>
    </div>
  </div>
  <div class="field">
    <label for="<?php echo $id; ?>-title">כותרת לסיפור (אופציונלי)</label>
    <input id="<?php echo $id; ?>-title" name="title" type="text" maxlength="100" placeholder="משפט קצר שתופס את הרגע">
  </div>
  <div class="field">
    <label for="<?php echo $id; ?>-text">הסיפור <span class="req" aria-hidden="true">*</span></label>
    <textarea id="<?php echo $id; ?>-text" name="text" rows="7" required minlength="20" maxlength="12000" placeholder="רגע קטן שזכור לך: משהו שנאמר, שנעשה, שנבנה, שצחקתם עליו…"></textarea>
  </div>
  <div class="form__row">
    <div class="field field--file">
      <label for="<?php echo $id; ?>-photos">תמונות (אפשר כמה)</label>
      <input id="<?php echo $id; ?>-photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
    </div>
    <div class="field">
      <label for="<?php echo $id; ?>-email">מייל למקרה שנרצה לשאול (לא יוצג)</label>
      <input id="<?php echo $id; ?>-email" name="email" type="email" maxlength="120" autocomplete="email">
    </div>
  </div>
  <label class="consent"><input type="checkbox" name="consent" value="1" required> <span>אני מאשר/ת למשפחה לפרסם את הסיפור (והתמונות) באתר, בשמי כפי שכתבתי.</span></label>
  <div class="form__actions">
    <button class="btn btn--sun" type="submit"><?php echo memorial_icon('send'); ?>שליחה למשפחה</button>
    <p class="form__note">כל סיפור נקרא על ידי המשפחה לפני שהוא מתפרסם.</p>
  </div>
  <div class="form-msg" data-msg role="status" aria-live="polite" hidden></div>
</form>
<?php
    return ob_get_clean();
}

/** טופס "ביקרתם במקום?" */
function memorial_visit_form($place_id = 0, $id = 'visit-form')
{
    $places = get_posts(['post_type' => 'bench', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    $eid = esc_attr($id);
    ob_start(); ?>
<form class="form" id="<?php echo $eid; ?>" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" data-ajax-form>
  <input type="hidden" name="action" value="memorial_visit">
  <div class="hp" aria-hidden="true"><label>אל תמלאו שדה זה<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
  <?php echo memorial_form_notice('visit'); ?>
  <div class="form__row">
    <div class="field">
      <label for="<?php echo $eid; ?>-bench">איזה מקום ביקרתם?</label>
      <select id="<?php echo $eid; ?>-bench" name="bench">
        <?php foreach ($places as $b) : ?>
          <option value="<?php echo (int) $b->ID; ?>" <?php selected((int) $place_id, $b->ID); ?>><?php echo esc_html($b->post_title); ?></option>
        <?php endforeach; ?>
        <option value="0">מקום אחר שלא מופיע</option>
      </select>
    </div>
    <div class="field">
      <label for="<?php echo $eid; ?>-name">השם שלך <span class="req" aria-hidden="true">*</span></label>
      <input id="<?php echo $eid; ?>-name" name="name" type="text" required maxlength="80" autocomplete="name">
    </div>
  </div>
  <div class="field field--file">
    <label for="<?php echo $eid; ?>-photo">תמונה מהביקור <span class="req" aria-hidden="true">*</span></label>
    <input id="<?php echo $eid; ?>-photo" name="photo[]" type="file" accept="image/jpeg,image/png,image/webp" required>
  </div>
  <div class="field">
    <label for="<?php echo $eid; ?>-msg">כמה מילים (אופציונלי)</label>
    <textarea id="<?php echo $eid; ?>-msg" name="message" rows="3" maxlength="1500" placeholder="מה ראיתם? מה הרגשתם?"></textarea>
  </div>
  <div class="field">
    <label for="<?php echo $eid; ?>-email">מייל (לא יוצג)</label>
    <input id="<?php echo $eid; ?>-email" name="email" type="email" maxlength="120" autocomplete="email">
  </div>
  <div class="form__actions">
    <button class="btn btn--sun" type="submit"><?php echo memorial_icon('camera'); ?>שליחת התמונה</button>
    <p class="form__note">המשפחה תשמח לקבל ד״ש. התמונה תתפרסם בגלריית המבקרים אחרי אישור.</p>
  </div>
  <div class="form-msg" data-msg role="status" aria-live="polite" hidden></div>
</form>
<?php
    return ob_get_clean();
}

/** תוכן פוסט שהוא קישור בלבד -> כרטיס קישור */
add_filter('the_content', function ($html) {
    if (!is_singular('post') || !in_the_loop() || !function_exists('memorial_link_only_url')) {
        return $html;
    }
    $url = memorial_link_only_url(get_the_ID());
    if (!$url) {
        return $html;
    }
    $label = memorial_domain_label($url);
    $shots = '';
    if (preg_match_all('/<img[^>]+>/i', $html, $m)) {
        $shots = '<div class="linkcard__shots">' . implode('', $m[0]) . '</div>';
    }
    return $shots . '<div class="linkcard">' . memorial_icon('external') . '<div><p class="linkcard__src">' . esc_html($label) . '</p><p class="linkcard__t">הכתבה/הפוסט המקורי נמצא באתר חיצוני.</p><a class="btn btn--sun" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">לקריאה ב' . esc_html($label) . memorial_icon('external') . '</a></div></div>';
}, 5);
