<?php
class WP_MarkupCollection_PostAdminPage {
	/* Inject */ public $options;
	/* Inject */ public $post_util;
	/* Inject */ public $html_util;
	private $page_type;

	public function create($page_type) {
		$this->page_type = $page_type;
		add_action('do_meta_boxes', array($this, 'do_meta_boxes_action'), 20, 2);
	}

	public function destroy() {
		remove_action('do_meta_boxes', array($this, 'do_meta_boxes_action'), 20, 2);
	}

	public function do_meta_boxes_action($type, $content) {
		$options = call_user_func($this->options);
		$html_util = call_user_func($this->html_util);

		if ($content == 'side') {
			$location = $options->widget_location;
			if (!$location !== 'none') {
				$context_priority = explode(',', $location);
				$context = $context_priority[0];
				$priority = $context_priority[1];

				$post_page = $html_util->template(dirname(__FILE__) . '/post-admin-page.tmpl.php', array($this, 'meta_box'));
				add_meta_box('wp-markup-collection', 'WP-MarkupCollection', $post_page, $type, $context, $priority);
			}
		}
	}

	public function meta_box($post) {
		$options = call_user_func($this->options);
		$post_util = call_user_func($this->post_util);

		if ($this->page_type === 'new') {
			$current_filter = $options->default_filter;
		} else {
			$current_filter = $post_util->filter($post->ID);
		}

		$selected = array('(custom)' => false, '(none)' => true);
		if ($current_filter) {
			$selected['(custom)'] = true;
		}

		$filters = array();
		foreach($options->filters as $filter) {
			$command = $filter['command'];
			$filters[$command] = $filter['name'];
			if ($current_filter === $command) {
				$selected[$command] = true;
				$selected['(custom)'] = false;
			} else {
				$selected[$command] = false;
			}
		}
		$params = array(
			'current_filter' => $current_filter,
			'selected' => $selected,
			'filters' => $filters,
		);
		return $params;
	}
}
