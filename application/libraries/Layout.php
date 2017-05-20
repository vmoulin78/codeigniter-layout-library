<?php
/**
 * @name        CodeIgniter Layout Library
 * @author      Vincent MOULIN
 * @license     MIT License Copyright (c) 2017 Vincent MOULIN
 * @version     2.0.0
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
        
        $this->template  = $this->CI->config->item('layout_default_template');
        $this->title     = $this->CI->config->item('layout_default_title');
        $this->charset   = $this->CI->config->item('layout_default_charset');
        $this->content   = array('main' => '');
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
     * Get the absolute href of the asset whose href is $href and location is $location
     *
     * @access private
     * @param $href
     * @param $location ['local'|'remote']
     * @return The absolute href of the asset whose href is $href and location is $location
     */
    private function asset_absolute_href($href, $location) {
        switch ($location) {
            case 'local':
                return base_url() . $this->CI->config->item('layout_web_folder') . '/' . $href;
            case 'remote':
                return $href;
            default:
                show_error('Layout error: Incorrect parameter');
        }
    }

    /**
     * Return true if there are no duplicate added css assets and false otherwise
     *
     * @access private
     * @return true if there are no duplicate added css assets and false otherwise
     */
    private function check_css_unicity() {
        if (count(array_unique(array_column($this->css, 'absolute_href'))) == count($this->css)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if there are no duplicate added javascript assets and false otherwise
     *
     * @access private
     * @return true if there are no duplicate added javascript assets and false otherwise
     */
    private function check_js_unicity() {
        if (count(array_unique(array_column($this->js, 'absolute_href'))) == count($this->js)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if there are some untriggered css assets remaining and false otherwise
     *
     * @access private
     * @return true if there are some untriggered css assets remaining and false otherwise
     */
    private function untriggered_css_remaining() {
        foreach ($this->css as $css) {
            if ( ! $css['triggered']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if there are some untriggered javascript assets remaining and false otherwise
     *
     * @access private
     * @return true if there are some untriggered javascript assets remaining and false otherwise
     */
    private function untriggered_js_remaining() {
        foreach ($this->js as $js) {
            if ( ! $js['triggered']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add the css asset $css to the layout
     *
     * If $css is a string, it is considered as a 'local' css asset with no additional attributes and no tags.
     * Example: $CI->layout->add_css('css/controllers/Welcome/actions/hello.css');
     *
     * If $css is an array, it must be an associative array and must have one mandatory key 'href'.
     * The optional keys are:
     *     'location'    -> default value: 'local'
     *     'attributes'  -> default value: [] (i.e. no additional attributes)
     *     'tags'        -> default value: [] (i.e. no tags)
     *
     * Example 1:
     * $CI->layout->add_css(array(
     *     'href'        => 'css/controllers/Welcome/actions/hello.css',
     *     'location'    => 'local', // this line may be removed because 'local' is the default value for 'location'
     *     'attributes'  => ['media' => 'screen'],
     *     'tags'        => ['tag1', 'tag2'],
     * ));
     *
     * Example 2:
     * $CI->layout->add_css(array(
     *     'href'        => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
     *     'location'    => 'remote',
     *     'attributes'  => array(
     *         'integrity'    => 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u',
     *         'crossorigin'  => 'anonymous',
     *     ),
     * ));
     *
     * @access public
     * @param $css
     * @return true if the css asset $css has been added successfully and false otherwise
     */
    public function add_css($css) {
        if ( ! is_array($css)) {
            $css = array('href' => $css);
        }

        if (isset($css['href'])
            && is_string($css['href'])
            && ( ! empty($css['href']))
        ) {
            $href = $css['href'];
        } else {
            show_error('Layout error: Incorrect href for css file');
        }

        if ( ! isset($css['location'])) {
            $location = 'local';
        } else {
            $location = $css['location'];
            if ( ! in_array($location, array('local', 'remote'))) {
                show_error('Layout error: Incorrect location for css file');
            }
        }

        if (isset($css['attributes'])) {
            $attributes = $css['attributes'];
        } else {
            $attributes = array();
        }

        if ( ! isset($css['tags'])) {
            $tags = array();
        } else {
            $tags = $css['tags'];
            if ( ! empty(array_diff($tags, $this->CI->config->item('layout_css_tags')))) {
                show_error('Layout error: Unknown tag for css file');
            }
        }

        if (($location === 'local')
            && ( ! file_exists(FCPATH . $this->CI->config->item('layout_web_folder') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $href)))
        ) {
            return false;
        }

        $this->css[] = array(
            'absolute_href'  => $this->asset_absolute_href($href, $location),
            'attributes'     => $attributes,
            'tags'           => $tags,
            'triggered'      => false,
        );

        return true;
    }

    /**
     * Add the javascript asset $js to the layout
     *
     * If $js is a string, it is considered as a 'local' javascript asset with no additional attributes and no tags.
     * Example: $CI->layout->add_js('js/controllers/Welcome/actions/hello.js');
     *
     * If $js is an array, it must be an associative array and must have one mandatory key 'href'.
     * The optional keys are:
     *     'location'    -> default value: 'local'
     *     'attributes'  -> default value: [] (i.e. no additional attributes)
     *     'tags'        -> default value: [] (i.e. no tags)
     *
     * Example 1 (assuming jquery is a 'local' asset):
     * $CI->layout->add_js(array(
     *     'href'        => 'third_party/jquery/js/jquery.js',
     *     'location'    => 'local', // this line may be removed because 'local' is the default value for 'location'
     *     'attributes'  => ['charset' => 'UTF-8'],
     *     'tags'        => ['tag1', 'tag2'],
     * ));
     *
     * Example 2 (assuming bootstrap is a 'remote' asset):
     * $CI->layout->add_js(array(
     *     'href'        => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
     *     'location'    => 'remote',
     *     'attributes'  => array(
     *         'integrity'    => 'sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa',
     *         'crossorigin'  => 'anonymous',
     *     ),
     * ));
     *
     * @access public
     * @param $js
     * @return true if the javascript asset $js has been added successfully and false otherwise
     */
    public function add_js($js) {
        if ( ! is_array($js)) {
            $js = array('href' => $js);
        }

        if (isset($js['href'])
            && is_string($js['href'])
            && ( ! empty($js['href']))
        ) {
            $href = $js['href'];
        } else {
            show_error('Layout error: Incorrect href for javascript file');
        }

        if ( ! isset($js['location'])) {
            $location = 'local';
        } else {
            $location = $js['location'];
            if ( ! in_array($location, array('local', 'remote'))) {
                show_error('Layout error: Incorrect location for javascript file');
            }
        }

        if (isset($js['attributes'])) {
            $attributes = $js['attributes'];
        } else {
            $attributes = array();
        }

        if ( ! isset($js['tags'])) {
            $tags = array();
        } else {
            $tags = $js['tags'];
            if ( ! empty(array_diff($tags, $this->CI->config->item('layout_js_tags')))) {
                show_error('Layout error: Unknown tag for javascript file');
            }
        }

        if (($location === 'local')
            && ( ! file_exists(FCPATH . $this->CI->config->item('layout_web_folder') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $href)))
        ) {
            return false;
        }

        $this->js[] = array(
            'absolute_href'  => $this->asset_absolute_href($href, $location),
            'attributes'     => $attributes,
            'tags'           => $tags,
            'triggered'      => false,
        );

        return true;
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
        if (($tags !== null)
            && ( ! is_array($tags))
        ) {
            $tags = array($tags);
        }

        foreach ($this->CI->config->item('layout_basic_css') as $css) {
            if (is_null($tags)
                || (isset($css['tags']) && ( ! empty(array_intersect($css['tags'], $tags))))
            ) {
                if ($this->add_css($css) === false) {
                    show_error('Layout error: Incorrect css file in basic assets');
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
        if (($tags !== null)
            && ( ! is_array($tags))
        ) {
            $tags = array($tags);
        }

        foreach ($this->CI->config->item('layout_basic_js') as $js) {
            if (is_null($tags)
                || (isset($js['tags']) && ( ! empty(array_intersect($js['tags'], $tags))))
            ) {
                if ($this->add_js($js) === false) {
                    show_error('Layout error: Incorrect javascript file in basic assets');
                }
            }
        }
        
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
            if (( ! isset($css['tags']))
                || (empty(array_intersect($css['tags'], $tags)))
            ) {
                if ($this->add_css($css) === false) {
                    show_error('Layout error: Incorrect css file in basic assets');
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
            if (( ! isset($js['tags']))
                || (empty(array_intersect($js['tags'], $tags)))
            ) {
                if ($this->add_js($js) === false) {
                    show_error('Layout error: Incorrect javascript file in basic assets');
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

    /******************************************************************************/

    /*
    |-------------------------------------------------------------------------------
    | The triggers
    |-------------------------------------------------------------------------------
    */

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
     * Trigger the insertion of the css assets that have not already been 'triggered' and have a tag in common with the list $tags
     * If $tags is null, then all the css assets that have not already been 'triggered' are inserted.
     * You may call this method with a single tag instead of an array.
     * All the inserted css assets are marked as 'triggered' so that they may not be inserted again.
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

        foreach ($this->css as &$css) {
            if (( ! $css['triggered'])
                && (is_null($tags) || ( ! empty(array_intersect($css['tags'], $tags))))
            ) {
                echo '<link rel="stylesheet" type="text/css" href="' . $css['absolute_href'] . '"';
                foreach ($css['attributes'] as $attribute_name => $attribute_value) {
                    echo ' ' . $attribute_name . '="' . $attribute_value . '"';
                }
                echo ' />';

                $css['triggered'] = true;
            }
        }
    }

    /**
     * Trigger the insertion of the javascript assets that have not already been 'triggered' and have a tag in common with the list $tags
     * If $tags is null, then all the javascript assets that have not already been 'triggered' are inserted.
     * You may call this method with a single tag instead of an array.
     * All the inserted javascript assets are marked as 'triggered' so that they may not be inserted again.
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

        foreach ($this->js as &$js) {
            if (( ! $js['triggered'])
                && (is_null($tags) || ( ! empty(array_intersect($js['tags'], $tags))))
            ) {
                echo '<script type="text/javascript" src="' . $js['absolute_href'] . '"';
                foreach ($js['attributes'] as $attribute_name => $attribute_value) {
                    echo ' ' . $attribute_name . '="' . $attribute_value . '"';
                }
                echo '></script>';

                $js['triggered'] = true;
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
            && (file_exists(APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template) || file_exists(APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template . '.php'))
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the template $template is a root template
     *
     * @access private
     * @param $template
     * @return true if the template $template is a root template and false otherwise
     */
    private function is_root_template($template) {
        if (is_string($template)
            && ( ! empty($template))
            && (file_exists(APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template . '.php'))
        ) {
            return true;
        } else {
            return false;
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
     * Get the current root template
     *
     * @access private
     * @return The current root template
     */
    private function get_current_root_template() {
        return end($this->templates_chains_stack[count($this->templates_chains_stack) - 1]);
    }

    /**
     * Get the current templates chain
     *
     * @access private
     * @return The current templates chain
     */
    private function get_current_templates_chain() {
        return end($this->templates_chains_stack);
    }

    /**
     * Push the template $template in the current templates chain
     *
     * @access private
     * @param $template
     * @return void
     */
    private function push_templates_chain_item($template) {
        $CI = $this->CI;

        array_push($this->templates_chains_stack[count($this->templates_chains_stack) - 1], $template);

        if ( ! $this->is_root_template($template)) {
            include(APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $template . '.php');
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
     * The template where this method is called extends the template $template
     *
     * @access public
     * @param $template
     * @return void
     */
    public function extend_template($template) {
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
        $this->include_snippet(APPPATH . 'templates' . DIRECTORY_SEPARATOR . $this->get_current_root_template() . '.php');
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
            if ($this->is_root_template($template)) {
                $file = APPPATH . 'templates' . DIRECTORY_SEPARATOR . $block . '.php';
            } else {
                $file = APPPATH . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $block . '.php';
            }

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
            $this->add_css('css/' . $view . '.css');
        }

        if (in_array('js', $autoloaded_assets)) {
            $this->add_js('js/' . $view . '.js');
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
    public function load_view($view, $data = array(), $content_section = 'main', $autoloaded_assets = array()) {
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
        if ( ! $this->is_template($this->template)) {
            show_error('Layout error: The template does not exist.');
        }

        $this->load_view($view, $data, 'main', $autoloaded_assets);

        if ( ! $this->check_css_unicity()) {
            show_error('Layout error: The added css assets are not unique.');
        }
        if ( ! $this->check_js_unicity()) {
            show_error('Layout error: The added javascript assets are not unique.');
        }

        $this->push_templates_chain($this->template);

        $output = $this->CI->load->view('../templates/' . $this->get_current_root_template(), array('CI' => $this->CI), true);

        if ($this->untriggered_css_remaining()) {
            show_error('Layout error: At least one css asset has been added but not triggered.');
        }
        if ($this->untriggered_js_remaining()) {
            show_error('Layout error: At least one javascript asset has been added but not triggered.');
        }

        echo $output;
    }

    /**
     * Note: This method should be called only in a controller action and if:
     *           - the name of this action matches the name of the view
     *           - the structure of the folder ./application/views is compliant with the requirements
     * Render the view corresponding to the controller action with the defined template
     * The css and javascript files corresponding to the action and those corresponding to the controller that contains this action
     * are automatically added (provided that the structure of the Layout web folder is compliant with the requirements)
     *
     * @access public
     * @param $data An associative array of data used in the rendered view
     * @return void
     */
    public function render_action_view($data = array()) {
        $controller = $this->CI->router->fetch_class();
        $action = $this->CI->router->fetch_method();
        
        $this->add_css('css/controllers/' . $controller . '/controller.css');
        $this->add_css('css/controllers/' . $controller . '/actions/' . $action . '.css');

        $this->add_js('js/controllers/' . $controller . '/controller.js');
        $this->add_js('js/controllers/' . $controller . '/actions/' . $action . '.js');

        $this->render_view('controllers/' . $controller . '/actions/' . $action, $data);
    }

    /******************************************************************************/
}
