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
        $data['times15_today'] = $this->reservationsystem_model->getTimes(6, 15, 0);
        $data['times15_tomorrow'] = $this->reservationsystem_model->getTimes(6, 15, 1);
        $data['times30'] = $this->reservationsystem_model->getTimes(6, 30, 0);

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
            'slots' => $this->input->get('slots'),
            'verificationCode' => "",
        );

        $errors = $this->validateInput($getData);

        if (count($errors) > 0) {
            $data = array(
                'status' => 'fail',
                'errors' => $errors,
            );
        }
        else { // Add to database
            $slots = $this->parseSlots($getData['slots']);
            $getData['slots'] = $slots;
            $getData['verificationCode'] = $this->getVerificationCode();
            //$this->reservationsystem_model->createReservation($getData);

            $data = array(
                'status' => 'success',
                'data' => $getData,
            );
        }

        echo json_encode($data);
    }

    private function validateInput($getData) {
        $errors = [];

        // Check if data is valid
        $idNumber = $getData['idnumber'];
        if (strlen($idNumber) > 8 || strlen($idNumber < 8) ||
            substr($idNumber, 0, 1) != '1') { // Invalid id number
            $errors[] = "ID Number";
        }
        else { // Check if valid id number start
            $year = substr($idNumber, 1, 2);

            if ($year < "08" || $year > date("y") + "") {
                $errors[] = "ID Number";
            }
        }

        if ($getData['collegeid'] == "0") {
            $errors[] = "College";
        }
        if ($getData['typeid'] == 0) {
            $errors[] = "Type";
        }
        if (strlen($getData['email']) < 4) {
            $errors[] = "Email Address";
        }

        return $errors;
    }

    private function parseSlots($slotsString) {
        // "3_2016-10-20_06:45:00_07:00:00"
        $slots = [];

        foreach($slotsString as $slotString) {
            $slotArray = explode("_", $slotString);
            $slot = array(
                'computerid' => $slotArray[0],
                'date' => $slotArray[1],
                'startTime' => $slotArray[2],
                'endTime' => $slotArray[3],
            );

            $slots[] = $slot;
        }

        return $slots;
    }

    public function getVerificationCode() {
        $this->load->helper('string');

        $code = random_string('sha1');

        /*while ($this->isExistingVerificationCode($code)) {
            $code = random_string('sha1');
        }*/

        return $code;
    }

    public function isExistingVerificationCode($verificationCode) {
        return $this->reservationsystem_model->isExistingVerificationCode($verificationCode);
    }
}
