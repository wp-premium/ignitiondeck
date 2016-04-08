<?php
add_action('admin_menu', 'idf_admin_menus');

function idf_admin_menus() {
	if (current_user_can('manage_options')) {
		$home = add_menu_page(__('IgnitionDeck', 'idf'), __('IgnitionDeck', 'idf'), 'manage_options', 'idf', 'idf_main_menu', plugins_url( '/images/ignitiondeck-menu.png', __FILE__ ));
		$theme_list = add_submenu_page( 'idf', __('Themes', 'ignitiondeck'), __('Themes', 'ignitiondeck'), 'manage_options', 'idf-themes', 'idf_theme_list');
		$extension_list = add_submenu_page( 'idf', __('Extensions', 'ignitiondeck'), __('Extensions', 'ignitiondeck'), 'manage_options', 'idf-extensions', 'idf_extension_list');
		$menu_array = array($home,
						$theme_list,
						$extension_list
						);
		foreach ($menu_array as $menu) {
			add_action('admin_print_styles-'.$menu, 'idf_admin_enqueues');
		}
	}
}

function idf_main_menu() {
	$idf_registered = get_option('idf_registered');
	$platform = idf_platform();
	$plugins_path = plugin_dir_path(dirname(__FILE__));
	$platforms = idf_platforms();
	if (isset($_POST['commerce_submit'])) {
		$platform = sanitize_text_field($_POST['commerce_selection']);
		update_option('idf_commerce_platform', $platform);
	}
	if (isset($_POST['update_idcf'])) {
		if (file_exists($plugins_path.'ignitiondeck-crowdfunding')) {
			deactivate_plugins($plugins_path.'ignitiondeck-crowdfunding/ignitiondeck.php');
			$dir = $plugins_path.'ignitiondeck-crowdfunding';
			rrmdir($dir);
		}
		idf_idcf_delivery();
		echo '<script>location.href="'.site_url('/wp-admin/admin.php?page=idf').'";</script>';
	}
	include_once 'templates/admin/_idfMenu.php';
}

function idf_extension_list() {
	$plugins = get_plugins();
	/*$plugin_array = array();
	if (!empty($plugins)) {
		foreach ($plugins as $plugin) {
			$plugin_array[] = $plugin['basename'];
		}
	}*/
	$prefix = 'http';
	if (is_ssl()) {
		$prefix = 'https';
	}
	$api = $prefix.'://www.ignitiondeck.com/id/?action=get_extensions';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $api);

	$json = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($json);
	include_once 'templates/admin/_extensionList.php';
}

function idf_theme_list() {
	$themes = wp_get_themes();
	$name_array = array();
	if (!empty($themes)) {
		foreach ($themes as $theme) {
			$name_array[] = $theme->Name;
		}
	}
	$active_theme = wp_get_theme();
	$active_name = $active_theme->Name;
	$prefix = 'http';
	if (is_ssl()) {
		$prefix = 'https';
	}
	$api = $prefix.'://www.ignitiondeck.com/id/?action=get_themes';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $api);

	$json = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($json);
	include_once 'templates/admin/_themeList.php';
}

add_action('admin_enqueue_scripts', 'idf_additional_enqueues');

function idf_additional_enqueues() {
	global $post;
	if (isset($post->post_type) && $post->post_type == 'ignition_product') {
		$platform = idf_platform();
		if (empty($platform) || $platform !== 'legacy') {
			idf_admin_enqueues();
		}
	}
}

function idf_admin_enqueues() {
	if (function_exists('get_plugin_data')) {
		$idf_data = get_plugin_data(__FILE__);
	}
	wp_register_script('idf-admin', plugins_url('/js/idf-admin.js', __FILE__));
	wp_register_script('idf-admin-media', plugins_url('/js/idf-admin-media.js', __FILE__));
	wp_register_style('idf-admin', plugins_url('/css/idf-admin.css', __FILE__));
	wp_register_style('magnific', plugins_url('lib/magnific/magnific.css', __FILE__));
	wp_register_script('magnific', plugins_url('lib/magnific/magnific.js', __FILE__));
	wp_enqueue_script('jquery');
	wp_enqueue_media();
	wp_enqueue_script('magnific');
	wp_enqueue_script('idf-admin');
	wp_enqueue_script('idf-admin-media');
	wp_enqueue_style('magnific');
	wp_enqueue_style('idf-admin');
	$idf_ajaxurl = site_url('/wp-admin/admin-ajax.php');
	$platform = idf_platform();
	wp_localize_script('idf-admin', 'idf_admin_siteurl', site_url());
	wp_localize_script('idf-admin', 'idf_admin_ajaxurl', $idf_ajaxurl);
	wp_localize_script('idf-admin', 'idf_platform', $platform);
	$prefix = 'http';
	if (is_ssl()) {
		$prefix = 'https';
	}
	wp_localize_script('idf-admin', 'launchpad_link', $prefix.'://ignitiondeck.com/id/id-launchpad-checkout/');
	wp_localize_script('idf-admin', 'idf_version', (isset($idf_data['Version']) ? $idf_data['Version'] : '0.0.0'));
}

add_action('admin_init', 'filter_idcf_admin');

function filter_idcf_admin() {
	$platform = idf_platform();
	if (!empty($platform) && $platform !== 'legacy') {
		//remove_submenu_page('ignitiondeck', 'project-settings');
		remove_submenu_page('ignitiondeck', 'payment-options');
		remove_submenu_page('ignitiondeck', 'custom-settings');
		//add_filter('idcf_project_settings_tab', 'filter_idcf_project_settings_tab');
		add_filter('idcf_custom_settings_tab', 'filter_idcf_custom_settings_tab');
		add_filter('idcf_payment_settings_tab', 'filter_idcf_payment_settings_tab');
		remove_action('add_meta_boxes', 'add_ty_url');
	}
	if ($platform == 'wc' || $platform == 'edd') {
		remove_action('add_meta_boxes', 'add_purchase_url');
	}
}

add_action('plugins_loaded', 'filter_idc_admin');

function filter_idc_admin() {
	$platform = idf_platform();
	if ($platform !== 'idc') {
		remove_action('add_meta_boxes', 'mdid_project_metaboxes');
	}
}

function filter_idcf_project_settings_tab($tab) {
	//$tabs = null;
	return null;
}

function filter_idcf_custom_settings_tab($tab) {
	//$tabs = null;
	return null;
}

function filter_idcf_payment_settings_tab($tab) {
	//$tabs = null;
	return null;
}
?>