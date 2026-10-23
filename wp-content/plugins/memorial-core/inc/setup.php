<?php
/**
 * התקנה ראשונית: עמודים בסיסיים, פרקים וערכים לדוגמה.
 * לא דורס כלום: מוסיף רק מה שחסר.
 */
if (!defined('ABSPATH')) {
    exit;
}

/** עמודי האתר: slug => כותרת. ערכת הנושא מזהה אותם לפי ה-slug ומחילה עליהם את התבנית המתאימה */
function memorial_default_pages()
{
    return [
        'stories'       => 'סיפורים',
        'about'         => 'אודות',
        'places'        => 'מקומות הנצחה',
        'creations'     => 'יצירות',
        'photos'        => 'אלבום תמונות',
        'share'         => 'שליחת סיפור או תמונה',
        'tributes'      => 'הספדים והנצחה',
        'accessibility' => 'הצהרת נגישות',
        'privacy'       => 'מדיניות פרטיות',
    ];
}

function memorial_default_page_content($slug)
{
    $contact = memorial_opt('contact_email');
    if ($slug === 'accessibility') {
        return "<p>אנחנו רוצים שהאתר יהיה נגיש לכולם. האתר בנוי עם מבנה כותרות ברור, ניווט במקלדת, ניגודיות טובה, טקסט חלופי לתמונות ותמיכה מלאה בכתיבה מימין לשמאל. בפינת המסך יש סרגל נגישות להגדלת טקסט, ניגודיות גבוהה, הדגשת קישורים, גופן קריא ועצירת אנימציות.</p>\n<p>נתקלתם בקושי? כתבו לנו ל-" . esc_html($contact) . " ונטפל בכך בהקדם.</p>";
    }
    if ($slug === 'privacy') {
        return "<p>האתר נועד להנצחה ולשיתוף זיכרונות. מי ששולח סיפור או תמונה מאשר את פרסומם באתר בשמו כפי שכתב. כתובות מייל שנמסרות בטפסים משמשות רק ליצירת קשר עם השולח/ת, ואינן מוצגות באתר.</p>\n<p>כל תוכן שנשלח נבדק לפני פרסום. אפשר לבקש הסרה של סיפור או תמונה בכל עת במייל " . esc_html($contact) . ".</p>\n<p>האתר אינו משתמש בעוגיות שיווק. אם הוגדרה מדידת צפיות (Google Analytics), היא סופרת צפיות בעמודים בלבד, ללא מידע אישי.</p>";
    }
    return '';
}

/** פרקי חיים וערכים לדוגמה (עורכים אותם אחר כך במסך הניהול) */
function memorial_default_terms()
{
    return [
        'chapter' => [
            ['childhood', 'ילדות', '#e9a825', 'sparkle', 'שנות הילדות והבית שבו גדל.'],
            ['youth', 'נעורים', '#e8743b', 'palette', 'שנות הנעורים, החברים והתחביבים.'],
            ['studies', 'לימודים', '#3f8fd6', 'graduation', 'שנות הלימוד והחברים שמהן.'],
            ['service', 'שירות', '#5a8f4a', 'users', 'השירות הצבאי או הלאומי, והחברים לדרך.'],
            ['work', 'עבודה', '#2f9aa8', 'ruler', 'הדרך המקצועית.'],
            ['family', 'משפחה', '#d45d8f', 'heart', 'הבית והמשפחה: הרגעים הקטנים שבהם היה הכי הוא.'],
            ['legacy', 'יצירה ונתינה', '#7b4a9e', 'hammer', 'מה יצר ומה נתן לאחרים.'],
        ],
        'value' => [
            ['connection', 'חיבור', '#e8743b', 'users', ''],
            ['giving', 'נתינה', '#5a8f4a', 'heart', ''],
            ['creativity', 'יצירתיות', '#e9a825', 'lightbulb', ''],
            ['humor', 'הומור וחיוך', '#3f8fd6', 'sun', ''],
        ],
    ];
}

function memorial_seed_defaults($with_pages = false)
{
    // הטקסונומיות רשומות ב-init; בהפעלת התוסף נרשום אותן ידנית אם צריך
    if (!taxonomy_exists('chapter')) {
        memorial_register_content_types();
    }
    foreach (memorial_default_terms() as $tax => $rows) {
        $existing = get_terms(['taxonomy' => $tax, 'hide_empty' => false, 'fields' => 'ids']);
        if (is_wp_error($existing) || $existing) {
            continue; // כבר הוגדרו, לא דורסים
        }
        foreach ($rows as $i => $r) {
            $res = wp_insert_term($r[1], $tax, ['slug' => $r[0], 'description' => $r[4]]);
            if (!is_wp_error($res)) {
                update_term_meta($res['term_id'], 'memorial_color', $r[2]);
                update_term_meta($res['term_id'], 'memorial_icon', $r[3]);
                update_term_meta($res['term_id'], 'memorial_order', (string) ($i + 1));
            }
        }
    }
    if ($with_pages) {
        foreach (memorial_default_pages() as $slug => $title) {
            if (get_page_by_path($slug)) {
                continue;
            }
            wp_insert_post([
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_name' => $slug,
                'post_title' => $title,
                'post_content' => memorial_default_page_content($slug),
            ]);
        }
    }
}

add_action('admin_post_memorial_setup', function () {
    if (!current_user_can('manage_options')) {
        wp_die('אין הרשאה.');
    }
    check_admin_referer('memorial_setup');
    memorial_seed_defaults(true);
    flush_rewrite_rules();
    wp_safe_redirect(admin_url('options-general.php?page=memorial-settings&memorial_setup=1'));
    exit;
});
