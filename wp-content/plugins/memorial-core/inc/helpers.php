<?php
if (!defined('ABSPATH')) {
    exit;
}

/** האם ערכת הנושא Memorial פעילה (כולל ערכת בת שלה) */
function memorial_theme_active()
{
    return get_template() === 'memorial';
}

/** ברירות המחדל של אפשרויות האתר (ריקות: הערכים נגזרים בזמן ריצה) */
function memorial_option_keys()
{
    return [
        'person_name', 'short_name', 'honorific', 'birth_year', 'death_date', 'passing_phrase',
        'hero_lede', 'hero_roles', 'footer_about', 'hero_image', 'header_image', 'timeline',
        'notify_emails', 'feedback_email', 'contact_email', 'from_email', 'yahrzeit_note', 'ga_id',
    ];
}

/** אפשרות מוגדרת, או ערך חלופי הגיוני כשהיא ריקה */
function memorial_opt($key, $default = '')
{
    $opts = get_option('memorial_options', []);
    if (isset($opts[$key]) && $opts[$key] !== '') {
        return $opts[$key];
    }
    $host = preg_replace('/^www\./', '', (string) wp_parse_url(home_url(), PHP_URL_HOST));
    switch ($key) {
        case 'person_name':
            return get_bloginfo('name');
        case 'short_name':
            $parts = preg_split('/\s+/u', trim((string) memorial_opt('person_name')));
            return $parts ? $parts[0] : '';
        case 'passing_phrase':
            return 'לפטירתו של';
        case 'notify_emails':
        case 'feedback_email':
        case 'contact_email':
            return get_option('admin_email');
        case 'from_email':
            return 'wordpress@' . ($host ?: 'localhost');
    }
    return $default;
}

/** שם מלא, שם פרטי, ושם מלא עם תואר */
function memorial_name()
{
    return (string) memorial_opt('person_name');
}

function memorial_short()
{
    return (string) memorial_opt('short_name');
}

function memorial_title_name()
{
    $h = trim((string) memorial_opt('honorific'));
    return memorial_name() . ($h ? ' ' . $h : '');
}

/** כתובות מייל לקבלת סיפורים/תמונות חדשות */
function memorial_notify_list()
{
    $list = array_filter(array_map('trim', explode(',', (string) memorial_opt('notify_emails'))));
    return array_values(array_filter($list, 'is_email'));
}

/** מספר עברי (1-999) באותיות עם גרשיים */
function memorial_heb_num($n)
{
    $n = (int) $n;
    $map = [400 => 'ת', 300 => 'ש', 200 => 'ר', 100 => 'ק', 90 => 'צ', 80 => 'פ', 70 => 'ע', 60 => 'ס', 50 => 'נ', 40 => 'מ', 30 => 'ל', 20 => 'כ', 10 => 'י', 9 => 'ט', 8 => 'ח', 7 => 'ז', 6 => 'ו', 5 => 'ה', 4 => 'ד', 3 => 'ג', 2 => 'ב', 1 => 'א'];
    $s = '';
    foreach ($map as $v => $l) {
        while ($n >= $v) {
            if ($n == 15) { $s .= 'טו'; $n = 0; break; }
            if ($n == 16) { $s .= 'טז'; $n = 0; break; }
            $s .= $l;
            $n -= $v;
        }
    }
    $len = mb_strlen($s);
    if ($len === 0) {
        return '';
    }
    if ($len === 1) {
        return $s . '׳';
    }
    return mb_substr($s, 0, $len - 1) . '״' . mb_substr($s, -1);
}

/** שנה עברית מעוברת? */
function memorial_heb_leap($y)
{
    return ((7 * (int) $y + 1) % 19) < 7;
}

/** שמות החודשים העבריים לפי המספור של PHP (calendar) */
function memorial_heb_month_name($m, $leap)
{
    $names = [1 => 'תשרי', 2 => 'חשוון', 3 => 'כסלו', 4 => 'טבת', 5 => 'שבט', 6 => 'אדר א׳', 7 => $leap ? 'אדר ב׳' : 'אדר', 8 => 'ניסן', 9 => 'אייר', 10 => 'סיוון', 11 => 'תמוז', 12 => 'אב', 13 => 'אלול'];
    return $names[(int) $m] ?? '';
}

/**
 * יום הזיכרון העברי: נגזר מתאריך הפטירה שהוגדר באפשרויות האתר.
 * מחזיר מידע על האזכרה הקרובה (או ריק אם אין תאריך או שהרחבת calendar חסרה).
 */
