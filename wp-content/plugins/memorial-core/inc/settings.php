<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', function () {
    add_options_page('הגדרות אתר הזיכרון', 'אתר הזיכרון', 'manage_options', 'memorial-settings', 'memorial_settings_page');
});

function memorial_sanitize_options($in)
{
    $in = is_array($in) ? $in : [];
    $t = function ($k) use ($in) {
        return sanitize_text_field($in[$k] ?? '');
    };
    $out = [];
    foreach (['person_name', 'short_name', 'honorific', 'passing_phrase', 'hero_roles', 'yahrzeit_note'] as $k) {
        $out[$k] = $t($k);
    }
    $out['hero_lede'] = sanitize_textarea_field($in['hero_lede'] ?? '');
    $out['footer_about'] = sanitize_textarea_field($in['footer_about'] ?? '');
    $out['timeline'] = sanitize_textarea_field($in['timeline'] ?? '');
    $by = preg_replace('/\D/', '', (string) ($in['birth_year'] ?? ''));
    $out['birth_year'] = strlen($by) === 4 ? $by : '';
    $dd = trim((string) ($in['death_date'] ?? ''));
    $out['death_date'] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dd) ? $dd : '';
    $out['hero_image'] = (string) absint($in['hero_image'] ?? 0) ?: '';
    $out['header_image'] = (string) absint($in['header_image'] ?? 0) ?: '';
    $out['notify_emails'] = implode(',', array_filter(array_map('sanitize_email', array_map('trim', explode(',', (string) ($in['notify_emails'] ?? ''))))));
    $out['feedback_email'] = sanitize_email($in['feedback_email'] ?? '');
    $out['contact_email'] = sanitize_email($in['contact_email'] ?? '');
    $out['from_email'] = sanitize_email($in['from_email'] ?? '');
    $ga = strtoupper(trim((string) ($in['ga_id'] ?? '')));
    $out['ga_id'] = preg_match('/^G-[A-Z0-9]{6,14}$/', $ga) ? $ga : '';
    return $out;
}

add_action('admin_init', function () {
    register_setting('memorial_settings', 'memorial_options', ['sanitize_callback' => 'memorial_sanitize_options']);
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'settings_page_memorial-settings') {
        return;
    }
    wp_enqueue_media();
    wp_add_inline_script('jquery-core', <<<'JS'
jQuery(function($){
  $(document).on('click', '.memorial-pick-image', function(e){
    e.preventDefault();
    var box = $(this).closest('.memorial-image-field');
    var frame = wp.media({title:'בחירת תמונה', multiple:false, library:{type:'image'}});
    frame.on('select', function(){
      var d = frame.state().get('selection').first().toJSON();
      box.find('input[type=hidden]').val(d.id);
      var src = (d.sizes && d.sizes.medium) ? d.sizes.medium.url : d.url;
      box.find('.memorial-image-preview').html('<img src="'+src+'" style="max-width:220px;height:auto;display:block;margin-bottom:8px">');
    });
    frame.open();
  });
  $(document).on('click', '.memorial-clear-image', function(e){
    e.preventDefault();
    var box = $(this).closest('.memorial-image-field');
    box.find('input[type=hidden]').val(''); box.find('.memorial-image-preview').empty();
  });
});
JS
    );
});

function memorial_image_field($key, $label, $help = '')
{
    $o = get_option('memorial_options', []);
    $id = (int) ($o[$key] ?? 0);
    echo '<tr><th>' . esc_html($label) . '</th><td><div class="memorial-image-field">';
    echo '<div class="memorial-image-preview">' . ($id ? wp_get_attachment_image($id, 'medium', false, ['style' => 'max-width:220px;height:auto;display:block;margin-bottom:8px']) : '') . '</div>';
    echo '<input type="hidden" name="memorial_options[' . esc_attr($key) . ']" value="' . esc_attr($id ?: '') . '">';
    echo '<button type="button" class="button memorial-pick-image">בחירת תמונה</button> <button type="button" class="button memorial-clear-image">הסרה</button>';
    if ($help) {
        echo '<p class="description">' . esc_html($help) . '</p>';
    }
    echo '</div></td></tr>';
}

