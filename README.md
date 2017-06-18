# CodeIgniter Layout Library
* **Author** : Vincent MOULIN
* **License** : MIT License Copyright (c) 2017 Vincent MOULIN
* **Version** : 3.3.0

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
* **add_css_uri()** : Add a css uri asset to the layout
* **add_css_str()** : Add a css string asset to the layout
* **add_css_php()** : Add a css php asset to the layout
* **add_js_uri()** : Add a javascript uri asset to the layout  
Example: `$CI->layout->add_js_uri('js/app.js');`
* **add_js_str()** : Add a javascript string asset to the layout  
Example: `$CI->layout->add_js_str("var base_url = '" . $CI->config->item('base_url') . "';");`
* **add_js_php()** : Add a javascript php asset to the layout  
Example: `$CI->layout->add_js_php(['My_class', 'my_method']);`  
`My_class::my_method()` is a php function which returns some javascript code.
* **add_basic_css()** : Add the basic css assets according to the given tags or all the basic css assets
* **add_basic_js()** : Add the basic javascript assets according to the given tags or all the basic javascript assets
* **add_basic_assets()** : Add the basic css and javascript assets according to the given tags or all the basic css and javascript assets
* **add_basic_css_except()** : Add the basic css assets except those that have a tag in common with the given tags
* **add_basic_js_except()** : Add the basic javascript assets except those that have a tag in common with the given tags
* **add_basic_assets_except()** : Add the basic css and javascript assets except those that have a tag in common with the given tags

Note: The basic assets are defined in the variables $config['layout_basic_css'] and $config['layout_basic_js'] of the configuration file config_layout.php  
Note: If a basic asset is a string, it is considered as a local uri asset with no attributes and no tags.

Example:

    $config['layout_basic_css'] = array(
        'css/app.css',
        array(
            'type'        => 'uri',
            'uri'         => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
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
            'type'        => 'uri',
            'uri'         => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
            'location'    => 'remote',
            'attributes'  => array(
                'integrity'    => 'sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa',
                'crossorigin'  => 'anonymous',
            ),
        ),
        array(
            'type'     => 'str',
            'content'  => '/* some javascript code */',
        ),
        array(
            'type'      => 'php',
            'callback'  => 'my_function',
        ),
        array(
            'type'      => 'php',
            'callback'  => ['My_class', 'my_method'],
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
* **include_template()** : Include a template
* **block()** : Define a block which should be implemented in a sub-template (but may also be implemented in the same template or inherited from an ancestor template)

Note:
* A template is a folder in the `./application/templates` folder.
* Each template folder contains a PHP or YAML configuration file (`config.php` or `config.yml`).
* This configuration file contains only one data: `parent_template` whose value is null or the name of the parent template.
* For more details, see the examples in the `./application/templates` folder.

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