<?php
class WP_MarkupCollection_PostUtil {
	private $wpdb;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	private function getter($id, $name) {
		return get_post_meta($id, $name, true);
	}

	private function setter($id, $name, $value) {
		if ($value === null) {
			delete_post_meta($id, $name);
		} else {
			update_post_meta($id, $name, $value);
		}
	}

	public function filter($id) {
		if (func_num_args() === 1) {
			return $this->getter($id, WPMC_META_FILTER);
		}
		$arg = func_get_arg(1);
		if ($arg !== null) {
			$arg = addslashes($arg);
		}
		$this->setter($id, WPMC_META_FILTER, $arg);
	}

	public function cache($id) {
		if (func_num_args() === 1) {
			return $this->getter($id, WPMC_META_CACHE);
		}
		$arg = func_get_arg(1);
		if ($arg !== null) {
			$arg = addslashes($arg);
		}
		$this->setter($id, WPMC_META_CACHE, $arg);
	}

	public function get_cache($id, $hash) {
		$cache = explode('|', $this->cache($id), 2);

		if($hash === $cache[0]) {
			return $cache[1];
		}

		return null;
	}

	public function meta($id) {
		if (func_num_args() === 1) {
			return $this->getter($id, WPMC_META);
		}
		$arg = func_get_arg(1);
		$this->setter($id, WPMC_META, $arg);
	}

	public function set_cache($id, $hash, $content) {
		$this->cache($id, $hash . '|' . $content);
	}

	public function delete_cache() {
		$postmeta = $this->wpdb->postmeta;
		$meta_key = WPMC_META_CACHE;
		$sql = "DELETE FROM $postmeta WHERE meta_key = '$meta_key'";
		$this->wpdb->query($sql);
	}

	public function count_cache() {
		$postmeta = $this->wpdb->postmeta;
		$meta_key = WPMC_META_CACHE;
		$sql = "SELECT count(*) FROM $postmeta WHERE meta_key = '$meta_key'";
		return $this->wpdb->get_var($sql);
	}

	public function wrap_content($content, $quote = true) {
		# This is for fallback if the plugin was disabled.
		# And Crayon syntax highlighter apply by detect pre tag.
		$content = htmlspecialchars($content);
		$content = sprintf("<pre class='%s'>\n%s\n</pre>", WPMC_MARKUP_CLASS, $content);
		if ($quote) {
			$content = quotemeta($content);
		}
		return $content;
	}

	public function unwrap_content($content, $quote = true) {
		if ($quote) {
			$content = stripslashes($content);
		}
		$regexp = sprintf("{^<pre class='%s'>\r?\n(.*)\r?\n</pre>$}s", WPMC_MARKUP_CLASS);
		$content = preg_replace($regexp, '$1', $content);
		return htmlspecialchars_decode($content);
	}

	public function is_markup($content) {
		$regexp = sprintf("{^<pre class='%s'>\r?\n(.*)\r?\n</pre>$}s", WPMC_MARKUP_CLASS);
		return preg_match($regexp, $content) ? true : false;
	}

}
