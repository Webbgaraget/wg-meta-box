<?php

/**
 * Abstract class adding common functionality for each input field
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
abstract class WGMetaBoxInput
{
	/**
	 * Generates HTML markup for input field
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	abstract protected function render();
	
	/**
	 * Sets value
	 *
	 * @param string $value 
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function set_value( $value )
	{
		if ( is_array( $this->properties ) )
		{
			$this->properties['value'] = $value;
		}
	}
}