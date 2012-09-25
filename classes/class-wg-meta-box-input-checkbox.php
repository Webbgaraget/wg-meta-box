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
			$this->properties['checked'] = true;
		}
	}
	
	/**
	 * Returns markup for checkbox
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

		// Value
		if ( isset( $this->properties['slug'] ) )
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
		$output .= '<th scope="row"><label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label></th>';
		
		/*** Add input field **/
		$output .= '<td><input type="checkbox" ' . implode( ' ', $attributes ) . '>';
		
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