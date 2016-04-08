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

function idf_has_edd() {
	$platform = idf_platform();
	if ($platform == 'edd') {
		return true;
	}
	return false;
}

function idf_platforms() {
	$platforms = array();
	if (!function_exists('is_id_licensed') || !is_id_licensed()) {
		return $platforms;
	}
	if (class_exists('ID_Member')) {
		$platforms[] = 'idc';
	}
	if (class_exists('EDD_API')) {
		$platforms[] = 'edd';
	}
	if (class_exists('WC_Install')) {
		$platforms[] = 'wc';
	}
	return $platforms;
}

function idf_idcf_delivery() {
	$plugins_path = plugin_dir_path(dirname(__FILE__));
	if (!file_exists($plugins_path.'ignitiondeck-crowdfunding')) {
		$prefix = 'http';
		if (is_ssl()) {
			$prefix = 'https';
		}
		if ( ini_get('allow_url_fopen') ) {
			$idcf = file_get_contents($prefix.'://www.ignitiondeck.com/idf/idcf_latest.zip');
		} else {
			$url = $prefix.'://www.ignitiondeck.com/idf/idcf_latest.zip';
			$idcf_curl = curl_init();
			curl_setopt($idcf_curl, CURLOPT_URL, $url);
			curl_setopt($idcf_curl, CURLOPT_HEADER, 0);
			curl_setopt($idcf_curl, CURLOPT_RETURNTRANSFER, 1);
			$idcf = curl_exec($idcf_curl);
			curl_close($idcf_curl);
		}
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
		if ( ini_get('allow_url_fopen') ) {
			$fh = file_get_contents($prefix.'://www.ignitiondeck.com/idf/fh_latest.zip');
		} else {
			$url = $prefix.'://www.ignitiondeck.com/idf/fh_latest.zip';
			$fh_curl = curl_init();
			curl_setopt($fh_curl, CURLOPT_URL, $url);
			curl_setopt($fh_curl, CURLOPT_HEADER, 0);
			curl_setopt($fh_curl, CURLOPT_RETURNTRANSFER, 1);
			$fh = curl_exec($fh_curl);
			curl_close($fh_curl);
		}
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

function idf_handle_video($video) {
	if (empty($video)) {
		return;
	}
	$array = array('iframe', 'embed', 'object');
	foreach ($array as $accepted) {
		if (strpos($video, $accepted)) {
			return html_entity_decode(stripslashes($video));
		}
	}
	return wp_oembed_get($video);
}

/**
 * function for getting client's IP address
 */
function idf_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * Function to get the prefix for using before appended query string variables
 */
function idf_get_querystring_prefix() {
	// Get permalink structure for '?' or '&'
	$prefix = '?';
	$permalink_structure = get_option('permalink_structure');
	if (empty($permalink_structure)) {
		$prefix = '&';
	}
	return $prefix;
}

/**
 * Function to get the layout of image, depending on it's width and size
 */
function idf_image_layout_by_dimensions($width, $height) {
	if ($width > $height) {
		$image = "landscape";
	} else if ($width < $height) {
		$image = "portrait";
	} else {
		$image = "square";
	}
	return $image;
}

function idf_registered() {
	idf_idcf_delivery();
	idf_fh_delivery();
	update_option('idf_registered', 1);
	if (isset($_POST['Email'])) {
		$email = esc_attr($_POST['Email']);
		update_option('id_account', $email);
	}
	exit;
}

add_action('wp_ajax_idf_registered', 'idf_registered');

function idf_reset_account() {
	$options_array = array();
	array_push($options_array, 'idf_registered', 'id_account');
	foreach ($options_array as $k=>$v) {
		delete_option($v);
	}
	exit;
}

add_action('wp_ajax_idf_reset_account', 'idf_reset_account');

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

?>