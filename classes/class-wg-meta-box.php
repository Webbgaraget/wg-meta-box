<?php
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input.php" );
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input-checkbox.php" );
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input-select.php" );
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input-text.php" );
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input-textarea.php" );
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input-rich-edit.php" );
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input-date.php" );
require_once( dirname( __FILE__ ) . "/class-wg-meta-box-input-custom.php" );

/**
 * Class facilitating creating meta boxes in WordPress
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class WGMetaBox
{
	protected function __construct( $id, $title, $fields, $post_type, $context, $priority, $callback_args )
	{
		$this->class_names = array(
			'checkbox' => 'Wg_Meta_Box_Input_Checkbox',
			'select'   => 'Wg_Meta_Box_Input_Select',
			'text'     => 'Wg_Meta_Box_Input_Text',
			'textarea' => 'Wg_Meta_Box_Input_Textarea',
			'richedit' => 'Wg_Meta_Box_Input_Rich_Edit',
			'date'     => 'Wg_Meta_Box_Input_Date',
			'custom'   => 'Wg_Meta_Box_Input_Custom'
		);

		$this->params = array(
			'id'            => $id,
			'title'         => $title,
			'fields'        => $fields,
			'post_type'     => $post_type,
			'context'       => $context,
			'priority'      => $priority,
			'callback_args' => $callback_args
		);
		
		$this->post_type = $post_type;
		
		add_action( 'admin_menu', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
		
        // Add columns to the admin column
		add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_columns' ) );
	}
	
	/**
	 * Adds meta box to post type of specified name
	 *
	 * @param string $id 
	 * @param string $title 
	 * @param array $fields 
	 * @param string|array $post_types
	 * @param string $context 
	 * @param string $priority 
	 * @param string $callback_args 
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public static function add_meta_box( $id, $title, Array $fields, $post_types, $context = 'advanced', $priority = 'default', $callback_args = null)
	{
        if ( !is_array( $post_types ) )
        {
            $post_types = array( $post_types );
        }
        foreach( $post_types as $post_type )
        {
            new static( $id, $title, $fields, $post_type, $context, $priority, $callback_args );
        }
	}
		
	/**
	 * Adds meta box
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function add()
	{
		add_meta_box( $this->params['id'], $this->params['title'], array( $this, 'render' ), $this->params['post_type'], $this->params['context'], $this->params['priority'], $this->params['callback_args'] );
	}
	
	/**
	 * Called when saving post
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function save( $post_id, $post )
	{
        if ( is_null( $post ) ) return;
        
		// Verify not doing autosave
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		{
			return $post->ID;
		}
		// Verify user has rights
	    if ( !current_user_can( 'edit_post', $post->ID ) )
		{
	        return $post->ID;
		}
		// Verify nonce
	    if ( !isset( $_POST[$this->params['id'] . '-nonce'] ) || !wp_verify_nonce( $_POST[$this->params['id'] . '-nonce'], plugin_basename(__FILE__) ) )
	    {
	        return $post->ID;
	    }
	
		$page_meta = array();
		foreach( $this->params['fields'] as $slug => $field )
		{
			// Save text, textarea, select, richedit, date and custom field
			if ( in_array( $field['type'], array( 'text', 'textarea', 'select', 'richedit', 'date', 'checkbox' ) ) )
			{
				$name = "{$this->params['id']}-{$slug}";
				
                // If the field isn't set (checkbox), use empty string (default for get_post_meta()).
				$value = isset( $_POST[$name] ) ? $_POST[$name] : "";
				
				$page_meta[$name] = $value;
			}
			// Save custom field
			elseif ( in_array( $field['type'], array( 'custom' ) ) )
			{
			    $name = "{$this->params['id']}-{$slug}";
			    
			    // Call custom callback if defined
			    if ( is_array( $field['callbacks'] ) && isset( $field['callbacks']['save'] ) )
			    {
                    // If the field isn't set (checkbox), use empty string (default for get_post_meta()).
                    $value = isset( $_POST[$name] ) ? $_POST[$name] : "";

			        $value = call_user_func( $field['callbacks']['save'], $name, $value );
			    }
			    else
			    {
                    $value = isset( $_POST[$name] ) ? $_POST[$name] : "";
			    }
			    $page_meta[$name] = $value;
			}
			
		}

	    foreach( $page_meta as $key => $value )
	    {
	        if ( $post->post_type == 'revision' )
	        {
	            return;
	        }

	        if ( get_post_meta( $post->ID, $key, FALSE ) )
	        {
	            update_post_meta( $post->ID, $key, $value );
	        }
	        else
	        {
	            add_post_meta( $post->ID, $key, $value );
	        }
	    }
	}

	
	/**
	 * Renders the meta box
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function render($post, $context)
	{
		global $post;
		$output = "";
		$output .= '<input type="hidden" name="' . $this->params['id'] . '-nonce" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '">';
		
		if ( isset( $context['args'] ) && isset( $context['args']['description'] ) )
		{
		    $output .= call_user_func( $context['args']['description'] );
		}
		
		$output .= '<table class="form-table">';

		// Loop through each field
		foreach( $this->params['fields'] as $slug => $field )
		{
			if ( !isset( $field['type'] ) )
			{
				throw new Exception( "Type not defined for {$slug}" );
			}
			if ( array_key_exists( $field['type'], $this->class_names ) )
			{
				$value = get_post_meta( $post->ID, "{$this->params['id']}-{$slug}", true );
				$field['slug'] = $slug;
				$field = new $this->class_names[$field['type']]( $this->params['id'], $field );

				$field->set_value( $value );
				$output .= $field->render();
			}
			else
			{
				throw new Exception( "Field has unknown type: {$field['type']}" );
			}
		}
		$output .= "</table>";
		echo $output;
	}
	
	/**
	 * Add columns to the admin column
	 *
	 * @param string $post_columns 
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function add_columns( $post_columns )
	{
		// Loop through each field
		foreach( $this->params['fields'] as $slug => $field )
		{
			if ( !isset( $field['type'] ) )
			{
				throw new Exception( "Type not defined for {$slug}" );
			}
			if ( array_key_exists( $field['type'], $this->class_names ) )
			{
			    $field['slug'] = $slug;
				$field = new $this->class_names[$field['type']]( $this->params['id'], $field );
                if ( $field->show_in_admin_column() )
                {
                    $post_columns = array_merge( $post_columns, array( $field->get_slug() => $field->get_column_label() ) );
		            add_filter( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'populate_column' ) );
                }
			}
			else
			{
				throw new Exception( "Field has unknown type: {$field['type']}" );
			}
		}
		return $post_columns;
	}
	
	/**
	 * Populates admin column with meta data
	 *
	 * @param string $column_name 
	 * @param string $post_id 
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function populate_column( $slug )
	{
	    global $post;
	    
	    // Do return if the column slug isn't among the fields (i.e. other plugin)
	    if ( !array_key_exists( $slug, $this->params['fields'] ) ) return;
	    
        $field = $this->params['fields'][$slug];        
	    $field['slug'] = $slug;
	    $field['post'] = $post;
		$field = new $this->class_names[$field['type']]( $this->params['id'], $field );
        echo $field->get_column_value();
	}
	
	/**
	 * Enqueue needed JS
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function enqueue_js()
	{
	    wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css' );
	}
}