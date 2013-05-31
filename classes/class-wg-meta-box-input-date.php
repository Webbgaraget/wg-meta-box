<?php

/**
 * Class adding functionality to add date field
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class Wg_Meta_Box_Input_Date extends Wg_Meta_Box_Input
{
	public function __construct( $namespace, $properties )
	{
		$this->default_properties = array(
			'dateFormat' => 'yy-mm-dd',
		);
		parent::__construct( $namespace, $properties );

		add_action( 'admin_footer', array( $this, 'render_js' ) );
	}
	
	/**
	 * Returns markup for input field
	 *
	 * @return string
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function renderMarkup( $num )
	{
		
		/* Setup attributes */
		$attributes = array();
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
			$attributes[] = 'class="text ' . $this->properties['class'] . '"';
		}
		else
		{
			$attributes[] = 'class="text"';
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
		$output = '<div class="label">';
		$output .= '<label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</div>';
		
		/*** Add input field **/
		$output .= '<div class="input">';
		$output .= '<input type="text" ' . implode( ' ', $attributes ) . '>';
		
		// Description
		if ( isset( $this->properties['description'] ) )
		{
			$output .= '<span class="description">' . $this->properties['description'] . '</span>';
		}

		$output .= '</div>';
		
		return $output;
	}
	
	/**
	 * Renders necessary JS
	 *
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	function render_js()
	{
	    
	    /*** Set attributes ***/
	    $attrs = array();
	    // First day
	    if ( isset( $this->properties['first_day'] ) )
	    {
	        $attrs['firstDay'] = $this->properties['first_day'];
	    }
	    // Format
	    if ( isset( $this->properties['format'] ) )
	    {
	        $attrs['dateFormat'] = $this->properties['format'];
	    }
	    // Default 'yy-mm-dd'
	    else
	    {
	        $attrs['dateFormat'] = 'yy-mm-dd';
	    }
	    
        $attr_string = "";
        if ( count( $attrs ) > 0 )
        {
            foreach( $attrs as $name => $value )
            {
                $attr_string .= "{$name}:'{$value}',";
            }
        }
	    ?>
        <script type="text/javascript">
        	jQuery(document).ready(function()
        	{
        		jQuery("#<?php echo $this->namespace; ?>-<?php echo $this->properties['slug']; ?>").datepicker({<?php echo $attr_string; ?>});
        	});
        	</script>
	    <?php
	}
}