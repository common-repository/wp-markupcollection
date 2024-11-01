<?php
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once('autoload.php');

use \Michelf\Markdown;
use \Michelf\MarkdownExtra;

if (isset($argv[0])) {
	if (isset($argv[1]) && $argv[1] === '--extra') {
		echo MarkdownExtra::defaultTransform(read_from_stdin());
	} else {
		echo Markdown::defaultTransform(read_from_stdin());
	}
}