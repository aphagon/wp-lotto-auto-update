<?php

use WpLottoAutoUpdate\Page;

defined('ABSPATH') || die();

if (!function_exists('wp_lotto_auto_update_container_thailotto_layout_highlight')) {
	add_action('wp_lotto_auto_update_container_thailotto_layout_highlight', 'wp_lotto_auto_update_container_thailotto_layout_highlight', 10, 2);
	/**
	 * @param array $data
	 * @param string $date
	 */
	function wp_lotto_auto_update_container_thailotto_layout_highlight(array $data, string $date)
	{
		$current_id = get_the_ID();
		$items = !empty($data['items']) ? $data['items'] : null;
		$title = sprintf('%s %s', __('ผลหวยรัฐบาล', 'wp-lotto-auto-update'), $data['title']);
?>

		<h2 class="wp-lotto-auto-update__title">
			<?php if ($current_id !== Page::optionID('thailotto')) : ?>
				<a class="wp-lotto-auto-update__title-link" href="<?php echo esc_url(get_permalink(Page::optionID('thailotto'))); ?>">
					<?php echo $title; ?>
				</a>
			<?php else :
				echo $title;
			endif; ?>
		</h2>
		<div class="wp-lotto-auto-update-thailotto__highlight">
			<section class="wp-lotto-auto-update-thailotto__first-prize">
				<h3><?php _e('รางวัลที่ 1', 'wp-lotto-auto-update'); ?></h3>
				<strong><?php echo isset($items[1]['data'][0]) ? $items[1]['data'][0] : '-'; ?></strong>
				<em><?php printf(
						__('รางวัลละ <strong>%s</strong> บาท', 'wp-lotto-auto-update'),
						isset($items[1]['info'][1]) ? number_format($items[1]['info'][1]) : '-',
					); ?></em>
			</section>

			<?php
			foreach ([
				10 => __('เลขหน้า 3 ตัว', 'wp-lotto-auto-update'),
				6 => __('เลขหลัง 3 ตัว', 'wp-lotto-auto-update')
			] as $num => $title) : ?>
				<section class="wp-lotto-auto-update-thailotto__<?php echo $num === 10 ? 'first' : 'last'; ?>-three-digits-prize">
					<h3><?php echo $title; ?></h3>
					<strong><?php echo isset($items[$num]['data'][0]) ? $items[$num]['data'][0] : '-'; ?></strong>
					<strong><?php echo isset($items[$num]['data'][1]) ? $items[$num]['data'][1] : '-'; ?></strong>
					<em><?php printf(
							__('%s รางวัล รางวัลละ %s บาท', 'wp-lotto-auto-update'),
							isset($items[$num]['info'][0]) ? $items[$num]['info'][0] : '-',
							isset($items[$num]['info'][1]) ? number_format($items[$num]['info'][1]) : '-',
						); ?></em>
				</section>
			<?php endforeach; ?>

			<section class="wp-lotto-auto-update-thailotto__last-two-digits-prize">
				<h3><?php _e('เลขท้าย 2 ตัว', 'wp-lotto-auto-update'); ?></h3>
				<strong><?php echo isset($items[7]['data'][0]) ? $items[7]['data'][0] : '-'; ?></strong>
				<em><?php printf(
						'%s <strong>%s</strong> %s',
						__('รางวัลละ', 'wp-lotto-auto-update'),
						isset($items[7]['info'][1]) ? number_format($items[7]['info'][1]) : '-',
						__('บาท', 'wp-lotto-auto-update')
					); ?></em>
			</section>
		</div>

<?php
	}
}
