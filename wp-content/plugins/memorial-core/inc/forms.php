<?php
/**
 * טפסים: שליחת סיפור, תמונת ביקור במקום הנצחה, משוב על האתר.
 * ללא פלאגינים; מוגן ב-honeypot, הגבלת קצב, בדיקת קבצים ואימות תוכן.
 * סיפורים ותמונות נשמרים כ"ממתין לבדיקה" ומתפרסמים רק אחרי אישור.
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_post_nopriv_memorial_story', 'memorial_handle_story');
add_action('admin_post_memorial_story', 'memorial_handle_story');
add_action('admin_post_nopriv_memorial_visit', 'memorial_handle_visit');
add_action('admin_post_memorial_visit', 'memorial_handle_visit');
add_action('admin_post_nopriv_memorial_feedback', 'memorial_handle_feedback');
add_action('admin_post_memorial_feedback', 'memorial_handle_feedback');

function memorial_form_respond($ok, $message, $kind)
{
    $is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'fetch');
    if ($is_ajax) {
        wp_send_json(['ok' => (bool) $ok, 'message' => $message], $ok ? 200 : 400);
    }
    $back = wp_get_referer() ?: home_url('/');
    $back = remove_query_arg(['memorial_sent', 'memorial_err'], $back);
    $back = add_query_arg($ok ? ['memorial_sent' => $kind] : ['memorial_err' => rawurlencode($message)], $back);
    wp_safe_redirect($back . ($ok ? '#sent' : '#form-error'));
    exit;
}

function memorial_client_key($bucket)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0';
    return 'memorial_rl_' . $bucket . '_' . md5($ip . wp_salt());
}

/** הגבלת קצב: עד $max בקשות לשעה מכתובת אחת */
function memorial_rate_ok($bucket, $max = 5)
{
    $key = memorial_client_key($bucket);
    $n = (int) get_transient($key);
    if ($n >= $max) {
        return false;
    }
    set_transient($key, $n + 1, HOUR_IN_SECONDS);
    return true;
}

function memorial_looks_spammy($text)
{
    if (preg_match_all('~https?://~i', $text) > 2) {
        return true;
    }
    if (preg_match('~(viagra|casino|crypto|bitcoin|forex|loan|seo services|http://bit\.ly)~i', $text)) {
        return true;
    }
    return false;
}

function memorial_common_guard($bucket, $max = 5)
{
    if (!empty($_POST['website']) || !empty($_POST['hp_url'])) {
        // honeypot: מתנהגים כאילו הצליח
        memorial_form_respond(true, 'תודה!', $bucket);
    }
    if (!memorial_rate_ok($bucket, $max)) {
        memorial_form_respond(false, 'נשלחו הרבה פניות בזמן קצר. אפשר לנסות שוב עוד כשעה.', $bucket);
    }
}

