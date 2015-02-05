<?php

/**
 * Class adding functionality to add a image
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 * @author Simon Strandman (simon@snowbits.se)
 */
class Wg_Meta_Box_Input_Color extends Wg_Meta_Box_Input
{
	public function __construct( $namespace, $properties )
	{
		$this->default_properties = array(
			'value' => '#EE7402',
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
	public function render()
	{
		$output = "<tr>";

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
		$output .= '<th scope="row">';
		$output .= '<label for="' . $this->namespace . '-' . $this->properties['slug'] . '">' . $this->properties['label'] . '</label>';

		if ( $this->is_required() )
		{
			$output .= '<br><small><em>' . __( 'Required' ) . '</em></small>';
		}

		$output .= '</th>';

		/*** Add input field **/
		$output .= '<td><input style="vertical-align: top" type="text" ' . implode( ' ', $attributes ) . '>';
		$output .= '<br><div style="display: inline-block; border: 1px solid #ccc; margin-top: 5px;" id="'. $this->namespace .'-' .$this->properties['slug'] . '-picker"></div>';

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

	    ?>
        <script type="text/javascript">
        	jQuery(document).ready(function()
        	{
        		jQuery("#<?php echo $this->namespace ?>-<?php echo $this->properties['slug'] ?>-picker").farbtastic('#<?php echo $this->namespace ?>-<?php echo $this->properties['slug'] ?>');
        	});
        	</script>
	    <?php
	}
}