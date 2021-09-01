<?php

namespace WpLottoAutoUpdate;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use Puc_v4_Factory;

final class Activate
{
	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 * @var Activate
	 */
	private static $instance;

	/**
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Activate
	 */
	public static function instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();

			// Load Composer Autoload
			require_once WP_LOTTO_AUTO_UPDATE_DIR . 'vendor/autoload.php';

			// Load Layout Files.
			foreach (glob(WP_LOTTO_AUTO_UPDATE_DIR . 'layouts/*.php') as $filename) {
				require_once $filename;
			}

			\register_activation_hook(WP_LOTTO_AUTO_UPDATE_FILE, [self::$instance, 'activate']);
			\register_uninstall_hook(WP_LOTTO_AUTO_UPDATE_FILE, 'WpLottoAutoUpdate\\Activate::uninstall');

			\add_action('plugins_loaded', [self::$instance, 'loadPlugin']);
		}

		return self::$instance;
	}

	public function loadPlugin()
	{
		$updateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/aphagon/wp-lotto-auto-update',
			\untrailingslashit(WP_LOTTO_AUTO_UPDATE_FILE),
			'wp-lotto-auto-update'
		);
		$updateChecker->checkForUpdates();

		\add_shortcode('wp-lotto-auto-update',  'WpLottoAutoUpdate\\ShortCode::render');
		\add_action('init', [self::$instance, 'addQueryVars']);
		\add_action('wp_enqueue_scripts', [self::$instance, 'enqueueScripts']);
	}

	public function activate()
	{
		// Create folder cache.
		if (!is_dir(WP_LOTTO_AUTO_UPDATE_CACHE_DIR)) {
			mkdir(WP_LOTTO_AUTO_UPDATE_CACHE_DIR, 0777, true);
		}

		\add_option('wp_lotto_auto_update_permalinks_flushed', 0);

		$this->insertPage();
	}

	public function uninstall()
	{
		if (!\current_user_can('activate_plugins'))
			return;

		// Remove cache directory.
		$it = new \RecursiveDirectoryIterator(WP_LOTTO_AUTO_UPDATE_CACHE_DIR, \FilesystemIterator::SKIP_DOTS);
		$it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($it as $file) {
			if ($file->isDir()) {
				rmdir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}
		rmdir(WP_LOTTO_AUTO_UPDATE_CACHE_DIR);

		\delete_option('wp_lotto_auto_update_permalinks_flushed');
	}

	public function enqueueScripts()
	{
		global $post;
		if (is_a($post, 'WP_Post') && \has_shortcode($post->post_content, 'wp-lotto-auto-update')) {
			\wp_register_style(
				'wp-lotto-auto-update-stylesheet',
				WP_LOTTO_AUTO_UPDATE_ASSETS_URL . 'css/style.min.css',
				[],
				filemtime(
					WP_LOTTO_AUTO_UPDATE_ASSETS_DIR . 'css/style.min.css'
				)
			);
			\wp_enqueue_style('wp-lotto-auto-update-stylesheet');
		}
	}

	/**
	 * Adds a rewrite rule that transforms a URL structure to a set of query vars.
	 *
	 * @return void
	 */
	public function addQueryVars()
	{
		global $wp;
		$wp->add_query_var('lotto-date');

		\add_rewrite_rule(
			'(.?.+?)/lotto-date-([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})/?$',
			'index.php?pagename=$matches[1]&lotto-date=$matches[2]',
			'top'
		);

		if (!\get_option('wp_lotto_auto_update_permalinks_flushed')) {
			\flush_rewrite_rules(false);
			\update_option('wp_lotto_auto_update_permalinks_flushed', 1);
		}
	}


	private function insertPage()
	{
		$page_obj = \get_page_by_path(__('ตรวจลอตเตอรี่', 'wp-lotto-auto-update'));
		if (!$page_obj) {
			\wp_insert_post([
				'post_title' => __('ตรวจลอตเตอรี่', 'wp-lotto-auto-update'),
				'post_name' => __('ตรวจลอตเตอรี่', 'wp-lotto-auto-update'),
				'post_content' => '[wp-lotto-auto-update path="thailotto"]',
				'comment_status' => 'closed',
				'post_status' => 'publish',
				'post_type' => 'page',
			]);
		} else {
			if ($page_obj->post_status == 'trash') {
				\wp_update_post([
					'ID' => $page_obj->ID,
					'post_status' => 'publish'
				]);
			}
		}
	}
}
