<?php
class WP_MarkupCollection_ProcessUtil {
	public $paths;

	public function __construct($paths) {
		$this->paths = $paths;
	}

	public function safe_exec($args, $content) {
		$desc = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w"),
		);

		$command = $args[0];
		if ($command[0] != '/') {
			$args[0] = $this->resolve_path($command);
			if ($args[0] === NULL) {
				throw new Exception(sprintf("'%s' not found. nor executable. ", $command));
			}
		}
		foreach($args as $i => $arg) {
			$args[$i] = escapeshellarg($arg);
		}
		$cmdline = implode(' ', $args);
		$process = proc_open($cmdline, $desc, $pipes);
		if ($process) {
			fwrite($pipes[0], $content );
			fclose($pipes[0]);

			$result = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$error = stream_get_contents($pipes[2]);
			fclose($pipes[2]);

			$ret = proc_close($process);

			if ($ret == 0) {
				$content = $result;
			} else {
				throw new Exception($error);
			}
		}

		return $content;
	}

	public function args($command) {
		preg_match_all('{
			(?: \s* (?| (["\'])((?:\\\\["\']|.)+?)\g{-2}
			|
			(([^"\'\s]+)) ) \s* )
		}x', $command, $matches);
		$matches = $matches[2];

		foreach($matches as $value) {
			$value = preg_replace('{\\\\(.)}', '${1}', $value);
		}
		return $matches;
	}

	public function resolve_path($command) {
		foreach($this->paths as $path) {
			$file = $path . '/' . $command;
			if (file_exists($file)) {
				return $file;
			}
		}
		return NULL;
	}
}