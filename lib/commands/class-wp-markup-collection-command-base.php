<?php
abstract class WP_MarkupCollection_Command_Base {
	protected $name;
	protected $options;
	protected $template_engine;
	protected $command_options = array();
	protected $process_util;
	protected $args;
	protected $meta;

	public static function getInfo($name) {
		return array(
			'internal' => false,
			'require' => null,
		);
	}

	public function __construct($args, $meta, $options, $process_util, $template_engine) {
		$this->args = $args;
		$this->meta = $meta;
		$this->options = $options;
		$this->process_util = $process_util;
		$this->template_engine = $template_engine;
		$this->check_command_options($args);
	}

	private function check_command_options($args) {
		$command = array_shift($args);
		$need_value = array();
		$option_lookup = array();
		foreach($this->command_options as $option) {
			if (preg_match('/(.*):$/', $option, $matches)) {
				$option = $matches[1];
				$need_value[$option] = true;
			}
			$option_lookup[$option] = true;
		}

		for($i = 0; $i < count($args); $i++) {
			$arg = $args[$i];
			if (preg_match('/(--.*)=.*/', $arg, $matches)) {
				$arg = $matches[1];
				if (!isset($option_lookup[$arg])) {
					throw new Exception(sprintf("'%s' not acceptable option '%s'.", $command, $arg));
				}
				if (!isset($need_value[$arg])) {
					throw new Exception(sprintf("'%s' option '%s' doesn't allow an argument.", $command, $arg));
				}
			} else {
				if (!isset($option_lookup[$arg])) {
					throw new Exception(sprintf("'%s' not acceptable option '%s'.", $command, $arg));
				}
				if (isset($need_value[$arg])) {
					$i++;
				}
			}
		}
	}

	public function pre_process(&$raw_content) {
		// do nothing
	}

	public function execute($source) {
		$process_util = call_user_func($this->process_util);

		$args = $this->args;
		return $process_util->safe_exec($args, $source);
	}

	public function post_process(&$content) {
		$this->decode_entity_reference($content);
	}

	private function decode_entity_reference(&$content) {
		$options = call_user_func($this->options);

		if ($options->decode_character_entity_reference && $options->decode_numeric_character_reference) {
			$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
			return;
		}

		if ($options->decode_character_entity_reference) {
			$content = htmlspecialchars_decode($content);
		}

		if ($options->decode_numeric_character_reference) {
			$content = preg_replace_callback('/&#(\d{2,5}|x[a-fA-F0-7]{2,8});/', array($this, 'numeric_character_decode'), $content);
		}
	}

	private function numeric_character_decode($matches){
		$convmap = array(0x0, 0x10000, 0, 0xfffff);
		$number = $matches[1];
		if ($number[0] === 'x') {
			$number = hexdec(substr($number, 1));
		}
		return mb_decode_numericentity("&#$number;", $convmap, 'UTF-8');
	}
}

abstract class WP_MarkupCollection_Command_PHPRunner extends WP_MarkupCollection_Command_Base {
	public static function getInfo($name) {
		$info = parent::getInfo($name);
		return array_merge($info, array(
			'internal' => true,
		));
	}

	public function execute($source) {
		$options = call_user_func($this->options);

		if ($options->phprunner == 'exec') {
			return $this->exec($source);
		}
		return $this->post($source);
	}

	private function exec($source) {
		$process_util = call_user_func($this->process_util);

		$args = $this->args;
		$args[0] = $process_util->resolve_path($args[0] . '.php');
		$bootstrap = dirname(__FILE__) . '/../../bootstrap.php';
		if (!is_executable($bootstrap)) {
			$perms = fileperms($bootstrap) | 0111;
			chmod($bootstrap, $perms);
		}
		array_unshift($args, $bootstrap);

		return $process_util->safe_exec($args, $source);
	}

	private function post($source) {
		$process_util = call_user_func($this->process_util);

		$loopback = '127.0.0.1';
		$host = $_SERVER['SERVER_NAME'];
		$port = $_SERVER['SERVER_PORT'];
		$runner_path = WPMC_PLUGIN_URL . '/runner.php';

		$sock = stream_socket_client(
			"tcp://$loopback:$port",
			$errno, $errstr, 1,
			STREAM_CLIENT_CONNECT,
			stream_context_create(array(
				'socket' => array('bindto' => "$loopback:0")
			))
		);

		$args = $this->args;
		$filter = array_shift($args);
		$filter = $process_util->resolve_path($filter . '.php');

		$data = http_build_query(array(
			'filter' => $filter,
			'args' => $args,
			'text' => $source
		), '', '&');

		$request = array(
			"POST $runner_path HTTP/1.0",
			"Host: $host",
			'Content-type: application/x-www-form-urlencoded',
			'Content-length: ' . strlen($data) . '',
			'Connection: Close',
			'',
			$data,
		);

		fwrite($sock, implode("\r\n", $request));
		ob_start();
		fpassthru($sock);
		$response = ob_get_clean();
		fclose($sock);

		$response = explode("\r\n\r\n", $response, 2);

		$headers = explode("\r\n", $response[0]);
		if (!preg_match('/^HTTP\/1\.1 200 /', $headers[0])) {
			throw new Exception('PHPRunner internal error: ' . $headers[0]);
		}
		return $response[1];
	}
}
