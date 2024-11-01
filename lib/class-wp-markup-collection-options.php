<?php
class WP_MarkupCollection_Options {
	private $data;
	private $settings;
	private $default_options;

	public function __construct($settings) {
		$this->settings = $settings;
		$this->default_options = array(
			'widget_location' => 'side,high',
			'filters' => implode("\n", array(
				'# <filter name>|<command> [option...]',
				'PHP Markdown|phpmarkdown',
				'PHP Markdown Extra|phpmarkdown --extra',
				'PHP Markdown Lib|phpmarkdownlib',
				'PHP Markdown Lib (Extra)|phpmarkdownlib --extra',
				'PHP Textile|phptextile',
				'PHP reStructuredText|phprestructuredtext',
				'HatenaSyntax|hatenasyntax',
				'Text_Wiki (BBcode)|textwiki -f bbcode',
				'Text_Wiki (coWiki)|textwiki -f cowiki',
				'Text_Wiki (Creole)|textwiki -f creole',
				'Text_Wiki (DokuWiki)|textwiki -f dokuwiki',
				'Text_Wiki (MediaWiki)|textwiki -f mediawiki',
				'Text_Wiki (Tiki)|textwiki -f tiki',
				'multimarkdown|multimarkdown',
				'pandoc (Markdown)|pandoc -f markdown_github --no-highlight',
				'pandoc (MediaWiki)|pandoc -f mediawiki',
				'pandoc (reStructuredText)|pandoc -f rst',
				'pandoc (Textile)|pandoc -f textile',
				'pandoc (LaTeX)|pandoc -f latex',
			)),
			'default_filter' => '',
			'code_block_template' => '',
			'default_lang' => 'text',
			'decode_character_entity_reference' => false,
			'decode_numeric_character_reference' => false,
			'filter_priority' => 0,
			'cache_enabled' => true,
			'phprunner' => 'post',
		);
	}

	public function __get($property) {

		if ($property === 'paths') {
			return $this->paths();
		}

		if ($property === 'filters') {
			return $this->filters();
		}

		$data = $this->data();
		if(array_key_exists($property, $data)) {
			return $data[$property];
		}

		throw new Exception('Property "' . $property . '" is not accessible.');
	}

	public function reload() {
		unset($this->data);
	}

	public function data() {
		if(!isset($this->data)) {
			$data = get_option(WPMC_OPTIONS);
			if (!is_array($data)) {
				$data = array();
			}
			$this->data = array_merge($this->default_options, $data);
		}
		return $this->data;
	}

	public function default_options() {
		return $this->default_options;
	}

	private function paths() {
		$settings = $this->settings;
		$ext_bin_paths = isset($settings['ext_bin_path'])
			? explode(':', $settings['ext_bin_path'])
			: array();
		$plugin_paths = isset($settings['plugin_path'])
			? array($settings['plugin_path'] . '/bin')
			: array();
		$paths = explode(':', getenv('PATH'));
		return array_merge($ext_bin_paths, $plugin_paths, $paths);
	}

	private function filters() {
		$ret = array();
		$data = $this->data();
		foreach (explode("\n", $data['filters']) as $filter) {
			$filter = trim($filter);
			if ($filter === '' || substr($filter, 0, 1) === '#' ) {
				continue;
			}
			$filter_data = explode('|', $filter);
			$ret[] = array(
				'name'    => trim($filter_data[0]),
				'command' => isset($filter_data[1]) ? trim($filter_data[1]) : '',
			);
		}
		return $ret;
	}
}
