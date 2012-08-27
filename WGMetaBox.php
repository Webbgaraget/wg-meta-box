<?php
require_once( dirname( __FILE__ ) . "/WGMetaBoxInput.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputCheckbox.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputSelect.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputText.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputTextarea.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputRichEdit.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputDate.php" );

/**
 * Class facilitating creating meta boxes in WordPress
 *
 * @author Erik Hedberg (erik@webbgaraget.se)
 */
class WGMetaBox
{
	protected function __construct( $id, $title, $fields, $post_type, $context, $priority, $callback_args )
	{
		$this->params = array(
			'id'            => $id,
			'title'         => $title,
			'fields'        => $fields,
			'post_type'     => $post_type,
			'context'       => $context,
			'priority'      => $priority,
			'callback_args' => $callback_args
		);
		
		add_action( 'admin_menu', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
	}
	
	/**
	 * Adds meta box to post type of specified name
	 *
	 * @param string $id 
	 * @param string $title 
	 * @param array $fields 
	 * @param string $post_type
	 * @param string $context 
	 * @param string $priority 
	 * @param string $callback_args 
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public static function add_meta_box( $id, $title, Array $fields, $post_type, $context = 'advanced', $priority = 'default', $callback_args = null)
	{
		$meta_box = new static( $id, $title, $fields, $post_type, $context, $priority, $callback_args );
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
	public function save()
	{
		global $post;
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
			// Save text, textarea, select, richedit and date
			if ( in_array( $field['type'], array( 'text', 'textarea', 'select', 'richedit', 'date' ) ) )
			{
				$name = "{$this->params['id']}-{$slug}";
				$page_meta[$name] = $_POST[$name];
			}
			// Save checkbox
			elseif ( in_array( $field['type'], array( 'checkbox') ) )
			{
				$name = "{$this->params['id']}-{$slug}";
				$page_meta[$name] = ($_POST[$name] == $slug);
			}
			
		}
		
		
	    foreach( $page_meta as $key => $value )
	    {
	        if ( $this->post->post_type == 'revision' )
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
	public function render()
	{
		global $post;
		$class_names = array(
			'checkbox' => 'WGMetaBoxInputCheckbox',
			'select'   => 'WGMetaBoxInputSelect',
			'text'     => 'WGMetaBoxInputText',
			'textarea' => 'WGMetaBoxInputTextarea',
			'richedit' => 'WGMetaBoxInputRichEdit',
			'date'     => 'WGMetaBoxInputDate'
		);
		$output = "";
		$output .= '<input type="hidden" name="' . $this->params['id'] . '-nonce" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '">';
		$output .= '<table class="form-table">';

		// Loop through each field
		foreach( $this->params['fields'] as $slug => $field )
		{
			if ( !isset( $field['type'] ) )
			{
				throw new Exception( "Type not defined for {$slug}" );
			}
			if ( array_key_exists( $field['type'], $class_names ) )
			{
				$value = get_post_meta( $post->ID, "{$this->params['id']}-{$slug}", true );
				$field['slug'] = $slug;
				$field = new $class_names[$field['type']]( $this->params['id'], $field );
				if ( isset( $value ) && !is_null( $value ) && mb_strlen( $value ) != 0 )
				{
					$field->set_value( $value );
				}
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