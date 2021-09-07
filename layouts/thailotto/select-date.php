<?php

defined('ABSPATH') || die();

use WpLottoAutoUpdate\Helper;
use WpLottoAutoUpdate\Page;

if (!function_exists('wp_lotto_auto_update_container_thailotto_layout_select_date')) {
	add_action('wp_lotto_auto_update_container_thailotto_layout_select_date', 'wp_lotto_auto_update_container_thailotto_layout_select_date', 10, 2);
	/**
	 * @param array $data
	 * @param string $date
	 */
	function wp_lotto_auto_update_container_thailotto_layout_select_date(array $data, string $date)
	{
		$time = strtotime($date);
		$dd = date_i18n('j', $time);
		$mm = date_i18n('F', $time);
		$yyyy = intval(date_i18n('Y', $time)) - 543;

		$lottoYears = Helper::getThaiLottoYears();
		if (empty($lottoYears['success'])) {
			_e('Failed to retrieve data.', 'wp-lotto-auto-update');
			return;
		}

		$jsonYears = [];
		foreach ($lottoYears['data'] as $year => $histories) {
			foreach ($histories as $history) {
				$time = strtotime($history);
				$dd = date_i18n('j', $time);
				$mm = date_i18n('F', $time);
				$yyyy = intval(date_i18n('Y', $time)) + 543;

				$jsonYears[$year][] = sprintf('%d %s %d', $dd, $mm, $yyyy);
			}
		}
?>

		<section class="wp-lotto-auto-update__select-date-form">
			<div class="wp-lotto-auto-update__select-date-form__column">
				<select id="WpLottoAutoUpdateThaiLottoSelectYear" class="wp-lotto-auto-update__select-date-form__column-select">
					<option value=""><?php _e('ปี', 'wp-lotto-auto-update'); ?></option>
					<?php
					$years = array_keys($lottoYears['data']);
					rsort($years);
					foreach ($years as $year) {
						$selected = ($yyyy == $year) ? 'selected="selected"' : '';
						printf('<option value="%s" %s>%d</option>', $year, $selected, intval($year) + 543);
					}
					?>
				</select>
			</div>
			<div class="wp-lotto-auto-update__select-date-form__column">
				<select id="WpLottoAutoUpdateThaiLottoSelectDate" class="wp-lotto-auto-update__select-date-form__column-select">
					<option value=""><?php _e('งวดประจำวันที่', 'wp-lotto-auto-update'); ?></option>
					<?php
					if (!empty($jsonYears[$yyyy])) {
						foreach ($jsonYears[$yyyy] as $date) {
							list($d, $m, $y) = array_map('trim', explode(' ', $date));
							$arrMonth = array_flip(Helper::getMonths());
							$d = sprintf('%d-%02d-%02d', intval(trim($y)), $arrMonth[trim($m)], trim($d));

							$selected = ($data['date'] == $d) ? 'selected="selected"' : '';
							printf('<option value="%s" %s>%s</option>', $d, $selected, $date);
						}
					}
					?>
				</select>
			</div>
			<div class="wp-lotto-auto-update__select-date-form__column">
				<button type="button" class="wp-lotto-auto-update__select-date-form__column-button" onclick="return clickWpLottoAutoUpdateThaiLottoSelectDate();"><?php _e('ค้นหา', 'wp-lotto-auto-update'); ?></button>
			</div>
		</section>

		<script>
			const lottoYears = <?php echo json_encode($jsonYears); ?>;
			const lottoMonths = <?php echo json_encode(Helper::getMonths()); ?>;

			const optsDays = document.getElementById('WpLottoAutoUpdateThaiLottoSelectDate');
			document.getElementById('WpLottoAutoUpdateThaiLottoSelectYear').addEventListener('change', function() {
				optsDays.innerHTML = '';
				optsDays.options[optsDays.options.length] = new Option('<?php echo _e('งวดประจำวันที่', 'wp-lotto-auto-update'); ?>', '');
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

			function clickWpLottoAutoUpdateThaiLottoSelectDate() {
				const date = document.getElementById("WpLottoAutoUpdateThaiLottoSelectDate").value;
				if (date != '') {
					window.location = '<?php echo esc_url(get_permalink(Page::optionID('thailotto'))); ?>lotto-date-' + date;
				} else {
					alert('<?php _e('จำเป็นต้องเลือกงวด', 'wp-lotto-auto-update'); ?>');
				}

				return false;
			}
		</script>
<?php
	}
}
