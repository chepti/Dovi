<?php
/**
 * SEO: כותרות, תיאורים, Open Graph, סכמות JSON-LD, robots, מפת אתר ו-llms.txt
 * (כל הטקסטים נגזרים מהגדרות האתר, ללא תוכן קבוע)
 */
if (!defined('ABSPATH')) {
    exit;
}

function memorial_site_name()
{
    return memorial_title_name();
}

function memorial_site_tagline()
{
    return memorial_title_name() . ': אתר הזיכרון';
}

/** תיאור ברירת מחדל לאתר */
function memorial_home_description()
{
    $about = trim((string) memorial_opt('footer_about'));
    $lede = trim((string) memorial_opt('hero_lede'));
    $d = $about ?: $lede;
    if (!$d) {
        $d = 'אתר הזיכרון של ' . memorial_name() . ': סיפורים מחברים ומשפחה, מקומות הנצחה, יצירות ואלבום תמונות.';
    }
    return $d;
}

function memorial_current_page_slug()
{
    if (is_page()) {
        return urldecode(get_post_field('post_name', get_queried_object_id()));
    }
    return '';
}

function memorial_seo_title($default = '')
{
    if (!memorial_theme_active()) {
        return $default;
    }
    $sep = ' | ';
    $name = memorial_name();
    $site = memorial_site_name();

    if (is_front_page()) {
        return memorial_site_tagline() . $sep . 'סיפורים, מקומות ויצירה';
    }
    if (is_singular('story')) {
        $a = get_post_meta(get_the_ID(), 'story_author', true);
        $t = get_the_title();
        return $a ? "$t – $a על $name" : "$t – סיפור על $name";
    }
    if (is_singular('bench')) {
        return get_the_title() . ' – מקום הנצחה לזכר ' . $name;
    }
    if (is_tax('chapter')) {
        return $name . ': ' . single_term_title('', false) . $sep . 'סיפורים';
    }
    if (is_tax('value')) {
        return single_term_title('', false) . ' – ערך בחייו של ' . $name;
    }
    if (is_singular('post')) {
        return get_the_title() . $sep . $name;
    }
    if (is_page()) {
        return get_the_title() . $sep . $site;
    }
    if (is_search()) {
        return 'חיפוש: ' . get_search_query() . $sep . $site;
    }
    if (is_404()) {
        return 'העמוד לא נמצא' . $sep . $site;
    }
    return $site;
}
add_filter('pre_get_document_title', 'memorial_seo_title', 20);

function memorial_seo_description()
{
    $d = '';
    if (is_front_page()) {
        $d = memorial_home_description();
    } elseif (is_singular(['story', 'bench', 'post', 'page'])) {
        $d = memorial_excerpt(get_the_ID(), 155);
        if (is_singular('story') && $d) {
            $a = get_post_meta(get_the_ID(), 'story_author', true);
            if ($a) {
                $d = trim($a . ': ' . $d);
            }
        }
    } elseif (is_tax(['chapter', 'value'])) {
        $t = get_queried_object();
        $d = $t->description ?: ('סיפורים על ' . memorial_name() . ' בפרק "' . $t->name . '".');
    }
    if (!$d) {
        $d = memorial_home_description();
    }
    return mb_substr($d, 0, 200);
}

/** תמונה לשיתוף: ['url','w','h'] או null אם אין */
function memorial_seo_image()
{
    if (is_singular() && has_post_thumbnail()) {
        $src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        if ($src) {
            return ['url' => $src[0], 'w' => $src[1], 'h' => $src[2]];
        }
    }
    if (is_singular('bench')) {
        $ids = memorial_gallery_ids(get_the_ID(), 'bench_gallery');
        if ($ids) {
            $src = wp_get_attachment_image_src($ids[0], 'large');
            if ($src) {
                return ['url' => $src[0], 'w' => $src[1], 'h' => $src[2]];
            }
        }
    }
    return memorial_default_og();
}

