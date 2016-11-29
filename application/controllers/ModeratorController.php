<?php

/**
 * Created by PhpStorm.
 * User: patricktobias
 * Date: 29/11/2016
 * Time: 2:49 PM
 */
class ModeratorController extends CI_Controller
{
    public function index()
    {
        $this->home();
    }

    public function home()
    {

        //$maxNumberOfSlots = $this->student->getMaxNumberOfSlots();
        //$data['buildings'] = $this->student->queryNonEmptyBuildings();
        //$data['colleges'] = $this->student->queryColleges();
        //$data['types'] = $this->student->queryTypes();
        //$data['email_extensions'] = $this->student->queryEmailExtensions();

        //$data['tab'] = 1; // set to first tab on open

        $this->load->view('moderator/m_header'); // include bootstrap 3 header
        $this->load->view('moderator/home'); // $this->load->view('home', $data); set to this if data is set
        //$this->load->view('m_footer'); // include bootstrap 3 footer
    }
}