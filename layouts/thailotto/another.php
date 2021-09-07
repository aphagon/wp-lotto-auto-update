<?php

defined('ABSPATH') || die();

if (!function_exists('wp_lotto_auto_update_layout_thailotto_another')) {
	/**
	 * @param array $data {
	 * 	'date' => 'YYYY-MM-DD',
	 * 	'data' => array
	 * }
	 *
	 * @return string
	 */
	function wp_lotto_auto_update_layout_thailotto_another(array $data)
	{
		foreach ($data['result']['data']['items'] as $key => $value) :
			if (in_array($key, [1, 6, 7, 10, 11])) :
				continue;
			endif;

			\do_action('wp_lotto_auto_update_layout_thailotto_another_before'); ?>

			<section class="lotto-auto-free-thailotto-another">
				<header>
					<h3>
						<?php printf(__('ผลสลากกินแบ่งรัฐบาล <strong>รางวัลที่ %d</strong>', 'lotto-auto-free'), $key); ?>
						<small>
							<?php printf(__('มี %s รางวัล รางวัลละ %s บาท', 'lotto-auto-free'), $value['info'][0], number_format($value['info'][1])); ?>
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
			\do_action('wp_lotto_auto_update_layout_thailotto_another_after');
		endforeach;
	}
}
