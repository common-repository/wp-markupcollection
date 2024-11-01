<?php
class WP_MarkupCollection_Command_PhpMarkdown extends WP_MarkupCollection_Command_PHPRunner {

	protected $command_options = array('--extra');

	public static function getInfo($name) {
		$info = parent::getInfo($name);
		$require = $name === 'phpmarkdownlib' ? 'PHP Version >= 5.3' : null;
		return array_merge($info, array(
			'require' => $require,
		));
	}

	public function post_process(&$content) {
		$regex = '{
			<pre>
			<code
				(?:(?:\s+[^>]*)?\s+
				class \s* = \s* (["\'])(?<LANG>\w*)\g{-2}) ?
				.*?
			>
			(?<CODE>.*?)
			</code>
			</pre>
		}sx';
		$content = preg_replace_callback($regex, array($this, 'callback'), $content);
		parent::post_process($content);
	}

	private function callback($matches) {
		$options = call_user_func($this->options);
		$template_engine = call_user_func($this->template_engine);

		$tmpl = $options->code_block_template;
		if ($tmpl === "") {
			return $matches[0];
		}

		if (!isset($matches['LANG']) || $matches['LANG'] === '') {
			$matches['LANG'] = $options->default_lang;
		}
		return $template_engine->process($tmpl, $matches);
	}
}