function memorial_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $o = get_option('memorial_options', []);
    $g = function ($k) use ($o) {
        return esc_attr($o[$k] ?? '');
    };
    $ta = function ($k) use ($o) {
        return esc_textarea($o[$k] ?? '');
    };
    ?>
    <div class="wrap" dir="rtl">
        <h1>הגדרות אתר הזיכרון</h1>
        <?php if (isset($_GET['memorial_setup'])) : ?>
            <div class="notice notice-success is-dismissible"><p>העמודים וערכי ברירת המחדל נוצרו. אפשר לערוך אותם בכל רגע: עמודים, סיפורים &gt; פרקי חיים, סיפורים &gt; ערכים.</p></div>
        <?php endif; ?>

        <div class="card" style="max-width:none">
            <h2>התחלה מהירה</h2>
            <p>יוצר את העמודים הבסיסיים (סיפורים, אודות, מקומות הנצחה, יצירות, אלבום, שליחת סיפור, הספדים והנצחה, נגישות ופרטיות) ומוסיף פרקי חיים וערכים לדוגמה. לא דורס עמודים או פרקים שכבר קיימים.</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="memorial_setup">
                <?php wp_nonce_field('memorial_setup'); ?>
                <?php submit_button('יצירת עמודים וערכי ברירת מחדל', 'secondary', 'submit', false); ?>
            </form>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields('memorial_settings'); ?>

            <h2>על מי האתר</h2>
            <table class="form-table" role="presentation">
                <tr><th><label for="pn">שם מלא</label></th>
                    <td><input id="pn" class="regular-text" name="memorial_options[person_name]" value="<?php echo $g('person_name'); ?>" placeholder="למשל: ישראל ישראלי"></td></tr>
                <tr><th><label for="sn">שם פרטי (לשימוש בטקסטים)</label></th>
                    <td><input id="sn" class="regular-text" name="memorial_options[short_name]" value="<?php echo $g('short_name'); ?>" placeholder="למשל: ישראל">
                        <p class="description">אם ריק, יילקח המילה הראשונה בשם המלא.</p></td></tr>
                <tr><th><label for="hn">תואר או ברכה</label></th>
                    <td><input id="hn" class="regular-text" name="memorial_options[honorific]" value="<?php echo $g('honorific'); ?>" placeholder="למשל: ז״ל או הי״ד">
                        <p class="description">מופיע ליד השם בכותרות. אפשר להשאיר ריק.</p></td></tr>
                <tr><th><label for="by">שנת לידה</label></th>
                    <td><input id="by" type="number" min="1800" max="2200" name="memorial_options[birth_year]" value="<?php echo $g('birth_year'); ?>" style="width:7em"></td></tr>
                <tr><th><label for="dd">תאריך פטירה</label></th>
                    <td><input id="dd" type="date" name="memorial_options[death_date]" value="<?php echo $g('death_date'); ?>">
                        <p class="description">משמש לחישוב יום הזיכרון העברי ולבאנר האזכרה. התאריך העברי נגזר מהתאריך הלועזי (מי שנפטר אחרי השקיעה: ייתכן שהתאריך העברי יהיה יום אחד אחרי).</p></td></tr>
                <tr><th><label for="pp">ניסוח האזכרה בבאנר</label></th>
                    <td><input id="pp" class="regular-text" name="memorial_options[passing_phrase]" value="<?php echo $g('passing_phrase'); ?>" placeholder="לפטירתו של">
                        <p class="description">למשל: לפטירתו של, לפטירתה של, לנפילתו של. הבאנר יציג: "מחר, כ״ה בחשוון תשפ״ז, שנתיים <?php echo esc_html(memorial_opt('passing_phrase')); ?> <?php echo esc_html(memorial_short() ?: 'ישראל'); ?>".</p></td></tr>
                <tr><th><label for="yn">הודעה מיוחדת בבאנר האזכרה</label></th>
                    <td><input id="yn" class="large-text" name="memorial_options[yahrzeit_note]" value="<?php echo $g('yahrzeit_note'); ?>" placeholder="למשל: טקס זיכרון ב-18:00 בבית העלמין">
                        <p class="description">הבאנר מוצג שלושה שבועות לפני האזכרה ועד שבוע אחריה.</p></td></tr>
            </table>

            <h2>טקסטים ותמונות</h2>
            <table class="form-table" role="presentation">
                <tr><th><label for="hr">תפקידים או תארים</label></th>
                    <td><input id="hr" class="large-text" name="memorial_options[hero_roles]" value="<?php echo $g('hero_roles'); ?>" placeholder="למשל: מורה, אבא, מטייל, חבר">
                        <p class="description">מופרדים בפסיק. מוצגים בדף הבית מתחת לשם.</p></td></tr>
                <tr><th><label for="hl">פסקת פתיחה בדף הבית</label></th>
                    <td><textarea id="hl" class="large-text" rows="3" name="memorial_options[hero_lede]"><?php echo $ta('hero_lede'); ?></textarea></td></tr>
                <tr><th><label for="fa">משפט בפוטר ובתיאור האתר</label></th>
                    <td><textarea id="fa" class="large-text" rows="2" name="memorial_options[footer_about]"><?php echo $ta('footer_about'); ?></textarea>
                        <p class="description">משפט או שניים עליו/ עליה. משמש גם בתיאור לגוגל ובשיתוף.</p></td></tr>
                <?php memorial_image_field('hero_image', 'תמונה ראשית (דף הבית ושיתוף)', 'מומלץ תמונה אנכית או ריבועית באיכות טובה. אם לא נבחרה, תוצג איורית ברירת מחדל.'); ?>
                <?php memorial_image_field('header_image', 'תמונת נוף לראש עמודים (לא חובה)', 'מוצגת ברקע כותרות העמודים הראשיים.'); ?>
                <tr><th><label for="tl">ציר זמן</label></th>
                    <td><textarea id="tl" class="large-text code" rows="8" name="memorial_options[timeline]" placeholder="1950 | נולד בחיפה | ילדות בבית עם גינה | childhood&#10;1968 | מתגייס | שירות צבאי | service"><?php echo $ta('timeline'); ?></textarea>
                        <p class="description">שורה לכל תחנה: <code>מתי | כותרת | טקסט קצר | מזהה פרק (slug, לא חובה)</code>. אם מזהה הפרק קיים, התחנה תקשר לסיפורים של אותו פרק. ריק = ללא ציר זמן.</p></td></tr>
            </table>

            <h2>מיילים והתראות</h2>
            <table class="form-table" role="presentation">
                <tr><th><label for="ne">מיילים שמקבלים סיפורים ותמונות חדשים</label></th>
                    <td><input id="ne" class="large-text" name="memorial_options[notify_emails]" value="<?php echo $g('notify_emails'); ?>" placeholder="mom@example.com,sister@example.com">
                        <p class="description">כתובות מופרדות בפסיק. אם ריק, ההתראות יישלחו למייל מנהל האתר (<?php echo esc_html(get_option('admin_email')); ?>).</p></td></tr>
                <tr><th><label for="fe">מייל למשוב על האתר</label></th>
                    <td><input id="fe" class="regular-text" name="memorial_options[feedback_email]" value="<?php echo $g('feedback_email'); ?>"></td></tr>
                <tr><th><label for="ce">מייל ליצירת קשר (הצהרת נגישות)</label></th>
                    <td><input id="ce" class="regular-text" name="memorial_options[contact_email]" value="<?php echo $g('contact_email'); ?>"></td></tr>
                <tr><th><label for="fm">כתובת השולח של ההתראות</label></th>
                    <td><input id="fm" class="regular-text" dir="ltr" name="memorial_options[from_email]" value="<?php echo $g('from_email'); ?>" placeholder="noreply@yourdomain.org">
                        <p class="description">מומלץ כתובת בדומיין של האתר, כדי שההתראות לא ייפלו לספאם. אם אפשר, הגדירו גם SMTP בעזרת תוסף דוא״ל.</p></td></tr>
            </table>

            <h2>מדידה</h2>
            <table class="form-table" role="presentation">
                <tr><th><label for="ga">מזהה מדידה של Google Analytics</label></th>
                    <td><input id="ga" class="regular-text" dir="ltr" name="memorial_options[ga_id]" value="<?php echo $g('ga_id'); ?>" placeholder="G-XXXXXXXXXX">
                        <p class="description">מדידת צפיות בעמודים בלבד, ללא מידע אישי. מי שמחובר לניהול האתר לא נספר. ריק = ללא מדידה.</p></td></tr>
            </table>
            <?php submit_button('שמירה'); ?>
        </form>
    </div>
    <?php
}
