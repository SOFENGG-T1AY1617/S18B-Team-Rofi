<?php
/**
 * Created by PhpStorm.
 * User: psion
 * Date: 10/26/2016
 * Time: 8:59 PM
 */

header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller
{

    /**
     * Index Page for this controller.
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        if(isset($_SESSION['email'])) {
            $this->initAdmin();
        }
        else {
          $this->loginView();
        }
    }

    private function initAdmin(){

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/home'); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer

    }

    public function addView(){

        $data['buildings'] = $this->admin->queryAllBuildings();

        $data['rooms'] = $this->admin->queryAllRooms();



        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_add', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }
    public function loginView(){
        //$data['login'] = $this->admin->queryAllModerators();

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/login'); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }


    public function modView(){
        $data['moderators'] = $this->admin->queryAllModerators();

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_moderator', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }
    public function adminView(){
        $data['administrators'] = $this->admin->queryAllAdministators();

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_admin', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }
    public function schedulingView(){
        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_scheduling'); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function ruleView(){
        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_rules'); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function signIn() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $admin = $this->admin->queryAdminAccount($email, $password);

        if ($admin != null && count($admin) > 0) {
            // TODO SET SESSIONS
            $this->initAdmin();
            /*$_SESSION['email'] = $email;
            $this->index();*/
        }
        else {
            // TODO ADD ERROR MESSAGE
            $this->loginView();
        }
    }

}

