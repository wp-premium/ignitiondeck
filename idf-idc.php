<?php
function is_idc_key_valid($data) {
	$valid = 0;
	if (isset($data['response'])) {
		if ($data['response']) {
			if (isset($data['download'])) {
				if ($data['download'] == '29') {
					$valid = 1;
				}
			}
		}
	}
	return $valid;
}

function idc_license_type($valid) {
	switch ($valid) {
		case 1:
			return 2;
			break;
		
		default:
			return 0;
			break;
	}
}
?>