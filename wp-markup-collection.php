<?php
/*
Plugin Name: WP-MarkupCollection
Plugin URI: https://github.com/ko1nksm/wp-markup-collection
Description: This plugin allows you to write posts using Markdown, MediaWiki, reStructuredText, Textile, etc. and user created custom filter.
Author: Koichi Nakashima
Version: 1.1.2
Author URI: http://nksm.name/
Text Domain: wp-markup-collection
Domain Path: /languages/
*/
/*
    Copyright 2014 Koichi Nakashima (email : koichi@nksm.name)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH')) {
	exit;
}

define('WPMC_DOMAIN',       'wp-markup-collection');
define('WPMC_OPTIONS',      'wp_markup_collection_options');
define('WPMC_META',         '_wp_markup_collection_meta');
define('WPMC_META_FILTER',  'wp_markup_collection_meta_filter');
define('WPMC_META_CACHE',   '_wp_markup_collection_meta_cache');
define('WPMC_CONVERT_HTML', 'wp_markup_collection_convert_html');
define('WPMC_MARKUP_CLASS', 'wp-markup-collection');

if (!defined('WPMC_PLUGIN_URL')) {
	$paths = explode('/', dirname(__FILE__));
	$base = array_pop($paths);
	define('WPMC_PLUGIN_URL', plugins_url() . '/' . $base);
}

function wpmc__($text) {
	return __($text, WPMC_DOMAIN);
}

function wpmc_e($text) {
	_e($text, WPMC_DOMAIN);
}

function wpmc_debug($text) {
	if (defined('WPMC_DEBUG')) {
		trigger_error('[WP-MarkupCollection] ' . $text);
	}
}

require_once(dirname(__FILE__) . '/class-wp-markup-collection-class-factory.php');
require_once(dirname(__FILE__) . '/lib/commands/class-wp-markup-collection-command-base.php');
if (defined('WPMC_CUSTOM_FILE')) {
	require_once(WPMC_CUSTOM_FILE);
}

class WP_MarkupCollection {
	private $class_factory;
	public $plugin_header_translate;

	private function __construct() {
		$this->plugin_header_translate = array(
			wpmc__('This plugin allows you to write posts using Markdown, MediaWiki, reStructuredText, Textile, etc. and user created custom filter.'),
		);

		$custom_class_factory_name = defined('WPMC_CUSTOM_CLASS_FACTORY')
			? WPMC_CUSTOM_CLASS_FACTORY
			: 'WP_MarkupCollection_CustomClassFactory';

		$settings = array();
		if(defined('WPMC_EXT_BIN_PATH')) {
			$settings['ext_bin_path'] = WPMC_EXT_BIN_PATH;
		}
		$class_factory = class_exists($custom_class_factory_name)
			? new $custom_class_factory_name($settings)
			: new WP_MarkupCollection_DefaultClassFactory($settings);

		$class_factory->get_instance('hooks')->init();

		if (is_admin()) {
			add_action( 'load-post.php', array($this, 'load_post_action') );
			add_action( 'load-post-new.php', array($this, 'load_post_new_action') );
			$class_factory->get_instance('option_admin_page')->create();
		}

		add_action('plugins_loaded', array($this, 'plugins_loaded_action'));

		$this->class_factory = $class_factory;
	}

	public static function run() {
		new WP_MarkupCollection;
	}

	public function load_post_action() {
		$this->class_factory->get_instance('post_admin_page')->create('edit');
	}

	public function load_post_new_action() {
		$this->class_factory->get_instance('post_admin_page')->create('new');
	}

	public function plugins_loaded_action() {
		$basename = basename(dirname(__FILE__));
		load_plugin_textdomain(WPMC_DOMAIN, false, $basename . '/languages/' );
	}
}

WP_MarkupCollection::run();
