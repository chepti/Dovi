<?php
if (!defined('ABSPATH')) {
    exit;
}

/** עמודות ברשימת הסיפורים */
add_filter('manage_story_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['story_author'] = 'כותב/ת';
        }
    }
    return $new;
});
add_action('manage_story_posts_custom_column', function ($col, $id) {
    if ($col === 'story_author') {
        echo esc_html(get_post_meta($id, 'story_author', true));
    }
}, 10, 2);

/** ווידג'ט בלוח הבקרה: ממתינים לאישור */
add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget('memorial_pending', 'ממתינים לאישור', function () {
        $stories = get_posts(['post_type' => 'story', 'post_status' => 'pending', 'numberposts' => 20]);
        $visits = get_posts(['post_type' => 'visit', 'post_status' => 'pending', 'numberposts' => 20]);
        if (!$stories && !$visits) {
            echo '<p>אין כרגע סיפורים או תמונות שממתינים.</p>';
            return;
        }
        if ($stories) {
            echo '<h4>סיפורים (' . count($stories) . ')</h4><ul>';
            foreach ($stories as $s) {
                echo '<li><a href="' . esc_url(get_edit_post_link($s->ID)) . '">' . esc_html($s->post_title) . '</a> · ' . esc_html(get_post_meta($s->ID, 'story_author', true)) . '</li>';
            }
            echo '</ul>';
        }
        if ($visits) {
            echo '<h4>תמונות מביקורים (' . count($visits) . ')</h4><ul>';
            foreach ($visits as $v) {
                echo '<li><a href="' . esc_url(get_edit_post_link($v->ID)) . '">' . esc_html($v->post_title) . '</a></li>';
            }
            echo '</ul>';
        }
    });
});

/** מונה "ממתין" בתפריט */
add_action('admin_menu', function () {
    global $menu;
    foreach (['story', 'visit'] as $pt) {
        $n = (int) wp_count_posts($pt)->pending;
        if (!$n) {
            continue;
        }
        foreach ($menu as $k => $m) {
            if (isset($m[2]) && $m[2] === 'edit.php?post_type=' . $pt) {
                $menu[$k][0] .= ' <span class="awaiting-mod">' . $n . '</span>';
            }
        }
    }
}, 99);

/** כיבוי עורך בלוקים לסוגי התוכן שלנו (העורך הקלאסי פשוט יותר למשפחה) */
add_filter('use_block_editor_for_post_type', function ($use, $type) {
    return in_array($type, ['story', 'bench', 'visit', 'creation', 'post', 'page'], true) ? false : $use;
}, 10, 2);

/** הודעה למנהל כשערכת הנושא Memorial לא פעילה */
add_action('admin_notices', function () {
    if (!current_user_can('switch_themes') || memorial_theme_active()) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || !in_array($screen->id, ['plugins', 'dashboard', 'settings_page_memorial-settings'], true)) {
        return;
    }
    echo '<div class="notice notice-warning"><p>התוסף Memorial Core עובד יחד עם ערכת הנושא <strong>Memorial</strong>. כדי שהאתר יוצג נכון, יש להפעיל אותה במסך מראה &gt; ערכות נושא.</p></div>';
});
