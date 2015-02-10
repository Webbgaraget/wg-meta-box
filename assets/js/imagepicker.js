(function(win, doc, $) {
	"use strict";

	var ImagePicker = ImagePicker || function()
	{

	};

	// Init for all groups
	$(win).ready(function()
	{
		$('.input-image').each(function(i, input)
		{
			var picker = new ImagePicker($(input));
		});
	});

})(this, document, jQuery);