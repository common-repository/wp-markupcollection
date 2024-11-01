<?php
require_once(dirname(__FILE__) . '/lib/functions.php');

if (isset($argv[0])) {
	if (isset($argv[1]) && $argv[1] === '--extra') {
		require_once('php-markdown-extra/markdown.php');
	} else {
		require_once('php-markdown/markdown.php');
	}

	echo Markdown(read_from_stdin());
}