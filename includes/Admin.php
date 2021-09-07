<?php

namespace WpLottoAutoUpdate;

defined('ABSPATH') || die();

class Admin
{
	private $options;

	public function __construct()
	{
	}

	public function runAction()
	{
		\add_action('admin_menu', [$this, 'addPluginPage']);
		\add_action('admin_init', [$this, 'pageInit']);
	}

	public function addPluginPage()
	{
		\add_options_page(
			\__('Lotto Setting', 'wp-lotto-auto-update'),
			\__('Lotto', 'wp-lotto-auto-update'),
			'manage_options',
			'wp-lotto-auto-update-setting',
			[$this, 'createPage']
		);
	}

	public function createPage()
	{
		$this->options = \get_option('wp_lotto_auto_update_get_options'); ?>

		<div class="wrap">
			<h2><?php echo \__('Lotto Setting', 'wp-lotto-auto-update'); ?></h2>
			<p></p>
			<?php \settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				\settings_fields('wp_lotto_auto_update_group');
				\do_settings_sections('wp_lotto_auto_update_admin_sections');
				\submit_button();
				?>
			</form>
		</div>

<?php
	}

	public function pageInit()
	{
		\register_setting(
			'wp_lotto_auto_update_group',
			'wp_lotto_auto_update_get_options',
			[$this, 'sanitize']
		);

		\add_settings_section(
			'wp_lotto_auto_update_admin_form_section_page_ids',
			\__('Setup PageID', 'wp-lotto-auto-update'),
			[$this, 'sectionInfo'],
			'wp_lotto_auto_update_admin_sections'
		);

		\add_settings_field(
			'PageIDThaiLottoField',
			\__('ThaiLotto', 'wp-lotto-auto-update'),
			[$this, 'inputPageThaiLottoCallback'],
			'wp_lotto_auto_update_admin_sections',
			'wp_lotto_auto_update_admin_form_section_page_ids'
		);
	}

	public function sanitize($input)
	{
		$sanitary_values = [];
		if (isset($input['page_ids']['thailotto'])) {
			$sanitary_values['page_ids']['thailotto'] = intval($input['page_ids']['thailotto']);
		}

		return $sanitary_values;
	}

	public function sectionInfo()
	{
	}

	public function inputPageThaiLottoCallback()
	{
		$checked = !empty($this->options['page_ids']['thailotto']);
		printf(
			'<input class="small-text" type="number" name="wp_lotto_auto_update_get_options[page_ids][thailotto]" id="PageIDThaiLottoInput" value="%s">',
			$checked ? \esc_attr($this->options['page_ids']['thailotto']) : 0
		);

		print(' <span class="description">ShortCode: <code>[wp-lotto-auto-update path="thailotto"]</code></span>');
	}
}
