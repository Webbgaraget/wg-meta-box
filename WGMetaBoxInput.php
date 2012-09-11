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
	 * Whether the meta is supposed to be shown in the admin column
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function show_in_admin_column()
	{
	    return isset( $this->properties['admin-column'] ) && $this->properties['admin-column'];
	}
	
	/**
	 * Gets the value
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function get_value()
	{
	    // In case value already set
	    if ( isset( $this->properties['value'] ) )
	    {
	        return $this->properties['value'];
	    }
	    // In case value defined in post-meta
        if ( $value = get_post_meta( $this->properties['post']->ID, "{$this->namespace}-{$this->properties['slug']}", true ) )
        {
            return $value;
        }
        return null;
	}
	
	/**
	 * Retrieves the value to be echoed in the admin column
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
     public function get_column_value()
     {
         return $this->get_value();
     }
     
     /**
      * Gets the admin column label
      *
      * @return void
      * @author Erik Hedberg (erik@webbgaraget.se)
      */
     public function get_column_label()
     {
         if ( isset( $this->properties['admin-column-label'] ) )
         {
             return $this->properties['admin-column-label'];
         }
         return $this->get_label();
     }
}