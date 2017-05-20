<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Layout configuration
|--------------------------------------------------------------------------
|
| 'layout_web_folder' = The name of the web folder
|
| 'layout_default_template'  = The default template of the page
| 'layout_default_title'     = The default title of the page
| 'layout_default_charset'   = The default charset of the page
|
| 'layout_basic_css'  = The basic css assets
| 'layout_basic_js'   = The basic javascript assets
| 'layout_css_tags'   = The tags that may be used for the css assets
| 'layout_js_tags'    = The tags that may be used for the javascript assets
|
| 'layout_breadcrumb_opening_tag'       = The opening tag for the breadcrumb
| 'layout_breadcrumb_closing_tag'       = The closing tag for the breadcrumb
| 'layout_breadcrumb_item_opening_tag'  = The opening tag for a breadcrumb item
| 'layout_breadcrumb_item_closing_tag'  = The closing tag for a breadcrumb item
| 'layout_breadcrumb_item_separator'    = The item separator for the breadcrumb
|
*/

$config['layout_web_folder'] = 'web';

$config['layout_default_template']  = 'main_template';
$config['layout_default_title']     = 'The default title';
$config['layout_default_charset']   = $this->item('charset');

$config['layout_basic_css']  = array();
$config['layout_basic_js']   = array();
$config['layout_css_tags']   = array();
$config['layout_js_tags']    = array();

$config['layout_breadcrumb_opening_tag']       = '<div id="breadcrumb">';
$config['layout_breadcrumb_closing_tag']       = '</div>';
$config['layout_breadcrumb_item_opening_tag']  = '<span>';
$config['layout_breadcrumb_item_closing_tag']  = '</span>';
$config['layout_breadcrumb_item_separator']    = ' > ';
