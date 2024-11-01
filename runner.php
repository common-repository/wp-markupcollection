<?php
if (!class_exists('WP_MarkupCollection_Runner')) {
	class WP_MarkupCollection_SecurityViolationException extends Exception {}
	class WP_MarkupCollection_FileNotFoundException extends Exception {}

	class WP_MarkupCollection_Runner {
		public static function run($server, $post) {
			try {
				set_error_handler('WP_MarkupCollection_Runner::error_handler');

				WP_MarkupCollection_Runner::security_check($server);

				$filter = $post['filter'];
				if (!file_exists($filter)) {
					$msg = sprintf('File not found: %s', $filter);
					throw new WP_MarkupCollection_FileNotFoundException($msg);
				}

				$argv = array($filter);
				if (isset($post['args'])) {
					$argv = array_merge($argv, $post['args']);
				}
				$argc = count($argv);
				global $wpmc_post_text;
				$wpmc_post_text = isset($post['text']) ? $post['text'] : '';
				restore_error_handler();
			} catch (Exception $e) {
				restore_error_handler();
				throw $e;
			}
			require($filter);
		}

		public static function error_handler($errno, $errstr, $errfile, $errline) {
			throw new RuntimeException("[$errno] $errstr on line $errline in file $errfile");
		}

		private static function security_check($server) {
			if ($server['REMOTE_ADDR'] != '127.0.0.1') {
				$msg = sprintf('This remote host is not allowed: %s', $server['REMOTE_ADDR']);
				throw new WP_MarkupCollection_SecurityViolationException($msg);
			}
		}
	}
}

if (PHP_SAPI === 'cli') {
	WP_MarkupCollection_Runner::run($_SERVER, $_POST);
} else {
	try {
		WP_MarkupCollection_Runner::run($_SERVER, $_POST);
	} catch(WP_MarkupCollection_SecurityViolationException $e) {
		error_log($e->getMessage());
		header ('HTTP/1.1 403 Forbidden');
	} catch(WP_MarkupCollection_FileNotFoundException $e) {
		error_log($e->getMessage());
		header ('HTTP/1.1 404 Not Found');
	} catch(Exception $e) {
		error_log($e->getMessage());
		header ('HTTP/1.1 500 Internal Server Error');
	}
}
