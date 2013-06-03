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

		// Remove buttons
		this.$removeButtons = this.$fieldset.find('.field-remove-button');

		this.init();
	};

	RepeatableField.prototype = {
		init : function()
		{
			// No need to init if the field isn't repeatable
			if (this.min == 1 && this.max == 1)
				return;

			// Init remove buttons
			this.initRemoveButtons();

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
		initRemoveButtons : function()
		{
			var self = this;

			this.$removeButtons.off('click');

			this.$removeButtons = this.$fieldset.find('.field-remove-button');

			this.$removeButtons.on('click', function(evt)
			{
				var $this;
				var fieldNum;

				$this = $(this);
				fieldNum = $this.data('num');
				console.log("Removing " + fieldNum);

				evt.preventDefault();
				self.removeField(fieldNum);
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
			this.redefineLabels();
			this.checkIfMaxReached();
			this.initRemoveButtons();
		},
		removeField : function(num)
		{
			// Remove field
			$('#' + this.$fieldset.attr('name') + '-' + num).parent().remove();

			// Remove label
			$('label[for="' + this.$fieldset.attr('name') + '-' + num + '"]').parent().remove();

			this.checkIfMaxReached();
			this.redefineLabels();
			this.initRemoveButtons();
		},
		createField : function()
		{
			var $fieldContainer;
			var $field;
			var $removeButton;
			var num;

			$fieldContainer = this.$inputTemplate.clone();
			$field = $fieldContainer.children().not('.field-remove-button');
			$removeButton = $fieldContainer.find('.field-remove-button');

			num = this.currentNumberOfFields - 1;
			console.log("Adding " + num);
			$removeButton.data('num', num);

			$field.attr('id' , this.$fieldset.attr('name') + '-' + num);

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
			if (this.$fieldset.find('label').length == this.max)
			{
				this.$button.attr('disabled', 'disabled');
			}
			else
			{
				this.$button.removeAttr('disabled');
			}
		},
		redefineLabels : function()
		{
			var self = this;
			var $labels;
			$labels = this.$fieldset.find('label');
			$labels.each(function(i, label)
			{
				var $label;
				$label = $(label);
				$label.text(self.fieldName + " #" + (i+1));
				self.currentNumberOfFields = i + 1;
			});
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