<?php header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

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
        //$this->load->model('reservationsystem_model');

        $data['buildings'] = $this->reservationsystem_model->queryAllBuildings();
        $data['colleges'] = $this->reservationsystem_model->queryColleges();
        $data['types'] = $this->reservationsystem_model->queryTypes();
        $data['times15'] = $this->reservationsystem_model->getTimes(6, 15);
        $data['times30'] = $this->reservationsystem_model->getTimes(6, 30);

        $data['tab'] = 1;

        $this->load->view('template/header'); // include bootstrap 3 header
        $this->load->view('home', $data); // $this->load->view('home', $data); set to this if data is set
        $this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function getRooms() {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),
        );

        $data = $this->reservationsystem_model->queryAllRoomsAtBuildingID($getData['buildingid']);
        /*$data = array(
          'result' => $this->reservationsystem_model->queryAllRoomsAtBuildingID($getData['buildingid']),
        );*/
        echo json_encode($data);
    }


    public function getComputers() {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),

            'roomid' => $this->input->get('roomid'),
            'date' => $this->input->get('currdate'),
        );

        //$date = date("Y-m-d", strtotime($getData['date']));
        $date = $getData['date'];

        if($getData['roomid']==0)
            $data = array(
                'computers' => $this->reservationsystem_model->queryAllComputersAtBuildingID($getData['buildingid']),
                'reservations' => $this->reservationsystem_model->queryReservationsAtBuildingIDOnDate($getData['buildingid'], $date),
                'date' => $date,
            );
        else
            $data = array(
                'computers' => $this->reservationsystem_model->queryComputersAtBuildingIDAndRoomID($getData['buildingid'],$getData['roomid']),
                'reservations' => $this->reservationsystem_model->queryReservationsAtRoomIDOnDate($getData['roomid'], $date),
                'date' => $date,
            );
        /*$data = array(
          'result' => $this->reservationsystem_model->queryAllRoomsAtBuildingID($getData['buildingid']),
        );*/
        echo json_encode($data);
    }

    public function submitReservation() {
        $getData = array(
            'idnumber' => $this->input->get('idnumber'),
            'collegeid' => $this->input->get('collegeid'),
            'typeid' => $this->input->get('typeid'),
            'email' => $this->input->get('email'),
            'date' => $this->input->get('date'),
            'slots' => $this->input->get('slots'),
        );

        $errors = [];
        $data = array();

        // Check if data is valid
        $idNumber = $getData['idnumber'];
        if (strlen($idNumber) > 8 || strlen($idNumber < 8) ||
            substr($idNumber, 0, 1) != '1') { // Invalid id number
            $errors[] = "ID Number";
        }
        else { // Check if valid id number start
            $year = substr($idNumber, 0, 3);
        }


        if (count($errors) > 0) {
            $data = array(
                'status' => 'fail',
                'errors' => $errors,
            );
        }
        else {
            $data = array(
                'status' => 'success',
            );
        }

        echo json_encode($data);
    }

    public function isExistingVerificationCode($verificationCode) {
        return $this->reservationsystem_model->isExistingVerificationCode($verificationCode);
    }
}
