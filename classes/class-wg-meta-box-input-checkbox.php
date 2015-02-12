<?php

/**
 * Class adding functionality to add individual checkbox
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class Wg_Meta_Box_Input_Checkbox extends Wg_Meta_Box_Input
{
	public function __construct( $namespace, $properties )
	{
		$this->default_properties = array(
		  'checked'      => false,
		  'admin-column' => array(
		      'label-checked'   => 'Yes',
		      'label-unchecked' => 'No'
		  )
		);

	    parent::__construct( $namespace, $properties );
	}

	/**
	 * By settings value, it means that the checkbox should be checked
	 *
	 * @param string $value
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function set_value( $value )
	{
		if ( $value )
		{
			if ( $this->_get_repetitions() == 1)
			{
				$this->properties['checked'] = true;
			}
			elseif ( isset( $value[$this->properties['num']] ) )
			{
				$this->properties['checked'] = true;
			}
		}
	}

	/**
	 * Returns markup for checkbox
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
		if ( $this->get_value() )
		{
			$attributes[] = 'value="' . $this->get_value() . '"';
		}
		else
		{
			$attributes[] = 'value="' . $this->properties['slug'] . '"';
		}

		// Class
		if ( isset( $this->properties['class'] ) )
		{
			$attributes[] = 'class="' . $this->properties['class'] . '"';
		}
		// Disabled
		if ( isset( $this->properties['disabled'] ) && $this->properties['disabled'] )
		{
			$attributes[] = 'disabled="disabled"';
		}
		// Checked
		if ( isset( $this->properties['checked'] ) && $this->properties['checked'] )
		{
			$attributes[] = 'checked="checked"';
		}


		/** Add label to markup **/
		$output = '<div class="label checkbox">';
		$output .= '<label for="' . $this->get_id() . '">' . $this->get_label() . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required', WGMetaBox::TEXT_DOMAIN ) . '</em></small>';
		}

		$output .= '</div>';

		/*** Add input field **/
		$output .= '<div class="input checkbox">';
		$output .= '<input type="checkbox" ' . implode( ' ', $attributes ) . '>';

		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</div>';

		return $output;
	}
}