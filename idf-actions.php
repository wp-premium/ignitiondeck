<?php
add_action('idf_key_transfer', 'idf_key_transfer');

function idf_key_transfer() {
	update_option('idf_key_transfer', 1);
}
?>