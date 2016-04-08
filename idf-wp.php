<?php
/*
This file is for general functions that modify the WordPress defaults
*/

add_action('pre_get_posts', 'idf_restrict_media_view');

function idf_restrict_media_view($query) {
	if ($query->get('post_type') == 'attachment' && !current_user_can('manage_options') && is_admin()) {
		if (!current_user_can('editor')) {
			if (is_multisite()) {
				require (ABSPATH . WPINC . '/pluggable.php');
			}
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			if ($user_id > 0) {
				$query->set('author', $user_id);
			}
		}
	}
}

function idf_add_creator_upload_cap() {
	$pass = false;
	global $pagenow;

	if (is_user_logged_in()) {

		// If this is the Upload page
		if ($pagenow == 'async-upload.php' || $pagenow == 'admin-ajax.php') {
			
			// If we have a referer page
			if (isset($_SERVER['HTTP_REFERER'])) {
				// Getting query string, and then it's variables
				$query_string = explode("?", $_SERVER['HTTP_REFERER']);
				$query_vars = array();
				// If we have no exploded array then there is no query string coming, so just return out of the function
				if (!isset($query_string[1])) {
					return;
				}
				parse_str($query_string[1], $query_vars);
		
				// If edit_project or create_project page we are on, then move forward
				if (isset($query_vars['edit_project']) || array_key_exists('edit_project', $query_vars) || isset($query_vars['create_project']) || array_key_exists('create_project', $query_vars))
				{
	
					if (is_multisite()) {
						require (ABSPATH . WPINC . '/pluggable.php');
					}
					global $current_user;
					get_currentuserinfo();
					$user_id = $current_user->ID;
					$user = get_user_by('id', $user_id);
					
					$add_cap = false;
					$dash_settings = get_option('md_dash_settings');
					if (!empty($dash_settings)) {
						$dash_settings = maybe_unserialize($dash_settings);
						$dash_id = $dash_settings['durl'];
					}
					if (isset($dash_id) && current_user_can('create_edit_projects')) {
						if (!current_user_can('upload_files')) {
							if (!empty($user)) {
								$user->add_cap('upload_files');
							}
						}
						if (isset($query_vars['create_project']) && $query_vars['create_project']) {
							$pass = true;
						}
						else if (isset($query_vars['edit_project'])) {
							$post_id = absint($query_vars['edit_project']);
							$post = get_post($post_id);
							if (!empty($post->ID) && $post->post_author == $user_id) {
								$pass = true;
							}
						}
					}
					if ($pass) {
						idc_add_upload_cap($user);
					}
					else {
						idc_remove_upload_cap($user);
					}
				
				}
			}
		}
	}
}
add_action('init', 'idf_add_creator_upload_cap', 10);

add_action('wp', 'idf_add_media_buttons');

function idf_add_media_buttons() {
	$pass = false;
	if (is_user_logged_in()) {
		if (is_multisite()) {
			require (ABSPATH . WPINC . '/pluggable.php');
		}
		global $current_user;
		$add_cap = false;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		$user = get_user_by('id', $user_id);
		$dash_settings = get_option('md_dash_settings');
		if (!empty($dash_settings)) {
			$dash_settings = maybe_unserialize($dash_settings);
			$dash_id = $dash_settings['durl'];
		}
		if (isset($dash_id) && is_page($dash_id) && current_user_can('create_edit_projects')) {
			if (!current_user_can('upload_files')) {
				if (!empty($user)) {
					$user->add_cap('upload_files');
				}
			}
			if (isset($_GET['create_project']) && $_GET['create_project']) {
				if (!current_user_can('publish_posts')) {
					$pass = true;
				}
			}
			else if (isset($_GET['edit_project'])) {
				$post_id = absint($_GET['edit_project']);
				$post = get_post($post_id);
				if (!empty($post->ID) && $post->post_author == $user_id) {
					if (!current_user_can('publish_posts')) {
						$pass = true;
					}
				}
			}
		}
		if ($pass) {
			idc_add_upload_cap($user);
		}
		else {
			idc_remove_upload_cap($user);
		}
	}
}

function idc_add_upload_cap($user) {
	if (!empty($user)) {
		$user->add_cap('edit_others_pages');
		$user->add_cap('edit_others_posts');
		$user->add_cap('edit_pages');
		$user->add_cap('edit_posts');
		$user->add_cap('edit_private_pages');
		$user->add_cap('edit_private_posts');
		$user->add_cap('edit_published_pages');
		$user->add_cap('edit_published_posts');
	}
}

function idc_remove_upload_cap($user) {
	if (!empty($user)) {
		// Getting the user's role
		$user_roles = $user->roles;
		$role_caps = array();
		foreach ($user_roles as $user_role) {
			$role_details = get_role($user_role);
			$role_caps = array_merge($role_caps, $role_details->capabilities);
		}

		// Making an array of keys, to get only caps in a single array
		$role_caps = array_keys($role_caps);
		
		// ensure we don't remove caps from users with roles that enable them
		if (!in_array('edit_others_pages', $role_caps)) {
			$user->remove_cap('edit_others_pages');
		}
		if (!in_array('edit_others_posts', $role_caps)) {
			$user->remove_cap('edit_others_posts');
		}
		if (!in_array('edit_pages', $role_caps)) {
			$user->remove_cap('edit_pages');
		}
		if (!in_array('edit_posts', $role_caps)) {
			$user->remove_cap('edit_posts');
		}
		if (!in_array('edit_private_pages', $role_caps)) {
			$user->remove_cap('edit_private_pages');
		}
		if (!in_array('edit_private_posts', $role_caps)) {
			$user->remove_cap('edit_private_posts');
		}
		if (!in_array('edit_published_pages', $role_caps)) {
			$user->remove_cap('edit_published_pages');
		}
		if (!in_array('edit_published_posts', $role_caps)) {
			$user->remove_cap('edit_published_posts');
		}
	}
}
?>