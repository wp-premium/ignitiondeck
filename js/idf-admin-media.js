jQuery(document).ready(function() {
	jQuery('.ignitiondeck .add_media').click(function(e) {
		var button = jQuery(this);
		var inputID = jQuery(button).data('input');
		wp.media.editor.send.attachment = function(props, attachment){
			var attachID = jQuery(document.getElementById(inputID)).val(attachment.id);
			// Triggering an event that media is selected, passing attachment id as argument
			jQuery(document).trigger('idfMediaSelected', [attachment]);
		};
		wp.media.editor.open(button);
		return false;
	});
});