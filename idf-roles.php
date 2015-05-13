<?php
add_action('init', 'idf_set_roles');

function idf_set_roles() {
	global $crowdfunding;
	global $current_user;
	if (is_multisite()) {
		require (ABSPATH . WPINC . '/pluggable.php');
	}
	get_currentuserinfo();
	$user_id = $current_user->ID;
	$user = get_user_by('id', $user_id);
	// setup general roles for product suite
	if (current_user_can('administrator') && !current_user_can('create_edit_projects')) {
		$admin = get_role('administrator');
		$cap_array = array(
			'create_edit_projects',
		);
		foreach ($cap_array as $cap) {
			$admin->add_cap($cap);
		}
	}

	$creator = false;
	if ($crowdfunding) {
		// we know that IDC is set to commerce platform and IDCF is installed
		if (is_id_pro()) {
			$cperms = md_ide_creator_permissions();
			if ($cperms) {
				if (md_ide_opt_in_required()) {
					$enable_creator = get_user_meta($user_id, 'enable_creator', true);
					$user_projects = get_user_meta($user_id, 'ide_user_projects', true);
					if ($enable_creator || !empty($user_projects)) {
						$creator = true;
					}
				}
				else {
					$creator = true;
				}
			}
		}
	}
	if ($creator) {
		if (!current_user_can('create_edit_projects')) {
			if ($user_id > 0) {
				if (!empty($user)) {
					$user->add_cap('create_edit_projects');
				}
			}
		}
	}
}
?>