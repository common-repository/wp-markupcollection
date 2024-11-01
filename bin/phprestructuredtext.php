<?php
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once 'php-restructuredtext/rst.php';

if (isset($argv[0])) {
	echo RST(read_from_stdin());
}
