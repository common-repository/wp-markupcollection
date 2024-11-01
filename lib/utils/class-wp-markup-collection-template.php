<?php
class WP_MarkupCollection_Template {
	public function process($tmpl, $params) {
		$replace = array();
		foreach($params as $key => $value) {
			$replace['$' . $key] = $value;
		}
		return strtr($tmpl, $replace);
	}
}
