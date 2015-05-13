jQuery(document).ready(function() {
	jQuery('#id_account').magnificPopup({
		type: 'iframe',

		iframe: {
			patterns: {
				ignitiondeck: {
					index: 'ignitiondeck.com',

					//id: 'http://ignitiondeck.com/id/id-free-registration/',

					src: launchpad_link,
				}

				//srcAction: '#id_account'
			}
		}
	});
	jQuery('#id_account').click(function(e) {
		//e.preventDefault();
	});

	window.addEventListener('message', idfRegister, false);

	// Themes Page

	jQuery('.activate_theme').click(function(e) {
		e.preventDefault();
		var slug = jQuery(this).data('theme');
		jQuery.ajax({
			url: idf_admin_ajaxurl,
			type: 'POST',
			data: {action: 'idf_activate_theme', theme: slug},
			success: function(res) {
				if (res == 1) {
					location.reload();
					//location.href= idf_siteurl + '/wp-admin/themes.php?page=theme-settings';
				}
			}
		});
	});
	// Extensions Page
	jQuery('.extension-link .active-installed').click(function(e) {
		e.preventDefault();
		var extension = jQuery(this).data('extension');
		jQuery.ajax({
			url: idf_admin_ajaxurl,
			type: 'POST',
			data: {action: 'idf_activate_extension', extension: extension},
			success: function(res) {
				if (res == 1) {
					location.reload();
				}
			}
		});
	});
	// new / edit post page
	if (idf_platform !== 'legacy') {
		jQuery('input[value="pwyw"]').attr('disabled', 'disabled');
	}
	
	// For iTheme exchange functions
	if (idf_platform == 'itexchange') {
		jQuery('span[addlevel]').click(function(e) {
			//console.log('itexc add level function called');
			// Calling function with delay so that the other click event for span[addlevel] does its work
			var time_delay = setTimeout(function () {
				//console.log('timeout function called');
				var element_number = parseInt(jQuery('div[levels]').attr('levels'));
				jQuery('#ign_level'+ element_number +'title').parent('div').after('<div><label for="iditexch_product_id_'+ element_number +'">Exchange Product ID</label><input type="text" value="" id="iditexch_product_id_'+ element_number +'" name="levels['+ element_number +'][iditexch_product_id]"></div>');
				clearTimeout(time_delay);
			}, 200);
		});
		
		if (jQuery('.iditexch-moveable-fields').length > 0) {
			jQuery('.iditexch-moveable-fields').each(function(index, element) {
				var element_number = parseInt(jQuery(this).attr('level'));
				console.log('each function, element: ', element_number);
				var tomove = jQuery('.iditexch-moveable-fields[level='+ element_number +']');
				jQuery('#ign_level_'+ element_number).parent('div').append(tomove);
			});
		}
	}

});
function idfRegister(e) {
	//console.log(e.data);
	if (e.data == 'idf: registered') {
		// they have completed registration
		setTimeout(function() {
			jQuery.magnificPopup.close();
			jQuery.ajax({
				url: idf_admin_ajaxurl,
				type: 'POST',
				data: {action: 'idf_registered'},
				success: function(res) {
					//console.log(res);
					location.reload();
				}
			});
		}, 1500);
		
	}
}