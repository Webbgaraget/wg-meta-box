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
			$attributes[]     = 'value="' . $this->get_value() . '"';

            // Get the media name and url of the chosen image for display purposes
            $previewImage     = get_post( $this->get_value() );
            $previewImageName = $previewImage->post_title;
            $previewUrl       = wp_get_attachment_url( $this->get_value() );
		}
        else
        {
            $previewUrl = '';
            $previewImageName = __( 'no image choosen' );
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

        $output .= '<p style="margin:0;">' . __( 'Image' ) . ': <span class="input-image-filename">' . $previewImageName . '</span></p>';
        $output .= '<input class="input-image-input" type="hidden" ' . implode( ' ', $attributes ) . '>';
		$output .= '<button style="vertical-align: top" class="input-image-choose button">' . __( "Choose image" ) . '</button>';
		$output .= '<br>';

		if ( $previewUrl )
		{
        	$output .= '<img class="input-image-preview" src="'. $previewUrl .'">';
            $output .= '<a class="input-image-remove" href="#">' . __( 'Remove image' ) . '</a>';
		}
		else
		{
        	$output .= '<img class="input-image-preview hidden" src="'. $previewUrl .'">';
            $output .= '<a class="input-image-remove hidden" href="#">' . __( 'Remove image' ) . '</a>';
		}

        $output .= '<br>';

		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</div>';
		return $output;
	}
}