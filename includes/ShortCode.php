<?php

namespace WpLottoAutoUpdate;

defined('ABSPATH') || die();

use WpLottoAutoUpdate\Helper;

class ShortCode
{
	public function __construct()
	{
	}

	public function runAction()
	{
		\add_shortcode('wp-lotto-auto-update',  [$this, 'render']);
	}

	/**
	 * The [wp-lotto-auto-update path="x" page_id="x"] shortCode.
	 *
	 * Accepts a title and will display a box.
	 *
	 * @param array $atts attributes. Default empty.
	 * @return string ShortCode output.
	 */
	public function render($atts)
	{
		// normalize attribute keys, lowercase
		$atts = array_change_key_case((array) $atts, CASE_LOWER);
		$atts = \shortcode_atts([
			'path' => '',
		], $atts);
		extract($atts);

		$date = \get_query_var('lotto-date');
		if (!Helper::isDate($date)) {
			$date = date('Y-m-d', \current_time('timestamp'));
		}

		if ($path !== '' && !Helper::isPath($path)) {
			return sprintf('<p>%s</p>', \__('Path does not exist.', 'wp-lotto-auto-update'));
		}

		$result = Helper::curl($path, $date);
		if (empty($result['success'])) {
			return sprintf('<p>%s</p>', \__('Failed to retrieve data.', 'wp-lotto-auto-update'));
		}

		ob_start();

		if ($path == '') {
			$path = 'main';
		}

		$container_start = '
		<article class="wp-lotto-auto-update-container-' . $path . '">
			<div class="wp-lotto-auto-update-container__wrap">';

		echo \apply_filters('wp_lotto_auto_update_container_start', $container_start, $path);

		\do_action("wp_lotto_auto_update_container_content_{$path}", [
			'date' => $date,
			'result' => $result,
		]);

		$container_end = '
			</div>
		</article>';

		echo \apply_filters('wp_lotto_auto_update_container_end', $container_end, $path);

		return ob_get_clean();
	}
}
