<?php

/**
 * Plugin Name: WP Lotto Auto Update
 * Plugin URI: https://github.com/aphagon/wp-lotto-auto-update
 * Description: WP Lotto Auto Update
 * Author: Aphagon
 * Author URI: https://www.facebook.com/vilet.sz
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

define('WP_LOTTO_AUTO_UPDATE_FILE', trailingslashit(__FILE__));
define('WP_LOTTO_AUTO_UPDATE_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WP_LOTTO_AUTO_UPDATE_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('WP_LOTTO_AUTO_UPDATE_ASSETS_DIR', trailingslashit(plugin_dir_path(__FILE__)) . 'assets/');
define('WP_LOTTO_AUTO_UPDATE_ASSETS_URL', trailingslashit(plugin_dir_url(__FILE__)) . 'assets/');
define('WP_LOTTO_AUTO_UPDATE_CACHE_DIR', trailingslashit(WP_CONTENT_DIR) . 'wp-lotto-auto-update-cache/');
define('WP_LOTTO_AUTO_UPDATE_LAYOUT_DIR', trailingslashit(plugin_dir_path(__FILE__)) . 'layouts/');
define('WP_LOTTO_AUTO_UPDATE_API_URL', 'https://aphagon-dev.000webhostapp.com/lotto/');

require_once WP_LOTTO_AUTO_UPDATE_DIR . 'includes/Activate.php';
$lottoActivate = WpLottoAutoUpdate\Activate::instance();
