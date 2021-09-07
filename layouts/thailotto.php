<?php

defined('ABSPATH') || die();

// Load Files.
foreach (glob(WP_LOTTO_AUTO_UPDATE_DIR . 'layouts/thailotto/*.php') as $file) {
	require_once $file;
}

if (!function_exists('wp_lotto_auto_update_container_thailotto')) {
	/**
	 * @param array $data
	 * @param array $layouts
	 * @param string $date YYYY-MM-DD
	 *
	 * @return string
	 */
	function wp_lotto_auto_update_container_thailotto(array $data, array $layouts, string $date)
	{
		$lists = apply_filters('wp_lotto_auto_update_container_thailotto_layouts', [
			// 'search',
			// 'select-date',
			'highlight',
			// 'another-first',
			// 'another',
		]);

		if (empty($layouts)) {
			$layouts = $lists;
		}

		foreach ($layouts as $layout) {
			if (in_array($layout, $lists)) {
				do_action("wp_lotto_auto_update_container_thailotto_layout_{$layout}_before", $data, $date);
				do_action("wp_lotto_auto_update_container_thailotto_layout_{$layout}", $data, $date);
				do_action("wp_lotto_auto_update_container_thailotto_layout_{$layout}_after", $data, $date);
			}
		}
	}
}
