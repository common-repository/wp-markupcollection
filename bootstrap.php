#!/usr/bin/env php
<?php
array_shift($argv);
$argc--;
if (isset($argv[0])) {
	require_once($argv[0]);
}
