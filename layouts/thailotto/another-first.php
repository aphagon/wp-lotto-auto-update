<?php

defined('ABSPATH') || die();

if (!function_exists('wp_lotto_auto_update_layout_thailotto_another_first')) {
	/**
	 * @param array $data {
	 * 	'date' => 'YYYY-MM-DD',
	 * 	'data' => array
	 * }
	 *
	 * @return string
	 */
	function wp_lotto_auto_update_layout_thailotto_another_first(array $data)
	{
		$items = !empty($data['result']['data']['items']) ? $data['result']['data']['items'] : null;
?>
		<section class="wp-lotto-auto-update">
			<div class="wp-lotto-auto-update__desktop">
				<h3><?php _e('รางวัลข้างเคียงรางวัลที่ 1', 'lotto-auto-free'); ?></h3>
				<em>
					<?php printf(
						__('%s รางวัล รางวัลละ <strong>%s</strong> บาท', 'lotto-auto-free'),
						isset($items[11]['info'][0]) ? $items[11]['info'][0] : '-',
						isset($items[11]['info'][1]) ? number_format($items[11]['info'][1]) : '-'
					);
					?>
				</em>
			</div>

			<?php foreach ($items[11]['data'] as $num) : ?>
				<strong><?php echo $num; ?></strong>
			<?php endforeach; ?>

			<div class="wp-lotto-auto-update__mobile">
				<em>
					<?php printf(
						__('%s รางวัล รางวัลละ <strong>%s</strong> บาท', 'lotto-auto-free'),
						isset($items[11]['info'][0]) ? $items[11]['info'][0] : '-',
						isset($items[11]['info'][1]) ? number_format($items[11]['info'][1]) : '-'
					);
					?>
				</em>
			</div>
		</section>

<?php
	}
}
