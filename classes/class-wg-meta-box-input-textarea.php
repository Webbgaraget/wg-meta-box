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
	public function render_markup()
	{

		/* Setup attributes */
		$attributes = array();
		// Name
		$attributes[] = 'name="' . $this->get_name() . '"';
		$attributes[] = 'id="' . $this->get_id() . '"';
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
		$value = $this->get_value();


		/** Add label to markup **/

		// Add to markup
		$output = '<div class="label">';

		$output .= '<label for="' . $this->get_id() . '">' . $this->get_label() . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required', WGMetaBox::TEXT_DOMAIN ) . '</em></small>';
		}

		$output .= '</div>';
		$output .= '<div class="input">';

		/*** Add input field **/
		$output .= '<textarea ' . implode( ' ', $attributes ) . '>' . $value . '</textarea>';

		/*** Add delete button ***/
		if ( $this->_get_is_repeatable() )
		{
			$output .= '<a href="#" class="field-remove-button" data-num="' . $this->properties['num'] . '">' . __( 'Remove', WGMetaBox::TEXT_DOMAIN ) . '</a>';
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