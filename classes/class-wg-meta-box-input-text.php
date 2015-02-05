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
	public function render_markup()
	{
		/* Setup attributes */
		$attributes = array();
		// Name
		$attributes[] = 'name="' . $this->get_name() . '"';
		$attributes[] = 'id="' . $this->get_id() . '"';
		// Value
		if ( isset( $this->properties['value'] ) )
		{
			$attributes[] = 'value="' . $this->get_value() . '"';
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
		$output .= '<label for="' . $this->get_id() . '">' . $this->get_label() . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</div>';
		$output .= '<div class="input">';

		/*** Add input field **/
		$output .= '<input type="text" ' . implode( ' ', $attributes ) . '>';

		/*** Add delete button ***/
		if ( $this->_get_is_repeatable() )
		{
			$output .= '<a href="#" class="field-remove-button" data-num="' . $this->properties['num'] . '">Remove</a>';
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