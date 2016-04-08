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

	// disconnect account
	jQuery('#idf_reset_account').click(function(e) {
		e.preventDefault();
		var dcButton = jQuery(this);
		jQuery(dcButton).addClass('processing');
		jQuery.ajax({
			url: idf_admin_ajaxurl,
			type: 'POST',
			data: {action: 'idf_reset_account'},
			success: function(res) {
				console.log(res);
				jQuery(document).trigger('idfResetAccount');
				jQuery(dcButton).removeClass('processing');
				location.reload();

			},
			error: function(error) {
				jQuery(dcButton).removeClass('processing');
			}
		});
	});

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

});
function idfRegister(e) {
	resVar = e.data;
	if (idf_version <= '1.2.1') {
		resVar = e.data.response;
	}
	if (e.origin == 'http://ignitiondeck.com') {
		if (resVar !== undefined) {
			if (resVar == 'idf: registered') {
				// they have completed registration
				var email;
				if (e.data.customer !== undefined) {
					console.log(email);
					email = e.data.customer;
				}
				setTimeout(function() {
					jQuery.magnificPopup.close();
					jQuery.ajax({
						url: idf_admin_ajaxurl,
						type: 'POST',
						data: {action: 'idf_registered', Email: email},
						success: function(res) {
							//console.log(res);
							location.reload();
						}
					});
				}, 1500);
			}
		}
	}
}

function openLBGlobal(lbSource, openCallback, closeCallback) {
	jQuery.magnificPopup.open({
		type: 'inline',
		items: {
			src: jQuery(lbSource)
		},
		callbacks: {
			open: function() {
				if (openCallback !== undefined && openCallback !== null)
					openCallback();
			},
			close: function() {
				if (closeCallback !== undefined && closeCallback !== null)
					closeCallback();
			}
		}
	});
}

function closeLBGlobal() {
	jQuery.magnificPopup.close();
}