<?php
/*
Due to security reason, Executable commands are limited.
And also usable option are defined.
Function provided by shell not allowed.
(redirects, pipes, variable expansion, etc)

If you are want to use another command as filter,
need to create custom classes.

Please refer to the following code. This is example for using sed.
*/

// Do nothing if called directly.
if (!defined('ABSPATH')) {
	exit;
}

class WP_MarkupCollection_Command_Sed_Example extends WP_MarkupCollection_Command_Base {

	// allowd options.
	protected $command_options = array(
		'-e:', //Colon means one parameter require.
		'-r',
	);

	// override info if you need.
	public static function getInfo($name) {
		$info = parent::getInfo($name);
		return array_merge($info, array(
			// 'internal' => false,
			// 'require' => null,
		));
	}

	// use to check args or modify raw_content (markup)
	public function pre_process(&$raw_content) {
		// do nothing
	}

	// use to modify content (html)
	public function post_process(&$content) {
		// do nothing
	}
}

class WP_MarkupCollection_CustomClassFactory extends WP_MarkupCollection_DefaultClassFactory {
	public function command_factory() {
		$command_factory = parent::command_factory();
		$command_factory->commands['sed'] = 'WP_MarkupCollection_Command_Sed_Example';
		return $command_factory;
	}
}
