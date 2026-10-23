<?php
/**
 * ניהול תמונות: מסך אחד שמציג את תמונות הספרייה בטבלה (תמונה ממוזערת, כיתוב, טקסט חלופי ואלבום)
 * ושומר את כל השינויים בבת אחת. אחרי שמירה מנקה את מטמון LiteSpeed.
 */
if (!defined('ABSPATH')) {
    exit;
}

const MEMORIAL_MEDIA_PER_PAGE = 40;

add_action('admin_menu', function () {
    add_media_page('ניהול תמונות', 'ניהול תמונות', 'upload_files', 'memorial-media', 'memorial_media_manager_page');
});

/** שאילתת התמונות לפי סינון: חיפוש, אלבום ("none" = ללא אלבום), ללא טקסט חלופי */
function memorial_media_query($paged, $search, $album, $missing_alt)
{
    $args = [
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'posts_per_page' => MEMORIAL_MEDIA_PER_PAGE,
        'paged' => max(1, (int) $paged),
        'orderby' => 'date',
        'order' => 'DESC',
    ];
    if ($search !== '') {
        $args['s'] = $search;
    }
    if ($album === 'none') {
        $args['tax_query'] = [['taxonomy' => 'album', 'operator' => 'NOT EXISTS']];
    } elseif ($album !== '') {
        $args['tax_query'] = [['taxonomy' => 'album', 'field' => 'term_id', 'terms' => (int) $album]];
    }
    if ($missing_alt) {
        $args['meta_query'] = [
            'relation' => 'OR',
            ['key' => '_wp_attachment_image_alt', 'compare' => 'NOT EXISTS'],
            ['key' => '_wp_attachment_image_alt', 'value' => '', 'compare' => '='],
        ];
    }
    return new WP_Query($args);
}

