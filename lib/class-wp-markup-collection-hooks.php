<?php
class WP_MarkupCollection_Hooks {
	/* Inject */ public $options;
	/* Inject */ public $post_util;
	/* Inject */ public $command_factory;

	public function init() {
		$options = call_user_func($this->options);

		remove_filter('the_content', 'wptexturize');
		remove_filter('the_content', 'wpautop');
		add_filter('the_posts', array($this, 'the_posts_filter'), $options->filter_priority, 1);
		add_filter('wp_insert_post_data', array($this, 'wp_insert_post_data_filter'), 10, 2);
		add_filter('edit_post_content',   array($this, 'edit_post_content_filter'), 10, 2);

		add_action('init', array($this, 'init_action'));
	}

	public function init_action() {
		global $pagenow;
		$post_util = call_user_func($this->post_util);

		if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === "POST") {

			if (in_array($pagenow, array('post.php', 'post-new.php'))) {
				if (isset($_POST[WPMC_META_FILTER]) && $_POST[WPMC_META_FILTER]) {
					$_POST['content'] = $post_util->wrap_content($_POST['content'], false);
				}
			}
		}
	}

	public function wp_insert_post_data_filter($data, $postarr) {
		$post_util = call_user_func($this->post_util);

		if (!in_array($postarr['post_type'], array('post', 'page'))) {
			return $data;
		}

		if (!isset($postarr[WPMC_META_FILTER])) {
			return $data;
		}

		$id = $postarr['ID'];

		$filter = stripslashes($postarr[WPMC_META_FILTER]);
		if ($filter === '(custom)') {
			$filter = $post_util->filter($id);
		}
		if (!$filter) {
			$post_util->filter($id, null);
			return $data;
		}

		$raw_content = $post_util->unwrap_content($data['post_content']);

		try {
			$content = $this->exec_command($id, $filter, $raw_content);

			if (isset($postarr[WPMC_CONVERT_HTML])) {
				$data['post_content'] = $content;
				$post_util->filter($id, null);
				$post_util->cache($id, null);
			} else {
				$data['post_content'] = $post_util->wrap_content($raw_content);
				$post_util->filter($id, $filter);
			}
		} catch(Exception $e) {
			wp_die($e->getMessage());
		}
		return $data;
	}

	public function edit_post_content_filter($content) {
		$post_util = call_user_func($this->post_util);

		if ($post_util->is_markup($content)) {
			$content = $post_util->unwrap_content($content, false);
		}
		return $content;
	}

	public function the_posts_filter($posts) {
		$options = call_user_func($this->options);

		remove_filter( 'the_posts', array($this, 'the_posts_filter'), $options->filter_priority, 1);

		wpmc_debug('start the_posts filter');
		$start_time = microtime(true);
		foreach($posts as $post) {
			$this->apply_filter($post);
		}
		$end_time = microtime(true);
		wpmc_debug('end the_posts filter: ' . round(($end_time - $start_time)*1000, 3) . 'ms');

		return $posts;
	}

	private function apply_filter($post) {
		$options = call_user_func($this->options);
		$post_util = call_user_func($this->post_util);

		$content = $post->post_content;

		if (!in_array($post->post_type, array('post', 'page', 'revision'))) {
			return;
		}

		if (!$post_util->is_markup($content)) {
			return;
		}

		if ($post->post_type === 'revision') {
			$filter = $post_util->filter($post->post_parent);
			$use_cache = false;
		} else {
			$filter = $post_util->filter($post->ID);
			$use_cache = $options->cache_enabled;
		}
		if (!$filter) {
			return;
		}

		wpmc_debug('apply filter: post_id=' . $post->ID);

		$raw_content = $post_util->unwrap_content($content, false);

		try {
			$content = $this->exec_command($post->ID, $filter, $raw_content, $use_cache);
		} catch(Exception $e) {
			$content = '<pre>' . esc_html($raw_content) . '</pre>';
		}
		$post->post_content = $content;
	}

	private function exec_command($id, $filter, $source, $use_cache = false) {
		$post_util = call_user_func($this->post_util);
		$command_factory = call_user_func($this->command_factory);

		$meta = $post_util->meta($id);

		$command = $command_factory->create($filter, $meta);
		$command->pre_process($source);

		$content = null;
		$hash = md5($filter . $source);
		if ($use_cache) {
			$content = $post_util->get_cache($id, $hash);
		}

		if(is_null($content) || $content === '') {
			wpmc_debug('exec: ' . $filter);
			$content = $command->execute($source);
		} else {
			wpmc_debug('cache found');
		}

		if ($use_cache) {
			wpmc_debug('store cache: ' . $id);
			$post_util->set_cache($id, $hash, $content);
		}

		$command->post_process($content);

		return $content;
	}

}
