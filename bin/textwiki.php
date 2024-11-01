<?php
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once 'Text/Wiki.php';

if (isset($argv[0])) {
	$commands = array(
		'bbcode'    => 'BBCode',
		'cowiki'    => 'Cowiki',
		'creole'    => 'Creole',
		'default'   => 'Default',
		'dokuwiki'  => 'Doku',
		'mediawiki' => 'Mediawiki',
		'tiki'      => 'Tiki',
	);

	$command = 'Default';
	if ($argv[1] === '-f') {
		if (isset($commands[$argv[2]])) {
			$command = $commands[$argv[2]];
		}
	}

	$wiki = Text_Wiki::factory($command);
	$wiki->enableRule('html');
	$wiki->setFormatConf('Xhtml', 'translate', HTML_SPECIALCHARS);
	echo $wiki->transform(read_from_stdin());
}