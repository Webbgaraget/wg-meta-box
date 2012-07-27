<?php
abstract class WGMetaBoxInput
{
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