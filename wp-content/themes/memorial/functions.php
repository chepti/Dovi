<?php
/**
 * ערכת הנושא Memorial: אתר זיכרון משפחתי בעברית.
 * ללא בונה עמודים. טוענת רק מה שצריך. דורשת את התוסף Memorial Core.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('MEMORIAL_THEME_VERSION', '1.0.0');

require_once get_template_directory() . '/inc/icons.php';
require_once get_template_directory() . '/inc/illustrations.php';
require_once get_template_directory() . '/inc/config.php';
require_once get_template_directory() . '/inc/template-tags.php';

add_action('after_setup_theme', function () {
    load_theme_textdomain('memorial', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');
    register_nav_menus([
        'primary' => 'תפריט ראשי',
        'footer'  => 'תפריט תחתון',
    ]);
    set_post_thumbnail_size(768, 576, false);
    $GLOBALS['content_width'] = 760;
});

/** בלי התוסף Memorial Core אין תוכן להציג: הודעה למנהל ולגולש */
function memorial_core_missing()
{
    return !defined('MEMORIAL_CORE_VERSION');
}

add_action('admin_notices', function () {
    if (memorial_core_missing() && current_user_can('activate_plugins')) {
        echo '<div class="notice notice-error"><p>ערכת הנושא Memorial דורשת את התוסף <strong>Memorial Core</strong>. יש להתקין ולהפעיל אותו.</p></div>';
    }
});
add_filter('template_include', function ($tpl) {
    if (memorial_core_missing() && !is_admin()) {
        return get_template_directory() . '/inc/missing-plugin.php';
    }
    return $tpl;
}, 1);

/* ----- קבצים ----- */
add_action('wp_enqueue_scripts', function () {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    $v = function ($rel) use ($dir) {
        $f = $dir . '/' . $rel;
        return file_exists($f) ? filemtime($f) : MEMORIAL_THEME_VERSION;
    };

    wp_enqueue_style('memorial-fonts', $uri . '/assets/fonts/fonts.css', [], $v('assets/fonts/fonts.css'));
    wp_enqueue_style('memorial-main', $uri . '/assets/css/main.css', ['memorial-fonts'], $v('assets/css/main.css'));
    wp_enqueue_script('memorial-main', $uri . '/assets/js/main.js', [], $v('assets/js/main.js'), ['strategy' => 'defer', 'in_footer' => true]);
    wp_localize_script('memorial-main', 'MEMORIAL', [
        'ajax'    => admin_url('admin-post.php'),
        'home'    => home_url('/'),
        'random'  => home_url('/random-story/'),
        'page'    => (is_singular() || is_page()) ? get_permalink() : home_url(add_query_arg([])),
    ]);

    if (memorial_needs_map()) {
        wp_enqueue_style('memorial-leaflet', $uri . '/assets/vendor/leaflet/leaflet.css', [], '1.9.4');
        wp_enqueue_script('memorial-leaflet', $uri . '/assets/vendor/leaflet/leaflet.js', [], '1.9.4', ['strategy' => 'defer', 'in_footer' => true]);
        wp_enqueue_script('memorial-map', $uri . '/assets/js/map.js', ['memorial-leaflet'], $v('assets/js/map.js'), ['strategy' => 'defer', 'in_footer' => true]);
        wp_localize_script('memorial-map', 'MEMORIAL_MAP', ['marker' => $uri . '/assets/vendor/leaflet/images/', 'points' => memorial_map_points()]);
    }

    // לא צריך jQuery / בלוקים בצד הציבורי
    if (!is_admin() && !is_customize_preview()) {
        wp_dequeue_style('global-styles');
        if (!is_singular() || !has_blocks()) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
            wp_dequeue_style('classic-theme-styles');
        }
    }
}, 20);

function memorial_needs_map()
{
    if (is_singular('bench')) {
        return true;
    }
    if (is_page()) {
        return get_page_template_slug() === 'templates/page-places.php'
            || urldecode(get_post_field('post_name', get_queried_object_id())) === 'places';
    }
    return false;
}

/* ----- ניקוי head ----- */
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
});
add_filter('wp_resource_hints', function ($urls, $type) {
    return $type === 'dns-prefetch' ? array_filter($urls, function ($u) {
        return strpos(is_array($u) ? ($u['href'] ?? '') : $u, 's.w.org') === false;
    }) : $urls;
}, 10, 2);

