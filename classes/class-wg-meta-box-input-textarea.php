<?php

/**
 * Class adding functionality to add textarea
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */	
class Wg_Meta_Box_Input_Textarea extends Wg_Meta_Box_Input
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
	 */
	public function render()
	{
		$output = "<tr>";
		
		/* Setup attributes */
		$attributes = array();
		// Name
		$attributes[] = 'name="' . $this->namespace . '-' . $this->properties['slug']. '"';
		$attributes[] = 'id="' . $this->namespace . '-' . $this->properties['slug']. '"';
		// Class
		if ( isset( $this->properties['class'] ) )
		{
			$attributes[] = 'class="large-text ' . $this->properties['class'] . '"';
		}
		else
		{
			$attributes[] = 'class="large-text"';
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
		
		/** Set up value **/
		$value = '';
		if ( isset( $this->properties['value'] ) )
		{
			$value = $this->properties['value'];
		}
		
		
		/** Add label to markup **/

		// Add to markup
		$output .= '<th scope="row"><label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label></th>';
		
		/*** Add input field **/
		$output .= '<td><textarea ' . implode( ' ', $attributes ) . '>' . $value . '</textarea>';
		
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