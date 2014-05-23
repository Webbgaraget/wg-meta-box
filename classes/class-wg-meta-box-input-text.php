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
	public function render()
	{
		$output = "<tr>";

		/* Setup attributes */
		$attributes = $this->get_attributes();
		// Name
		$attributes[] = 'name="' . $this->namespace . '-' . $this->properties['slug']. '"';
		$attributes[] = 'id="' . $this->namespace . '-' . $this->properties['slug']. '"';
		// Value
		if ( isset( $this->properties['value'] ) )
		{
			$attributes[] = 'value="' . $this->properties['value'] . '"';
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


		/** Add label to markup **/
		$output .= '<th scope="row">';
		$output .= '<label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</th>';

		/*** Add input field **/
		$output .= '<td><input type="text" ' . implode( ' ', $attributes ) . '>';

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