function memorial_yahrzeit()
{
    $out = ['fell' => null, 'next' => null, 'days_until' => null, 'heb_label' => '', 'year_no' => 0, 'years_count' => 0];
    $death = (string) memorial_opt('death_date');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $death)) {
        return $out;
    }
    if (!function_exists('gregoriantojd') || !function_exists('jewishtojd') || !function_exists('jdtojewish') || !function_exists('jdtogregorian')) {
        return $out;
    }
    try {
        $tz = new DateTimeZone('Asia/Jerusalem');
        $today = new DateTime('today', $tz);
        $fell = new DateTime($death, $tz);
    } catch (Exception $e) {
        return $out;
    }
    $out['fell'] = $fell;

    $dj = explode('/', jdtojewish(gregoriantojd((int) $fell->format('n'), (int) $fell->format('j'), (int) $fell->format('Y'))));
    $hm = (int) $dj[0];
    $hd = (int) $dj[1];
    $hyd = (int) $dj[2];

    $tj = explode('/', jdtojewish(gregoriantojd((int) $today->format('n'), (int) $today->format('j'), (int) $today->format('Y'))));
    $hy = (int) $tj[2];

    for ($y = $hy; $y <= $hy + 1; $y++) {
        $leap = memorial_heb_leap($y);
        $m = $hm;
        if ($m === 6 && !$leap) {
            $m = 7;
        }
        $d = $hd;
        $jd = jewishtojd($m, $d, $y);
        $back = explode('/', jdtojewish($jd));
        if ((int) $back[1] !== $d) {
            $d = max(1, $d - 1);
            $jd = jewishtojd($m, $d, $y);
        }
        $g = explode('/', jdtogregorian($jd));
        $next = new DateTime(sprintf('%04d-%02d-%02d', $g[2], $g[0], $g[1]), $tz);
        if ($next >= $today) {
            $out['next'] = $next;
            $out['year_no'] = $y;
            $out['days_until'] = (int) $today->diff($next)->format('%r%a');
            $out['heb_label'] = memorial_heb_num($d) . ' ב' . memorial_heb_month_name($m, $leap) . ' ' . memorial_heb_num($y - 5000);
            $out['years_count'] = $y - $hyd;
            break;
        }
    }
    return $out;
}

/** זמן קריאה בדקות */
function memorial_reading_minutes($html)
{
    $words = count(preg_split('/\s+/u', trim(strip_tags($html)), -1, PREG_SPLIT_NO_EMPTY));
    return max(1, (int) ceil($words / 200));
}

/** תקציר נקי מטקסט */
function memorial_excerpt($post = null, $chars = 150)
{
    $post = get_post($post);
    if (!$post) {
        return '';
    }
    $text = $post->post_excerpt ?: $post->post_content;
    $text = wp_strip_all_tags(strip_shortcodes($text));
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    $text = preg_replace('/[\x{FFFD}\p{Cs}]/u', '', (string) $text);
    $text = preg_replace('~https?://\S+~u', '', $text);
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    if (mb_strlen($text, 'UTF-8') <= $chars) {
        return $text;
    }
    $cut = mb_substr($text, 0, $chars, 'UTF-8');
    $sp = mb_strrpos($cut, ' ', 0, 'UTF-8');
    if ($sp !== false && $sp > $chars * 0.6) {
        $cut = mb_substr($cut, 0, $sp, 'UTF-8');
    }
    return preg_replace('/[\s,.;:\-–—]+$/u', '', $cut) . '…';
}

/** הכתובת הראשונה בתוכן, אם התוכן כולו הוא קישור */
function memorial_link_only_url($post = null)
{
    $post = get_post($post);
    if (!$post) {
        return '';
    }
    $t = trim(wp_strip_all_tags($post->post_content));
    if (preg_match('~^(https?://\S+)$~u', $t, $m)) {
        return $m[1];
    }
    return '';
}

function memorial_domain_label($url)
{
    $host = preg_replace('/^www\./', '', (string) wp_parse_url($url, PHP_URL_HOST));
    $map = [
        'facebook.com' => 'פייסבוק', 'm.facebook.com' => 'פייסבוק', 'instagram.com' => 'אינסטגרם',
        'youtube.com' => 'יוטיוב', 'youtu.be' => 'יוטיוב', 'm.youtube.com' => 'יוטיוב',
    ];
    return $map[$host] ?? $host;
}

/** תמונת פתיחה לשיתוף (ברירת מחדל): תמונת הגיבור, אחרת סמל האתר */
function memorial_default_og()
{
    $id = (int) memorial_opt('hero_image');
    if ($id && ($src = wp_get_attachment_image_src($id, 'large'))) {
        return ['url' => $src[0], 'w' => (int) $src[1], 'h' => (int) $src[2]];
    }
    $icon = get_site_icon_url(512);
    return $icon ? ['url' => $icon, 'w' => 512, 'h' => 512] : null;
}

