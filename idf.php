<?php

//error_reporting(E_ALL);
//@ini_set('display_errors', 1);

/*
Plugin Name: IgnitionDeck Framework
URI: http://IgnitionDeck.com
Description: A crowdfunding and ecommerce for WordPress that helps you crowdfund, pre-order, and sell goods online.
Version: 1.2.1
Author: Virtuous Giant
Author URI: http://VirtuousGiant.com
License: GPL2
*/

define( 'IDF_PATH', plugin_dir_path(__FILE__) );

include_once 'idf-globals.php';
global $active_plugins;
include_once 'idf-update.php';
include_once 'classes/class-idf.php';
include_once 'idf-functions.php';
include_once 'idf-admin.php';
include_once 'idf-roles.php';
include_once 'idf-wp.php';
include_once 'idf-actions.php';
if (idf_platform() == 'idc') {
	include_once 'idf-idc.php';
}
if (in_array('ignitiondeck-crowdfunding/ignitiondeck.php', $active_plugins)) {
	include_once 'idf-idcf.php';
}

register_activation_hook(__FILE__, 'idf_activation');

function idf_activation() {
	$key_transfer = get_option('idf_key_transfer');
	if (!$key_transfer) {
		$key_data = array(
			'keys' => array(
				'idcf_key' => '',
				'idc_key' => '',
			),
			'types' => array(
				'idcf_type' => 0,
				'idc_type' => 0,
			),
		);
		// Key transfer for IDCF
		$idcf_key = get_option('id_license_key');
		if (function_exists('id_validate_license')) {
			$idcf_response = id_validate_license($idcf_key);
			$idcf_valid = is_idcf_key_valid($idcf_response);
			if ($idcf_valid) {
				$key_data['types']['idcf_type'] = idcf_license_type($idcf_response);
				$key_data['keys']['idcf_key'] = $idcf_key;
			}
		}
		// Key transfer for IDC
		$idc_gen = get_option('md_receipt_settings');
		if (!empty($idc_gen)) {
			$general = maybe_unserialize($idc_gen);
			$idc_key = (isset($idc_gen['license_key']) ? $idc_gen['license_key'] : '');
			if (function_exists('idc_validate_key')) {
				$idc_response = idc_validate_key($idc_key);
				$idc_valid = is_idc_key_valid($idc_response);
				if ($idc_valid) {
					$key_data['types']['idc_type'] = idc_license_type();
					$key_data['keys']['idc_key'] = $idc_key;
				}
			}
		}
		$license_type = idf_parse_license($key_data);
		if ($license_type) {
			do_action('idf_key_transfer');
		}
	}
}

add_action( 'init', 'idf_textdomain' );
function idf_textdomain() {
	load_plugin_textdomain( 'idf', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
}

add_action('wp_enqueue_scripts', 'idf_lightbox');
add_action('login_enqueue_scripts', 'idf_lightbox');

function idf_lightbox() {
	if (function_exists('get_plugin_data')) {
		$idf_data = get_plugin_data(__FILE__);
	}
	wp_register_script('idf-lite', plugins_url('js/idf-lite.js', __FILE__));
	wp_register_script('idf', plugins_url('js/idf.js', __FILE__));
	wp_register_style('magnific', plugins_url('lib/magnific/magnific.css', __FILE__));
	wp_register_script('magnific', plugins_url('lib/magnific/magnific.js', __FILE__));
	wp_register_style('idf', plugins_url('css/idf.css', __FILE__));
	wp_enqueue_script('jquery');
	if (idf_enable_checkout()) {
		$checkout_url = '';
		$platform = idf_platform();
		if ($platform == 'wc' && !is_admin()) {
			if (class_exists('WooCommerce')) {
				global $woocommerce;
				$checkout_url = $woocommerce->cart->get_checkout_url();
			}
		}
		else if ($platform == 'edd' && class_exists('Easy_Digital_Downloads') && !is_admin()) {
			$checkout_url = edd_get_checkout_uri();
		}
		wp_enqueue_style('magnific');
		wp_enqueue_style('idf');
		wp_enqueue_script('idf');
		wp_enqueue_script('magnific');
		wp_localize_script('idf', 'idf_platform', $platform);
		// Let's set the ajax url
		$idf_ajaxurl = site_url('/wp-admin/admin-ajax.php');
		wp_localize_script('idf', 'idf_siteurl', site_url());
		wp_localize_script('idf', 'idf_ajaxurl', $idf_ajaxurl);
		if (isset($checkout_url)) {
			wp_localize_script('idf', 'idf_checkout_url', $checkout_url);
		}
		wp_localize_script('idf', 'idf_version', (!empty($idf_data) ? $idf_data['Version'] : '0.0.0'));
	}
	else {
		wp_enqueue_script('idf-lite');
		wp_localize_script('idf-lite', 'idf_version', (isset($idf_data['Version']) ? $idf_data['Version'] : '0.0.0'));
	}
}

function idf_font_awesome() {
	wp_register_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
	wp_enqueue_style('font-awesome');
}
add_action('wp_enqueue_scripts', 'idf_font_awesome');
add_action('admin_enqueue_scripts', 'idf_font_awesome');
?>