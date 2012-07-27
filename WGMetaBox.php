<?php
require_once( dirname( __FILE__ ) . "/WGMetaBoxInput.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputCheckbox.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputSelect.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputText.php" );
require_once( dirname( __FILE__ ) . "/WGMetaBoxInputTextarea.php" );

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
	public static function add_meta_box( $id, $title, $fields, $post_type, $context = 'advanced', $priority = 'default', $callback_args = null)
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

	    if ( !current_user_can( 'edit_post', $post->ID ) )
	        return $post->ID;
	
		$page_meta = array();
		foreach( $this->params['fields'] as $type => $field )
		{
			// Save text, textarea, select
			if ( in_array( $type, array( 'text', 'textarea', 'select' ) ) )
			{
				$name = "{$this->params['id']}-{$field['slug']}";
				$page_meta[$name] = $_POST[$name];
			}
			// Save checkbox
			if ( in_array( $type, array( 'checkbox') ) )
			{
				$name = "{$this->params['id']}-{$field['slug']}";
				$page_meta[$name] = ($_POST[$name] == $field['slug']);
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
			'textarea' => 'WGMetaBoxInputTextarea'
		);
		$output = '<table class="form-table">';

		// Loop through each field
		foreach( $this->params['fields'] as $type => $field )
		{
			if ( array_key_exists( $type, $class_names ) )
			{
				if ( !isset( $field['slug'] ) )
				{
					throw new Exception( "Slug not defined for {$type}" );
				}
				$value = get_post_meta( $post->ID, "{$this->params['id']}-{$field['slug']}", true );
				$field = new $class_names[$type]( $this->params['id'], $field );
				if ( isset( $value ) && !is_null( $value ) && mb_strlen( $value ) != 0 )
				{
					$field->set_value( $value );
				}
				$output .= $field->render();
			}
			else
			{
				throw new Exception( "Field has unknown name: {$type}" );
			}
		}
		$output .= "</table>";
		echo $output;
	}
}