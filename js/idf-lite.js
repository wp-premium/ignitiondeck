function adjustHeights(elem) {
	var fontstep = 2;
	if (jQuery(elem).height() > jQuery(elem).parent().height() || jQuery(elem).width() > jQuery(elem).parent().width()) {
		jQuery(elem).css('font-size',((jQuery(elem).css('font-size').substr(0,2)-fontstep)) + 'px').css('line-height',((jQuery(elem).css('font-size').substr(0,2))) + 'px');
	}
}