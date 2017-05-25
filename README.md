# CodeIgniter Layout Library
* **Author** : Vincent MOULIN
* **License** : MIT License Copyright (c) 2017 Vincent MOULIN
* **Version** : 2.1.0

## Installation
1. Copy the file ./application/libraries/Layout.php
2. Copy the file ./application/config/config_layout.php
3. Open the file ./application/config/autoload.php:
   * add 'layout' to the array $autoload['libraries']
   * add 'url' to the array $autoload['helper'] if not already added
   * add 'config_layout' to the array $autoload['config']
4. Create the structure ("Welcome" and "hello" is for example):

    ```text
    ├── application
    │   ├── templates
    │   └── views
    │       └── controllers
    │           └── Welcome
    │               └── actions
    │                   └── hello.php
    │
    └── web
        ├── css
        │   ├── controllers
        │   │   └── Welcome
        │   │       ├── actions
        │   │       │   └── hello.css
        │   │       │
        │   │       └── controller.css
        │   │
        │   └── app.css
        │
        └── js
            ├── controllers
            │   └── Welcome
            │       ├── actions
            │       │   └── hello.js
            │       │
            │       └── controller.js
            │
            └── app.js
    ```
Note: Create the css and javascript files only if needed !  
If you have an app.css or app.js file, you need to add it to the configuration file config_layout.php:

    $config['layout_basic_css'] = array(
        'css/app.css',
    );
    $config['layout_basic_js'] = array(
        'js/app.js',
    );

## Description
### The getters
* **get_template()**
* **get_title()**
* **get_charset()**
* **get_metadata()**
* **get_http_equiv()**
* **get_breadcrumb()**
* **get_content()**
* **get_content_section()**
* **get_css()**
* **get_js()**

### The setters
* **set_template()** : Set the template of the page
* **set_title()** : Set the title of the page
* **set_charset()** : Set the charset of the page
* **set_metadata()** : Set a "name" metadata of the page
* **unset_metadata()** : Unset a "name" metadata or all the "name" metadata
* **set_http_equiv()** : Set a "http-equiv" metadata of the page
* **unset_http_equiv()** : Unset a "http-equiv" metadata or all the "http-equiv" metadata
* **add_breadcrumb_item()** : Add an item to the breadcrumb
* **remove_breadcrumb_item()** : Remove an item from the breadcrumb

### The "breadcrumb" section
* **return_breadcrumb()** : Return the breadcrumb

### The "asset" section
* **add_css()** : Add a css asset to the layout
* **add_js()** : Add a javascript asset to the layout
* **add_basic_css()** : Add the basic css assets according to the given tags or all the basic css assets
* **add_basic_js()** : Add the basic javascript assets according to the given tags or all the basic javascript assets
* **add_basic_assets()** : Add the basic css and javascript assets according to the given tags or all the basic css and javascript assets
* **add_basic_css_except()** : Add the basic css assets except those that have a tag in common with the given tags
* **add_basic_js_except()** : Add the basic javascript assets except those that have a tag in common with the given tags
* **add_basic_assets_except()** : Add the basic css and javascript assets except those that have a tag in common with the given tags

Note: The basic assets are defined in the variables $config['layout_basic_css'] and $config['layout_basic_js'] of the configuration file config_layout.php

Example:

    $config['layout_basic_css'] = array(
        'css/app.css',
        array(
            'href'        => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
            'location'    => 'remote',
            'attributes'  => array(
                'integrity'    => 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u',
                'crossorigin'  => 'anonymous',
            ),
        ),
    );
    $config['layout_basic_js'] = array(
        'js/app.js',
        array(
            'href'        => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
            'location'    => 'remote',
            'attributes'  => array(
                'integrity'    => 'sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa',
                'crossorigin'  => 'anonymous',
            ),
        ),
    );

### The triggers
* **trigger_title()** : Trigger the insertion of the title of the page
* **trigger_charset()** : Trigger the insertion of the charset of the page
* **trigger_metadata()** : Trigger the insertion of all the "name" metadata
* **trigger_http_equiv()** : Trigger the insertion of all the "http-equiv" metadata
* **trigger_breadcrumb()** : Trigger the insertion of the breadcrumb
* **trigger_content_section()** : Trigger the insertion of a content section
* **trigger_css()** : Trigger the insertion of the css assets according to the given tags or all the css assets
* **trigger_js()** : Trigger the insertion of the javascript assets according to the given tags or all the javascript assets
* **trigger_css_except()** : Trigger the insertion of the css assets except those that have a tag in common with the given tags
* **trigger_js_except()** : Trigger the insertion of the javascript assets except those that have a tag in common with the given tags

### The "template" section
* **extend_template()** : Extend a template
* **include_template()** : Include a template
* **block()** : Define a block which should be implemented in a sub-template (but may also be implemented in the same template or inherited from an ancestor template)

Note:
* A root template (i.e. a template that does not extend another template) is a **file** in the templates folder.
* A template that extends another template is a **folder** in the templates folder (this folder must contain a file with the same name plus the extension and this file contains only one line: `<?php $CI->layout->extend_template('*parent_template*'); ?>`).

### The "view" section
* **render_action_view()** : Render a view with the defined template  
The rendered view corresponds to the controller action where this method is called.  
The css and javascript files corresponding to the action and those corresponding to the controller that contains this action are automatically added (provided that the structure of the Layout web folder is compliant with the requirements).  
Note: This is the method that you will probably use most of the time.
* **render_view()** : Render a view with the defined template  
Note: This is the method that you will use if the name of the action does not match the name of the view.
* **load_view()** : Load a view output in a content section  
Example:
   * in the controller: `$this->layout->load_view('widget_view', $data_for_widget_view, 'widget_section');`
   * in the template, you put `<?php $CI->layout->trigger_content_section('widget_section'); ?>` where you want to insert the loaded view "widget_view".
* **return_view()** : Return a view output