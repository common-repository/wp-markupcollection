<?php
class WP_MarkupCollection_Command_Factory {
	/* Inject */ public $options;
	/* Inject */ public $template_engine;
	/* Inject */ public $process_util;
	public $commands;

	public function __construct($commands) {
		$this->commands = $commands;
	}

	public function create($filter, $meta = array()) {
		$process_util = call_user_func($this->process_util);

		$args = $process_util->args($filter);
		$name = $args[0];

		if (!isset($this->commands[$name])) {
			throw new Exception(sprintf("Command name '%s' not found", $name));
		}
		$class_name = $this->commands[$name];
		$instance = new $class_name($args, $meta, $this->options, $this->process_util, $this->template_engine);
		return $instance;
	}
}
