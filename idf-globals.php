<?php
function idf_active_plugins() {
	if (is_multisite()) {
	$active_plugins = get_site_option( 'active_sitewide_plugins');
	}
	else {
		$active_plugins = get_option('active_plugins');
	}
	return $active_plugins;
}

global $active_plugins;
$active_plugins = idf_active_plugins();
?>