<?php
if (!defined('ABSPATH')) {
    exit;
}

function memorial_register_content_types()
{
    // סיפורים
    register_post_type('story', [
        'labels' => [
            'name' => 'סיפורים',
            'singular_name' => 'סיפור',
            'add_new' => 'סיפור חדש',
            'add_new_item' => 'הוספת סיפור',
            'edit_item' => 'עריכת סיפור',
            'new_item' => 'סיפור חדש',
            'view_item' => 'צפייה בסיפור',
            'search_items' => 'חיפוש סיפורים',
            'not_found' => 'לא נמצאו סיפורים',
            'all_items' => 'כל הסיפורים',
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'story', 'with_front' => false],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'menu_icon' => 'dashicons-format-quote',
        'menu_position' => 5,
        'show_in_rest' => true,
    ]);

    // מקומות הנצחה (המזהה הפנימי נשאר bench: ספסל, נטיעה, פינת זיכרון, שביל)
    register_post_type('bench', [
        'labels' => [
            'name' => 'מקומות הנצחה',
            'singular_name' => 'מקום הנצחה',
            'add_new' => 'מקום חדש',
            'add_new_item' => 'הוספת מקום הנצחה',
            'edit_item' => 'עריכת מקום הנצחה',
            'all_items' => 'כל המקומות',
            'not_found' => 'לא נמצאו מקומות',
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'place', 'with_front' => false],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'menu_icon' => 'dashicons-location-alt',
        'menu_position' => 6,
        'show_in_rest' => true,
    ]);

    register_post_type('creation', [
        'labels' => [
            'name' => 'יצירות',
            'singular_name' => 'יצירה',
            'add_new' => 'יצירה חדשה',
            'add_new_item' => 'הוספת יצירה',
            'edit_item' => 'עריכת יצירה',
            'all_items' => 'כל היצירות',
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'has_archive' => false,
        'supports' => ['title', 'thumbnail', 'excerpt', 'page-attributes'],
        'menu_icon' => 'dashicons-art',
        'menu_position' => 7,
    ]);

    register_post_type('visit', [
        'labels' => [
            'name' => 'ביקורים במקומות',
            'singular_name' => 'ביקור במקום',
            'add_new' => 'ביקור חדש',
            'add_new_item' => 'הוספת תמונת ביקור',
            'edit_item' => 'עריכת ביקור',
            'all_items' => 'כל הביקורים',
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'has_archive' => false,
        'supports' => ['title', 'thumbnail', 'editor'],
        'menu_icon' => 'dashicons-camera',
        'menu_position' => 8,
    ]);

    // פרקי חיים (מוגדרים בניהול: צבע, אייקון, תיאור וסדר)
    register_taxonomy('chapter', ['story'], [
        'labels' => ['name' => 'פרקי חיים', 'singular_name' => 'פרק חיים', 'add_new_item' => 'פרק חדש', 'all_items' => 'כל הפרקים'],
        'public' => true,
        'hierarchical' => false,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'chapter', 'with_front' => false],
    ]);

    // ערכים (מוגדרים בניהול: צבע, אייקון, ציטוט ותיאור)
    register_taxonomy('value', ['story'], [
        'labels' => ['name' => 'ערכים', 'singular_name' => 'ערך', 'add_new_item' => 'ערך חדש', 'all_items' => 'כל הערכים'],
        'public' => true,
        'hierarchical' => false,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'value', 'with_front' => false],
    ]);

    // סוגי יצירה
    register_taxonomy('medium', ['creation'], [
        'labels' => ['name' => 'סוגי יצירה', 'singular_name' => 'סוג יצירה', 'all_items' => 'כל הסוגים'],
        'public' => false,
        'show_ui' => true,
        'hierarchical' => false,
        'show_admin_column' => true,
        'rewrite' => false,
    ]);

    // אלבומי תמונות (על קבצי מדיה)
    register_taxonomy('album', ['attachment'], [
        'labels' => ['name' => 'אלבומים', 'singular_name' => 'אלבום', 'all_items' => 'כל האלבומים', 'add_new_item' => 'אלבום חדש'],
        'public' => false,
        'show_ui' => true,
        'hierarchical' => false,
        'show_admin_column' => true,
        'rewrite' => false,
        'update_count_callback' => '_update_generic_term_count',
    ]);
}
add_action('init', 'memorial_register_content_types', 5);
