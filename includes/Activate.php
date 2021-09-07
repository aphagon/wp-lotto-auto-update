<?php

namespace WpLottoAutoUpdate;

defined('ABSPATH') || die();

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
	 * class Page
	 *
	 * @access private
	 * @var Page
	 */
	private $page;

	/**
	 * class ShortCode
	 *
	 * @access private
	 * @var ShortCode
	 */
	private $shortCode;

	/**
	 * class Admin
	 *
	 * @access private
	 * @var Admin
	 */
	private $admin;

	public function __construct()
	{
		$this->page = new Page();
		$this->shortCode = new ShortCode();
		$this->admin = new Admin();
	}

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
			// Load Composer Autoload
			require_once WP_LOTTO_AUTO_UPDATE_DIR . 'vendor/autoload.php';

			self::$instance = new self();

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

		\add_action('wp_enqueue_scripts', [self::$instance, 'enqueueScripts']);

		// Run hook short code.
		$this->shortCode->runAction();

		// Run hook page.
		$this->page->runAction();

		// Run hook admin form.
		if (\is_admin()) {
			$this->admin->runAction();
		}

		// Hooks Templates.
		\add_action('wp_lotto_auto_update_container_thailotto', 'wp_lotto_auto_update_container_thailotto', 10, 3);
	}

	public function activate()
	{
		// Create folder cache.
		if (!is_dir(WP_LOTTO_AUTO_UPDATE_CACHE_DIR)) {
			mkdir(WP_LOTTO_AUTO_UPDATE_CACHE_DIR, 0777, true);
		}

		$this->page->insert();

		if (get_option('wp_lotto_auto_update_activate_plugin') == '') {
			$this->remoteInstallDomain();
			update_option('wp_lotto_auto_update_activate_plugin', '1');
		}
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

		$this->page->trash();

		if (get_option('wp_lotto_auto_update_activate_plugin') == '1') {
			$this->remoteInstallDomain();
			delete_option('wp_lotto_auto_update_activate_plugin');
		}
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

	private function remoteInstallDomain()
	{
		$urlparts = parse_url(home_url());
		$domain = $urlparts['host'];
		\wp_remote_post(WP_LOTTO_AUTO_UPDATE_API_URL . 'action-plugin', [
			'method' => 'POST',
			'body' => [
				'script' => 'wordpress',
				'domain' => $domain,
			],
		]);
	}
}