/** העלאת קבצי תמונה מהטופס; מחזיר מזהי קבצים */
function memorial_handle_uploads($field, $parent_id, $max = 6)
{
    if (empty($_FILES[$field]) || empty($_FILES[$field]['name'])) {
        return [];
    }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $mimes = ['jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
    $names = (array) $_FILES[$field]['name'];
    $ids = [];
    $count = 0;
    foreach ($names as $i => $name) {
        if (!$name || $count >= $max) {
            continue;
        }
        if ((int) ($_FILES[$field]['error'][$i] ?? 0) !== UPLOAD_ERR_OK) {
            continue;
        }
        if (($_FILES[$field]['size'][$i] ?? 0) > 12 * MB_IN_BYTES) {
            continue;
        }
        $_FILES['memorial_one'] = [
            'name' => $_FILES[$field]['name'][$i],
            'type' => $_FILES[$field]['type'][$i],
            'tmp_name' => $_FILES[$field]['tmp_name'][$i],
            'error' => $_FILES[$field]['error'][$i],
            'size' => $_FILES[$field]['size'][$i],
        ];
        $check = wp_check_filetype_and_ext($_FILES['memorial_one']['tmp_name'], $_FILES['memorial_one']['name'], $mimes);
        if (empty($check['type'])) {
            continue;
        }
        $id = media_handle_upload('memorial_one', $parent_id, [], ['test_form' => false, 'mimes' => $mimes]);
        if (!is_wp_error($id)) {
            $ids[] = $id;
            $count++;
        }
    }
    return $ids;
}

function memorial_mail($subject, $body, $reply_to = '', $to = null)
{
    $to = $to ?: memorial_notify_list();
    if (!$to) {
        $to = [get_option('admin_email')];
    }
    $from_name = wp_specialchars_decode(memorial_name() ?: get_bloginfo('name'), ENT_QUOTES);
    $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ' . $from_name . ' <' . memorial_opt('from_email') . '>'];
    if ($reply_to && is_email($reply_to)) {
        $headers[] = 'Reply-To: ' . $reply_to;
    }
    // מייל בעברית: יישור ימין, כיוון RTL, וקישורים לחיצים
    $html = esc_html($body);
    $html = preg_replace('~(https?://[^\s<]+)~u', '<a href="$1" style="color:#c24e18">$1</a>', $html);
    $html = nl2br($html);
    $html = '<!doctype html><html dir="rtl" lang="he"><head><meta charset="utf-8"></head><body style="margin:0;padding:0;background:#fbf6ec">'
        . '<div dir="rtl" style="direction:rtl;text-align:right;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.7;color:#1f2b45;max-width:640px;margin:0 auto;padding:24px">'
        . '<div style="border-top:5px solid #c24e18;background:#ffffff;border-radius:14px;padding:22px 24px;direction:rtl;text-align:right">' . $html . '</div>'
        . '<p style="direction:rtl;text-align:right;font-size:13px;color:#4a5672;margin:14px 6px 0">נשלח אוטומטית מאתר הזיכרון של ' . esc_html(memorial_name()) . '</p>'
        . '</div></body></html>';
    return wp_mail($to, $subject, $html, $headers);
}

function memorial_handle_story()
{
    memorial_common_guard('story', 5);

    $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $relation = sanitize_text_field(wp_unslash($_POST['relation'] ?? ''));
    $title = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
    $text = sanitize_textarea_field(wp_unslash($_POST['text'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $consent = !empty($_POST['consent']);
    $short = memorial_short();

    if (mb_strlen($name) < 2) {
        memorial_form_respond(false, 'נשמח לדעת איך קוראים לך.', 'story');
    }
    if (mb_strlen($text) < 20) {
        memorial_form_respond(false, 'הסיפור קצר מדי. אפשר לכתוב כמה משפטים על רגע שזכור לך.', 'story');
    }
    if (mb_strlen($text) > 12000) {
        memorial_form_respond(false, 'הסיפור ארוך מאוד. אפשר לקצר או לשלוח בכמה חלקים.', 'story');
    }
    if (!$consent) {
        memorial_form_respond(false, 'כדי לפרסם יש לאשר שהמשפחה רשאית לפרסם את הסיפור באתר.', 'story');
    }
    if (memorial_looks_spammy($text . ' ' . $title)) {
        memorial_form_respond(false, 'לא הצלחנו לקבל את ההודעה. נא להסיר קישורים מהטקסט ולנסות שוב.', 'story');
    }

    $post_id = wp_insert_post([
        'post_type' => 'story',
        'post_status' => 'pending',
        'post_title' => $title ?: ('סיפור מאת ' . $name),
        'post_content' => $text,
    ], true);
    if (is_wp_error($post_id)) {
        memorial_form_respond(false, 'משהו השתבש בשמירה. אפשר לנסות שוב בעוד רגע.', 'story');
    }
    update_post_meta($post_id, 'story_author', $name);
    update_post_meta($post_id, 'story_relation', $relation);
    if ($email) {
        update_post_meta($post_id, 'story_email', $email);
    }
    $imgs = memorial_handle_uploads('photos', $post_id, 6);
    if ($imgs) {
        update_post_meta($post_id, 'story_gallery', implode(',', $imgs));
    }

    $edit = admin_url('post.php?post=' . $post_id . '&action=edit');
    $body = "סיפור חדש על $short ממתין לאישור.\n\n"
        . "שם: $name\nקשר ל$short: " . ($relation ?: '—') . "\nמייל: " . ($email ?: '—') . "\nכותרת: " . ($title ?: '—')
        . "\nתמונות: " . count($imgs) . "\n\n----\n$text\n----\n\n"
        . "לקריאה, עריכה ופרסום: $edit\n(יש להתחבר לניהול האתר, ואז ללחוץ \"פרסום\")";
    memorial_mail('סיפור חדש על ' . $short . ': ' . ($title ?: $name), $body, $email);

    memorial_form_respond(true, 'תודה רבה. הסיפור התקבל, והמשפחה תקרא אותו ותפרסם אותו בקרוב.', 'story');
}

function memorial_handle_visit()
{
    memorial_common_guard('visit', 6);

    $place = (int) ($_POST['bench'] ?? 0);
    $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $msg = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));
    $place_label = sanitize_text_field(wp_unslash($_POST['bench_other'] ?? ''));

    if (mb_strlen($name) < 2) {
        memorial_form_respond(false, 'נשמח לדעת איך קוראים לך.', 'visit');
    }
    if (empty($_FILES['photo']['name'][0])) {
        memorial_form_respond(false, 'נא לצרף תמונה מהביקור.', 'visit');
    }
    if (memorial_looks_spammy($msg)) {
        memorial_form_respond(false, 'לא הצלחנו לקבל את ההודעה. נא להסיר קישורים מהטקסט ולנסות שוב.', 'visit');
    }

    $b = $place ? get_post($place) : null;
    $label = $b && $b->post_type === 'bench' ? $b->post_title : ($place_label ?: 'מקום אחר');

    $post_id = wp_insert_post([
        'post_type' => 'visit',
        'post_status' => 'pending',
        'post_title' => 'ביקור: ' . $label . ' (' . $name . ')',
        'post_content' => $msg,
    ], true);
    if (is_wp_error($post_id)) {
        memorial_form_respond(false, 'משהו השתבש בשמירה. אפשר לנסות שוב בעוד רגע.', 'visit');
    }
    if ($b && $b->post_type === 'bench') {
        update_post_meta($post_id, 'visit_bench', $b->ID);
    }
    update_post_meta($post_id, 'visit_name', $name);
    if ($email) {
        update_post_meta($post_id, 'visit_email', $email);
    }
    $imgs = memorial_handle_uploads('photo', $post_id, 3);
    if (!$imgs) {
        wp_delete_post($post_id, true);
        memorial_form_respond(false, 'לא הצלחנו לקרוא את התמונה. נא לשלוח קובץ JPG או PNG עד 12MB.', 'visit');
    }
    set_post_thumbnail($post_id, $imgs[0]);

    $edit = admin_url('post.php?post=' . $post_id . '&action=edit');
    $body = "תמונה חדשה מביקור במקום הנצחה ממתינה לאישור.\n\nמקום: $label\nשם: $name\nמייל: " . ($email ?: '—') . "\nהודעה: " . ($msg ?: '—') . "\n\nלצפייה ופרסום: $edit";
    memorial_mail('ביקור במקום הנצחה: ' . $label, $body, $email);

    memorial_form_respond(true, 'תודה! התמונה התקבלה והמשפחה תשמח לראות אותה.', 'visit');
}

function memorial_handle_feedback()
{
    memorial_common_guard('feedback', 4);

    $msg = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $page = esc_url_raw(wp_unslash($_POST['page'] ?? ''));
    if (mb_strlen($msg) < 3) {
        memorial_form_respond(false, 'נא לכתוב כמה מילים.', 'feedback');
    }
    if (memorial_looks_spammy($msg)) {
        memorial_form_respond(false, 'לא הצלחנו לקבל את ההודעה. נא להסיר קישורים מהטקסט ולנסות שוב.', 'feedback');
    }
    $ua = sanitize_text_field(substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200));
    $body = "משוב חדש על האתר\n\nעמוד: $page\nמייל לחזרה: " . ($email ?: '—') . "\nדפדפן: $ua\n\n----\n$msg\n----";
    memorial_mail('משוב על אתר הזיכרון של ' . memorial_name(), $body, $email, [memorial_opt('feedback_email')]);
    memorial_form_respond(true, 'תודה על המשוב!', 'feedback');
}