function memorial_canonical_url()
{
    if (is_singular()) {
        return get_permalink();
    }
    if (is_front_page()) {
        return home_url('/');
    }
    if (is_tax()) {
        return get_term_link(get_queried_object());
    }
    return '';
}

/** ניתוב ה-meta ב-head */
add_action('wp_head', function () {
    if (is_admin() || !memorial_theme_active()) {
        return;
    }
    $title = memorial_seo_title();
    $desc = memorial_seo_description();
    $img = memorial_seo_image();
    $url = memorial_canonical_url() ?: home_url(add_query_arg([]));
    $type = is_singular(['story', 'post']) ? 'article' : 'website';

    echo "\n<!-- memorial-seo -->\n";
    echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta name="theme-color" content="#1f2b45">' . "\n";
    echo '<meta property="og:locale" content="he_IL">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(memorial_site_tagline()) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    if ($img) {
        echo '<meta property="og:image" content="' . esc_url($img['url']) . '">' . "\n";
        echo '<meta property="og:image:width" content="' . (int) $img['w'] . '">' . "\n";
        echo '<meta property="og:image:height" content="' . (int) $img['h'] . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="' . ($img ? 'summary_large_image' : 'summary') . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($img) {
        echo '<meta name="twitter:image" content="' . esc_url($img['url']) . '">' . "\n";
    }
    if (is_singular(['story', 'post'])) {
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";
    }
    if ($canon = memorial_canonical_url()) {
        // ליבת וורדפרס כבר מדפיסה rel=canonical לתוכן בודד; כאן רק לשאר
        if (!is_singular()) {
            echo '<link rel="canonical" href="' . esc_url($canon) . '">' . "\n";
        }
    }
    memorial_print_jsonld();
    echo "<!-- /memorial-seo -->\n";
}, 3);

add_filter('wp_robots', function ($robots) {
    if (!memorial_theme_active()) {
        return $robots;
    }
    if (is_search() || is_404() || isset($_GET['memorial_sent']) || isset($_GET['memorial_err'])) {
        $robots['noindex'] = true;
        $robots['follow'] = true;
        unset($robots['index']);
    } else {
        $robots['index'] = true;
        $robots['follow'] = true;
        $robots['max-image-preview'] = 'large';
        $robots['max-snippet'] = '-1';
    }
    return $robots;
});

/* ----- Google Analytics (GA4): צפיות בעמודים בלבד, בלי מידע אישי ----- */
add_action('wp_head', function () {
    if (is_admin() || is_user_logged_in() || !memorial_theme_active()) {
        return;
    }
    $id = (string) memorial_opt('ga_id');
    if (!$id || !preg_match('/^G-[A-Z0-9]{6,14}$/', $id)) {
        return;
    }
    echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr($id) . '"></script>' . "\n";
    echo '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",' . wp_json_encode($id) . ');</script>' . "\n";
}, 4);

/* ----- JSON-LD ----- */
function memorial_about_page_url()
{
    $p = get_page_by_path('about');
    return $p ? get_permalink($p) : home_url('/');
}

function memorial_person_node()
{
    $home = home_url('/');
    $node = [
        '@type' => 'Person',
        '@id' => $home . '#person',
        'name' => memorial_name(),
        'url' => memorial_about_page_url(),
    ];
    if ($by = memorial_opt('birth_year')) {
        $node['birthDate'] = (string) $by;
    }
    if ($dd = memorial_opt('death_date')) {
        $node['deathDate'] = (string) $dd;
    }
    if ($about = trim((string) memorial_opt('footer_about'))) {
        $node['description'] = $about;
    }
    if ($img = memorial_default_og()) {
        $node['image'] = $img['url'];
    }
    return $node;
}

