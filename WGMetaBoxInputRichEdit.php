<?php

/**
 * Class adding functionality to add rich text editor
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class WGMetaBoxInputRichEdit extends WGMetaBoxInput
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
		if ( !isset( $this->properties['slug'] ) )
		{
			throw new Exception( "Slug must be defined" );
		}
		
		/** Editor properties ***/
        $settings = array();
		if ( isset( $this->properties['settings'] ) )
		{
		    $settings = $this->properties['settings'];
		}
		
		/*** Class **/
		if ( isset( $this->properties['class'] ) )
		{
            if ( isset( $settings['editor_class'] ) )
            {
                $settings['editor_class'] .= ' ' . $this->properties['class'];
            }
            else
            {
                $settings['editor_class'] = $this->properties['class'];
            }
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
		
        // Catch output since wp_editor() echoes the result
		ob_start();
		wp_editor( $this->properties['value'], $this->namespace . '-' . $this->properties['slug'], $settings );
		$output .= ob_get_contents();
		ob_end_clean();
		
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