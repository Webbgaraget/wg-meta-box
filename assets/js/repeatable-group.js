(function(win, doc, $)
{
	"use strict";

	var RepeatableGroup = RepeatableGroup || function($groupContainer)
	{
		// The group container
		this.$groupContainer = $groupContainer;

		// Add button
		this.$button = this.$groupContainer.find('.add-group-button');

		// Current number of groups
		this.currentNumberOfGroups = this.$groupContainer.find('.group-repeatable-section').length;

		// Name of group
		this.groupName = this.$groupContainer.data('label');

		// Group label template
		this.$groupTemplate = this.$groupContainer.find('.group-repeatable-section').first().clone();

		// Remove buttons
		this.$removeButtons = this.$groupContainer.find('.group-remove-button');

		this.init();
	};

	RepeatableGroup.prototype = {
		init : function()
		{
			// Init remove buttons
			this.initRemoveButtons();
			this.initListeners();
		},

		initListeners : function()
		{
			var self = this;
			this.$button.on('click', function()
			{
				self.addGroup();
			});
		},

		initRemoveButtons : function()
		{
			var self = this;

			this.$removeButtons.off('click');

			this.$removeButtons = this.$groupContainer.find('.group-remove-button');

			this.$removeButtons.on('click', function(evt)
			{
				var $this;
				var groupNum;

				$this = $(this);
				groupNum = $this.data('num');
				console.log("Removing " + groupNum);

				evt.preventDefault();
				self.removeGroup(groupNum);
			});

		},

		addGroup : function()
		{
			var $group;

			this.currentNumberOfGroups++;

			$group = this.createGroup();

			this.$button.before($group);

			this.initRemoveButtons();
		},

		removeGroup : function(num)
		{
			// Remove group
			$('#' + this.$groupContainer.attr('name') + '-' + num).parent().remove();

			this.initRemoveButtons();
		},

		createGroup : function()
		{
			var $group;
			var $removeButton;
			var num;
			var groupName;

			$group = this.$groupTemplate.clone();
			groupName = $group.attr('name');
			$removeButton = $group.find('.group-remove-button');

			num = this.currentNumberOfGroups - 1;
			console.log("Adding group " + num);
			$removeButton.data('num', num);

			$group.attr('id' , this.$groupContainer.data('name') + '-' + num);

			// Fix up attributes
			$group.find('fieldset').each(
				function(i, fieldset)
				{
					var $fieldset = $(fieldset);
					var name = $fieldset.attr('name');
					$fieldset.find('textarea, input, select, label').each(
						function(j, element)
						{
							var $element = $(element);

							// Set for attribute on labels, nothing more is needed
							if ( $element.is('label') )
							{
								$element.attr('for', name + '-' + num );
								return;
							}

							// Empty the new fields
							if ( $element.is('textarea') )
							{
								$element.text('');
							}
							else
							{
								$element.attr('value', '');
							}

							// Set id's
							$element.attr('id', name + '-' + num );

							// Set name attributes
							var oldName = $element.attr('name');
							$element.attr('name', oldName.replace(/\[.*?\]/, "[" + num + "]" ));
						}
					);
				}
			);

			return $group;
		},
	};

	// Init for all groups
	$(win).ready(function()
	{
		$('.group-repeatable').each(function(i, group)
		{
			var repeatable = new RepeatableGroup($(group));
		});
	});

})(this, document, jQuery);