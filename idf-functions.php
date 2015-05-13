<?php
function idf_platform() {
	$platform = get_option('idf_commerce_platform', 'legacy');
	return $platform;
}

function idf_enable_checkout() {
	if (class_exists('ID_Project') && is_id_licensed()) {
		return true;
	}
	return false;
}

function idf_idcf_delivery() {
	$plugins_path = plugin_dir_path(dirname(__FILE__));
	if (!file_exists($plugins_path.'ignitiondeck-crowdfunding')) {
		$prefix = 'http';
		if (is_ssl()) {
			$prefix = 'https';
		}
		$idcf = file_get_contents($prefix.'://ignitiondeck.com/idf/idcf_latest.zip');
		if (!empty($idcf)) {
			$put_idcf = file_put_contents($plugins_path.'idcf_latest.zip', $idcf);
			$idcf_zip = new ZipArchive;
			$idcf_zip_res = $idcf_zip->open($plugins_path.'idcf_latest.zip');
			if ($idcf_zip_res) {
				$idcf_zip->extractTo($plugins_path);
				$idcf_zip->close();
				unlink($plugins_path.'idcf_latest.zip');
			}
		}
	}
	activate_plugin($plugins_path.'ignitiondeck-crowdfunding/ignitiondeck.php');
}

function idf_fh_delivery() {
	$themes_path = plugin_dir_path(dirname(dirname(__FILE__))).'themes/';
	if (!file_exists($themes_path.'fivehundred')) {
		$prefix = 'http';
		if (is_ssl()) {
			$prefix = 'https';
		}
		$fh = file_get_contents($prefix.'://ignitiondeck.com/idf/fh_latest.zip');
		if (!empty($fh)) {
			$put_fh = file_put_contents($themes_path.'fh_latest.zip', $fh);
			$fh_zip = new ZipArchive;
			$fh_zip_res = $fh_zip->open($themes_path.'fh_latest.zip');
			if ($fh_zip_res) {
				$fh_zip->extractTo($themes_path);
				$fh_zip->close();
				unlink($themes_path.'fh_latest.zip');
			}
		}
	}
}

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
		         if (filetype($dir."/".$object) == "dir") {
		         	rrmdir($dir."/".$object);
		         }
		         else {
		         	unlink($dir."/".$object);
		         }
		    }
		}
		reset($objects); 
		rmdir($dir); 
	}
}

function idf_pw_gen($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function idf_sharing_settings() {
	if (class_exists('ID_Project')) {
		$settings = ID_Project::get_id_settings();
	}
	return (!empty($settings) ? $settings : null);
}

/**
 * Function to validate URL and return a proper URL if it's just a domain name
 * @url_string		The string passed as url to be formatted properly
 * @http_secure		If want the return in https:// format
 */
function id_validate_url($url_string, $http_secure = false) {
	// Using PHP 5+ version filter_var function if it exists
	if (function_exists('filter_var')) {
		$res = filter_var ($url_string, FILTER_VALIDATE_URL);
		// If it's a valid url, return it
		if ($res) {
			if ($http_secure) {
				return preg_replace('/https?/', 'https', $res);
			} else {
				return $res;
			}
		}
		else {
		    $match_res = preg_match ( '/((?:[\w]+\.)+)([a-zA-Z]{2,4})/' , $url_string );
	        // If we have a domain name coming, append http with it
	        if ($match_res === 1) {
				// There are chances that there is a "//" already in the start of the $url_string, taking that into account
				$protocol = (($http_secure) ? 'https' : 'http');
				if (substr($url_string, 0, 2) == "//") {
					return $protocol.":".$url_string;
				} else {
					return $protocol."://".$url_string;
				}
	        }
	        // Not match as URL and domain, return false
	        else {
	            return false;
	        }
		}
	}
	// If filter_var doesn't exists or it maybe just a domain name, then use regex
	else {
	    $match_res = preg_match ( '/((?:[\w]+\.)+)([a-zA-Z]{2,4})/' , $url_string );
	    // echo "match_res: ".$match_res."<br>";
	    // If we have a domain name coming, then check if it has http or doesn't have it
        if ($match_res === 1) {
            $match_http_str = preg_match ( '/https?:\/\//', $url_string );
            if ($match_http_str === 1) {
                // It has http/https in it, so simply return it, but checking argument if https is to be returned
                if ($http_secure) {
					return preg_replace('/https?/', 'https', $url_string);
				} else {
					return $url_string;
				}
            }
            else {
                // Doesn't have http/https in the URL, so append http
				$protocol = (($http_secure) ? 'https' : 'http');

                // There are chances that there is a "//" already in the start of the $url_string, taking that into account
				if (substr($url_string, 0, 2) == "//") {
					return $protocol.":".$url_string;
				} else {
					return $protocol."://".$url_string;
				}
            }
        }
        // Not match as URL and domain, return false
        else {
            return false;
        }
	}
}

function idf_registered() {
	idf_idcf_delivery();
	idf_fh_delivery();
	update_option('idf_registered', 1);
	exit;
}

add_action('wp_ajax_idf_registered', 'idf_registered');
add_action('wp_ajax_nopriv_idf_registered', 'idf_registered');

function idf_activate_theme() {
	if (isset($_POST['theme']) && current_user_can('manage_options')) {
		$slug = esc_attr($_POST['theme']);
		$slug = str_replace('500', 'fivehundred', $slug);
		switch_theme($slug);
		echo 1;
	}
	exit;
}

add_action('wp_ajax_idf_activate_theme', 'idf_activate_theme');

function idf_activate_extension() {
	if (isset($_POST['extension']) && current_user_can('manage_options')) {
		$extension = $_POST['extension'];
		if (!empty($extension)) {
			$plugin_path = dirname(IDF_PATH).'/'.$extension.'/'.$extension.'.php';
			activate_plugin($plugin_path);
			echo 1;
		}
	}
	exit;
}

add_action('wp_ajax_idf_activate_extension', 'idf_activate_extension');

/**
 * AJAX function, to set the iT Exchange product in the cart
 */
function iditexch_add_product_to_cart() {
	$product_id = $_POST['product_id'];
	// Adds the product to the cart. second arg is optional and designates the quantity.
	it_exchange_add_product_to_shopping_cart( $product_id, 1 );
}
add_action('wp_ajax_iditexch_add_product_to_cart', 'iditexch_add_product_to_cart');
?>