<?php

/**
 * Class adding functionality to add individual checkbox
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class WGMetaBoxInputCheckbox extends WGMetaBoxInput
{
	public function __construct( $namespace, $properties )
	{
		$this->namespace = $namespace;
		$this->properties = $properties;
		
        $this->labels = array(
            "No",
            "Yes"
        );
		if ( isset( $this->properties['admin-column-labels']) && is_array( $this->properties['admin-column-labels'] ) )
		{
		    $this->labels = $this->properties['admin-column-labels'];
		}
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
		if ( isset( $this->properties['slug'] ) )
		{
			$attributes[] = 'name="' . $this->namespace . '-' . $this->properties['slug']. '"';
			$attributes[] = 'id="' . $this->namespace . '-' . $this->properties['slug']. '"';
		}
		else
		{
			throw new Exception( "Slug must be defined" );
		}
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
		// Validate label
		if ( !isset( $this->properties['label'] ) )
		{
			throw new Exception( "Label must be defined" );
		}
		// Add to markup
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
	
	/**
	 * Retrieves the value to be echoed in the admin column
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
     public function get_column_value()
     {
         return $this->get_value() == "1" ? $this->labels[1] : $this->labels[0];
     }
}