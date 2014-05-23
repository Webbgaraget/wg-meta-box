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

		// Register sortable columns
		add_filter( 'manage_edit-' . $post_type . '_sortable_columns', array( $this, 'register_sortable_columns' ) );

		// Add instructions for sorting meta values
		add_filter( 'pre_get_posts', array( $this, 'register_sortable_meta' ) );

		// Write error messages for required fields
		add_action( 'admin_notices', array( $this, 'show_admin_messages' ) );

		// Supress "post published" message
		add_filter( 'post_updated_messages', array( $this, 'supress_default_message' ) );
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

		// List of missing fields
		$required_missing = array();

		foreach( $this->params['fields'] as $slug => $field )
		{
			$value = '';
			// Save text, textarea, richedit, date and custom field
			if ( in_array( $field['type'], array( 'text', 'textarea', 'richedit', 'date', 'checkbox' ) ) )
			{
				$name = "{$this->params['id']}-{$slug}";

                // If the field isn't set (checkbox), use empty string (default for get_post_meta()).
				$value = isset( $_POST[$name] ) ? $_POST[$name] : '';

				$page_meta[$name] = $value;
			}
			// Select
			elseif ( $field['type'] == 'select' )
			{
				$name = "{$this->params['id']}-{$slug}";
				$value = isset( $_POST[$name] ) && $_POST[$name] != '0' ? $_POST[$name] : '';
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
                    $value = isset( $_POST[$name] ) ? $_POST[$name] : '';

			        $value = call_user_func( $field['callbacks']['save'], $name, $value );
			    }
			    else
			    {
                    $value = isset( $_POST[$name] ) ? $_POST[$name] : '';
			    }
			    $page_meta[$name] = $value;
			}

			// Check if field is required and not set
			if ( isset( $field['required']) && $field['required'] && '' == $value )
				$required_missing[] = $slug;
		}

	    foreach( $page_meta as $key => $value )
	    {
	        if ( $post->post_type == 'revision' )
	        {
	            return;
	        }

	        update_post_meta( $post->ID, $key, $value );
	    }

		// Any required fields missing?
		if ( count( $required_missing ) > 0 )
		{
			set_transient( $this->params['id'] . '-required-missing-fields', $required_missing );
			set_transient( $this->params['id'] . '-missing-required-fields', 1 );

			// Remove action hook and set post status to draft
			remove_action( 'save_post', array( $this, 'save' ), 10, 2 );
			wp_update_post(
				array(
					'ID'          => $post->ID,
					'post_status' => 'draft',
				)
			);
			add_action( 'save_post', array( $this, 'save' ), 10, 2 );
		}
		else
		{
			delete_transient( $this->params['id'] . '-required-missing-fields' );
			delete_transient( $this->params['id'] . '-missing-required-fields' );
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

				if ( ! empty( $value ) )
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
                	// Namespace column slug to avoid conflicts
                	$column_slug = $this->params['id'] . '-' . $field->get_slug();
                    $post_columns = array_merge( $post_columns, array( $column_slug => $field->get_column_label() ) );
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

	    $slug = substr( $slug, strlen( $this->params['id'] ) + 1 );
	    // Do return if the column slug isn't among the fields (i.e. other plugin)
	    if ( !$slug || !array_key_exists( $slug, $this->params['fields'] ) ) return;

        $field = $this->params['fields'][$slug];

	    $field['slug'] = $slug;
	    $field['post'] = $post;
		$field = new $this->class_names[$field['type']]( $this->params['id'], $field );
        echo $field->get_column_value();
	}

	/**
	 * Registers additional sortable columns
	 *
	 * @param array Names of sortable columns
	 * @return arrray
	 */
	public function register_sortable_columns( $columns )
	{
		foreach( $this->params['fields'] as $slug => $field )
		{
			$field['slug'] = $this->params['id'] . '-' . $slug;
			$field = new $this->class_names[$field['type']]( $this->params['id'], $field );

			if ( $field->is_sortable() )
			{
				$columns[$field->get_slug()] = $field->get_slug();
			}
		}
		return $columns;
	}

	/**
	 * Register instruction on how to order meta values
	 *
	 * @param array Query vars
	 * @return array
	 */
	public function register_sortable_meta( $query )
	{
	   if( ! is_admin() )
	        return;

	    $orderby = $query->get( 'orderby' );

	    if ( strpos( $orderby, $this->params['id'] ) !== false )
	    {
	    	$slug = substr( $orderby, strlen( $this->params['id'] ) + 1 );

	    	if ( array_key_exists( $slug, $this->params['fields'] ) )
	    	{
		        $query->set( 'meta_key', $orderby );
		        $query->set( 'orderby','meta_value' );
	    	}
	    }
	    return $query;
	}

	/**
	 * Prints message if required field missing
	 *
	 * @return void
	 */
	public function show_admin_messages()
	{
		$fields = get_transient( $this->params['id'] . '-required-missing-fields' );
		$missing = get_transient( $this->params['id'] . '-missing-required-fields' );
		$count = count( $fields );

		if ( $missing && is_array( $fields ) )
		{
			// Get labels instead of slugs
			$labels = array();

			foreach( $fields as $slug )
			{
				$labels[] = $this->params['fields'][$slug]['label'];
			}

			echo '<div class="error"><p><strong>';
			if ( $count == 1 )
				echo _e( "The required field in {$this->params['title']} is missing value: " );
			else
				echo _e( "The following required fields in {$this->params['title']} are missing values: " );
			echo implode( ', ', $labels );
			echo '</strong></p></div>';

			delete_transient( $this->params['id'] . '-required-missing-fields' );
			delete_transient( $this->params['id'] . '-missing-required-fields' );
		}
	}

	/**
	 * Supresses the standard 'post published' message in case a required
	 * field is missing
	 */
	public function supress_default_message( $messages )
	{
		$missing_fields = get_transient( $this->params['id'] . '-required-missing-fields' );

		if ( is_array( $missing_fields ) && count( $missing_fields) > 0 )
		{
			$messages['post'][6] = $messages['post'][10];
			$messages['page'][6] = $messages['page'][10];
		}

		return $messages;
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