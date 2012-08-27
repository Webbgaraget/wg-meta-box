# WGMetaBox
`WGMetaBox` is a library for WordPress that facilitates programmatically adding meta boxes to post types. It takes care of rendering the form and saving the values.

* Say good bye to large chunks of code for handling meta boxes
* Say hello to the beauty of easily adding meta boxes programmatically
* Speed up your development phase
* Never ever to bother about the problem why the meta data isn't stored as supposed to
* Feel safe that your user won't change the meta boxes (as with a meta box GUI plugin)

# Example

Here's an example of how a select and a text field are added:

	$fields = array(
	    'favorite-color' => array(
	        'type'    => 'select',
	        'label'   => 'Favorite color',
	        'options' => array(
	            'r' => 'Red',
	            'g' => 'Green',
	            'b' => 'Blue'
	        ),
	        'value'   => 'g'
	    ),
	    'name' => array(
	        'type'        => 'text',
	        'label'       => 'Name',
	        'placeholder' => 'Name'
	        )
	    );
	WGMetaBox::add_meta_box( 'example', 'Example', $fields, 'page' );

# Creating a meta box
The syntax for creating a meta box is very similar to WordPress' native function [``add_meta_box()``](http://codex.wordpress.org/Function_Reference/add_meta_box). It takes the same parameters, except number three.

There's no need to register ``WGMetaBox::add_meta_box()`` with any action hook whatsoever. That is being taken care of by the library.

## Parameters
* **$id** HTML 'id' attribute of the meta box. Will be used for namespacing the input fields. _(required)_

* **$title** Name of the meta box. _(required)_

* **$fields** Array with specification of the input fields. See section about the fields below. _(required)_

* **$post_type** Name of post type to register the meta box with. _(required)_

* **$context** Context _(optional)_

* **$priority** Priority _(optional)_

## Input fields
Currently there are four input fields supported: *text*, *textarea*, *select*, *checkbox*, *rich editor*. Each is specified by the syntax familiar to the WordPress developer. The parameters are made up of an associative array.

### Common arguments
There are some common and some field specific arguments. The common arguments are the following.

* **type** Field type. Corresponds to one of the four supported. _(string, required)_

* **label** Field label. _(string, required)_

* **slug** Combined with the meta box id, the slug constitutes the key for the meta value. _(string, required)_

In the example in the introduction, the select field would be accessible with the key ``example-favorite-color``.

* **class** Additional class(es) of the input field. _(string, optional, default: "")_

* **disabled** Whether the input field is disabled or not. _(boolean, optional, default: false)_

### Text

* **value** Field value. _(string, optional, default: '')_

* **placeholder** Placeholder text. _(string, optional, default: '')_

### Textarea

* **value** Field value. _(string, optional, default: '')_

* **placeholder** Placeholder text. _(string, optional, default: '')_

### Select

**options** Array with the selectable options. The key will be the value and the value the option value. _(array, required)_

* **size** Corresponds to the HTML 'size' attribute _(integer, optional, default: 1)_

* **multiple** Whether it's a multiple select or not. _(boolean, optional, default: false)_

### Checkbox

* **checked** Whether the box is checked or not. _(boolean, optional, default: false)_


### Rich editor
The rich editor corresponds to the WYSIWYG editor used by default in WordPress. Note that the common property _class_ and settings property _editor\_class_ are the same.

* **settings** Settings corresponding to [wp_editor()](http://codex.wordpress.org/Function_Reference/wp_editor) settings. _(array, optional, default: [])__

### Date
Input field with datepicker utilizing jQuery UI's [datepicker](http://jqueryui.com/demos/datepicker/).

* **first\_day** Number of first day of week, where sunday is 0. _(integer, optional, default: 0)_
* **format** Date format according to spec found [here](http://docs.jquery.com/UI/Datepicker/formatDate). _(string, optional, default: 'yy-mm-dd')_

# Accessing the meta values
Since this library only creates the meta box (and handles saving) the meta values are accessed the regular WordPress way, by using ``get_post_meta()``. Each input field's key is a concatenation of the meta box's id, a dash ("-") and the fields slug, i.e. ``example-favorite-color``.


# Changelog
### 2012-08-27 0.2
* Added rich text editor and date field.


### 2012-07-27 Alpha 0.1 release

# License (MIT)

Copyright (c) 2012 Webbgaraget AB http://www.webbgaraget.se/

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
sell copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.