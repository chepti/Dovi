<?php
if (!defined('ABSPATH')) {
    exit;
}

/** שדות מותאמים לכל סוג תוכן (בלי ACF) */
function memorial_meta_schema()
{
    $short = memorial_short();
    return [
        'story' => [
            'title' => 'פרטי הסיפור',
            'fields' => [
                'story_author'   => ['label' => 'שם הכותב/ת', 'type' => 'text', 'help' => 'כך יופיע בכותרת הסיפור'],
                'story_relation' => ['label' => 'הקשר ל' . $short, 'type' => 'text', 'help' => 'למשל: אח, חבר מהלימודים, מפקד, שכן'],
                'story_source'   => ['label' => 'קישור למקור (אם יש)', 'type' => 'url'],
                'story_related'  => ['label' => 'מזהה פוסט קשור (הספד מלא)', 'type' => 'number', 'help' => 'אופציונלי'],
                'story_gallery'  => ['label' => 'תמונות נוספות', 'type' => 'gallery'],
                'story_email'    => ['label' => 'מייל השולח/ת (פנימי, לא מוצג)', 'type' => 'text'],
            ],
        ],
        'bench' => [
            'title' => 'פרטי המקום',
            'fields' => [
                'bench_region'     => ['label' => 'אזור', 'type' => 'text', 'help' => 'למשל: גליל תחתון, שפלה'],
                'bench_maps'       => ['label' => 'קישור למפות גוגל', 'type' => 'url', 'help' => 'הדביקו קישור ממפות גוגל, והקואורדינטות יתמלאו אוטומטית בשמירה'],
                'bench_lat'        => ['label' => 'קו רוחב (lat)', 'type' => 'text'],
                'bench_lng'        => ['label' => 'קו אורך (lng)', 'type' => 'text'],
                'bench_approx'     => ['label' => 'מיקום משוער בלבד (מציג אזור במקום סיכה מדויקת)', 'type' => 'checkbox'],
                'bench_directions' => ['label' => 'איך מגיעים', 'type' => 'textarea'],
                'bench_video'      => ['label' => 'קישור לסרטון (פייסבוק)', 'type' => 'url', 'help' => 'אופציונלי. הסרטון נטען רק אחרי לחיצה'],
                'bench_gallery'    => ['label' => 'גלריית תמונות', 'type' => 'gallery'],
                'bench_order'      => ['label' => 'סדר הצגה', 'type' => 'number'],
            ],
        ],
        'creation' => [
            'title' => 'פרטי היצירה',
            'fields' => [
                'creation_year' => ['label' => 'שנה / תקופה', 'type' => 'text'],
                'creation_alt'  => ['label' => 'תיאור לקוראי מסך (alt)', 'type' => 'text', 'help' => 'תיאור קצר של מה שרואים בתמונה'],
            ],
        ],
        'visit' => [
            'title' => 'פרטי הביקור',
            'fields' => [
                'visit_bench' => ['label' => 'מקום (מזהה)', 'type' => 'bench'],
                'visit_name'  => ['label' => 'שם המבקר/ת', 'type' => 'text'],
                'visit_email' => ['label' => 'מייל (פנימי)', 'type' => 'text'],
            ],
        ],
    ];
}

add_action('add_meta_boxes', function () {
    foreach (memorial_meta_schema() as $type => $def) {
        add_meta_box('memorial_' . $type . '_meta', $def['title'], 'memorial_render_meta_box', $type, 'normal', 'high', ['type' => $type]);
    }
    add_meta_box('memorial_story_help', 'איך מפרסמים?', function () {
        echo '<p>סיפור שנשלח דרך האתר מופיע כאן במצב <strong>ממתין לבדיקה</strong>. קראו, תקנו אם צריך, ולחצו <strong>פרסום</strong> כדי שיופיע באתר.</p>';
    }, 'story', 'side');
});

