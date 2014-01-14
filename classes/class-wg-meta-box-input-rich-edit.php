<?php

/**
 * Class adding functionality to add rich text editor
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class Wg_Meta_Box_Input_Rich_Edit extends Wg_Meta_Box_Input
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
		// Value
		$value = '';
		if ( isset( $this->properties['value'] ) )
		{
			$value = $this->properties['value'];
		}

		// Name
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
		$output .= '<th scope="row">';
		$output .= '<label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</th>';
		
		/*** Add input field **/
		$output .= '<td>';
		
        // Catch output since wp_editor() echoes the result
		ob_start();
		wp_editor( $value, $this->namespace . '-' . $this->properties['slug'], $settings );
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