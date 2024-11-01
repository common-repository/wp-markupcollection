<?php
class WP_MarkupCollection_ClassFactory_Exception extends Exception {
	public function  __construct($message) {
		parent::__construct($message);
		$backtrace = debug_backtrace();
		$this->file = $backtrace[1]['file'];
		$this->line = $backtrace[1]['line'];
	}
}

class WP_MarkupCollection_DI {
	private $instance = array();

	public function __get($name) {
		$class = get_class($this);
		$instance = new ReflectionClass($class);
		$method_names = array_map(
			create_function('$m', 'return $m->name;'),
			$instance->getMethods()
		);

		if(!in_array($name, $method_names)) {
			throw new WP_MarkupCollection_ClassFactory_Exception("Undefined property: $class::$name");
		}
		$object = array($this, $name);

		// call_user_func($object) = get instance (lazy initialized)
 		return $object;
	}

	public function __call($name, $args) {
		if (!isset($this->instance[$name])) {
			$this->instance[$name] = call_user_func(array($this, $name));
		}
		return $this->instance[$name];
	}

	public function inject($target) {
		$names = array_slice(func_get_args(), 1);
		foreach($names as $name) {
			$target->$name = $this->$name;
		}
	}

	public function get_instance($name) {
		return call_user_func(array($this, $name));
	}
}

class WP_MarkupCollection_DefaultClassFactory extends WP_MarkupCollection_DI {
	private $settings;

	protected static function import($name) {
		require_once(dirname(__FILE__) . '/lib/' . $name . '.php');
	}

	public function __construct($settings = null) {
		$this->settings = $settings;
	}

	public function hooks() {
		self::import('class-wp-markup-collection-hooks');
		$object = new WP_MarkupCollection_Hooks;
		$this->inject($object, 'options', 'post_util', 'command_factory');
		return $object;
	}

	public function post_util() {
		$this->import('class-wp-markup-collection-post-util');
		return new WP_MarkupCollection_PostUtil;
	}

	public function options() {
		self::import('class-wp-markup-collection-options');

		$plugin_path = isset($this->settings['plugin_path'])
						? $this->settings['plugin_path']
						: dirname(__FILE__);
		$ext_bin_path = isset($this->settings['ext_bin_path'])
						? $this->settings['ext_bin_path']
						: null;

		$settings = compact('plugin_path', 'ext_bin_path');

		return new WP_MarkupCollection_Options($settings);
	}

	public function option_admin_page() {
		self::import('pages/class-wp-markup-collection-option-admin-page');
		$object = new WP_MarkupCollection_OptionAdminPage;
		$this->inject($object, 'options', 'post_util', 'html_util', 'command_factory', 'process_util');
		return $object;
	}

	public function post_admin_page() {
		self::import('pages/class-wp-markup-collection-post-admin-page');
		$object = new WP_MarkupCollection_PostAdminPage();
		$this->inject($object, 'options', 'post_util', 'html_util');
		return $object;
	}

	public function process_util() {
		self::import('utils/class-wp-markup-collection-process-util');
		$options = $this->get_instance('options');
		return new WP_MarkupCollection_ProcessUtil($options->paths);
	}

	public function html_util() {
		self::import('utils/class-wp-markup-collection-html-util');
		return new WP_MarkupCollection_HtmlUtil;
	}

	public function template_engine() {
		self::import('utils/class-wp-markup-collection-template');
		return new WP_MarkupCollection_Template;
	}

	public function command_factory() {
		self::import('commands/class-wp-markup-collection-command-factory');
		self::import('commands/class-wp-markup-collection-command-phpmarkdown');
		self::import('commands/class-wp-markup-collection-command-multimarkdown');
		self::import('commands/class-wp-markup-collection-command-pandoc');
		self::import('commands/class-wp-markup-collection-command-textwiki');
		self::import('commands/class-wp-markup-collection-command-textile');
		self::import('commands/class-wp-markup-collection-command-restructuredtext');
		self::import('commands/class-wp-markup-collection-command-hatenasyntax');
		$object = new WP_MarkupCollection_Command_Factory(array(
			'phpmarkdown'         => 'WP_MarkupCollection_Command_PhpMarkdown',
			'phpmarkdownlib'      => 'WP_MarkupCollection_Command_PhpMarkdown',
			'phptextile'          => 'WP_MarkupCollection_Command_Textile',
			'textwiki'            => 'WP_MarkupCollection_Command_TextWiki',
			'phprestructuredtext' => 'WP_MarkupCollection_Command_reStructuredText',
			'hatenasyntax'        => 'WP_MarkupCollection_Command_HatenaSyntax',
			'multimarkdown'       => 'WP_MarkupCollection_Command_MultiMarkdown',
			'pandoc'              => 'WP_MarkupCollection_Command_Pandoc',
		));
		$this->inject($object, 'options', 'template_engine', 'process_util');
		return $object;
	}
}
