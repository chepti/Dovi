<?php
/**
 * Plugin Name: Memorial Core
 * Description: סוגי תוכן, טפסים, SEO, ניהול תמונות ומפה לאתר זיכרון משפחתי: סיפורים, מקומות הנצחה ויצירות. עובד יחד עם ערכת הנושא Memorial.
 * Version: 1.0.0
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Author: חולמים תקשוב
 * Author URI: https://chepti.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: memorial-core
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MEMORIAL_CORE_VERSION', '1.0.0');
define('MEMORIAL_CORE_DIR', plugin_dir_path(__FILE__));
define('MEMORIAL_CORE_URL', plugin_dir_url(__FILE__));

require_once MEMORIAL_CORE_DIR . 'inc/helpers.php';
require_once MEMORIAL_CORE_DIR . 'inc/post-types.php';
require_once MEMORIAL_CORE_DIR . 'inc/terms.php';
require_once MEMORIAL_CORE_DIR . 'inc/meta-boxes.php';
require_once MEMORIAL_CORE_DIR . 'inc/settings.php';
require_once MEMORIAL_CORE_DIR . 'inc/setup.php';
require_once MEMORIAL_CORE_DIR . 'inc/forms.php';
require_once MEMORIAL_CORE_DIR . 'inc/seo.php';
require_once MEMORIAL_CORE_DIR . 'inc/routing.php';
require_once MEMORIAL_CORE_DIR . 'inc/media-manager.php';
require_once MEMORIAL_CORE_DIR . 'inc/admin.php';

register_activation_hook(__FILE__, function () {
    memorial_register_content_types();
    memorial_seed_defaults();
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
