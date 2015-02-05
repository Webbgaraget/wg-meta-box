<?php

/**
 * Class adding functionality to add a image
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 * @author Simon Strandman (simon@webbgaraget.se)
 */
class Wg_Meta_Box_Input_Image extends Wg_Meta_Box_Input
{
	public function __construct( $namespace, $properties )
	{
		$this->default_properties = array();
		parent::__construct( $namespace, $properties );

		add_action( 'admin_footer', array( $this, 'render_js' ) );
	}

	/**
	 * Returns markup for input field
	 *
	 * @return string
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 * @author Simon Strandman (simon@webbgaraget.se)
	 */
	public function render_markup()
	{
		/* Setup attributes */
		$attributes = array();
		// Name
		$attributes[] = 'name="' . $this->get_name() . '"';
		$attributes[] = 'id="' . $this->get_id() . '"';

		// Value
		if ( $this->get_value() )
		{
			$attributes[] = 'value="' . $this->get_value() . '"';
            // Get the media name and url of the chosen image for display purposes
            $preview_image = get_post( $this->properties['value'] );
            $preview_image_name = $preview_image->post_title;
            $preview_url = wp_get_attachment_url( $this->properties['value'] );
		}
        else
        {
            $preview_url = '';
            $preview_image_name = 'ingen bild vald';
        }
		// Class
		if ( isset( $this->properties['class'] ) )
		{
			$attributes[] = 'class="text ' . $this->properties['class'] . '"';
		}
		else
		{
			$attributes[] = 'class="text"';
		}
		// Disabled
		if ( isset( $this->properties['disabled'] ) && $this->properties['disabled'] )
		{
			$attributes[] = 'disabled="disabled"';
		}
		// Placeholder
		if ( isset( $this->properties['placeholder'] ) )
		{
			$attributes[] = 'placeholder="' . $this->properties['placeholder'] . '"';
		}


		/** Add label to markup **/
		$output = '<div class="label">';
		$output .= '<label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</div>';

		/*** Add input field **/
        $output .= '<div class="input">';

        $output .= '<p style="margin:0;">Vald bild: <span id="' . $this->namespace . '-' . $this->properties['slug'] . '-label" style="font-style:italic;">' . $preview_image_name . '</span></p>';
        $output .= '<input type="hidden" ' . implode( ' ', $attributes ) . '>';
		$output .= '<input style="vertical-align: top" type="button" id="'. $this->namespace .'-' .$this->properties['slug'] . '-button" value="Välj bild">';
		$output .= '<br>';
        $output .= '<img style="width: 290px; height: auto; padding: 5px; border: 1px solid #ccc; margin-top: 5px;" id="'. $this->namespace .'-' .$this->properties['slug'] . '-preview" src="'. $preview_url .'">';
        $output .= '<br>';

        if ( isset( $this->properties['value'] ) )
        {
            $output .= '<a style="display: block;" id="' . $this->namespace . '-' . $this->properties['slug'] . '-remove" href="#">Ta bort vald bild</a>';
        }
        else {
            $output .= '<a style="display: none;" id="' . $this->namespace . '-' . $this->properties['slug'] . '-remove" href="#">Ta bort vald bild</a>';
        }

		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</div>';
		return $output;
	}

	/**
	 * Renders necessary JS
	 *
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 * @author Simon Strandman (simon@snowbits.se)
	 */
	function render_js()
	{

	    ?>
        <script type="text/javascript">
        	jQuery(document).ready(function()
        	{
                // Calling .wpImagepicker(element, preview, label)
                // element = the hidden field that contains the attachment id
                // preview = the image that will contain the preview of the selected image
                // label = a span that will display the title of the selected image
                // remove = a link that will reset the selected image to none
        		jQuery("#<?php echo $this->namespace ?>-<?php echo $this->properties['slug'] ?>-button")
        			.wpImagepicker(
                        "#<?php echo $this->namespace ?>-<?php echo $this->properties['slug'] ?>",
        				"#<?php echo $this->namespace ?>-<?php echo $this->properties['slug'] ?>-preview",
                        "#<?php echo $this->namespace ?>-<?php echo $this->properties['slug'] ?>-label",
                        "#<?php echo $this->namespace ?>-<?php echo $this->properties['slug'] ?>-remove");
        	});
        	</script>
	    <?php
	}
}