<?php
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once('autoload.php');

if (isset($argv[0])) {
	echo HatenaSyntax::render(read_from_stdin());
}
