<?php
function is_idcf_key_valid($data) {
	$valid = 0;
	if (isset($data['response'])) {
		if ($data['response']) {
			if (isset($data['download'])) {
				if ($data['download'] == '30') {
					$valid = 1;
				}
				else if ($validate['download'] == '1') {
					$valid = 1;
				}
			}
		}
	}
	return $valid;
}

function idcf_license_type($data) {
	switch ($data['download']) {
		case '30':
			return 3;
			break;
		case '1':
			return 1;
			break;
		default:
			return 0;
			break;
	}
}
?>