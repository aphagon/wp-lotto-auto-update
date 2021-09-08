<?php

namespace WpLottoAutoUpdate;

defined('ABSPATH') || die();

class Page
{
	/**
	 * @access private
	 * @var array
	 */
	private $options;

	public function __construct()
	{
		$this->options = \get_option('wp_lotto_auto_update_get_options');
	}

	/**
	 * @param string $path
	 *
	 * @return int|false
	 */
	public static function optionID(string $path)
	{
		$obj = new self();

		return isset($obj->options['page_ids'][$path]) ? $obj->options['page_ids'][$path] : false;
	}

	public function runAction()
	{
		// Disable admin page.
		$uri_path = $_SERVER['REQUEST_URI'];
		if (strpos($uri_path, '/wp-admin') === false) {
			\add_filter('the_title', [$this, 'filterPostTitle']);
			\add_filter('document_title_parts', [$this, 'filterHeadTitle']);
		}
	}

	/**
	 * @param string $title
	 *
	 * @return string
	 */
	public function filterPostTitle(string $title)
	{
		global $post;

		if (
			is_a($post, 'WP_Post')
			&& \has_shortcode($post->post_content, 'wp-lotto-auto-update')
			&& isset($this->options['page_ids'])
			&& is_array($this->options['page_ids'])
		) {
			$date = !empty($_GET['lotto-date']) ? trim($_GET['lotto-date']) : '';
			foreach ($this->options['page_ids'] as $path => $page_id) {
				if ($page_id == $post->ID) {
					$res = Helper::curl($path, $date);
					if (
						!empty($res['success'])
						&& (($date = Helper::replaceThaiDate($res['data']['title'])) && $date !== '')
					) {
						return Helper::replaceTitleDate($title, $date);
					}
				}
			}
		}

		return $title;
	}

	/**
	 * Override default post/page title
	 *
	 * @param array $title
	 *
	 * @return array
	 */
	public function filterHeadTitle(array $title)
	{
		$title['title'] = $this->filterPostTitle($title['title']);

		return $title;
	}

	public function insert()
	{
		if (!isset($this->options['page_ids']) || !is_array($this->options['page_ids'])) {
			$this->options['page_ids'] = [];
		}

		if (!isset($this->options['page_ids']['thailotto'])) {
			$page_obj = \get_page_by_path(__('ตรวจสลากกินแบ่งรัฐบาล งวดที่ {dd} {mm} {yyyy}', 'wp-lotto-auto-update'));
			if (!$page_obj) {
				$page_id = \wp_insert_post([
					'post_title' => __('ตรวจสลากกินแบ่งรัฐบาล งวดที่ {dd} {mm} {yyyy}', 'wp-lotto-auto-update'),
					'post_name' => __('ตรวจสลากกินแบ่งรัฐบาล', 'wp-lotto-auto-update'),
					'post_content' => '[wp-lotto-auto-update path="thailotto"]',
					'comment_status' => 'closed',
					'post_status' => 'publish',
					'post_type' => 'page',
				]);
			} else {
				if ($page_obj->post_status == 'trash') {
					$page_id = \wp_update_post([
						'ID' => $page_obj->ID,
						'post_status' => 'publish'
					]);
				}
			}

			$this->options['page_ids']['thailotto'] = $page_id;
		}

		\update_option('wp_lotto_auto_update_get_options', $this->options);
	}

	public function trash()
	{
		if (
			!isset($this->options['page_ids'])
			|| !is_array($this->options['page_ids'])
		) {
			return;
		}

		foreach ($this->options['page_ids'] as $path => $page_id) {
			$post = \get_post($page_id);
			if ($post && $post->post_status == 'publish') {
				\wp_update_post([
					'ID' => $post->ID,
					'post_status' => 'trash'
				]);
			}
		}
	}
}