function memorial_print_jsonld()
{
    $home = home_url('/');
    $graph = [];

    $graph[] = [
        '@type' => 'WebSite',
        '@id' => $home . '#website',
        'url' => $home,
        'name' => memorial_site_tagline(),
        'inLanguage' => 'he-IL',
        'about' => ['@id' => $home . '#person'],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => $home . '?s={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $on_about = is_page() && memorial_current_page_slug() === 'about';
    if (is_front_page() || $on_about) {
        $graph[] = memorial_person_node();
    }

    if (is_singular('story') || is_singular('post')) {
        $author = get_post_meta(get_the_ID(), 'story_author', true);
        $img = memorial_seo_image();
        $logo = get_site_icon_url(512);
        $art = [
            '@type' => 'Article',
            '@id' => get_permalink() . '#article',
            'headline' => mb_substr(get_the_title(), 0, 110),
            'description' => memorial_excerpt(get_the_ID(), 200),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'inLanguage' => 'he-IL',
            'mainEntityOfPage' => get_permalink(),
            'about' => ['@id' => $home . '#person'],
            'isPartOf' => ['@id' => $home . '#website'],
            'publisher' => ['@type' => 'Organization', 'name' => 'אתר הזיכרון של ' . memorial_name()],
        ];
        if ($img) {
            $art['image'] = [$img['url']];
        }
        if ($logo) {
            $art['publisher']['logo'] = ['@type' => 'ImageObject', 'url' => $logo];
        }
        $art['author'] = $author ? ['@type' => 'Person', 'name' => $author] : ['@type' => 'Organization', 'name' => 'המשפחה והחברים של ' . memorial_short()];
        $graph[] = $art;
    }

    if (is_singular('bench')) {
        $id = get_the_ID();
        $img = memorial_seo_image();
        $place = [
            '@type' => 'Place',
            '@id' => get_permalink() . '#place',
            'name' => get_the_title(),
            'description' => memorial_excerpt($id, 200),
            'url' => get_permalink(),
        ];
        if ($img) {
            $place['image'] = $img['url'];
        }
        $lat = get_post_meta($id, 'bench_lat', true);
        $lng = get_post_meta($id, 'bench_lng', true);
        if ($lat && $lng && get_post_meta($id, 'bench_approx', true) !== '1') {
            $place['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float) $lat, 'longitude' => (float) $lng];
        }
        $graph[] = $place;
    }

    $bc = memorial_get_breadcrumbs();
    if (count($bc) > 1) {
        $items = [];
        foreach ($bc as $i => $b) {
            $it = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => wp_strip_all_tags($b['label'])];
            if (!empty($b['url'])) {
                $it['item'] = $b['url'];
            }
            $items[] = $it;
        }
        $graph[] = ['@type' => 'BreadcrumbList', '@id' => (memorial_canonical_url() ?: $home) . '#breadcrumb', 'itemListElement' => $items];
    }

    echo '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

/** כתובת ושם של עמוד לפי ה-slug שלו (עם חלופה כשהעמוד לא נוצר) */
function memorial_page_ref($slug, $fallback_label)
{
    $p = get_page_by_path($slug);
    return ['label' => $p ? get_the_title($p) : $fallback_label, 'url' => $p ? get_permalink($p) : home_url('/' . $slug . '/')];
}

/** פירורי לחם (משותף לתבנית ולסכמה) */
function memorial_get_breadcrumbs()
{
    $home = ['label' => 'בית', 'url' => home_url('/')];
    $items = [$home];

    if (is_front_page()) {
        return [$home];
    }
    if (is_singular('story')) {
        $items[] = memorial_page_ref('stories', 'סיפורים');
        $terms = get_the_terms(get_the_ID(), 'chapter');
        if ($terms && !is_wp_error($terms)) {
            $t = array_values($terms)[0];
            $items[] = ['label' => $t->name, 'url' => get_term_link($t)];
        }
        $items[] = ['label' => get_the_title(), 'url' => ''];
    } elseif (is_singular('bench')) {
        $items[] = memorial_page_ref('places', 'מקומות הנצחה');
        $items[] = ['label' => get_the_title(), 'url' => ''];
    } elseif (is_singular('post')) {
        $items[] = memorial_page_ref('tributes', 'הספדים והנצחה');
        $items[] = ['label' => get_the_title(), 'url' => ''];
    } elseif (is_tax('chapter') || is_tax('value')) {
        $items[] = memorial_page_ref('stories', 'סיפורים');
        $items[] = ['label' => single_term_title('', false), 'url' => ''];
    } elseif (is_page()) {
        $items[] = ['label' => get_the_title(), 'url' => ''];
    } elseif (is_search()) {
        $items[] = ['label' => 'חיפוש', 'url' => ''];
    } elseif (is_404()) {
        $items[] = ['label' => 'לא נמצא', 'url' => ''];
    }
    return $items;
}

/* ----- מפת אתר (WordPress core) ----- */
add_filter('wp_sitemaps_post_types', function ($types) {
    foreach (array_keys($types) as $k) {
        if (!in_array($k, ['page', 'post', 'story', 'bench'], true)) {
            unset($types[$k]);
        }
    }
    return $types;
});
add_filter('wp_sitemaps_taxonomies', function ($tax) {
    foreach (array_keys($tax) as $k) {
        if (!in_array($k, ['chapter', 'value'], true)) {
            unset($tax[$k]);
        }
    }
    return $tax;
});
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    return $name === 'users' ? false : $provider;
}, 10, 2);
add_filter('wp_sitemaps_posts_entry', function ($entry, $post) {
    $entry['lastmod'] = get_post_modified_time('c', true, $post);
    return $entry;
}, 10, 2);
add_filter('wp_sitemaps_posts_query_args', function ($args, $type) {
    $args['post_status'] = 'publish';
    return $args;
}, 10, 2);

