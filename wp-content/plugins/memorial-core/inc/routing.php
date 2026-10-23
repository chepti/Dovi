<?php
/**
 * סיפור אקראי (/random-story/) והרחבת החיפוש לסוגי התוכן של האתר.
 */
if (!defined('ABSPATH')) {
    exit;
}

function memorial_request_path()
{
    $p = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return '/' . trim(urldecode((string) $p), '/');
}

add_action('template_redirect', function () {
    if (memorial_request_path() !== '/random-story') {
        return;
    }
    $q = get_posts(['post_type' => 'story', 'post_status' => 'publish', 'numberposts' => 1, 'orderby' => 'rand', 'fields' => 'ids']);
    nocache_headers();
    header('X-Robots-Tag: noindex');
    $stories = get_page_by_path('stories');
    wp_safe_redirect($q ? get_permalink($q[0]) : ($stories ? get_permalink($stories) : home_url('/')), 302);
    exit;
}, 1);

/** חיפוש באתר: סיפורים, מקומות, עמודים ופוסטים */
add_action('pre_get_posts', function ($q) {
    if (!is_admin() && $q->is_main_query() && $q->is_search()) {
        $q->set('post_type', ['story', 'post', 'page', 'bench']);
        $q->set('posts_per_page', 24);
    }
});
