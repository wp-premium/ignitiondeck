<?php
function idf_parse_license($key_data) {
	$scale = max($key_data['types']);
	switch ($scale) {
		case '1':
			return update_option('idf_key', $key_data['keys']['idcf_key']);
			break;
		case '2':
			return update_option('idf_key', $key_data['keys']['idc_key']);
			break;
		case '3':
			return update_option('idf_key', $key_data['keys']['idcf_key']);
		default:
			return update_option('idf_key', '');
			break;
	}
}
?>