/*
 * Attaches the image uploader to the input field
 */
jQuery.fn.wpImagepicker = function (element, preview, label, remove_link) {
  jQuery.wpImagepicker(this, element, preview, label, remove_link);
  return this;
};

jQuery.wpImagepicker = function (_container, element, preview, label, remove_link) {
  var container = jQuery(_container).get(0);
  if (container)
    return container.wpImagepicker || (container.wpImagepicker = new jQuery._wpImagepicker(container, element, preview, label, remove_link));
};

jQuery._wpImagepicker = function(container, element, preview, label, remove_link) {
    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;

    // Runs when the image button is clicked.
    jQuery(container).click(function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            meta_image_frame.open();
            return;
        }

        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            library: { type: 'image' }
        });

        // Runs when an image is selected.
        meta_image_frame.on('select', function(){

            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

            // Sends the attachment URL to our custom image input field.
            jQuery(element).val(media_attachment.id);
            console.log(media_attachment);

            // Update the image preview to view our chosen image
            if (preview) {
                jQuery(preview).attr("src", media_attachment.url);
                // Show the preview image, since we hide it when we remove it
                jQuery(preview).show();
                // Show the remove link
                jQuery(remove_link).show();
            }
            // Update the label with the image title
            if (label) {
                jQuery(label).html(media_attachment.title);
            }


        });

        // Opens the media library frame.
        //wp.media.editor.open();
        meta_image_frame.open();
    });

    // Runs when the 'remove link' is clicked
    jQuery(remove_link).click(function(e) {
        e.preventDefault();
        // Reset the fields
        jQuery(element).val( '' );
        jQuery(preview).hide();
        jQuery(label).html( '' );
        // Hide itself
        jQuery(this).hide();
    });
};