<?php
if (!defined('ABSPATH')) {
    exit;
}

/** עמוד לפי slug: כתובת מלאה (עם חלופה אם העמוד לא נוצר) */
function memorial_page_url($slug)
{
    static $cache = [];
    if (!isset($cache[$slug])) {
        $p = get_page_by_path($slug);
        $cache[$slug] = $p ? get_permalink($p) : home_url('/' . $slug . '/');
    }
    return $cache[$slug];
}

/** האם קיים עמוד פורסם עם ה-slug הזה */
function memorial_page_exists($slug)
{
    static $cache = [];
    if (!isset($cache[$slug])) {
        $p = get_page_by_path($slug);
        $cache[$slug] = $p && $p->post_status === 'publish';
    }
    return $cache[$slug];
}

/** קישור בתפריט רק אם העמוד קיים: [כתובת, כותרת העמוד] או null */
function memorial_page_link($slug, $label = '')
{
    if (!memorial_page_exists($slug)) {
        return null;
    }
    $p = get_page_by_path($slug);
    return ['url' => get_permalink($p), 'label' => $label ?: get_the_title($p)];
}

/** תמונה ראשית של האתר (מוגדרת בהגדרות), או 0 */
function memorial_hero_image_id()
{
    return function_exists('memorial_opt') ? (int) memorial_opt('hero_image') : 0;
}

/** קטלוג "ממשיכים לגלות": כל עמוד מפנה להמשך. מוצגים רק עמודים שקיימים */
function memorial_catalog()
{
    $n = function_exists('memorial_short') ? memorial_short() : '';
    $all = [
        'stories'   => ['title' => 'סיפורים', 'blurb' => 'חברים ומשפחה מספרים על רגעים קטנים.', 'icon' => 'book'],
        'about'     => ['title' => 'אודות' . ($n ? ' ' . $n : ''), 'blurb' => 'הסיפור שמאחורי השם.', 'icon' => 'user'],
        'places'    => ['title' => 'מקומות הנצחה', 'blurb' => 'מקומות שנעשו לזכר ' . ($n ?: 'האדם') . ', על המפה.', 'icon' => 'map-pin'],
        'creations' => ['title' => 'יצירות', 'blurb' => 'ציורים, מתקנים ויצירות מכל סוג.', 'icon' => 'palette'],
        'tributes'  => ['title' => 'הספדים והנצחה', 'blurb' => 'דברי פרידה, הודעות וסיקור.', 'icon' => 'flame'],
        'photos'    => ['title' => 'אלבום תמונות', 'blurb' => 'תמונות מהחיים.', 'icon' => 'image'],
    ];
    $out = [];
    foreach ($all as $slug => $c) {
        if (memorial_page_exists($slug)) {
            $c['slug'] = $slug;
            $out[$slug] = $c;
        }
    }
    return $out;
}

/** נקודות מפה למקומות ההנצחה */
function memorial_map_points()
{
    $out = [];
    $q = get_posts(['post_type' => 'bench', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'meta_value_num', 'meta_key' => 'bench_order', 'order' => 'ASC']);
    foreach ($q as $b) {
        $lat = get_post_meta($b->ID, 'bench_lat', true);
        $lng = get_post_meta($b->ID, 'bench_lng', true);
        if (!$lat || !$lng) {
            continue;
        }
        $img = get_post_thumbnail_id($b->ID);
        if (!$img) {
            $g = memorial_gallery_ids($b->ID, 'bench_gallery');
            $img = $g ? $g[0] : 0;
        }
        $out[] = [
            'id' => $b->ID,
            'title' => get_the_title($b),
            'url' => get_permalink($b),
            'lat' => (float) $lat,
            'lng' => (float) $lng,
            'approx' => get_post_meta($b->ID, 'bench_approx', true) === '1',
            'region' => get_post_meta($b->ID, 'bench_region', true),
            'img' => $img ? (wp_get_attachment_image_url($img, 'medium') ?: '') : '',
        ];
    }
    return $out;
}
