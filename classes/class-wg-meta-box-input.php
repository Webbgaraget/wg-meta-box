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
				'required'    => false,
				'repeatable'  => false,
				'group_repeatable'  => false,
				'repetitions' => array(
					'min' => 1,
					'max' => -1,
				),
			),
			$this->default_properties
		);

		if ( $properties['group_repeatable'] )
		{
			// We don't support a repetable field inside a repeatable section at the moment
			$properties['repeatable'] = false;
		}

		// Do separate merge of admin-column, since array_merge() doesn't handle multi-dimensional arrays
		if ( array_key_exists( 'admin-column', $properties ) )
		{
			$this->default_properties['admin-column'] = array_merge( $this->default_properties['admin-column'], $properties['admin-column'] );
			unset( $properties['admin-column'] );
		}

		// Do separate merge of repetitions column, since array_merge() doesn't handle multi-dimensional arrays
		if ( array_key_exists( 'repetitions', $properties ) )
		{
			$this->default_properties['repetitions'] = array_merge( $this->default_properties['repetitions'], $properties['repetitions'] );
			unset( $properties['repetitions'] );
		}

		$this->properties = array_merge( $this->default_properties, $properties );


		// Verify repeatable only specified for select and input
		if ( !in_array( $this->get_type(), array( 'text', 'select', 'textarea' ) ) && ( $this->_get_max_repetitions() > 1 || $this->_get_min_repetitions > 1 ) )
		{
			throw new Exception( 'Repetition only supported for inputs of type text and select.' );
		}
	}

	/**
	 * Render the field
	 * @return string HTML markup for the field
	 */
	public function render()
	{
		$output      = '';
		$repetitions = $this->_get_repetitions();

		$min_repetitions = $this->_get_min_repetitions();
		$max_repetitions = $this->_get_max_repetitions();

		// Insert fieldset with info about repetitions
		$output .= '<fieldset class="repeatable" name="' . $this->namespace . '-' . $this->properties['slug'] . '" data-label="' . $this->properties['label'] . '" data-max-repetitions="' . $max_repetitions . '" data-min-repetitions="' . $min_repetitions . '">';

		for ( $i = 0; $i < $repetitions; $i++ )
		{
			$this->properties['num'] = $i;
			$output .= $this->render_markup();
		}

		// Add button for repeatable fields
		if ( $this->_get_is_repeatable() )
		{
			$output .= $this->_insert_add_button();
		}

		$output .= '</fieldset>';

		return $output;
	}

	/**
	 * Generates HTML markup for input field
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	abstract protected function render_markup();

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
			$label = $this->properties['label'];

			// In case of repeated field, add field number to label
			if ( $this->properties['repetitions'] > 1 )
			{
				$label .= ' #' . ( $this->properties['num'] + 1 );
			}
			return $label;
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
		return isset( $this->properties['admin-column']['sortable'] ) && $this->properties['admin-column']['sortable'];
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
	 * Get field type
	 * @return String Field type
	 */
	public function get_type()
	{
		return $this->properties['type'];
	}

	/**
	 * Gets the value
	 *
	 * @return void
	 * @author Erik Hedberg (erik@webbgaraget.se)
	 */
	public function get_value()
	{
		$num         = $this->properties['num'];
		$repetitions = $this->get_repetitions();
		$value       = null;

		if ( isset( $this->properties['post'] ) )
		{
			$meta = get_post_meta( $this->properties['post']->ID, "{$this->namespace}-{$this->properties['slug']}", true );
		}
		else
		{
			$meta = '';
		}

		// Retrieve all values
		if ( isset( $this->properties['value'] ) )
		{
			$value = $this->properties['value'];
		}
		elseif ( $meta !== '' )
		{
			$value = $meta;
		}
		else
		{
			$value = null;
		}

		// If only one repetition
		if ( $repetitions === 1 )
		{
			// Are the values stored as an array? Retrieve the first value.
			if ( is_array( $value ) )
			{
				$value = $value[0];
			}
			// Otherwise, the value is already set
		}
		else
		{
			// Does the value exist?
			if ( $value )
			{
				// In the case of the value is not an array, assume the existing value belongs to the first field only.
				if ( !is_array( $value ) )
				{
					if ( $this->properties['num'] !== 0 )
					{
						$value = null;
					}
					else
					{
						$value = $value;
					}
				}
				else
				{
					$value = isset( $value[$this->properties['num']] ) ? $value[$this->properties['num']] : null;
				}
			}
		}
		return $value;
	}

	/**
	 * Retrieve number of repetitions
	 * @return integer
	 */
	public function get_repetitions()
	{
		return $this->properties['repetitions'];
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

	 /**
	  * Get name of input field
	  * @return string name
	  */
	 protected function get_name()
	 {
	 	if ( $this->_get_is_group_repeatable() )
	 	{
	 		$name = $this->namespace . '[][' . $this->properties['slug'] . ']';
	 	}
	 	else
	 	{
			$name = $this->namespace . '-' . $this->properties['slug']. '[]';
	 	}
	 	return $name;
	 }

	 /**
	  * Get input field id
	  * @return string id
	  */
	 protected function get_id()
	 {
		return $this->namespace . '-' . $this->properties['slug']. '-' . $this->properties['num'];
	 }


	/**
	 * Get number of times a field will be repeated
	 * @return integer Number of repetitions
	 */
	protected function _get_repetitions()
	{
		$repetitions = 1;

		$min_repetitions = $this->properties['repetitions']['min'];
		$max_repetitions = $this->properties['repetitions']['max'];

		$repetitions = count( $this->properties['value'] );

		// Are there more repetitions than allowed max?
		if ( $max_repetitions !== -1 && $max_repetitions < $repetitions )
		{
			$repetitions = $max_repetitions;
		}
		// Are there less repetitions than minimum?
		elseif ( $min_repetitions > $repetitions )
		{
			$repetitions = $min_repetitions;
		}

		return $repetitions;
	}

	/**
	 * Get number of max repetitions
	 * @return integer Max repetitions
	 */
	protected function _get_max_repetitions()
	{
		return $this->properties['repetitions']['max'];
	}

	/**
	 * Get number of min repetitions
	 * @return integer Min repetitions
	 */
	protected function _get_min_repetitions()
	{
		return $this->properties['repetitions']['min'];
	}

	/**
	 * Get number of min repetitions
	 * @return integer Min repetitions
	 */
	protected function _get_is_repeatable()
	{
		return $this->properties['repeatable'];
	}


	/**
	 * Get number of min repetitions
	 * @return integer Min repetitions
	 */
	protected function _get_is_group_repeatable()
	{
		return $this->properties['group_repeatable'];
	}


	/**
	 * Insert add button for repeatable fields
	 * @return string Button markup
	 */
	protected function _insert_add_button()
	{
		$output = '';

		$output = '<input type="button" class="button add-field-button" id="' . $this->get_id() . '-add-new" value="' .  __( 'Add new' ) . '">';

		return $output;
	}
}