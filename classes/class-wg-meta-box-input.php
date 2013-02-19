<?php

/**
 * Abstract class adding common functionality for each input field
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
abstract class Wg_Meta_Box_Input
{
    
	public function __construct( $namespace, $properties )
    {
		// Validate slug
		if ( !isset( $properties['slug'] ) )
		{
			throw new Exception( 'Slug must be defined' );
		}
		
		// Validate label
		if ( !isset( $properties['label'] ) )
		{
			throw new Exception( 'Label must be defined' );
		}


        $this->namespace = $namespace;

        $this->default_properties = array_merge(
        	array(
	            'admin-column' => array(
	                'display'  => false,
	                'label'    => $properties['label'],
	                'callback' => array( $this, 'admin_column_callback' ),
	                'sortable' => false,
	            ),
	            'required' => false,
        	),
        	$this->default_properties
        );
        
        // Do separate merge of admin-column, since array_merge() doesn't handle multi-dimensional arrays
		if ( array_key_exists( 'admin-column', $properties ) )
		{
            $this->default_properties['admin-column'] = array_merge( $this->default_properties['admin-column'], $properties['admin-column'] );
            unset( $properties['admin-column'] );
		}
		$this->properties = array_merge( $this->default_properties, $properties );
    }
        
	/**
	 * Generates HTML markup for input field
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	abstract protected function render();
	
	/**
	 * Default callback function for admin column
	 *
	 * @param string $id 
	 * @param string $value 
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function admin_column_callback( $id, $value )
	{
	    return $value;
	}
	
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
	        throw new Exception( 'No slug defined' );
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
			throw new Exception( 'No label defined' );
	    }
	}

	/**
	 * Retrieves whether sortable
	 *
	 * @return boolean
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function is_sortable()
	{
		return $this->properties['admin-column']['sortable'];
	}

	/**
	 * Retrieves whether required
	 *
	 * @return boolean
	 */
	public function is_required()
	{
		return $this->properties['required'];
	}
	
	/**
	 * Whether the meta is supposed to be shown in the admin column
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function show_in_admin_column()
	{
	    return $this->properties['admin-column']['display'];
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
         if ( isset( $this->properties['admin-column']['callback'] ) )
         {
             $id = "{$this->namespace}-{$this->properties['slug']}";
             $value = call_user_func( $this->properties['admin-column']['callback'], $id, $this->get_value() );
         }
         else
         {
             $value = $this->get_value();
         }
         return $value;
     }
     
     /**
      * Gets the admin column label
      *
      * @return void
      * @author Erik Hedberg (erik@webbgaraget.se)
      */
     public function get_column_label()
     {
         if ( isset( $this->properties['admin-column']['label'] ) )
         {
             return $this->properties['admin-column']['label'];
         }
         return $this->get_label();
     }
}