<?php

/**
 * Class adding functionality to add select menu
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class WGMetaBoxInputSelect extends WGMetaBoxInput
{
	public function __construct( $namespace, $properties )
	{
		$this->namespace = $namespace;
		$this->properties = $properties;
		
        // Add default value to options
		if ( ( isset( $this->properties['default'] ) && $default = $this->properties['default'] ) || $default = "<i>None</i>" )
		{
            $this->properties['options'] = array_diff_key( array( '0' => $this->properties['default'] ), $this->properties['options'] ) + $this->properties['options'];
		}
	}
	
	/**
	 * Returns markup for select field
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
		// Multiple
		if ( isset( $this->properties['multiple'] ) && $this->properties['multiple'] )
		{
			$attributes[] = 'multiple="multiple"';
		}
		// Size
		if ( isset( $this->properties['size'] ) )
		{
			$attributes[] = 'size="' . $this->properties['size'] . '"';
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
		$output .= '<td>';
		if ( !isset( $this->properties['options'] ) )
		{
			throw new Exception( "Options must be defined" );
		}
		if ( !is_array( $this->properties['options'] ) )
		{
			throw new Exception( "Options param must be array" );
		}
		
		$output .= '<select ' . implode( ' ', $attributes ) . '>';
		foreach( $this->properties['options'] as $value => $name )
		{
			$selected = '';
			if ( $this->get_value() == $value )
			{
				$selected = ' selected="selected"';
			}
			$output .= '<option value="' . $value . '"' . $selected . '>' . $name . '</option>';
		}
		$output .= '</select>';
		
		// Description
		if ( isset($this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}
		$output .= '</td>';
		
		$output .= '</tr>';
		return $output;
	}
	
	/**
	 * Retrieves the default option
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function get_default_option()
	{
	    if ( isset( $this->properties['default_option'] ) )
	    {
	        return $this->properties['default_option'];
	    }
	    return null;
	}
	
	/**
	 * Retrieves the value
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function get_value()
	{
        $value = parent::get_value();

        // In case of default value
        if ( !$value || !array_key_exists( $value, $this->properties['options'] ) )
        {
            // Default value
            if ( isset( $this->properties['default'] ) )
            {
                return "0";
            }
            // Otherwise, there's no value
            return null;
        }
        return $value;
	}
	
	/**
	 * Retrieves the value to be echoed in the admin column
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
     public function get_column_value()
     {
         return $this->properties['options'][$this->get_value()];
     }
}