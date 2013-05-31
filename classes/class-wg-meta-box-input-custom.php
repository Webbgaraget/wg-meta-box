<?php

/**
 * Class adding functionality to add a custom input field
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class Wg_Meta_Box_Input_Custom extends Wg_Meta_Box_Input
{
	public function __construct( $namespace, $properties )
	{
        // Verify there's a render callback saved
        if ( !is_array( $properties['callbacks'] ) || !isset( $properties['callbacks']['render'] ) )
		{
			throw new Exception( "Render callback must be defined" );
		}

		$this->default_properties = array();
	    parent::__construct( $namespace, $properties );
	}
	
	/**
	 * Returns markup for input field
	 *
	 * @return string
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function renderMarkup( $num )
	{
       
        $id = $this->namespace .'-'. $this->properties['slug'];
        $value = $this->properties['value'];
		
		
		/** Add label to markup **/
		$output = '<div class="label">';
		$output .= '<label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</div>';
		
		/*** Add custom input **/
		$output .= '<div class="input">';
		
		$output .= call_user_func( $this->properties['callbacks']['render'], $id, $value );
		
		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</div>';
		return $output;
	}
}