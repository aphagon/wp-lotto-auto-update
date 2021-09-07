<?php

defined('ABSPATH') || die();

if (!function_exists('wp_lotto_auto_update_container_thailotto_layout_another')) {
	add_action('wp_lotto_auto_update_container_thailotto_layout_another', 'wp_lotto_auto_update_container_thailotto_layout_another', 10, 2);
	/**
	 * @param array $data
	 * @param string $date
	 */
	function wp_lotto_auto_update_container_thailotto_layout_another(array $data, string $date)
	{
		if (empty($data['items'])) {
			return;
		}

		foreach ($data['items'] as $key => $value) :
			if (in_array($key, [1, 6, 7, 10, 11])) :
				continue;
			endif;
?>

			<section class="wp-lotto-auto-update-thailotto__another">
				<header>
					<h3>
						<?php printf(__('ผลสลากกินแบ่งรัฐบาล <strong>รางวัลที่ %d</strong>', 'wp-lotto-auto-update'), $key); ?>
						<small>
							<?php printf(__('มี %s รางวัล รางวัลละ %s บาท', 'wp-lotto-auto-update'), $value['info'][0], number_format($value['info'][1])); ?>
						</small>
					</h3>
				</header>
				<ul>
					<li>
						<?php
						if (!empty($value['data'])) :
							$i = 0;
							foreach ($value['data'] as $number) :
								printf('<strong>%s</strong>', $number);

								if (++$i % 5 == 0) :
									echo '</li><li>';
								endif;
							endforeach;
						else : ?>
							<p>-</p>
						<?php endif; ?>
					</li>
				</ul>
			</section>

<?php
		endforeach;
	}
}
