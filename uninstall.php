<?php
if(defined('WP_UNINSTALL_PLUGIN') || isset($wpmc_uninstall_plugin_test)) {
	delete_option('wp_markup_collection_options');
}
