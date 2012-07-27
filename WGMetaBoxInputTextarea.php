<?php

/**
 * Class adding functionality to add textarea
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */	
class WGMetaBoxInputTextarea extends WGMetaBoxInput
{
	public function __construct( $namespace, $properties )
	{
		$this->namespace = $namespace;
		$this->properties = $properties;
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
		// Validate label
		if ( !isset( $this->properties['label'] ) )
		{
			throw new Exception( "Label must be defined" );
		}
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