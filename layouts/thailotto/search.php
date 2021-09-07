<?php

defined('ABSPATH') || die();

use WpLottoAutoUpdate\Curl;
use WpLottoAutoUpdate\Helper;

if (!function_exists('wp_lotto_auto_update_layout_thailotto_search')) {
	/**
	 * @param array $data {
	 * 	'date' => 'YYYY-MM-DD',
	 * 	'data' => array
	 * }
	 *
	 * @return string
	 */
	function wp_lotto_auto_update_layout_thailotto_search(array $data)
	{
?>

		<section id="LottoAutoFreeThaiLottoCheck" class="lotto-auto-free-select-date-form" style="margin-bottom: 0;">
			<div class="lotto-auto-free-select-date-form__column">
				<input type="text" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;" id="LottoAutoFreeThaiLottoCheckNumber" class="lotto-auto-free-select-date-form__column-input">
			</div>
			<div class="lotto-auto-free-select-date-form__column">
				<button type="button" class="lotto-auto-free-select-date-form__column-button" onclick="return clickLottoAutoFreeThaiLottoCheckLotto();"><?php _e('ตรวจผลรางวัล', 'lotto-auto-free'); ?></button>
			</div>
		</section>
		<section id="LottoAutoFreeThaiLottoMessage" style="margin-bottom: 30px"></section>

		<script>
			const lottoNumbers = <?php echo json_encode($data['result']['data']['items']); ?>;

			function clickLottoAutoFreeThaiLottoCheckLotto() {
				let msgEl = document.getElementById('LottoAutoFreeThaiLottoMessage');
				msg = '<?php _e('ไม่ถูกรางวัลใด ๆ', 'lotto-auto-free'); ?>';

				const filter = document.getElementById('LottoAutoFreeThaiLottoCheckNumber').value;

				let intRegex = /^\d+$/;
				if (intRegex.test(filter) && filter.length !== 6) {
					msgEl.innerHTML = '<?php _e('กรุณาระบุหมายเลขสลากให้ถูกต้อง', 'lotto-auto-free'); ?>';
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

				msgEl.innerHTML = '<p class="lotto-auto-free-thailotto-search-number-text">' + msg + '</p>';

				return false;
			}
		</script>
<?php
	}
}
