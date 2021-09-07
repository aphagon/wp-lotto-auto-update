<?php

defined('ABSPATH') || die();

if (!function_exists('wp_lotto_auto_update_container_thailotto_layout_another_first')) {
	add_action('wp_lotto_auto_update_container_thailotto_layout_another_first', 'wp_lotto_auto_update_container_thailotto_layout_another_first', 10, 2);
	/**
	 * @param array $data
	 * @param string $date
	 */
	function wp_lotto_auto_update_container_thailotto_layout_another_first(array $data, string $date)
	{
		if (empty($data['items'])) {
			return;
		}
		$items = $data['items'];
?>
		<section class="wp-lotto-auto-update-thailotto__another-first">
			<div class="wp-lotto-auto-update__desktop">
				<h3><?php _e('รางวัลข้างเคียงรางวัลที่ 1', 'wp-lotto-auto-update'); ?></h3>
				<em>
					<?php printf(
						__('%s รางวัล รางวัลละ <strong>%s</strong> บาท', 'wp-lotto-auto-update'),
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
						__('%s รางวัล รางวัลละ <strong>%s</strong> บาท', 'wp-lotto-auto-update'),
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
