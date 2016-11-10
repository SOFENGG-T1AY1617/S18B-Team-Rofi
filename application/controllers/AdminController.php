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
        $this->initAdmin();
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
        $data['login'] = $this->admin->queryAllModerators();


        $this->load->view('admin/login', $data); // $this->load->view('admin', $data); set to this if data is set
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
        date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

        $minuteInterval = $this->admin->getMinuteInterval();
        $maxNumberOfSlots = $this->admin->getMaxNumberOfSlots();
        $currentHour = date("H");
        $currentMinute = date("i");

        $data['buildings'] = $this->admin->queryAllBuildings();
        $data['colleges'] = $this->admin->queryColleges();
        $data['types'] = $this->admin->queryTypes();
        $data['times_today'] = $this->admin->getTimes($currentHour, $currentMinute, $minuteInterval, $this->student->getMinimumHour(), $this->student->getMaximumHour(), false);
        $data['times_tomorrow'] = $this->admin->getTimes(null, $currentMinute, $minuteInterval, $this->student->getMinimumHour(), $this->student->getMaximumHour(), true);

        $data['maxNumberOfSlots'] = $maxNumberOfSlots;

        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_scheduling', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function ruleView(){
        $this->load->view('admin/a_header'); // include bootstrap 3 header -> included in home
        $this->load->view('admin/a_rules'); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

}

