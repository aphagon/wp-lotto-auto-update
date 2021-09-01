<?php

namespace LottoAutoFree;

defined('ABSPATH') || die();

use WpLottoAutoUpdate\Helper;

class ShortCode
{
	/**
	 * The [wp-lotto-auto-update path="xxxx"] shortCode.
	 *
	 * Accepts a title and will display a box.
	 *
	 * @param array $atts attributes. Default empty.
	 * @return string ShortCode output.
	 */
	public static function render($atts)
	{
		// normalize attribute keys, lowercase
		$atts = array_change_key_case((array) $atts, CASE_LOWER);
		$atts = \shortcode_atts([
			'path' => '',
			'page_id' => '',
		], $atts);
		extract($atts);

		$date = \get_query_var('lotto-date');
		if (!Helper::isDate($date)) {
			$date = date('Y-m-d', \current_time('timestamp'));
		}

		if ($path !== '' && !Helper::isPath($path)) {
			return \__('Path does not exist.', 'wp-lotto-auto-update');
		}

		$result = Helper::curl($path, $date);
		if (empty($result['success'])) {
			return \__('Failed to retrieve data.', 'wp-lotto-auto-update');
		}

		ob_start();

		if ($path === '') {
			$path = 'main';
		}

		$start = '
		<article class="wp-lotto-auto-update-' . $path . '-wrap">
			<div class="wp-lotto-auto-update-wrap">';

		echo \apply_filters('wp_lotto_auto_update_template_start', $start, $path);

		\do_action("wp_lotto_auto_update_template_{$path}", [
			'page_id' => $page_id,
			'date' => $date,
			'result' => $result,
		]);

		$end = '
			</div>
		</article>';

		echo \apply_filters('wp_lotto_auto_update_template_end', $end, $path);

		return ob_get_clean();
	}
}
