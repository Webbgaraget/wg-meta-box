(function(win, doc, $) {
	"use strict";

	var ImagePicker = ImagePicker || function( $element ) {

		var meta_image_frame;

		var $chooseButton = $element.find('.input-image-choose');
		var $input        = $element.find('.input-image-input');
		var $preview      = $element.find('.input-image-preview');
		var $label        = $element.find('.input-image-filename');
		var $removeButton = $element.find('.input-image-remove');

		var openImagePicker = function(e){

			e.preventDefault();

			// If the frame already exists, re-open it.
			if ( meta_image_frame ) {
				meta_image_frame.open();
				return;
			}

			// Otherwise create a new media fram
			//meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			meta_image_frame = wp.media({
				library: { type: 'image' }
			});

			meta_image_frame.on('select', function(){

				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

				$input.val(media_attachment.id);
				console.log(media_attachment);

				$preview.attr("src", media_attachment.url).removeClass('hidden');
				$removeButton.removeClass('hidden');

				$label.text(media_attachment.title);
			});

			// Opens the media library frame.
			//wp.media.editor.open();
			meta_image_frame.open();
		};

		var remove = function(e) {
			e.preventDefault();

			// Reset the fields
			$input.val('');
			$label.text('ingen bild vald');

			// Hide this button and the preview
			$preview.addClass('hidden');
			$removeButton.addClass('hidden');
		};

		$chooseButton.click(openImagePicker);
		$removeButton.click(remove);
	};

	// Init for all groups
	$(win).ready(function()
	{
		var attachImageHandler = function( i, input ) {
			var picker = new ImagePicker($(input));
		};

		$('.input-image').each(attachImageHandler);

		$(document).on('wgmb:groupadded',
			function( event, id )
			{
				// Remove preview image and text.
				$('#' + id)
					.find('.input-image').each(attachImageHandler)
					.find('.input-image-remove, .input-image-preview').addClass('hidden')
					.end()
					.find('.input-image-filename').text('');
			}
		);
	});

})(this, document, jQuery);