<?php

//error_reporting(E_ALL);
//@ini_set('display_errors', 1);

/*
Plugin Name: IgnitionDeck Framework
URI: http://IgnitionDeck.com
Description: An e-commerce toolkit for WordPress
Version: 1.1.8
Author: Virtuous Giant
Author URI: http://VirtuousGiant.com
License: GPL2
*/

define( 'IDF_PATH', plugin_dir_path(__FILE__) );

include_once 'classes/class-idf.php';
include_once 'idf-functions.php';
include_once 'idf-admin.php';
include_once 'idf-roles.php';
include_once 'idf-wp.php';
if (idf_platform() == 'idc') {
	include_once 'idf-idc.php';
}

add_action( 'init', 'idf_textdomain' );
function idf_textdomain() {
	load_plugin_textdomain( 'idf', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
}

add_action('wp_enqueue_scripts', 'idf_lightbox');

function idf_lightbox() {
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
	}
	else {
		wp_enqueue_script('idf-lite');
	}
}
?>