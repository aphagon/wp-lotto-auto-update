<?php

namespace WpLottoAutoUpdate;

defined('ABSPATH') || die();

class Page
{
	public function __construct()
	{
	}

	public function runAction()
	{
		\add_action('init', [$this, 'addQueryVars']);
	}

	// Adds a rewrite rule that transforms a URL structure to a set of query vars.
	public function addQueryVars()
	{
		global $wp;
		$wp->add_query_var('lotto-date');

		\add_rewrite_rule(
			'(.?.+?)/lotto-date-([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})/?$',
			'index.php?pagename=$matches[1]&lotto-date=$matches[2]',
			'top'
		);

		if (!\get_option('wp_lotto_auto_update_permalinks_flushed')) {
			\flush_rewrite_rules(false);
			\update_option('wp_lotto_auto_update_permalinks_flushed', 1);
		}
	}

	public function insert()
	{
		$options = \get_option('wp_lotto_auto_update_get_options');
		if (!isset($options['page_ids']) || !is_array($options['page_ids'])) {
			$options['page_ids'] = [];
		}

		if (!isset($options['page_ids']['thailotto'])) {
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

			$options['page_ids']['thailotto'] = $page_id;
		}

		\update_option('wp_lotto_auto_update_get_options', $options);
	}

	public function trash()
	{
		$options = \get_option('wp_lotto_auto_update_get_options');
		if (isset($options['page_ids']) || is_array($options['page_ids'])) {
			foreach ($options['page_ids'] as $pages) {
				foreach ($pages as $page_id) {
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

		\delete_option('wp_lotto_auto_update_permalinks_flushed');
	}
}
