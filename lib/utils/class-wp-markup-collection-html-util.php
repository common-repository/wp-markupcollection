<?php
class WP_MarkupCollection_HtmlUtil {

	public function attrs($name, $data) {
		return new WP_MarkupCollection_HtmlAttrsUtil($name, $data);
	}

	public function template($template, $func) {
		$tmpl = new WP_MarkupCollection_HtmlTemplateUtil($template, $func);
		return array($tmpl, 'process');
	}

}

class WP_MarkupCollection_HtmlAttrsUtil {
	private $name;
	private $data;

	public function __construct($name, $data) {
		$this->name = $name;
		$this->data = $data;
	}

	public function name($key) {
		echo $this->name . '[' . $key . ']';
	}

	public function value($key) {
		echo esc_attr($this->data[$key]);
	}

	public function checked($key) {
		checked($this->data[$key]);
	}

	public function selected($key, $current = true) {
		selected($this->data[$key], $current);
	}
}

class WP_MarkupCollection_HtmlTemplateUtil {
	private $template;
	private $func;

	public function __construct($template, $func) {
		$this->template = $template;
		$this->func = $func;
	}

	public function process() {
		$args = func_get_args();
		$params = call_user_func_array($this->func, $args);
		extract($params, EXTR_SKIP);
		include($this->template);
	}
}