function memorial_render_meta_box($post, $box)
{
    $type = $box['args']['type'];
    $schema = memorial_meta_schema()[$type]['fields'];
    wp_nonce_field('memorial_meta_save', 'memorial_meta_nonce');
    echo '<table class="form-table" role="presentation"><tbody>';
    foreach ($schema as $key => $f) {
        $val = get_post_meta($post->ID, $key, true);
        echo '<tr><th scope="row"><label for="' . esc_attr($key) . '">' . esc_html($f['label']) . '</label></th><td>';
        switch ($f['type']) {
            case 'textarea':
                echo '<textarea class="large-text" rows="4" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '">' . esc_textarea($val) . '</textarea>';
                break;
            case 'checkbox':
                echo '<label><input type="checkbox" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="1" ' . checked($val, '1', false) . '> כן</label>';
                break;
            case 'gallery':
                $ids = array_filter(array_map('intval', explode(',', (string) $val)));
                echo '<div class="memorial-gallery-field" data-target="' . esc_attr($key) . '">';
                echo '<input type="hidden" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr(implode(',', $ids)) . '">';
                echo '<div class="memorial-gallery-thumbs" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px">';
                foreach ($ids as $id) {
                    echo '<span data-id="' . (int) $id . '">' . wp_get_attachment_image($id, [80, 80]) . '</span>';
                }
                echo '</div><button type="button" class="button memorial-gallery-pick">בחירת תמונות</button> <button type="button" class="button memorial-gallery-clear">ניקוי</button>';
                echo '</div>';
                break;
            case 'bench':
                $places = get_posts(['post_type' => 'bench', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
                echo '<select id="' . esc_attr($key) . '" name="' . esc_attr($key) . '"><option value="">— בחרו —</option>';
                foreach ($places as $b) {
                    echo '<option value="' . (int) $b->ID . '" ' . selected((int) $val, $b->ID, false) . '>' . esc_html($b->post_title) . '</option>';
                }
                echo '</select>';
                break;
            default:
                $t = in_array($f['type'], ['url', 'number'], true) ? $f['type'] : 'text';
                echo '<input class="regular-text" type="' . esc_attr($t) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" style="width:100%;max-width:520px">';
        }
        if (!empty($f['help'])) {
            echo '<p class="description">' . esc_html($f['help']) . '</p>';
        }
        echo '</td></tr>';
    }
    echo '</tbody></table>';
}

add_action('save_post', function ($post_id, $post) {
    if (!isset($_POST['memorial_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['memorial_meta_nonce'])), 'memorial_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $schema = memorial_meta_schema();
    if (!isset($schema[$post->post_type])) {
        return;
    }
    foreach ($schema[$post->post_type]['fields'] as $key => $f) {
        if ($f['type'] === 'checkbox') {
            update_post_meta($post_id, $key, isset($_POST[$key]) ? '1' : '0');
            continue;
        }
        if (!isset($_POST[$key])) {
            continue;
        }
        $raw = wp_unslash($_POST[$key]);
        switch ($f['type']) {
            case 'url':
                $v = esc_url_raw($raw);
                break;
            case 'textarea':
                $v = sanitize_textarea_field($raw);
                break;
            case 'number':
            case 'bench':
                $v = $raw === '' ? '' : (string) (int) $raw;
                break;
            case 'gallery':
                $v = implode(',', array_filter(array_map('intval', explode(',', $raw))));
                break;
            default:
                $v = sanitize_text_field($raw);
        }
        update_post_meta($post_id, $key, $v);
    }

    // קישור מפות גוגל -> קואורדינטות
    if ($post->post_type === 'bench') {
        $url = get_post_meta($post_id, 'bench_maps', true);
        if ($url && (!get_post_meta($post_id, 'bench_lat', true) || isset($_POST['bench_maps']))) {
            $c = memorial_coords_from_maps_url($url);
            if ($c) {
                update_post_meta($post_id, 'bench_lat', $c[0]);
                update_post_meta($post_id, 'bench_lng', $c[1]);
            }
        }
    }
}, 10, 2);

/** פענוח קואורדינטות מקישור מפות גוגל (כולל קישור קצר maps.app.goo.gl) */
function memorial_coords_from_maps_url($url)
{
    $try = function ($u) {
        if (preg_match('~@(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)~', $u, $m)) {
            return [$m[1], $m[2]];
        }
        if (preg_match('~[?&](?:q|ll|center)=(-?\d{1,2}\.\d+)(?:,|%2C)(-?\d{1,3}\.\d+)~i', $u, $m)) {
            return [$m[1], $m[2]];
        }
        if (preg_match('~!3d(-?\d{1,2}\.\d+)!4d(-?\d{1,3}\.\d+)~', $u, $m)) {
            return [$m[1], $m[2]];
        }
        return null;
    };
    if ($c = $try($url)) {
        return $c;
    }
    $host = (string) wp_parse_url($url, PHP_URL_HOST);
    if (!preg_match('~(^|\.)(goo\.gl|google\.com|google\.co\.il)$~', $host)) {
        return null;
    }
    $res = wp_remote_get($url, ['timeout' => 8, 'redirection' => 5, 'user-agent' => 'Mozilla/5.0']);
    if (is_wp_error($res)) {
        return null;
    }
    $body = wp_remote_retrieve_body($res);
    if ($c = $try($body)) {
        return $c;
    }
    if (!empty($res['http_response']) && method_exists($res['http_response'], 'get_response_object')) {
        $final = $res['http_response']->get_response_object()->url ?? '';
        if ($final && ($c = $try($final))) {
            return $c;
        }
    }
    return null;
}

/** סקריפט בחירת תמונות במסך העריכה */
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    wp_enqueue_media();
    wp_add_inline_script('jquery-core', <<<'JS'
jQuery(function($){
  $(document).on('click', '.memorial-gallery-pick', function(e){
    e.preventDefault();
    var box = $(this).closest('.memorial-gallery-field');
    var input = box.find('input[type=hidden]');
    var thumbs = box.find('.memorial-gallery-thumbs');
    var frame = wp.media({title:'בחירת תמונות', multiple:'add', library:{type:'image'}});
    frame.on('select', function(){
      var ids = [];
      thumbs.empty();
      frame.state().get('selection').each(function(a){
        var d = a.toJSON(); ids.push(d.id);
        var src = (d.sizes && d.sizes.thumbnail) ? d.sizes.thumbnail.url : d.url;
        thumbs.append('<span data-id="'+d.id+'"><img src="'+src+'" width="80" height="80" style="object-fit:cover"></span>');
      });
      input.val(ids.join(','));
    });
    frame.open();
  });
  $(document).on('click', '.memorial-gallery-clear', function(e){
    e.preventDefault();
    var box = $(this).closest('.memorial-gallery-field');
    box.find('input[type=hidden]').val(''); box.find('.memorial-gallery-thumbs').empty();
  });
});
JS
    );
});
