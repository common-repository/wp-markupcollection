<?php
class WP_MarkupCollection_Command_Textile extends WP_MarkupCollection_Command_PHPRunner {

	protected $command_options = array();

	public static function getInfo($name) {
		$info = parent::getInfo($name);
		return array_merge($info, array(
			'require' => 'PHP Version >= 5.3',
		));
	}

}
