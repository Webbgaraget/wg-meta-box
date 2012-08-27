<?php

/**
 * Class adding functionality to add date field
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class WGMetaBoxInputDate extends WGMetaBoxInput
{
	public function __construct( $namespace, $properties )
	{
		$this->namespace = $namespace;
		$this->properties = $properties;
		
		add_action( 'admin_footer', array( $this, 'render_js' ) );
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
		// Validate label
		if ( !isset( $this->properties['label'] ) )
		{
			throw new Exception( "Label must be defined" );
		}
		// Add to markup
		$output .= '<th scope="row"><label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label></th>';
		
		/*** Add input field **/
		$output .= '<td><input type="text" ' . implode( ' ', $attributes ) . '>';
		
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