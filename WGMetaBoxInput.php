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
	
	/**
	 * Retrieves the slug
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function get_slug()
	{
	    if ( isset( $this->properties['slug'] ) )
	    {
	        return $this->properties['slug'];
	    }
	    else
	    {
	        throw new Exception('No slug defined');
	    }
	}
	
	/**
	 * Retrieves the label
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function get_label()
	{
	    if ( isset( $this->properties['label'] ) )
	    {
	        return $this->properties['label'];
	    }
	    else
	    {
	        throw new Exception('No label defined');
	    }
	}
	
	/**
	 * Whether the meta is supposed to be shown in the overview
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function show_in_overview()
	{
	    return isset( $this->properties['overview'] ) && $this->properties['overview'];
	}
	
	/**
	 * Retrieves the value to be echoed in the overview
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
     public function get_column_value()
     {
         return get_post_meta( $this->properties['post']->ID, "{$this->namespace}-{$this->properties['slug']}", true );
     }
}