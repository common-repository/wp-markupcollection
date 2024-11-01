<?php
set_include_path(dirname(__FILE__) . '/../../vendor');

function read_from_stdin() {
	global $wpmc_post_text;
	if (isset($wpmc_post_text)) {
		$text = $wpmc_post_text;
		unset($wpmc_post_text);
		return $text;
	}
	return file_get_contents('php://stdin');
}