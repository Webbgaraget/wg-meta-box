(function(win, doc, $)
{
	"use strict";

	var RepeatableField = RepeatableField || function($fieldset)
	{
		// The fieldset container
		this.$fieldset = $fieldset;

		this.min = this.$fieldset.data('min-repetitions');
		this.max = this.$fieldset.data('max-repetitions');

		// Add button
		this.$button = this.$fieldset.find('.add-field-button');

		// Current number of fields
		this.currentNumberOfFields = this.$fieldset.find('.label').length;

		// Name of field
		this.fieldName = this.$fieldset.data('label');

		// Field label template
		this.$labelTemplate = this.$fieldset.find('.label:first').clone();

		// Field template
		this.$inputTemplate = this.$fieldset.find('.input:first').clone();

		this.init();
	};

	RepeatableField.prototype = {
		init : function()
		{
			// No need to init if the field isn't repeatable
			if (this.min == 1 && this.max == 1)
				return;

			this.initListeners();
			this.checkIfMaxReached();
		},
		initListeners : function()
		{
			var self = this;
			this.$button.on('click', function()
			{
				self.addField();
			});
		},
		addField : function()
		{
			var $label;
			var $field;

			this.currentNumberOfFields++;

			$field = this.createField();
			$label = this.createLabel();
			this.$button.before($label).before($field);
			this.checkIfMaxReached();
		},
		createField : function()
		{
			var $fieldContainer;
			var $field;
			$fieldContainer = this.$inputTemplate.clone();
			$field = $fieldContainer.children();


			$field.attr('id' , this.$fieldset.attr('name') + '-' + (this.currentNumberOfFields - 1));

			if ($field.children().is('textarea'))
			{
				$field.text('');
			}
			else
			{
				$field.attr('value', '');
			}

			return $fieldContainer;
		},
		createLabel : function()
		{
			var $label = this.$labelTemplate;
			$label = this.$labelTemplate.clone();

			$label.find('label')
				.text(this.fieldName + " #" + this.currentNumberOfFields)
				.attr('for', this.$fieldset.attr('name')  + '-' + (this.currentNumberOfFields - 1));
			return $label;
		},
		checkIfMaxReached : function()
		{
			if (this.currentNumberOfFields == this.max)
			{
				this.$button.attr('disabled', 'disabled');
			}
		}
	};

	// Init for all fieldsets
	$(win).ready(function()
	{
		$('.repeatable').each(function(i, fieldset)
		{
			var repeatable = new RepeatableField($(fieldset));
		});
	});

})(this, document, jQuery);