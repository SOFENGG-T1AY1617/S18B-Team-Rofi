<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once ('/../views/InnerViews/InnerViewList.php');

class Controller extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->home();
	}

	public function home()
    {
        $this->load->model('reservationsystem_model');

        $data['data']['rooms'] = $this->reservationsystem_model->queryRooms();
        //$data['step1'] = $this->load->view('InnerViews/step1', $data, true);

        //$this->load->model('');

        /*
         * PART WHERE YOU RETRIEVE DATA FROM THE DATABASE USING THE MODEL
         * $data['data-name'] = value;
         */



        $this->load->view('template/header'); // include bootstrap 3 header
        $this->load->view('home', $data); // $this->load->view('home', $data); set to this if data is set
        $this->load->view('template/footer'); // include bootstrap 3 footer
    }
}
