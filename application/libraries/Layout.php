<?php
/**
 * @name        CodeIgniter Layout Library
 * @author      Vincent MOULIN
 * @license     MIT License Copyright (c) 2017-2019 Vincent MOULIN
 * @version     4.0.0
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Layout Class
 */
class Layout
{
    private $CI;
    private $template;
    private $title;
    private $charset;
    private $metadata = array();
    private $http_equiv = array();
    private $breadcrumb = array();
    private $content;
    private $css = array();
    private $js = array();
    private $templates_chains_stack = array();
    
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct() {
        $this->CI =& get_instance();

        $default_content_section = $this->CI->config->item('layout_default_content_section');
        $this->content = array($default_content_section => '');
        
        $this->set_template($this->CI->config->item('layout_default_template'));
        $this->set_title($this->CI->config->item('layout_default_title'));
        $this->set_charset($this->CI->config->item('layout_default_charset'));
    }
    
    /******************************************************************************/

    /*
    |-------------------------------------------------------------------------------
    | The getters
    |-------------------------------------------------------------------------------
    */

    public function get_template() {
        return $this->template;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_charset() {
        return $this->charset;
    }

    public function get_metadata() {
        return $this->metadata;
    }

    public function get_http_equiv() {
        return $this->http_equiv;
    }

    public function get_breadcrumb() {
        return $this->breadcrumb;
    }

    public function get_content() {
        return $this->content;
    }

    public function get_content_section($content_section) {
        return $this->content[$content_section];
    }

    public function get_css() {
        return $this->css;
    }

    public function get_js() {
        return $this->js;
    }

    /******************************************************************************/
    
    /*
    |-------------------------------------------------------------------------------
    | The setters
    |-------------------------------------------------------------------------------
    */

    /**
     * Set the template of the page
     *
     * @access public
     * @param $template
     * @return $this or false if the template $template is incorrect
     */
    public function set_template($template) {
        if ( ! $this->is_template($template)) {
            show_error('Layout error: Incorrect template');
        }

        $this->template = $template;
        return $this;
    }

    /**
     * Set the title of the page
     *
     * @access public
     * @param $title
     * @return $this
     */
    public function set_title($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the charset of the page
     *
     * @access public
     * @param $charset
     * @return $this
     */
    public function set_charset($charset) {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Set the metadata whose name is $key with the content $value
     *
     * @access public
     * @param $key
     * @param $value
     * @return $this
     */
    public function set_metadata($key, $value) {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * Unset the metadata whose name is $key
     * If $key is null, all the "name" metadata are unsetted.
     *
     * @access public
     * @param $key
     * @return $this
     */
    public function unset_metadata($key = null) {
        if (is_null($key)) {
            $this->metadata = array();
        } else {
            unset($this->metadata[$key]);
        }

        return $this;
    }

    /**
     * Set the metadata whose http-equiv is $key with the content $value
     *
     * @access public
     * @param $key
     * @param $value
     * @return $this
     */
    public function set_http_equiv($key, $value) {
        $this->http_equiv[$key] = $value;
        return $this;
    }

    /**
     * Unset the metadata whose http-equiv is $key
     * If $key is null, all the "http-equiv" metadata are unsetted.
     *
     * @access public
     * @param $key
     * @return $this
     */
    public function unset_http_equiv($key = null) {
        if (is_null($key)) {
            $this->http_equiv = array();
        } else {
            unset($this->http_equiv[$key]);
        }

        return $this;
    }

    /**
     * Add the breadcrumb item with the label $label and the href $href
     * If $href is null, the breadcrumb item will be a simple text instead of a link.
     * If $position is 'first' (resp. 'last'), the breadcrumb item is added at the beginning (resp. end) of the breadcrumb.
     *
     * @access public
     * @param $label
     * @param $href
     * @param $position ['first'|'last']
     * @return $this
     */
    public function add_breadcrumb_item($label, $href = null, $position = 'last') {
        $breadcrumb_item = array(
            'label'  => $label,
            'href'   => $href,
        );

        if ($position === 'last') {
            array_push($this->breadcrumb, $breadcrumb_item);
        } elseif ($position === 'first') {
            array_unshift($this->breadcrumb, $breadcrumb_item);
        } else {
            show_error('Layout error: Incorrect parameter');
        }

        return $this;
    }

    /**
     * Remove an item of the breadcrumb
     * If $position is 'first' (resp. 'last'), the removed breadcrumb item is the first (resp. last) one.
     *
     * @access public
     * @param $position ['first'|'last']
     * @return $this
     */
    public function remove_breadcrumb_item($position = 'last') {
        if ($position === 'last') {
            array_pop($this->breadcrumb);
        } elseif ($position === 'first') {
            array_shift($this->breadcrumb);
        } else {
            show_error('Layout error: Incorrect parameter');
        }

        return $this;
    }

    /******************************************************************************/

    /*
    |-------------------------------------------------------------------------------
    | The "breadcrumb" section
    |-------------------------------------------------------------------------------
    */

    /**
     * Return the breadcrumb
     *
     * @access public
     * @return The breadcrumb
     */
    public function return_breadcrumb() {
        $retour = $this->CI->config->item('layout_breadcrumb_opening_tag');

        $flat_breadcrumb_items = array();
        foreach ($this->breadcrumb as $breadcrumb_item) {
            $flat_breadcrumb_item = $this->CI->config->item('layout_breadcrumb_item_opening_tag');

            if (is_null($breadcrumb_item['href'])) {
                $flat_breadcrumb_item .= $breadcrumb_item['label'];
            } else {
                $flat_breadcrumb_item .= '<a href="' . $breadcrumb_item['href'] . '">' . $breadcrumb_item['label'] . '</a>';
            }

            $flat_breadcrumb_item .= $this->CI->config->item('layout_breadcrumb_item_closing_tag');

            $flat_breadcrumb_items[] = $flat_breadcrumb_item;
        }

        $retour .= implode($this->CI->config->item('layout_breadcrumb_item_separator'), $flat_breadcrumb_items);

        $retour .= $this->CI->config->item('layout_breadcrumb_closing_tag');

        return $retour;
    }

    /******************************************************************************/

    /*
    |-------------------------------------------------------------------------------
    | The "asset" section
    |-------------------------------------------------------------------------------
    */

    /**
     * Get the absolute href of the asset whose uri is $uri and location is $location
     *
     * @access private
     * @param $uri
     * @param $location ['local'|'remote']
     * @return The absolute href of the asset whose uri is $uri and location is $location
     */
    private function asset_absolute_href($uri, $location) {
        switch ($location) {
            case 'local':
                return base_url() . $this->CI->config->item('layout_web_folder') . '/' . $uri;
            case 'remote':
                return $uri;
            default:
                show_error('Layout error: Incorrect parameter');
        }
    }

    /**
     * Check if the css tags $tags are correctly defined in $this->CI->config->item('layout_css_tags')
     *
     * @access private
     * @param $tags
     * @return void
     */
    private function check_css_tags(array $tags) {
        if ( ! empty(array_diff($tags, $this->CI->config->item('layout_css_tags')))) {
            show_error('Layout error: Unknown tag for css asset');
        }
    }

    /**
     * Check if the javascript tags $tags are correctly defined in $this->CI->config->item('layout_js_tags')
     *
     * @access private
     * @param $tags
     * @return void
     */
    private function check_js_tags(array $tags) {
        if ( ! empty(array_diff($tags, $this->CI->config->item('layout_js_tags')))) {
            show_error('Layout error: Unknown tag for javascript asset');
        }
    }

    /**
     * Complete the css asset data $css with the default values
     *
     * @access private
     * @param $css
     * @return void
     */
    private function complete_css_data(&$css) {
        if (is_string($css)) {
            $css = array(
                'type'  => 'uri',
                'uri'   => $css,
            );
        }

        $reflector = new ReflectionClass(__CLASS__);

        switch ($css['type']) {
            case 'uri':
                $parameters = array('location', 'attributes', 'tags');
                break;
            case 'str':
                $parameters = array('attributes', 'tags');
                break;
            case 'php':
                $parameters = array('args', 'attributes', 'tags');
                break;
            default:
                break;
        }

        foreach ($reflector->getMethod('add_css_' . $css['type'])->getParameters() as $item) {
            if (in_array($item->getName(), $parameters)) {
                if ( ! isset($css[$item->getName()])) {
                    $css[$item->getName()] = $item->getDefaultValue();
                }
            }
        }
    }

    /**
     * Complete the javascript asset data $js with the default values
     *
     * @access private
     * @param $js
     * @return void
     */
    private function complete_js_data(&$js) {
        if (is_string($js)) {
            $js = array(
                'type'  => 'uri',
                'uri'   => $js,
            );
        }

        $reflector = new ReflectionClass(__CLASS__);

        switch ($js['type']) {
            case 'uri':
                $parameters = array('location', 'attributes', 'tags');
                break;
            case 'str':
                $parameters = array('attributes', 'tags');
                break;
            case 'php':
                $parameters = array('args', 'attributes', 'tags');
                break;
            default:
                break;
        }

        foreach ($reflector->getMethod('add_js_' . $js['type'])->getParameters() as $item) {
            if (in_array($item->getName(), $parameters)) {
                if ( ! isset($js[$item->getName()])) {
                    $js[$item->getName()] = $item->getDefaultValue();
                }
            }
        }
    }

    /**
     * Add the css asset $css to the layout
     *
     * @access private
     * @param $css
     * @return $this
     */
    private function add_css(array $css) {
        switch ($css['type']) {
            case 'uri':
                return $this->add_css_uri($css['uri'], $css['location'], $css['attributes'], $css['tags']);
            case 'str':
                return $this->add_css_str($css['content'], $css['attributes'], $css['tags']);
            case 'php':
                return $this->add_css_php($css['callback'], $css['args'], $css['attributes'], $css['tags']);
            default:
                return;
        }
    }

    /**
     * Add the javascript asset $js to the layout
     *
     * @access private
     * @param $js
     * @return $this
     */
    private function add_js(array $js) {
        switch ($js['type']) {
            case 'uri':
                return $this->add_js_uri($js['uri'], $js['location'], $js['attributes'], $js['tags']);
            case 'str':
                return $this->add_js_str($js['content'], $js['attributes'], $js['tags']);
            case 'php':
                return $this->add_js_php($js['callback'], $js['args'], $js['attributes'], $js['tags']);
            default:
                return;
        }
    }

    /**
     * Add a css uri asset to the layout
     *
     * If $location is 'local' then the reference folder is ./{$config['layout_web_folder']}/
     *
     * Example 1:
     * $CI->layout->add_css_uri('css/controllers/Welcome/actions/hello.css');
     *
     * Example 2:
     * $CI->layout->add_css_uri(
     *     'css/controllers/Welcome/actions/hello.css',
     *     'local',
     *     ['media' => 'screen'],
     *     ['tag1', 'tag2']
     * );
     *
     * Example 3:
     * $CI->layout->add_css_uri(
     *     'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
     *     'remote',
     *     array(
     *         'integrity'    => 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u',
     *         'crossorigin'  => 'anonymous',
     *     )
     * );
     *
     * @access public
     * @param $uri
     * @param $location ['local'|'remote']
     * @param $attributes
     * @param $tags
     * @param $return_bool
     * @return $this if $return_bool is false, otherwise true if the css asset has been added successfully and false otherwise
     */
    public function add_css_uri($uri, $location = 'local', array $attributes = [], array $tags = [], bool $return_bool = false) {
        if ( ! in_array($location, array('local', 'remote'))) {
            show_error('Layout error: Incorrect location for css uri asset');
        }

        $this->check_css_tags($tags);

        if (($location == 'local')
            && ( ! file_exists(FCPATH . $this->CI->config->item('layout_web_folder') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uri)))
        ) {
            if ($return_bool) {
                return false;
            } else {
                show_error('Layout error: Incorrect css uri asset');
            }
        }

        $this->css[] = array(
            'type'           => 'uri',
            'absolute_href'  => $this->asset_absolute_href($uri, $location),
            'attributes'     => $attributes,
            'tags'           => $tags,
        );

        if ($return_bool) {
            return true;
        } else {
            return $this;
        }
    }

    /**
     * Add a css string asset to the layout
     *
     * Example:
     * $CI->layout->add_css_str('body {font-size: 14px;}');
     *
     * @access public
     * @param $content Some css code
     * @param $attributes
     * @param $tags
     * @param $return_bool
     * @return $this if $return_bool is false, otherwise true if the css asset has been added successfully and false otherwise
     */
    public function add_css_str($content, array $attributes = [], array $tags = [], bool $return_bool = false) {
        $this->check_css_tags($tags);

        if ( ! is_string($content)) {
            if ($return_bool) {
                return false;
            } else {
                show_error('Layout error: Incorrect css string asset');
            }
        }

        $this->css[] = array(
            'type'        => 'str',
            'content'     => $content,
            'attributes'  => $attributes,
            'tags'        => $tags,
        );

        if ($return_bool) {
            return true;
        } else {
            return $this;
        }
    }

    /**
     * Add a css php asset to the layout
     *
     * Example 1:
     * $CI->layout->add_css_php('my_function');
     *
     * Example 2:
     * $CI->layout->add_css_php(array('My_class', 'my_method'));
     *
     * @access public
     * @param $callback A php function which returns some css code
     * @param $args The arguments used with the php function $callback
     * @param $attributes
     * @param $tags
     * @param $return_bool
     * @return $this if $return_bool is false, otherwise true if the css asset has been added successfully and false otherwise
     */
    public function add_css_php($callback, array $args = [], array $attributes = [], array $tags = [], bool $return_bool = false) {
        $this->check_css_tags($tags);

        if ( ! is_callable($callback)) {
            if ($return_bool) {
                return false;
            } else {
                show_error('Layout error: Incorrect css php asset');
            }
        }

        $this->css[] = array(
            'type'        => 'php',
            'callback'    => $callback,
            'args'        => $args,
            'attributes'  => $attributes,
            'tags'        => $tags,
        );

        if ($return_bool) {
            return true;
        } else {
            return $this;
        }
    }

    /**
     * Add a javascript uri asset to the layout
     *
     * If $location is 'local' then the reference folder is ./{$config['layout_web_folder']}/
     *
     * Example 1:
     * $CI->layout->add_js_uri('js/controllers/Welcome/actions/hello.js');
     *
     * Example 2 (assuming jquery is a 'local' asset):
     * $CI->layout->add_js_uri(
     *     'third_party/jquery/js/jquery.js',
     *     'local',
     *     ['charset' => 'UTF-8'],
     *     ['tag1', 'tag2']
     * );
     *
     * Example 3 (assuming bootstrap is a 'remote' asset):
     * $CI->layout->add_js_uri(
     *     'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
     *     'remote',
     *     array(
     *         'integrity'    => 'sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa',
     *         'crossorigin'  => 'anonymous',
     *         'async'        => true,
     *         'defer'        => false,
     *     )
     * );
     *
     * @access public
     * @param $uri
     * @param $location ['local'|'remote']
     * @param $attributes
     * @param $tags
     * @param $return_bool
     * @return $this if $return_bool is false, otherwise true if the javascript asset has been added successfully and false otherwise
     */
    public function add_js_uri($uri, $location = 'local', array $attributes = [], array $tags = [], bool $return_bool = false) {
        if ( ! in_array($location, array('local', 'remote'))) {
            show_error('Layout error: Incorrect location for javascript uri asset');
        }

        $this->check_js_tags($tags);

        if (($location == 'local')
            && ( ! file_exists(FCPATH . $this->CI->config->item('layout_web_folder') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uri)))
        ) {
            if ($return_bool) {
                return false;
            } else {
                show_error('Layout error: Incorrect javascript uri asset');
            }
        }

        $this->js[] = array(
            'type'           => 'uri',
            'absolute_href'  => $this->asset_absolute_href($uri, $location),
            'attributes'     => $attributes,
            'tags'           => $tags,
        );

        if ($return_bool) {
            return true;
        } else {
            return $this;
        }
    }

    /**
     * Add a javascript string asset to the layout
     *
     * Example:
     * $CI->layout->add_js_str('var refresh_interval = 30;');
     *
     * @access public
     * @param $content Some javascript code
     * @param $attributes
     * @param $tags
     * @param $return_bool
     * @return $this if $return_bool is false, otherwise true if the javascript asset has been added successfully and false otherwise
     */
    public function add_js_str($content, array $attributes = [], array $tags = [], bool $return_bool = false) {
        $this->check_js_tags($tags);

        if ( ! is_string($content)) {
            if ($return_bool) {
                return false;
            } else {
                show_error('Layout error: Incorrect javascript string asset');
            }
        }

        $this->js[] = array(
            'type'        => 'str',
            'content'     => $content,
            'attributes'  => $attributes,
            'tags'        => $tags,
        );

        if ($return_bool) {
            return true;
        } else {
            return $this;
        }
    }

    /**
     * Add a javascript php asset to the layout
     *
     * Example 1:
     * $CI->layout->add_js_php('my_function');
     *
     * Example 2:
     * $CI->layout->add_js_php(array('My_class', 'my_method'));
     *
     * @access public
     * @param $callback A php function which returns some javascript code
     * @param $args The arguments used with the php function $callback
     * @param $attributes
     * @param $tags
     * @param $return_bool
     * @return $this if $return_bool is false, otherwise true if the javascript asset has been added successfully and false otherwise
     */
    public function add_js_php($callback, array $args = [], array $attributes = [], array $tags = [], bool $return_bool = false) {
        $this->check_js_tags($tags);

        if ( ! is_callable($callback)) {
            if ($return_bool) {
                return false;
            } else {
                show_error('Layout error: Incorrect javascript php asset');
            }
        }

        $this->js[] = array(
            'type'        => 'php',
            'callback'    => $callback,
            'args'        => $args,
            'attributes'  => $attributes,
            'tags'        => $tags,
        );

        if ($return_bool) {
            return true;
        } else {
            return $this;
        }
    }

    /**
     * Add the basic css assets that have a tag in common with the list $tags
     * If $tags is null, all the basic css assets are added.
     * You may call this method with a single tag instead of an array.
     * These assets are defined in the variable $config['layout_basic_css'] of the configuration file ./config/config_layout.php
     *
     * @access public
     * @param $tags
     * @return $this
     */
    public function add_basic_css($tags = null) {
        if (( ! is_null($tags))
            && ( ! is_array($tags))
        ) {
            $tags = array($tags);
        }

        foreach ($this->CI->config->item('layout_basic_css') as $css) {
            $this->complete_css_data($css);

            if (is_null($tags)
                || ( ! empty(array_intersect($css['tags'], $tags)))
            ) {
                $result = $this->add_css($css);
                if ($result === false) {
                    show_error('Layout error: Incorrect css asset in basic assets');
                }
            }
        }

        return $this;
    }

    /**
     * Add the basic javascript assets that have a tag in common with the list $tags
     * If $tags is null, all the basic javascript assets are added.
     * You may call this method with a single tag instead of an array.
     * These assets are defined in the variable $config['layout_basic_js'] of the configuration file ./config/config_layout.php
     *
     * @access public
     * @param $tags
     * @return $this
     */
    public function add_basic_js($tags = null) {
        if (( ! is_null($tags))
            && ( ! is_array($tags))
        ) {
            $tags = array($tags);
        }

        foreach ($this->CI->config->item('layout_basic_js') as $js) {
            $this->complete_js_data($js);

            if (is_null($tags)
                || ( ! empty(array_intersect($js['tags'], $tags)))
            ) {
                $result = $this->add_js($js);
                if ($result === false) {
                    show_error('Layout error: Incorrect javascript asset in basic assets');
                }
            }
        }
        
        return $this;
    }

    /**
     * Add the basic css and javascript assets that have a tag in common with the list $tags
     * If $tags is null, all the basic css and javascript assets are added.
     * You may call this method with a single tag instead of an array.
     * These assets are defined in the variables $config['layout_basic_css'] and $config['layout_basic_js'] of the configuration file ./config/config_layout.php
     *
     * @access public
     * @param $tags
     * @return $this
     */
    public function add_basic_assets($tags = null) {
        $this->add_basic_css($tags);
        $this->add_basic_js($tags);
        
        return $this;
    }

    /**
     * Add the basic css assets except those that have a tag in common with the list $tags
     * You may call this method with a single tag instead of an array.
     * These assets are defined in the variable $config['layout_basic_css'] of the configuration file ./config/config_layout.php
     *
     * @access public
     * @param $tags
     * @return $this
     */
    public function add_basic_css_except($tags) {
        if ( ! is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($this->CI->config->item('layout_basic_css') as $css) {
            $this->complete_css_data($css);

            if (empty(array_intersect($css['tags'], $tags))) {
                $result = $this->add_css($css);
                if ($result === false) {
                    show_error('Layout error: Incorrect css asset in basic assets');
                }
            }
        }

        return $this;
    }

    /**
     * Add the basic javascript assets except those that have a tag in common with the list $tags
     * You may call this method with a single tag instead of an array.
     * These assets are defined in the variable $config['layout_basic_js'] of the configuration file ./config/config_layout.php
     *
     * @access public
     * @param $tags
     * @return $this
     */
    public function add_basic_js_except($tags) {
        if ( ! is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($this->CI->config->item('layout_basic_js') as $js) {
            $this->complete_js_data($js);

            if (empty(array_intersect($js['tags'], $tags))) {
                $result = $this->add_js($js);
                if ($result === false) {
                    show_error('Layout error: Incorrect javascript asset in basic assets');
                }
            }
        }

        return $this;
    }

    /**
     * Add the basic css and javascript assets except those that have a tag in common with the list $tags
     * You may call this method with a single tag instead of an array.
     * These assets are defined in the variables $config['layout_basic_css'] and $config['layout_basic_js'] of the configuration file ./config/config_layout.php
     *
     * @access public
     * @param $tags
     * @return $this
     */
    public function add_basic_assets_except($tags) {
        $this->add_basic_css_except($tags);
        $this->add_basic_js_except($tags);
        
        return $this;
    }

    /******************************************************************************/

    /*
    |-------------------------------------------------------------------------------
    | The triggers
    |-------------------------------------------------------------------------------
    */

    /**
     * Insert the css asset $css
     *
     * @access private
     * @param $css
     * @return void
     */
    private function insert_css($css) {
        switch ($css['type']) {
            case 'uri':
                echo '<link rel="stylesheet" type="text/css" href="' . $css['absolute_href'] . '"';
                break;
            case 'str':
                echo '<style type="text/css"';
                break;
            case 'php':
                echo '<style type="text/css"';
                break;
            default:
                break;
        }

        foreach ($css['attributes'] as $attribute_name => $attribute_value) {
            if ($attribute_value === false) {
                continue;
            }

            echo ' ' . $attribute_name;
            if ($attribute_value !== true) {
                echo '="' . $attribute_value . '"';
            }
        }

        switch ($css['type']) {
            case 'uri':
                echo ' />';
                break;
            case 'str':
                echo '>';
                echo $css['content'];
                echo '</style>';
                break;
            case 'php':
                echo '>';
                echo call_user_func_array($css['callback'], $css['args']);
                echo '</style>';
                break;
            default:
                break;
        }
    }

    /**
     * Insert the javascript asset $js
     *
     * @access private
     * @param $js
     * @return void
     */
    private function insert_js($js) {
        switch ($js['type']) {
            case 'uri':
                echo '<script type="text/javascript" src="' . $js['absolute_href'] . '"';
                break;
            case 'str':
                echo '<script type="text/javascript"';
                break;
            case 'php':
                echo '<script type="text/javascript"';
                break;
            default:
                break;
        }

        foreach ($js['attributes'] as $attribute_name => $attribute_value) {
            if ($attribute_value === false) {
                continue;
            }

            echo ' ' . $attribute_name;
            if ($attribute_value !== true) {
                echo '="' . $attribute_value . '"';
            }
        }

        switch ($js['type']) {
            case 'uri':
                echo '></script>';
                break;
            case 'str':
                echo '>';
                echo $js['content'];
                echo '</script>';
                break;
            case 'php':
                echo '>';
                echo call_user_func_array($js['callback'], $js['args']);
                echo '</script>';
                break;
            default:
                break;
        }
    }

    /**
     * Trigger the insertion of the title of the page
     *
     * @access public
     * @return void
     */
    public function trigger_title() {
        echo '<title>' . $this->title . '</title>';
    }

    /**
     * Trigger the insertion of the charset of the page
     *
     * @access public
     * @return void
     */
    public function trigger_charset() {
        echo '<meta charset="' . $this->charset . '" />';
    }

    /**
     * Trigger the insertion of all the "name" metadata
     *
     * @access public
     * @return void
     */
    public function trigger_metadata() {
        foreach ($this->metadata as $key => $value) {
            echo '<meta name="' . $key . '" content="' . $value . '" />';
        }
    }

    /**
     * Trigger the insertion of all the "http-equiv" metadata
     *
     * @access public
     * @return void
     */
    public function trigger_http_equiv() {
        foreach ($this->http_equiv as $key => $value) {
            echo '<meta http-equiv="' . $key . '" content="' . $value . '" />';
        }
    }

    /**
     * Trigger the insertion of the breadcrumb
     *
     * @access public
     * @return void
     */
    public function trigger_breadcrumb() {
        echo $this->return_breadcrumb();
    }

    /**
     * Trigger the insertion of the content section $content_section
     *
     * @access public
     * @param $content_section
     * @return void
     */
    public function trigger_content_section($content_section) {
        echo $this->content[$content_section];
    }

    /**
     * Trigger the insertion of the css assets that have a tag in common with the list $tags
     * If $tags is null, then all the css assets are inserted.
     * You may call this method with a single tag instead of an array.
     *
     * @access public
     * @param $tags
     * @return void
     */
    public function trigger_css($tags = null) {
        if (( ! is_null($tags))
            && ( ! is_array($tags))
        ) {
            $tags = array($tags);
        }

        foreach ($this->css as $css) {
            if (is_null($tags)
                || ( ! empty(array_intersect($css['tags'], $tags)))
            ) {
                $this->insert_css($css);
            }
        }
    }

    /**
     * Trigger the insertion of the javascript assets that have a tag in common with the list $tags
     * If $tags is null, then all the javascript assets are inserted.
     * You may call this method with a single tag instead of an array.
     *
     * @access public
     * @param $tags
     * @return void
     */
    public function trigger_js($tags = null) {
        if (( ! is_null($tags))
            && ( ! is_array($tags))
        ) {
            $tags = array($tags);
        }

        foreach ($this->js as $js) {
            if (is_null($tags)
                || ( ! empty(array_intersect($js['tags'], $tags)))
            ) {
                $this->insert_js($js);
            }
        }
    }

    /**
     * Trigger the insertion of the css assets except those that have a tag in common with the list $tags
     * You may call this method with a single tag instead of an array.
     *
     * @access public
     * @param $tags
     * @return void
     */
    public function trigger_css_except($tags) {
        if ( ! is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($this->css as $css) {
            if (empty(array_intersect($css['tags'], $tags))) {
                $this->insert_css($css);
            }
        }
    }

    /**
     * Trigger the insertion of the javascript assets except those that have a tag in common with the list $tags
     * You may call this method with a single tag instead of an array.
     *
     * @access public
     * @param $tags
     * @return void
     */
    public function trigger_js_except($tags) {
        if ( ! is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($this->js as $js) {
            if (empty(array_intersect($js['tags'], $tags))) {
                $this->insert_js($js);
            }
        }
    }

    /******************************************************************************/

    /*
    |-------------------------------------------------------------------------------
    | The "template" section
    |-------------------------------------------------------------------------------
    */

    /**
     * Check if the template $template is a template
     *
     * @access private
     * @param $template
     * @return true if the template $template is a template and false otherwise
     */
    private function is_template($template) {
        if (is_string($template)
            && ( ! empty($template))
            && (file_exists(APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template))
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the configuration of the template $template
     *
     * @access private
     * @param $template
     * @return The configuration of the template $template
     */
    private function template_config($template) {
        $template_path = APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR;

        if (file_exists($template_path . 'config.yml')) {
            return yaml_parse_file($template_path . 'config.yml');
        } elseif (file_exists($template_path . 'config.php')) {
            include($template_path . 'config.php');
            return $config;
        } else {
            set_status_header(500);
            exit('Layout error: Missing template configuration file');
        }
    }

    /**
     * Include the snippet $snippet
     *
     * @access private
     * @param $snippet A filepath corresponding to a root template or a block
     * @return void
     */
    private function include_snippet($snippet) {
        $CI =& get_instance();

        include($snippet);
    }

    /**
     * Get the current templates chain
     *
     * @access private
     * @return The current templates chain
     */
    private function get_current_templates_chain() {
        if (count($this->templates_chains_stack) == 0) {
            return false;
        }

        $retour = array_slice($this->templates_chains_stack, -1);
        $retour = array_pop($retour);

        return $retour;
    }

    /**
     * Get the current root template
     *
     * @access private
     * @return The current root template
     */
    private function get_current_root_template() {
        $current_templates_chain = $this->get_current_templates_chain();

        if (($current_templates_chain === false)
            || (count($current_templates_chain) == 0)
        ) {
            return false;
        }

        return array_pop($current_templates_chain);
    }



    /**
     * Push the template $template in the current templates chain
     *
     * @access private
     * @param $template
     * @return void
     */
    private function push_templates_chain_item($template) {
        if ( ! $this->is_template($template)) {
            set_status_header(500);
            exit('Layout error: Incorrect templates structure');
        }

        $templates_chain = array_pop($this->templates_chains_stack);
        array_push($templates_chain, $template);
        array_push($this->templates_chains_stack, $templates_chain);

        $template_config = $this->template_config($template);
        if ( ! is_null($template_config['parent_template'])) {
            $this->push_templates_chain_item($template_config['parent_template']);
        }
    }

    /**
     * Push the templates chain of the template $template in the templates chains stack $this->templates_chains_stack
     *
     * @access private
     * @param $template
     * @return void
     */
    private function push_templates_chain($template) {
        array_push($this->templates_chains_stack, array());
        $this->push_templates_chain_item($template);
    }

    /**
     * Include the template $template
     *
     * @access public
     * @param $template
     * @return void
     */
    public function include_template($template) {
        $this->push_templates_chain($template);
        $current_root_template = $this->get_current_root_template();
        $this->include_snippet(APPPATH . 'templates' . DIRECTORY_SEPARATOR . $current_root_template . DIRECTORY_SEPARATOR . $current_root_template . '.php');
        array_pop($this->templates_chains_stack);
    }

    /**
     * Define a block $block which should be implemented in a sub-template (but may also be implemented in the same template or inherited from an ancestor template)
     *
     * @access public
     * @param $block
     * @return void
     */
    public function block($block) {
        foreach ($this->get_current_templates_chain() as $template) {
            $file = APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $block . '.php';

            if (file_exists($file)) {
                $this->include_snippet($file);
                return;
            }
        }
    }

    /******************************************************************************/

    /*
    |-------------------------------------------------------------------------------
    | The "view" section
    |-------------------------------------------------------------------------------
    */

    /**
     * Process the view $view
     *
     * @access private
     * @param $view
     * @param $data An associative array of data used in the view $view
     * @param $autoloaded_assets An array defining the assets which have to be autoloaded (this array may only accept the values: 'css', 'js')
     * @param $is_returned
     *        if true, the output of the view $view is returned
     *        if false, the output of the view $view is loaded in the content section $content_section
     * @param $content_section
     * @return The output of the view $view if $is_returned is true and void otherwise
     */
    private function process_view($view, $data, $autoloaded_assets, $is_returned = true, $content_section = null) {
        if (( ! is_string($view))
            || empty($view)
        ) {
            show_error('Layout error: Incorrect view');
        }

        if ( ! empty(array_diff($autoloaded_assets, array('css', 'js')))) {
            show_error('Layout error: Incorrect parameter');
        }

        if (in_array('css', $autoloaded_assets)) {
            $this->add_css_uri('css/' . $view . '.css', 'local', [], [], true);
        }

        if (in_array('js', $autoloaded_assets)) {
            $this->add_js_uri('js/' . $view . '.js', 'local', [], [], true);
        }

        if ($is_returned) {
            return $this->CI->load->view($view, $data, true);
        } else {
            if ( ! isset($this->content[$content_section])) {
                $this->content[$content_section] = '';
            }
            $this->content[$content_section] .= $this->CI->load->view($view, $data, true);
        }
    }

    /**
     * Load the output of the view $view in the content section $content_section
     *
     * @access public
     * @param $view
     * @param $data An associative array of data used in the view $view
     * @param $content_section
     * @param $autoloaded_assets An array defining the assets which have to be autoloaded (this array may only accept the values: 'css', 'js')
     * @return $this
     */
    public function load_view($view, $data = array(), $content_section = null, $autoloaded_assets = array()) {
        if (is_null($content_section)) {
            $content_section = $this->CI->config->item('layout_default_content_section');
        }

        $this->process_view($view, $data, $autoloaded_assets, false, $content_section);
        return $this;
    }

    /**
     * Return the output of the view $view
     *
     * @access public
     * @param $view
     * @param $data An associative array of data used in the view $view
     * @param $autoloaded_assets An array defining the assets which have to be autoloaded (this array may only accept the values: 'css', 'js')
     * @return The output of the view $view
     */
    public function return_view($view, $data = array(), $autoloaded_assets = array()) {
        return $this->process_view($view, $data, $autoloaded_assets);
    }

    /**
     * Render the view $view with the defined template
     *
     * @access public
     * @param $view
     * @param $data An associative array of data used in the view $view
     * @param $autoloaded_assets An array defining the assets which have to be autoloaded (this array may only accept the values: 'css', 'js')
     * @return void
     */
    public function render_view($view, $data = array(), $autoloaded_assets = array()) {
        $this->load_view($view, $data, $this->CI->config->item('layout_default_content_section'), $autoloaded_assets);

        $this->push_templates_chain($this->template);

        $current_root_template = $this->get_current_root_template();

        $this->CI->load->view('../templates/' . $current_root_template . '/' . $current_root_template, array('CI' => $this->CI));
    }

    /**
     * Render the view corresponding to the controller virtual action with the defined template
     * The application, controller and virtual action css/javascript files are automatically added (provided that the structure of the Layout web folder is compliant with the requirements)
     *
     * @access public
     * @param $virtual_action The name of the virtual action
     * @param $data An associative array of data used in the rendered view
     * @return void
     */
    public function render_virtual_action_view($virtual_action, $data = array()) {
        $directory   = $this->CI->router->fetch_directory();
        $controller  = $this->CI->router->fetch_class();

        $this->add_css_uri('css/app.css', 'local', [], [], true);
        $this->add_css_uri('css/controllers/' . $directory . $controller . '/controller.css', 'local', [], [], true);
        $this->add_css_uri('css/controllers/' . $directory . $controller . '/actions/' . $virtual_action . '.css', 'local', [], [], true);

        $this->add_js_uri('js/app.js', 'local', [], [], true);
        $this->add_js_uri('js/controllers/' . $directory . $controller . '/controller.js', 'local', [], [], true);
        $this->add_js_uri('js/controllers/' . $directory . $controller . '/actions/' . $virtual_action . '.js', 'local', [], [], true);

        $this->render_view('controllers/' . $directory . $controller . '/actions/' . $virtual_action, $data);
    }

    /**
     * Note: This method should be called only in a controller action and if:
     *           - the name of this action matches the name of the view
     *           - the structure of the folder ./application/views is compliant with the requirements
     * Render the view corresponding to the controller action with the defined template
     * The application, controller and action css/javascript files are automatically added (provided that the structure of the Layout web folder is compliant with the requirements)
     *
     * @access public
     * @param $data An associative array of data used in the rendered view
     * @return void
     */
    public function render_action_view($data = array()) {
        $action = $this->CI->router->fetch_method();

        $this->render_virtual_action_view($action, $data);
    }

    /******************************************************************************/
}
