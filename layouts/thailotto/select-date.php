<?php

defined('ABSPATH') || die();

use LottoAutoFree\Curl;
use LottoAutoFree\Helper;

if (!function_exists('wp_lotto_auto_update_layout_thailotto_select_date')) {
	/**
	 * @param array $data {
	 * 	'date' => 'YYYY-MM-DD',
	 * 	'data' => array
	 * }
	 *
	 * @return string
	 */
	function wp_lotto_auto_update_layout_thailotto_select_date(array $data)
	{
		$splitDate = Helper::splitDate($data['date']);
		$lottoYears = Curl::GetThaiLottoYears();
		if (empty($lottoYears['success'])) {
			_e('Failed to retrieve data.', 'lotto-auto-free');
			return;
		}

		$jsonYears = [];
		foreach ($lottoYears['data'] as $year => $histories) {
			foreach ($histories as $history) {
				$slDate = Helper::splitDate($history);
				$d = $slDate['day'];
				$m = $slDate['month'];
				$y = $slDate['year'];
				$jsonYears[$year][] = sprintf('%d %s %d', $d, Helper::getMonths($m), ($y + 543));;
			}
		}
?>

		<section class="lotto-auto-free-select-date-form">
			<div class="lotto-auto-free-select-date-form__column">
				<select id="LottoAutoFreeThaiLottoSelectYear" class="lotto-auto-free-select-date-form__column-select">
					<option value=""><?php _e('ปี', 'lotto-auto-free'); ?></option>
					<?php
					$years = array_keys($lottoYears['data']);
					rsort($years);
					foreach ($years as $year) {
						$selected = ($splitDate['year'] == $year) ? 'selected="selected"' : '';
						printf('<option value="%s" %s>%d</option>', $year, $selected, intval($year) + 543);
					}
					?>
				</select>
			</div>
			<div class="lotto-auto-free-select-date-form__column">
				<select id="LottoAutoFreeThaiLottoSelectDate" class="lotto-auto-free-select-date-form__column-select">
					<option value=""><?php _e('งวดประจำวันที่', 'lotto-auto-free'); ?></option>
					<?php
					if (!empty($jsonYears[$splitDate['year']])) {
						foreach ($jsonYears[$splitDate['year']] as $date) {
							list($d, $m, $y) = explode(' ', $date);
							$arrMonth = array_flip(Helper::getMonths());
							$d = sprintf('%d-%02d-%02d', intval(trim($y)) - 543, $arrMonth[trim($m)], trim($d));

							$selected = ($data['date'] == $d) ? 'selected="selected"' : '';
							printf('<option value="%s" %s>%s</option>', $d, $selected, $date);
						}
					}
					?>
				</select>
			</div>
			<div class="lotto-auto-free-select-date-form__column">
				<button type="button" class="lotto-auto-free-select-date-form__column-button" onclick="return clickLottoAutoFreeThaiLottoSelectDate();"><?php _e('ค้นหา', 'lotto-auto-free'); ?></button>
			</div>
		</section>

		<script>
			const lottoYears = <?php echo json_encode($jsonYears); ?>;
			const lottoMonths = <?php echo json_encode(Helper::getMonths()); ?>;

			const optsDays = document.getElementById('LottoAutoFreeThaiLottoSelectDate');
			document.getElementById('LottoAutoFreeThaiLottoSelectYear').addEventListener('change', function() {
				optsDays.innerHTML = '';
				optsDays.options[optsDays.options.length] = new Option('<?php echo _e('งวดประจำวันที่', 'lotto-auto-free'); ?>', '');
				if (this.value == '') return

				lottoYears[this.value].forEach(function(history, i) {
					const d = history.split(' ');

					let month, date;
					for (let i = 1; i < lottoMonths.length; i++) {
						if (lottoMonths[i] === d[1]) {
							month = ('0' + i).slice(-2);
							break;
						}
					}
					date = (parseInt(d[2]) - 543) + '-' + month + '-' + ('0' + d[0]).slice(-2);
					optsDays.options[optsDays.options.length] = new Option(history, date);
				});
			});

			function clickLottoAutoFreeThaiLottoSelectDate() {
				const date = document.getElementById("LottoAutoFreeThaiLottoSelectDate").value;
				if (date != '') {
					window.location = '<?php echo esc_url(get_permalink()); ?>lotto-date-' + date;
				} else {
					alert('<?php _e('จำเป็นต้องเลือกงวด', 'lotto-auto-free'); ?>');
				}

				return false;
			}
		</script>
<?php
	}
}
