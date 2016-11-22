<?php

/**
 * Created by PhpStorm.
 * User: psion
 * Date: 11/22/2016
 * Time: 3:52 PM
 */
class SuperuserController extends CI_Controller 
{

    /**
     * Index Page for this controller.
     * @see https://codeigniter.com/user_guide/general/urls.html
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    public function index()
    {
        /*if(isset($_SESSION['email'])) {
            $this->initAdmin();
        }
        else {
          $this->signInView("");
        }*/
        $this->loadView("");
    }

    private function initSuperuser(){

        $this->load->view('superuser/su_header'); // include bootstrap 3 header -> included in home
        $this->load->view('superuser/su_index'); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer

    }

    public function loadView($viewName) {

            switch ($viewName) {

                default:
                    $this->initSuperuser();
            }

    }
}