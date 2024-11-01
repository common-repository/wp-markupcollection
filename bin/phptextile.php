<?php
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once('autoload.php');

if (isset($argv[0])) {
	$parser = new \Netcarver\Textile\Parser('html5');
	echo $parser->textileThis(read_from_stdin());
}
