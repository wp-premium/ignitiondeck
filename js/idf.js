jQuery(document).ready(function() {
	if (idf_platform == 'wc') {
		jQuery.each(jQuery('.level-binding'), function(k,v) {
			var url = jQuery(this).attr('href');
			//console.log(url);
			if (url) {
				var urlIndex = url.indexOf('?add-to-cart=');
				// we know that ?add-to-cart= is 13 characters
				//console.log(urlIndex);
				var productID = url.substring(urlIndex + 13);
				//console.log(productID);
				jQuery(this).data('id', productID);
				jQuery('.idc_lightbox select[name="level_select"] option[value="'+ (k + 1) +'"]').data('id', productID);
			}
		});
	}
	else if (idf_platform == 'edd') {
		//var productID = jQuery()
		jQuery.each(jQuery('.level-binding'), function(k,v) {
			var url = jQuery(this).attr('href');
			if (url) {
				//console.log(url);
				var urlIndexa = url.indexOf('&download_id=');
				//console.log(urlIndexa);
				var urlIndexb = url.indexOf('&edd_options');
				//console.log(urlIndexb);
				// we know that &download_id= is 13 characters
				// we know that price_id]= is 10 characters
				var startPoint = urlIndexa + 13;
				var endPoint = urlIndexb - startPoint;
				//console.log(endPoint);
				var productID = url.substring(startPoint, (startPoint + endPoint));
				//console.log(productID);
				jQuery(this).data('id', productID);
				jQuery('.idc_lightbox select[name="level_select"] option').eq(k).data('id', productID);
			}
		});
	}
	else {

	}
	jQuery.each(jQuery('.id-full .btn-container a, .level-binding, .ign-supportnow a'), function() {
		var href = jQuery(this).attr('href');
		//console.log(href);
		if (href !== undefined) {
			if (href.length > 1 && href.indexOf('purchaseform') > 0) {
				// idcf
				jQuery(this).attr('href', '.idc_lightbox');
				jQuery(this).data('href', href);
			}
			else if (href.length > 1 && href.indexOf('add-to-cart') > 0) {
				// wc
				jQuery(this).attr('href', '.idc_lightbox');
				jQuery(this).data('href', href);
			}
			else if (href.length > 1 && href.indexOf('add_to_cart') > 0) {
				// edd
				jQuery(this).attr('href', '.idc_lightbox');
				jQuery(this).data('href', href);
			}
			jQuery(document).trigger('idcfSupportButtonSet', [href]);
		}
	});
	//jQuery('.id-full .btn-container a, .level-binding, .ign-supportnow a').attr('href', '.idc_lightbox');
	jQuery('.id-full .btn-container a, .ign-supportnow a').click(function(e) {
		var href = jQuery(this).data('href');
		if (jQuery(this).attr('href') == '.idc_lightbox') {
			e.preventDefault();
			var parentsCount = jQuery(this).parents().size();
			jQuery(this).parents().each(function(k, v) {
				if (jQuery(this).find('.idc_lightbox').length > 0) {
					var lbSource = jQuery(this).find('.idc_lightbox');
					openLB(lbSource, null);
					return false;
				}
				else {
					if (href !== undefined && k == (parentsCount - 1)) {
						location.href=href;
					}
				}
			});
		}
	});
	/*jQuery('.id-full .btn-container a, .ign-supportnow a').magnificPopup({
		type: 'inline',
		midClick: true,
		callbacks: {
			open: function() {
				var levelCount = jQuery('.idc_lightbox #level_select option').size();
				if (levelCount == 1) {
					jQuery('.idc_lightbox #lb_level_submit').click();
				}
			},
		}
	});*/
	jQuery('.level-binding').click(function(e) {
		e.preventDefault();
		var href = jQuery(this).data('href');
		if (jQuery(this).attr('href') == '.idc_lightbox') {
			var clickLevel = jQuery(this).index();
			jQuery(this).parents().each(function() {
				if (jQuery(this).children('.idc_lightbox').length > 0) {
					var lbSource = jQuery(this).children('.idc_lightbox');
					//console.log(lbSource);
					openLB(lbSource, clickLevel);
					return false;
				}
			});
		}
	});
	jQuery(document).bind('idc_lightbox_price_change', function(event, price) {
		jQuery('.idc_lightbox:visible input[name="total"]').val(price);
		var levelIndex = jQuery('.idc_lightbox:visible select[name="level_select"]').prop('selectedIndex');
		var levelPrice = jQuery('.idc_lightbox:visible select[name="level_select"] option').eq(levelIndex).data('price');
		if (parseFloat(price) < parseFloat(levelPrice)) {
			jQuery('.idc_lightbox:visible input[name="total"]').val(levelPrice);
		}
	});
	jQuery('input[name="lb_level_submit"]').click(function(e) {
		e.preventDefault();
		jQuery('.idc_lightbox:visible input.total').removeClass('error');
		var selLevel = jQuery('.idc_lightbox:visible select[name="level_select"]').val();
		var price = jQuery('.idc_lightbox:visible input.total').val();
		var pwyw = true;
		if (price == undefined) {
			var price = jQuery('.idc_lightbox:visible span.total').data('value');
			var pwyw = false;
		}
		var formAction = jQuery('.idc_lightbox:visible form').attr('action');
		var productID = jQuery('.idc_lightbox:visible select[name="level_select"] option:selected').data('id');
		var error = false;
		if (idf_platform == 'wc') {
			formAction = idf_checkout_url + '?add-to-cart=' + productID;
		}
		else if (idf_platform == 'edd') {
			formAction = idf_checkout_url + '?edd_action=add_to_cart&download_id=' + productID + '&edd_options[price_id]=' + (selLevel - 1);
		}
		else {
			if (idf_platform == 'idc') {
				formAction = formAction.replace('prodid', 'mdid_checkout');
			}
			formAction = formAction + '&level=' + selLevel + '&price=' + price;
		}
		jQuery('.idc_lightbox:visible form').attr('action', formAction);
		if (pwyw) {
			if (price <= 0) {
				var levelIndex = jQuery('.idc_lightbox:visible select[name="level_select"]').prop('selectedIndex');
				var levelPrice = jQuery('.idc_lightbox:visible select[name="level_select"] option').eq(levelIndex).data('price');
				if (levelPrice !== 0) {
					jQuery('.idc_lightbox:visible input.total').addClass('error');
					error = true;
				}
			}
		}
		if (!error) {
			jQuery('.idc_lightbox:visible form').submit();
		}
	});
});

