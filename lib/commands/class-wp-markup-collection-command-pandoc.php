<?php
class WP_MarkupCollection_Command_Pandoc extends WP_MarkupCollection_Command_Base {

	protected $command_options = array(
		'-f:', '-r:',   '--from:', '--read:',
		'-t:', '-w:',   '--to:', '--write:',
		                '--strict',
		'-R',           '--parse-raw',
		'-S',           '--smart',
		                '--old-dashes',
		                '--base-header-level:',
		                '--indented-code-classes:',
		                '--normalize',
		'-p',           '--preserve-tabs',
		                '--tab-stop',
		'-s',           '--standalone',
		'-M:',          '--metadata:',
		'-V:',          '--variable:',
		'-D:',          '--print-default-template:',
		                '--no-wrap',
		                '--columns:',
		                '--toc', '--table-of-contents',
		                '--toc-depth:',
		                '--no-highlight',
		                '--highlight-style:',
		                '--self-contained',
		                '--offline',
		'-5',           '--html5',
		                '--html-q-tags',
		                '--ascii',
		                '--reference-links',
		                '--atx-headers',
		                '--chapters',
		'-N',           '--number-sections',
		                '--number-offset:',
		                '--no-tex-ligatures',
		                '--listings',
		'-i',           '--incremental',
		                '--slide-level:',
		                '--section-divs',
		                '--default-image-extension:',
		                '--email-obfuscation:',
		                '--id-prefix:',
		'-T:',          '--title-prefix:',
		'-c:',          '--css:',
		                '--latex-engine:',
		                '--natbib',
		                '--biblatex',
		                '--gladtex',
		                '--dump-args',
		                '--ignore-args',
		'-v',           '--version',
		'-h',           '--help',
	);

	public function post_process(&$content) {
		$regex = '{
			<pre(?:
				(?:(?:\s+[^>]*)?\s+
				class \s* = \s* (["\'])(?:sourceCode\s+)?(?<LANG>\w*)\g{-2}) ?
				.*?
			)?>
			<code[^>]*>
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
