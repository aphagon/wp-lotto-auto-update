<?php

namespace WpLottoAutoUpdate;

defined('ABSPATH') || die();

use Wruczek\PhpFileCache\PhpFileCache;

final class Helper
{

	/**
	 * @param string $date yyyy-mm-dd
	 * @return bool
	 */
	public static function isDate(string $date)
	{
		return preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public static function isPath(string $path)
	{
		return in_array($path, static::getPaths());
	}

	/**
	 * @return array
	 */
	public static function getPaths()
	{
		return apply_filters('wp_lotto_auto_update_paths', [
			'thailotto'
		]);
	}

	/**
	 * @param string $str
	 * @param string $date
	 *
	 * @return string
	 */
	public static function replaceTitleDate(string $str, string $date)
	{
		if (!Helper::isDate($date)) {
			$date = date('Y-m-d', \current_time('timestamp'));
		}

		$time = strtotime($date);

		$dd = date('j', $time);
		$mm = date('F', $time);
		$yyyy = intval(date('Y', $time));

		return str_replace(['{dd}', '{mm}', '{yyyy}'], [$dd, $mm, $yyyy], $str);
	}

	/**
	 * @param int $month
	 *
	 * @return string|array
	 */
	public static function getMonths($month = 0)
	{
		$months = [
			'',
			'มกราคม',
			'กุมภาพันธ์',
			'มีนาคม',
			'เมษายน',
			'พฤษภาคม',
			'มิถุนายน',
			'กรกฎาคม',
			'สิงหาคม',
			'กันยายน',
			'ตุลาคม',
			'พฤศจิกายน',
			'ธันวาคม',
		];

		$key = sprintf('%d', $month);

		return !empty($months[$key]) ? $months[$key] : $months;
	}

	/**
	 * @param string $str
	 *
	 * @return string date YYYY-MM-DD
	 */
	public static function replaceThaiDate(string $str)
	{
		$exp = explode(' ', $str);
		$arr = array_map('trim', $exp);
		list($dd, $mm, $yyyy) = $arr;

		$months = array_flip(static::getMonths());
		$mm = !empty($months[$mm]) ? $months[$mm] : 0;

		$date = sprintf('%d-%02d-%02d', $yyyy, $mm, $dd);
		if (!Helper::isDate($date)) {
			return '';
		}

		return $date;
	}

	/**
	 * @param string $path
	 * @param string $date yyyy-mm-dd
	 * @return array|null
	 */
	public static function curl(string $path, string $date)
	{
		if ($path !== '' && $date !== '') {
			$path .= '/' . $date;
		}

		$cache = new PhpFileCache(WP_LOTTO_AUTO_UPDATE_CACHE_DIR, sprintf('%s.cache', md5($path)));
		return $cache->refreshIfExpired(md5($path), function () use ($path) {
			$response = \wp_remote_get(\esc_url_raw(WP_LOTTO_AUTO_UPDATE_API_URL . $path), [
				'timeout' => 15,
				'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'
			]);

			if (\is_wp_error($response)) {
				return '';
			}

			$responseBody = \wp_remote_retrieve_body($response);
			$result = json_decode($responseBody, true);
			if (is_array($result)) {
				return $result;
			}

			return null;
		}, 600); // 10 Min
	}

	/**
	 *
	 * @return array|null
	 */
	public static function getThaiLottoYears()
	{
		return static::curl('thailotto/years', '');
	}
}