function memorial_media_manager_page()
{
    if (!current_user_can('upload_files')) {
        wp_die('אין הרשאה.');
    }
    $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $album = isset($_GET['album']) ? sanitize_text_field(wp_unslash($_GET['album'])) : '';
    $missing = !empty($_GET['missing_alt']);
    $paged = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
    $albums = get_terms(['taxonomy' => 'album', 'hide_empty' => false]);
    $albums = is_wp_error($albums) ? [] : $albums;
    $q = memorial_media_query($paged, $search, $album, $missing);
    $base = admin_url('upload.php');
    $keep = array_filter(['page' => 'memorial-media', 's' => $search, 'album' => $album, 'missing_alt' => $missing ? 1 : '']);
    ?>
    <div class="wrap memorial-media" dir="rtl">
        <h1>ניהול תמונות</h1>
        <?php if (isset($_GET['saved'])) : ?>
            <div class="notice notice-success is-dismissible"><p>נשמרו שינויים ב-<?php echo (int) $_GET['saved']; ?> תמונות<?php echo has_action('litespeed_purge_all') ? ', והמטמון נוקה.' : '.'; ?></p></div>
        <?php endif; ?>
        <p>כיתוב, טקסט חלופי (alt) ואלבום לכל תמונה במקום אחד. השינויים נשמרים יחד בלחיצה על "שמירת שינויים". <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=album&post_type=attachment')); ?>">ניהול רשימת האלבומים</a></p>

        <form method="get" action="<?php echo esc_url($base); ?>" class="memorial-media__filters" style="margin:12px 0;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
            <input type="hidden" name="page" value="memorial-media">
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="חיפוש בשם הקובץ או בכיתוב">
            <select name="album">
                <option value="">כל האלבומים</option>
                <option value="none" <?php selected($album, 'none'); ?>>ללא אלבום</option>
                <?php foreach ($albums as $a) : ?>
                    <option value="<?php echo (int) $a->term_id; ?>" <?php selected($album, (string) $a->term_id); ?>><?php echo esc_html($a->name); ?></option>
                <?php endforeach; ?>
            </select>
            <label><input type="checkbox" name="missing_alt" value="1" <?php checked($missing); ?>> רק בלי טקסט חלופי</label>
            <button class="button" type="submit">סינון</button>
        </form>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="memorial_media_save">
            <input type="hidden" name="return" value="<?php echo esc_attr(add_query_arg(array_merge($keep, ['paged' => $paged]), $base)); ?>">
            <?php wp_nonce_field('memorial_media_save'); ?>
            <?php if (!$q->have_posts()) : ?>
                <p>לא נמצאו תמונות.</p>
            <?php else : ?>
                <table class="widefat striped" style="table-layout:fixed">
                    <thead><tr>
                        <th style="width:110px">תמונה</th>
                        <th>כיתוב</th>
                        <th>טקסט חלופי (alt)</th>
                        <th style="width:200px">אלבום</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($q->posts as $p) :
                        $id = $p->ID;
                        $alt = (string) get_post_meta($id, '_wp_attachment_image_alt', true);
                        $terms = wp_get_object_terms($id, 'album', ['fields' => 'ids']);
                        $cur = (!is_wp_error($terms) && $terms) ? (int) $terms[0] : 0;
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url(get_edit_post_link($id)); ?>"><?php echo wp_get_attachment_image($id, [90, 90], false, ['style' => 'width:90px;height:90px;object-fit:cover;border-radius:6px', 'loading' => 'lazy']); ?></a>
                            </td>
                            <td>
                                <textarea name="items[<?php echo (int) $id; ?>][caption]" rows="2" class="large-text"><?php echo esc_textarea($p->post_excerpt); ?></textarea>
                                <small style="color:#646970"><?php echo esc_html(wp_basename(get_attached_file($id))); ?></small>
                            </td>
                            <td><textarea name="items[<?php echo (int) $id; ?>][alt]" rows="2" class="large-text"><?php echo esc_textarea($alt); ?></textarea></td>
                            <td>
                                <select name="items[<?php echo (int) $id; ?>][album]" style="width:100%">
                                    <option value="0">— ללא אלבום —</option>
                                    <?php foreach ($albums as $a) : ?>
                                        <option value="<?php echo (int) $a->term_id; ?>" <?php selected($cur, $a->term_id); ?>><?php echo esc_html($a->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="submit" style="position:sticky;bottom:0;background:#f0f0f1;padding:10px 0;margin:0">
                    <button type="submit" class="button button-primary">שמירת שינויים</button>
                </p>
            <?php endif; ?>
        </form>

        <?php
        $links = paginate_links([
            'base' => add_query_arg('paged', '%#%', add_query_arg($keep, $base)),
            'format' => '',
            'current' => $paged,
            'total' => max(1, (int) $q->max_num_pages),
            'prev_text' => 'הקודם',
            'next_text' => 'הבא',
        ]);
        if ($links) {
            echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post($links) . '</div></div>';
        }
        wp_reset_postdata();
        ?>
    </div>
    <?php
}

add_action('admin_post_memorial_media_save', function () {
    if (!current_user_can('upload_files')) {
        wp_die('אין הרשאה.');
    }
    check_admin_referer('memorial_media_save');

    $items = isset($_POST['items']) && is_array($_POST['items']) ? wp_unslash($_POST['items']) : [];
    $valid_albums = [];
    $terms = get_terms(['taxonomy' => 'album', 'hide_empty' => false, 'fields' => 'ids']);
    if (!is_wp_error($terms)) {
        $valid_albums = array_map('intval', $terms);
    }
    $changed = 0;
    foreach ($items as $id => $row) {
        $id = (int) $id;
        $att = get_post($id);
        if (!$att || $att->post_type !== 'attachment' || !current_user_can('edit_post', $id) || strpos((string) $att->post_mime_type, 'image/') !== 0) {
            continue;
        }
        $dirty = false;

        $caption = sanitize_textarea_field($row['caption'] ?? '');
        if ($caption !== $att->post_excerpt) {
            wp_update_post(['ID' => $id, 'post_excerpt' => $caption]);
            $dirty = true;
        }

        $alt = sanitize_text_field($row['alt'] ?? '');
        if ($alt !== (string) get_post_meta($id, '_wp_attachment_image_alt', true)) {
            update_post_meta($id, '_wp_attachment_image_alt', $alt);
            $dirty = true;
        }

        $want = (int) ($row['album'] ?? 0);
        if ($want && !in_array($want, $valid_albums, true)) {
            $want = 0;
        }
        $have = wp_get_object_terms($id, 'album', ['fields' => 'ids']);
        $have = is_wp_error($have) ? [] : array_map('intval', $have);
        if (($want ? [$want] : []) !== $have) {
            wp_set_object_terms($id, $want ? [$want] : [], 'album');
            $dirty = true;
        }
        if ($dirty) {
            $changed++;
        }
    }

    // הגלריות באתר מוצגות מהמטמון: מנקים אותו כדי שהשינויים יופיעו מיד
    if ($changed && has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
    }

    $back = isset($_POST['return']) ? esc_url_raw(wp_unslash($_POST['return'])) : admin_url('upload.php?page=memorial-media');
    if (strpos($back, admin_url()) !== 0) {
        $back = admin_url('upload.php?page=memorial-media');
    }
    wp_safe_redirect(add_query_arg('saved', $changed, $back));
    exit;
});
