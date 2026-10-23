<?php
/**
 * שדות נוספים לפרקי חיים ולערכים: צבע, אייקון, סדר (ולערכים גם ציטוט ומקורו).
 */
if (!defined('ABSPATH')) {
    exit;
}

function memorial_term_fields($taxonomy)
{
    $fields = [
        'memorial_color' => ['label' => 'צבע', 'type' => 'color'],
        'memorial_icon'  => ['label' => 'אייקון', 'type' => 'icon'],
        'memorial_order' => ['label' => 'סדר הצגה', 'type' => 'number', 'help' => 'מספר קטן מופיע קודם'],
    ];
    if ($taxonomy === 'value') {
        $fields['memorial_quote'] = ['label' => 'ציטוט', 'type' => 'textarea', 'help' => 'משפט שכתב או שנאמר על האדם, ומתאר את הערך'];
        $fields['memorial_who'] = ['label' => 'מי אמר', 'type' => 'text', 'help' => 'למשל: מתוך מחברותיו, או שם האומר'];
    }
    return $fields;
}

function memorial_term_field_html($key, $f, $val)
{
    $name = esc_attr($key);
    switch ($f['type']) {
        case 'color':
            return '<input type="color" id="' . $name . '" name="' . $name . '" value="' . esc_attr($val ?: '#e8743b') . '">';
        case 'icon':
            $h = '<select id="' . $name . '" name="' . $name . '">';
            foreach (memorial_icon_choices() as $slug => $label) {
                $h .= '<option value="' . esc_attr($slug) . '" ' . selected($val, $slug, false) . '>' . esc_html($label) . '</option>';
            }
            return $h . '</select>';
        case 'number':
            return '<input type="number" id="' . $name . '" name="' . $name . '" value="' . esc_attr($val) . '" style="width:6em">';
        case 'textarea':
            return '<textarea id="' . $name . '" name="' . $name . '" rows="3" class="large-text">' . esc_textarea($val) . '</textarea>';
        default:
            return '<input type="text" id="' . $name . '" name="' . $name . '" value="' . esc_attr($val) . '" class="regular-text">';
    }
}

foreach (['chapter', 'value'] as $memorial_tax) {
    add_action($memorial_tax . '_add_form_fields', function ($taxonomy) {
        wp_nonce_field('memorial_term_save', 'memorial_term_nonce');
        foreach (memorial_term_fields($taxonomy) as $key => $f) {
            echo '<div class="form-field"><label for="' . esc_attr($key) . '">' . esc_html($f['label']) . '</label>';
            echo memorial_term_field_html($key, $f, '');
            if (!empty($f['help'])) {
                echo '<p>' . esc_html($f['help']) . '</p>';
            }
            echo '</div>';
        }
    });
    add_action($memorial_tax . '_edit_form_fields', function ($term, $taxonomy) {
        wp_nonce_field('memorial_term_save', 'memorial_term_nonce');
        foreach (memorial_term_fields($taxonomy) as $key => $f) {
            echo '<tr class="form-field"><th scope="row"><label for="' . esc_attr($key) . '">' . esc_html($f['label']) . '</label></th><td>';
            echo memorial_term_field_html($key, $f, (string) get_term_meta($term->term_id, $key, true));
            if (!empty($f['help'])) {
                echo '<p class="description">' . esc_html($f['help']) . '</p>';
            }
            echo '</td></tr>';
        }
    }, 10, 2);
    foreach (['created_', 'edited_'] as $memorial_hook) {
        add_action($memorial_hook . $memorial_tax, 'memorial_save_term_meta');
    }
}

function memorial_save_term_meta($term_id)
{
    if (!isset($_POST['memorial_term_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['memorial_term_nonce'])), 'memorial_term_save')) {
        return;
    }
    if (!current_user_can('manage_categories')) {
        return;
    }
    $term = get_term($term_id);
    if (!$term || is_wp_error($term)) {
        return;
    }
    foreach (memorial_term_fields($term->taxonomy) as $key => $f) {
        if (!isset($_POST[$key])) {
            continue;
        }
        $raw = wp_unslash($_POST[$key]);
        switch ($f['type']) {
            case 'color':
                $v = preg_match('/^#[0-9a-fA-F]{6}$/', $raw) ? $raw : '';
                break;
            case 'icon':
                $v = isset(memorial_icon_choices()[$raw]) ? $raw : '';
                break;
            case 'number':
                $v = $raw === '' ? '' : (string) (int) $raw;
                break;
            case 'textarea':
                $v = sanitize_textarea_field($raw);
                break;
            default:
                $v = sanitize_text_field($raw);
        }
        update_term_meta($term_id, $key, $v);
    }
}