/** מזהי גלריה כמערך */
function memorial_gallery_ids($post_id, $key)
{
    return array_values(array_filter(array_map('intval', explode(',', (string) get_post_meta($post_id, $key, true)))));
}

/* ----- פרקים וערכים: טקסונומיות שמוגדרות בניהול ----- */

/** צבעי ברירת מחדל לפרקים וערכים שלא הוגדר להם צבע */
function memorial_palette()
{
    return ['#e9a825', '#e8743b', '#c0543b', '#5a8f4a', '#3f8fd6', '#2f9aa8', '#d45d8f', '#7b4a9e', '#4a5672'];
}

/** סוגי האייקונים הזמינים לפרקים וערכים (שמות האייקונים בערכת הנושא) */
function memorial_icon_choices()
{
    return [
        'sparkle' => 'ניצוץ', 'book' => 'ספר', 'users' => 'אנשים', 'user' => 'אדם', 'heart' => 'לב', 'palette' => 'צבעים',
        'graduation' => 'לימודים', 'ruler' => 'הנדסה', 'hammer' => 'בנייה', 'flame' => 'נר זיכרון', 'ear' => 'הקשבה',
        'lightbulb' => 'רעיון', 'mountain' => 'הרים וטבע', 'map-pin' => 'מקום', 'sun' => 'שמש', 'camera' => 'צילום',
        'pen' => 'כתיבה', 'quote' => 'ציטוט', 'clock' => 'זמן', 'puzzle' => 'פאזל', 'image' => 'תמונה',
    ];
}

function memorial_term_order($term_id)
{
    $o = get_term_meta($term_id, 'memorial_order', true);
    return $o === '' ? 9999 : (int) $o;
}

/** נתוני טקסונומיה מסודרים: slug => [name, color, icon, desc, ...] */
function memorial_terms_data($taxonomy)
{
    static $cache = [];
    if (isset($cache[$taxonomy])) {
        return $cache[$taxonomy];
    }
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
    $out = [];
    if (!is_wp_error($terms)) {
        usort($terms, function ($a, $b) {
            $oa = memorial_term_order($a->term_id);
            $ob = memorial_term_order($b->term_id);
            return $oa === $ob ? $a->term_id <=> $b->term_id : $oa <=> $ob;
        });
        $pal = memorial_palette();
        foreach ($terms as $i => $t) {
            $color = (string) get_term_meta($t->term_id, 'memorial_color', true);
            $icon = (string) get_term_meta($t->term_id, 'memorial_icon', true);
            $out[$t->slug] = [
                'name' => $t->name,
                'color' => preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : $pal[$i % count($pal)],
                'icon' => isset(memorial_icon_choices()[$icon]) ? $icon : ($taxonomy === 'chapter' ? 'sparkle' : 'heart'),
                'desc' => $t->description,
                'quote' => (string) get_term_meta($t->term_id, 'memorial_quote', true),
                'who' => (string) get_term_meta($t->term_id, 'memorial_who', true),
                'term_id' => $t->term_id,
            ];
        }
    }
    $cache[$taxonomy] = $out;
    return $out;
}

/** פרקי חיים (הגדרה מהניהול: סיפורים > פרקי חיים) */
function memorial_chapters()
{
    return memorial_terms_data('chapter');
}

/** ערכים (הגדרה מהניהול: סיפורים > ערכים) */
function memorial_values()
{
    return memorial_terms_data('value');
}

function memorial_chapter_meta($term)
{
    $slug = is_object($term) ? $term->slug : $term;
    $all = memorial_chapters();
    return $all[$slug] ?? ['name' => is_object($term) ? $term->name : $slug, 'color' => '#4a5672', 'icon' => 'quote', 'desc' => ''];
}

function memorial_value_meta($term)
{
    $slug = is_object($term) ? $term->slug : $term;
    $all = memorial_values();
    return $all[$slug] ?? ['name' => is_object($term) ? $term->name : $slug, 'color' => '#4a5672', 'icon' => 'heart', 'desc' => '', 'quote' => '', 'who' => ''];
}

/** ציר הזמן שהוגדר בהגדרות: כל שורה "מתי | כותרת | טקסט | פרק (slug, לא חובה)" */
function memorial_timeline_items()
{
    $raw = (string) memorial_opt('timeline');
    $items = [];
    foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $p = array_map('trim', explode('|', $line));
        if (count($p) < 2) {
            continue;
        }
        $items[] = ['when' => $p[0], 'title' => $p[1], 'text' => $p[2] ?? '', 'chapter' => $p[3] ?? ''];
    }
    return $items;
}
