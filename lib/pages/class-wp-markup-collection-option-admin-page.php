<?php
class WP_MarkupCollection_OptionAdminPage {
	/* Inject */ public $options;
	/* Inject */ public $html_util;
	/* Inject */ public $post_util;
	/* Inject */ public $command_factory;
	/* Inject */ public $process_util;
	private $option_page = 'wp_markup_collection_options';

	public function create() {
		add_action('admin_init', array($this, 'admin_init_action'));
		add_action('admin_menu', array($this, 'admin_menu_action'));
	}

	public function destroy() {
		unregister_setting($this->option_page, WPMC_OPTIONS, array($this, 'validate'));
		remove_action('admin_init', array($this, 'admin_init_action'));
		remove_action('admin_menu', array($this, 'admin_menu_action'));
	}

	public function admin_init_action() {
		register_setting($this->option_page, WPMC_OPTIONS, array($this, 'validate'));
	}

	public function admin_menu_action() {
		$html_util = call_user_func($this->html_util);
		$tmpl = dirname(__FILE__) . '/option-admin-page.tmpl.php';
		$options_page = $html_util->template($tmpl, array($this, 'options_page_params'));
		add_options_page('WP-MarkupCollection', 'Markup Collection', 'manage_options', __FILE__, $options_page);
	}

	public function validate($input) {
		$post_util =  call_user_func($this->post_util);

		$checkboxes = array(
			'decode_character_entity_reference',
			'decode_numeric_character_reference',
			'cache_enabled',
		);
		foreach($checkboxes as $checkbox) {
			$input[$checkbox] = isset($input[$checkbox]);
		}
		$input['filters'] = str_replace("\r\n", "\n", $input['filters']);

		if (isset($input['delete_cache'])) {
			unset($input['delete_cache']);
			$post_util->delete_cache();
		}
		return $input;
	}

	public function options_page_params() {
		$options =  call_user_func($this->options);
		$html_util = call_user_func($this->html_util);
		$post_util =  call_user_func($this->post_util);
		$command_factory =  call_user_func($this->command_factory);
		$process_util =  call_user_func($this->process_util);

		$available_filters = array();
		foreach($command_factory->commands as $command => $class) {
			# same as $info = $class::getInfo($command); workaround for php 5.2
			$info = eval('return ' . $class . '::getInfo($command);');

			$command_path = $process_util->resolve_path($command);
			$installed = $command_path ? true : false;
			$executable = false;
			if ($command_path && is_executable($command_path)) {
				$executable = true;
			}
			$available_filters[] = array(
				'name' => $command,
				'internal'  => $info['internal'],
				'require'   => $info['require'],
				'installed' => $installed,
				'executable' => $executable,
			);
		}

		$params = array(
			'attrs' => $html_util->attrs(WPMC_OPTIONS, $options->data()),
			'cache_count' => $post_util->count_cache(),
			'option_page' => $this->option_page,
			'markup_filters' => $options->filters,
			'filter_paths' => $options->paths,
			'available_filters' => $available_filters,
		);
		return $params;
	}
}
