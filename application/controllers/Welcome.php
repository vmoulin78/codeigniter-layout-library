<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller
{
    public function index() {
        $this->layout->add_basic_assets()
                     ->render_action_view();
    }

    public function hello($name = 'World') {
        $data['name'] = $name;

        $this->layout->set_template('alternative_template')
                     ->set_title('The hello title')
                     ->set_metadata('description', 'The description')
                     ->set_http_equiv('refresh', 30)
                     ->add_breadcrumb_item('item 1')
                     ->add_breadcrumb_item('item 2', site_url('Welcome/index'))
                     ->add_breadcrumb_item('item 3', site_url('Welcome/hello'))
                     ->add_basic_assets()
                     ->render_action_view($data);
    }

    /**
     * Example for the virtual action view
     * The virtual action is : create_update
     * The create and update view are very similar, so following the DRY concept, we have only one common view create_update.php
     */
    public function create() {
        $data = array(
            // some data
        );

        $this->layout->add_basic_assets()
                     ->render_virtual_action_view('create_update', $data);
    }

    public function update() {
        $data = array(
            // some data
        );

        $this->layout->add_basic_assets()
                     ->render_virtual_action_view('create_update', $data);
    }
}