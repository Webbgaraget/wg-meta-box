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
	public function render()
	{
		$output = "<tr>";
        
        $id = $this->namespace .'-'. $this->properties['slug'];
        $value = $this->properties['value'];
		
		
		/** Add label to markup **/
		$output .= '<th scope="row"><label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label></th>';
		
		/*** Add custom input **/
		$output .= '<td>';
		
		$output .= call_user_func( $this->properties['callbacks']['render'], $id, $value );
		
		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</td>';
		
		$output .= '</tr>';
		return $output;
	}
}