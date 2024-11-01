<?php
class WP_MarkupCollection_Command_MultiMarkdown extends WP_MarkupCollection_Command_Base {

	protected $command_options = array(
		'-h',   '--help',
		'-v',   '--version',
		'-t:',  '--to:',
		'-c',   '--compatibility',
		'-f',   '--full',
		'-s',   '--snippet',
		        '--process-html',
		'-m',   '--metadata-keys',
		        '--random',
		'-a',   '--accept',
		'-r',   '--reject',
		        '--smart', '--nosmart',
		        '--notes', '--nonotes',
		        '--labels', '--nolabels',
		        '--mask', '--nomask',
	);

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