/** פונטים קריטיים לטעינה מוקדמת */
add_action('wp_head', function () {
    $u = get_template_directory_uri() . '/assets/fonts/';
    foreach (['fredoka-hebrew.woff2', 'bona-hebrew-700.woff2', 'frank-hebrew.woff2'] as $f) {
        echo '<link rel="preload" href="' . esc_url($u . $f) . '" as="font" type="font/woff2" crossorigin>' . "\n";
    }
}, 1);

/** העלאת תמונת הדף הראשי לטעינה מוקדמת */
add_action('wp_head', function () {
    if (is_front_page() && ($id = memorial_hero_image_id())) {
        $src = wp_get_attachment_image_src($id, 'large');
        if ($src) {
            echo '<link rel="preload" as="image" href="' . esc_url($src[0]) . '" fetchpriority="high">' . "\n";
        }
    }
}, 2);

add_filter('body_class', function ($c) {
    if (is_front_page()) {
        $c[] = 'is-home';
    }
    if (is_page()) {
        $c[] = 'page-' . sanitize_html_class(urldecode(get_post_field('post_name', get_queried_object_id())));
    }
    return $c;
});

add_filter('excerpt_more', function () {
    return '…';
});
add_filter('excerpt_length', function () {
    return 28;
}, 99);

/** קישורי תוכן חיצוניים נפתחים בבטחה */
add_filter('the_content', function ($html) {
    if (!is_singular()) {
        return $html;
    }
    return preg_replace_callback('~<a\s+([^>]*href="https?://(?!' . preg_quote((string) wp_parse_url(home_url(), PHP_URL_HOST), '~') . ')[^"]+"[^>]*)>~i', function ($m) {
        $attrs = $m[1];
        if (stripos($attrs, 'rel=') === false) {
            $attrs .= ' rel="noopener noreferrer"';
        }
        if (stripos($attrs, 'target=') === false) {
            $attrs .= ' target="_blank"';
        }
        return '<a ' . $attrs . '>';
    }, $html);
}, 30);

/** תבניות לפי כתובת העמוד (slug), כך שמספיק ליצור עמוד בשם הנכון */
add_filter('template_include', function ($tpl) {
    if (!is_page() || memorial_core_missing()) {
        return $tpl;
    }
    $slug = urldecode(get_post_field('post_name', get_queried_object_id()));
    $map = [
        'stories' => 'page-stories', 'about' => 'page-about', 'places' => 'page-places', 'creations' => 'page-creations',
        'photos' => 'page-photos', 'share' => 'page-share', 'tributes' => 'page-tributes',
    ];
    if (isset($map[$slug])) {
        $f = get_template_directory() . '/templates/' . $map[$slug] . '.php';
        if (file_exists($f)) {
            return $f;
        }
    }
    return $tpl;
}, 99);

/** ארכיוני פרקים וערכים: הכול בעמוד אחד, בסדר יציב */
add_action('pre_get_posts', function ($q) {
    if (!is_admin() && $q->is_main_query() && ($q->is_tax('chapter') || $q->is_tax('value'))) {
        $q->set('posts_per_page', 80);
        $q->set('orderby', 'ID');
        $q->set('order', 'ASC');
    }
});

/** מטמון LiteSpeed: דפים ציבוריים ל-4 שעות (באנר האזכרה וסיפור היום תלויי-תאריך) */
add_action('send_headers', function () {
    if (is_admin() || is_user_logged_in() || is_search() || $_SERVER['REQUEST_METHOD'] !== 'GET') {
        return;
    }
    if (isset($_GET['memorial_sent']) || isset($_GET['memorial_err'])) {
        return;
    }
    header('X-LiteSpeed-Cache-Control: public,max-age=14400');
});

/** טקסט alt: ברירת מחדל מהכיתוב או הכותרת, כדי שלא יהיו תמונות חסרות alt */
add_filter('wp_get_attachment_image_attributes', function ($attr, $att) {
    if (empty($attr['alt'])) {
        $cap = $att->post_excerpt ?: $att->post_title;
        if ($cap && !preg_match('/^(IMG|WhatsApp|PXL|\d{4,})/i', $cap)) {
            $attr['alt'] = $cap;
        } elseif (function_exists('memorial_name')) {
            $attr['alt'] = 'תמונה מאתר הזיכרון של ' . memorial_name();
        }
    }
    return $attr;
}, 10, 2);
