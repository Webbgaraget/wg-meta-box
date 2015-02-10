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
			$attributes[]       = 'value="' . $this->get_value() . '"';

            // Get the media name and url of the chosen image for display purposes
            $previewImage     = get_post( $this->get_value() );
            $previewImageName = $previewImage->post_title;
            $previewUrl       = wp_get_attachment_url( $this->get_value() );
		}
        else
        {
            $previewUrl = '';
            $previewImageName = 'ingen bild vald';
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
		$output .= '<label for="' . $this->get_id() . '">' . $this->properties['label'] . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</div>';

		/*** Add input field **/
        $output .= '<div class="input input-image">';

        $output .= '<p style="margin:0;">Vald bild: <span class="input-image-filename" style="font-style:italic;">' . $previewImageName . '</span></p>';
        $output .= '<input class="input-image-input" type="hidden" ' . implode( ' ', $attributes ) . '>';
		$output .= '<button style="vertical-align: top" class="input-image-choose">' . __( "Choose image" ) . '</button>';
		$output .= '<br>';
        $output .= '<img style="width: 290px; height: auto; padding: 5px; border: 1px solid #ccc; margin-top: 5px;" class="input-image-preview" src="'. $previewUrl .'">';
        $output .= '<br>';

        if ( isset( $this->properties['value'] ) )
        {
            $output .= '<a style="display: block;" class="input-image-remove" href="#">' . __( 'Remove image' ) . '</a>';
        }
        else
        {
            $output .= '<a style="display: none;" class="input-image-remove" href="#">' . __( 'Remove image' ) . '</a>';
        }

		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</div>';
		return $output;
	}
}