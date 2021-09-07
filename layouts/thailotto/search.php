<?php

defined('ABSPATH') || die();

use WpLottoAutoUpdate\Curl;
use WpLottoAutoUpdate\Helper;

if (!function_exists('wp_lotto_auto_update_container_thailotto_layout_search')) {
	add_action('wp_lotto_auto_update_container_thailotto_layout_search', 'wp_lotto_auto_update_container_thailotto_layout_search', 10, 2);
	/**
	 * @param array $data
	 * @param string $date
	 */
	function wp_lotto_auto_update_container_thailotto_layout_search(array $data, string $date)
	{
?>

		<section id="WpLottoAutoUpdateCheck" class="wp-lotto-auto-update__select-date-form" style="margin-bottom: 0;">
			<div class="wp-lotto-auto-update__select-date-form__column">
				<input type="text" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;" id="WpLottoAutoUpdateCheckNumber" class="wp-lotto-auto-update__select-date-form__column-input">
			</div>
			<div class="wp-lotto-auto-update__select-date-form__column">
				<button type="button" class="wp-lotto-auto-update__select-date-form__column-button" onclick="return clickWpLottoAutoUpdateCheckLotto();"><?php _e('ตรวจผลรางวัล', 'wp-lotto-auto-update'); ?></button>
			</div>
		</section>
		<section id="WpLottoAutoUpdateMessage" style="margin-bottom: 30px"></section>

		<script>
			const lottoNumbers = <?php echo json_encode($data['items']); ?>;

			function clickWpLottoAutoUpdateCheckLotto() {
				let msgEl = document.getElementById('WpLottoAutoUpdateMessage');
				msg = '<?php _e('ไม่ถูกรางวัลใด ๆ', 'wp-lotto-auto-update'); ?>';

				const filter = document.getElementById('WpLottoAutoUpdateCheckNumber').value;

				let intRegex = /^\d+$/;
				if (intRegex.test(filter) && filter.length !== 6) {
					msgEl.innerHTML = '<?php _e('กรุณาระบุหมายเลขสลากให้ถูกต้อง', 'wp-lotto-auto-update'); ?>';
					return false;
				}

				let numberWithCommas = function(x) {
					return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
				}

				let d2 = filter.substring(4, 6);
				let d3f = filter.substring(0, 3);
				let d3l = filter.substring(3, 6);

				const map = new Map(Object.entries(lottoNumbers));
				map.forEach(function(lotto, index) {
					for (let i = 0; i < lotto.data.length; i++) {
						let val = lotto.data[i];
						if (filter == val) {
							if (index == 11) {
								msg = filter + ' ถูกรางวัลข้างเคียงรางวัลที่ 1 จำนวนเงิน ' + numberWithCommas(lotto.info[1]) + ' บาท';
							} else {
								msg = filter + ' ถูกรางวัล รางวัลที่ ' + index + ' จำนวนเงิน ' + numberWithCommas(lotto.info[1]) + ' บาท';
							}
						} else if (d2 == val) {
							msg = filter + ' ถูกรางวัล เลขท้าย 2 ตัว จำนวนเงิน ' + numberWithCommas(lotto.info[1]) + ' บาท';
							break;
						} else if (d2 == val) {
							msg = filter + ' ถูกรางวัล เลขท้าย 2 ตัว จำนวนเงิน ' + numberWithCommas(lotto.info[1]) + ' บาท';
							break;
						} else if (d3l == val && index == 6) {
							msg = filter + ' ถูกรางวัล เลขท้าย 3 ตัว จำนวนเงิน ' + numberWithCommas(lotto.info[1]) + ' บาท';
							break;
						} else if (d3f == val && index == 10) {
							msg = filter + ' ถูกรางวัล เลขหน้า 3 ตัว จำนวนเงิน ' + numberWithCommas(lotto.info[1]) + ' บาท';
							break;
						}
					}
				});

				msgEl.innerHTML = '<p class="wp-lotto-auto-update__search-number-text">' + msg + '</p>';

				return false;
			}
		</script>
<?php
	}
}