function openLB(lbSource, clickLevel) {
	jQuery.magnificPopup.open({
		type: 'inline',
		items: {
			src: jQuery(lbSource)
		},
		callbacks: {
			open: function() {
				if (clickLevel != null) {
					jQuery(document).trigger('idc_lightbox_level_select', clickLevel);

					// jQuery('.idc_lightbox:visible select[name="level_select"]').prop('selectedIndex', clickLevel);
					// if (jQuery('.idc_lightbox:visible select[name="level_select"]').prop('selectedIndex') == -1) {
					// 	jQuery('.idc_lightbox:visible select[name="level_select"]').prop('selectedIndex', 0);
					// }
				}
				else {
					// clicked a support now button
					jQuery(document).trigger('idc_lightbox_general');
				}

				var levelCount = jQuery('.idc_lightbox:visible select[name="level_select"] option').size();
				if (levelCount == 1) {
					//jQuery('.idc_lightbox:visible .lb_level_submit').click();
					var selLevel = jQuery('.idc_lightbox:visible select[name="level_select"]').val();
					var price = jQuery('.idc_lightbox:visible input.total').val();
					if (price == 0 || price == undefined) {
						//pwyw project
					}
				}
				else if (levelCount == 0) {
					jQuery('.idc_lightbox:visible .lb_level_submit').val('No Levels Available').attr('disabled', 'disabled');
				}
				jQuery('.idc_lightbox:visible input[name="total"]').change(function() {
					var price = jQuery(this).val();
					var cleanPrice = price.replace(/[^0-9\.]+/g, '');
					// console.log('cleanPrice: ', cleanPrice);
					jQuery(document).trigger('idc_lightbox_price_change', cleanPrice);
				});
			},
			close: function() {
				
			}
		}
	});
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

function adjustHeights(elem) {
	var fontstep = 2;
	if (jQuery(elem).height() > jQuery(elem).parent().height() || jQuery(elem).width() > jQuery(elem).parent().width()) {
		jQuery(elem).css('font-size',((jQuery(elem).css('font-size').substr(0,2)-fontstep)) + 'px').css('line-height',((jQuery(elem).css('font-size').substr(0,2))) + 'px');
	}
}