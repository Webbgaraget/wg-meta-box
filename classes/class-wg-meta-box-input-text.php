<?php

/**
 * Class adding functionality to add text field
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class Wg_Meta_Box_Input_Text extends Wg_Meta_Box_Input
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
	public function renderMarkup( $num )
	{
		/* Setup attributes */
		$attributes = array();
		// Name
		$attributes[] = 'name="' . $this->namespace . '-' . $this->properties['slug']. '[]"';
		$attributes[] = 'id="' . $this->namespace . '-' . $this->properties['slug']. '-' . $num . '"';
		// Value
		if ( isset( $this->properties['value'] ) )
		{
			$value = $this->properties['value'][$num];
			$attributes[] = 'value="' . $value . '"';
		}
		// Class
		if ( isset( $this->properties['class'] ) )
		{
			$attributes[] = 'class="text large-text ' . $this->properties['class'] . '"';
		}
		else
		{
			$attributes[] = 'class="text large-text"';
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
		
		$output = '<div class="label">';

		
		/** Add label to markup **/
		$output .= '<label for="' . $this->namespace . '-' . $this->properties['slug']. '-' . $num . '">' . $this->properties['label'] . ' #' . ( $num + 1 ) . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</div>';
		$output .= '<div class="input">';
		
		/*** Add input field **/
		$output .= '<input type="text" ' . implode( ' ', $attributes ) . '>';
		
		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</div>';

		return $output;
	}
}