add_filter('robots_txt', function ($output, $public) {
    if (!memorial_theme_active()) {
        return $output;
    }
    $out = "User-agent: *\nAllow: /\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\nDisallow: /?s=\nDisallow: /search/\nDisallow: /wp-json/\nDisallow: /random-story/\n\n";
    $out .= 'Sitemap: ' . home_url('/wp-sitemap.xml') . "\n";
    return $out;
}, 10, 2);

/* ----- llms.txt ----- */
add_action('init', function () {
    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($path !== 'llms.txt') {
        return;
    }
    nocache_headers();
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    $lines = [];
    $lines[] = '# ' . memorial_site_tagline();
    $lines[] = '';
    $dates = '';
    if (memorial_opt('birth_year') || memorial_opt('death_date')) {
        $dd = (string) memorial_opt('death_date');
        $dates = ' (' . memorial_opt('birth_year') . '-' . ($dd ? substr($dd, 0, 4) : '') . ')';
    }
    $lines[] = '> אתר זיכרון בעברית ל' . memorial_title_name() . $dates . '. ' . memorial_home_description() . ' כל התוכן בעברית, מימין לשמאל, ובבעלות המשפחה.';
    $lines[] = '';
    $lines[] = '## עמודים עיקריים';
    $pages = [
        'about' => 'מי היה/הייתה', 'stories' => 'סיפורים מכל פרקי החיים', 'places' => 'מקומות הנצחה, מפה ותמונות',
        'creations' => 'יצירות', 'tributes' => 'הספדים והנצחה', 'photos' => 'אלבום תמונות', 'share' => 'שליחת סיפור או תמונה',
    ];
    foreach ($pages as $slug => $desc) {
        $p = get_page_by_path($slug);
        if ($p) {
            $lines[] = '- [' . get_the_title($p) . '](' . get_permalink($p) . '): ' . $desc;
        }
    }
    $lines[] = '';
    $lines[] = '## סיפורים';
    $stories = get_posts(['post_type' => 'story', 'numberposts' => 100, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC']);
    foreach ($stories as $s) {
        $a = get_post_meta($s->ID, 'story_author', true);
        $lines[] = '- [' . $s->post_title . '](' . get_permalink($s) . ')' . ($a ? ': ' . $a : '');
    }
    $lines[] = '';
    $lines[] = '## מפת אתר';
    $lines[] = '- [wp-sitemap.xml](' . home_url('/wp-sitemap.xml') . ')';
    echo implode("\n", $lines) . "\n";
    exit;
}, 1);
