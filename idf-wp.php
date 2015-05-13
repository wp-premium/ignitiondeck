<?php
/*
This file is for general functions that modify the WordPress defaults
*/

add_action('pre_get_posts', 'idf_restrict_media_view');

function idf_restrict_media_view($query) {
	if ($query->get('post_type') == 'attachment' && !current_user_can('manage_options')) {
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
	}
	if ($pass) {
		idc_add_upload_cap($user);
	}
	else {
		idc_remove_upload_cap($user);
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
		$user->remove_cap('edit_others_pages');
		$user->remove_cap('edit_others_posts');
		$user->remove_cap('edit_pages');
		$user->remove_cap('edit_posts');
		$user->remove_cap('edit_private_pages');
		$user->remove_cap('edit_private_posts');
		$user->remove_cap('edit_published_pages');
		$user->remove_cap('edit_published_posts');
	}
}
?>