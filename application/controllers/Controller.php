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

        //$maxNumberOfSlots = $this->student->getMaxNumberOfSlots();
        $data['buildings'] = $this->student->queryNonEmptyBuildings();
        $data['colleges'] = $this->student->queryColleges();
        $data['types'] = $this->student->queryTypes();
        $data['email_extensions'] = $this->student->queryEmailExtensions();

        $data['tab'] = 1; // set to first tab on open

        $this->load->view('template/header'); // include bootstrap 3 header
        $this->load->view('home', $data); // $this->load->view('home', $data); set to this if data is set
        $this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function getBusinessRules() {
        $getData = array(
            'roomid' => $this->input->get('roomid'),
        );

        $data = $this->student->queryBusinessRulesAtRoomID($getData['roomid']);

        echo json_encode($data);
    }

    public function getTimes() {

        date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

        $getData = array(
            'interval' => $this->input->get('interval'),
        );

        $currentHour = date("H");
        $currentMinute = date("i");

        $times_today = $this->student->getTimes($currentHour, $currentMinute, $getData['interval'], $this->student->getMinimumHour(), $this->student->getMaximumHour(), false);
        $times_tomorrow = $this->student->getTimes(null, $currentMinute, $getData['interval'], $this->student->getMinimumHour(), $this->student->getMaximumHour(), true);

        $data['times_today'] = null;
        $data['times_tomorrow'] = null;
        $data['times_today_DISPLAY'] = null;
        $data['times_tomorrow_DISPLAY'] = null;

        foreach ($times_today as $time)
            $data['times_today'][] = date("H:i:s", $time);

        foreach ($times_tomorrow as $time)
            $data['times_tomorrow'][] = date("H:i:s", $time);

        foreach ($times_today as $time)
            $data['times_today_DISPLAY'][] = date("h:i A", $time);

        foreach ($times_tomorrow as $time)
            $data['times_tomorrow_DISPLAY'][] = date("h:i A", $time);

        echo json_encode($data);

    }

    public function getRooms() {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),
        );

        //$data = $this->student->queryAllRoomsAtBuildingID($getData['buildingid']);
        $data = $this->student->queryNonEmptyRoomsAtBuildingID($getData['buildingid']);
        /*$data = array(
          'result' => $this->student->queryAllRoomsAtBuildingID($getData['buildingid']),
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
                'computers' => $this->student->queryAllComputersAtBuildingID($getData['buildingid']),
                'reservations' => $this->student->queryReservationsAtBuildingIDOnDate($getData['buildingid'], $date),
                'date' => $date,
            );
        else
            $data = array(
                'computers' => $this->student->queryComputersAtBuildingIDAndRoomID($getData['buildingid'],$getData['roomid']),
                'reservations' => $this->student->queryReservationsAtRoomIDOnDate($getData['roomid'], $date),
                'date' => $date,
            );
        /*$data = array(
          'result' => $this->student->queryAllRoomsAtBuildingID($getData['buildingid']),
        );*/
        echo json_encode($data);
    }

    public function checkType() {
        $typeid = $this->input->get('typeid');

        $type = $this->student->queryTypeAtTypeID($typeid);

        echo json_encode($type['type']);
    }

    public function submitReservation() {
        /*$reservationData = array(
            'idnumber' => $this->input->get('idnumber'),
            'collegeid' => $this->input->get('collegeid'),
            'typeid' => $this->input->get('typeid'),
            'email' => $this->input->get('email'),
            'slots' => $this->input->get('slots'),
            'verificationCode' => "",
            'departmentid' => $this->input->get('departmentid')
        );*/

        $reservationData = array(
            'idnumber' => $this->input->post('idnumber'),
            'password' => $this->input->post('password'),
            'slots' => $this->input->post('slots'),
            'verificationCode' => "",
            'departmentid' => $this->input->post('departmentid')
        );

        $errors = $this->validateInput($reservationData);
        if (count($errors) > 0) {
            $data = array(
                'status' => 'fail',
                'errors' => $errors,
            );
        }
        else if ((count($reservationData['slots']) +
            ($numReservations = $this->numReservations($reservationData['idnumber']))) > $this->getMaxReservations($reservationData['idnumber'], $reservationData['departmentid'])) {
            $data = array(
                'status' => 'fail',
                'errors' => $errors,
                'numReservations_status' => 'fail',
                'reserved' => $numReservations,
            );
        }
        else if (count($errors) == 0){ // Add to database
            $slots = $this->parseSlots($reservationData['slots']);
            $reservationData['slots'] = $slots;

            $reservationData['verificationCode'] = $this->getVerificationCode();

            // Get user's email
            $userData = $this->student->getUserData($reservationData['idnumber']);

            if ($this->sendVerificationEmail($userData['email'], $reservationData['verificationCode'])) {
                $this->student->createReservation($reservationData);
                $data = array(
                    'status' => 'success',
                    'data' => $reservationData,
                );
            }
            else {
                $data = array(
                    'status' => 'fail',
                    'errors' => $errors,
                    'email_status' => 'fail',
                );
            }

            /*$reservations = $this->getSameReservations($slots);
            //$reservations = 5;
            if (count($reservations) > 0) {
                $data = array(
                    'status' => 'fail',
                    'errors' => $errors,
                    'reservation_status' => 'fail',
                    'sameReservations_status' => $reservations,
                );
            }
            else {
                $reservationData['verificationCode'] = $this->getVerificationCode();
                if ($this->sendVerificationEmail($reservationData['email'], $reservationData['verificationCode'])) {
                    $this->student->createReservation($reservationData);
                    $data = array(
                        'status' => 'success',
                        'data' => $reservationData,
                    );
                }

                else {
                    $data = array(
                        'status' => 'fail',
                        'errors' => $errors,
                        'email_status' => 'fail',
                    );
                }
                $data = array(
                    'status' => 'success',
                    'data' => $reservationData,
                );
            }*/
        }

        echo json_encode($data);
    }

    private function numReservations($id) {
        $reservations = $this->student->queryOngoingReservationsByStudentID($id);
        //return 1;
        return count($reservations);
    }

    private function validateInput($reservationData) {
        $errors = [];

        // Check if id number exists
        if (!$this->student->isValidIDNumber($reservationData['idnumber'])) {
            // ID Number was not found
            $errors[] = "Username";
        }
        else if (!$this->student->isValidUser(array(
            'idnumber' => $reservationData['idnumber'],
            'password' => $reservationData['password']))) {
            // ID Number was found but password does not match

            $errors = "Password";
        }

        /*// Check if data is valid
        $idNumber = $reservationData['idnumber'];
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
        if ($reservationData['typeid'] == 0) {
            $errors[] = "Type";
        }
        else {
            $type = $this->student->queryTypeAtTypeID($reservationData['typeid']);

            if (strpos(strtolower($type['type']), 'graduate') !== false && $reservationData['collegeid'] == 0) {
                $errors[] = "College";
            }
        }

        if (count(explode("@", $reservationData['email'])) <= 1) {
            $errors[] = "Email Address";
        }
        else { // Check if valid email address
            $emailArray = explode("@", $reservationData['email']);
            if (strlen($emailArray[0]) < 4) {
                $errors[] = "Email Address";
            }
        }*/

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
        return $this->student->isExistingVerificationCode($verificationCode);
    }

    public function getMyReservations(){
        $slots = $this->input->get('slots');
        $data = [];


        foreach ($slots as $slot) {
            $arr = explode('_', $slot);

            $roomName = $this->student->queryRoomAndCompNoAtComputerID($arr[0]);

            $date = date('M', mktime(0, 0, 0, explode('-',$arr[1])[1], 10))." ".explode('-',$arr[1])[2].", ".explode('-',$arr[1])[0];
            $timeStart = date('h:iA',mktime(explode(':',$arr[2])[0],explode(':',$arr[2])[1]));
            $timeEnd =  date('h:iA',mktime(explode(':',$arr[3])[0],explode(':',$arr[3])[1]));//;

            $arr2 = array('id' => $slot,'roomName' => $roomName[0]->name, 'compNo' => $roomName[0]->computerno, 'date' => $date, 'start' => $timeStart, 'end' => $timeEnd);
            array_push($data, $arr2);
        }
        /*$data = array(
          'result' => $this->student->queryAllRoomsAtBuildingID($getData['buildingid']),
        );*/
        echo json_encode($data);
    }

    public function verify($verificationCode = NULL) {
        $numResult = $this->student->verifyReservation($verificationCode);
        if ($numResult > 0) {
            $data = array(
                'result' => "success",
                'message' => "Reservation confirmed successfully!",
            );
        }
        else {
            $data = array(
                'result' => "fail",
                'message' => "Sorry, unable to verify your email.",
            );
        }
        //echo json_encode($data);
        $this->load->view('template/header'); // include bootstrap 3 header
        $this->load->view('verification', $data); // $this->load->view('home', $data); set to this if data is set
        $this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function sendVerificationEmail($email, $verificationCode) {
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => 465,
            'smtp_user' => 'dlsu.pc.reservation@gmail.com', // Email
            'smtp_pass' => 'DLSU1234!',
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE,
        );
        $url = site_url("verify")."/".$verificationCode;
        $url = preg_replace(
            "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
            "<a href=\"\\0\">\\0</a>",
            $url);

        $message = "Dear User,<br/><br/>
            Please click on the URL below or paste it into your browser to verify your reservation:<br/>". $url .
            "<br/><br/>Thanks,
            <br/>DLSU Admin";


        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $config['mailtype'] = 'html';
        $this->email->from('dlsu.pc.reservation@gmail.com', "DLSU PC Reservation");
        $this->email->to($email);
        $this->email->subject("Confirm your reservation");
        $this->email->message($message);
        //$this->email->send();
        if($this->email->send())
        {
            return true;
        }
        else
        {
            show_error($this->email->print_debugger());
            return false;
        }
    }

    private function getSameReservations($slots) {
        $sameReservations = $this->student->querySameReservations($slots);
        $reservations = [];
        while ($data = mysqli_fetch_array($sameReservations)) {
            $reservation = array(
                'date' => $data['date'],
                'startTime' => $data['startTime'],
                'endTime' => $data['endTime'],
            );
            $reservations[] = $reservation;
        }

        return $reservations;
    }

    private function getMaxReservations($studentid, $departmentid) {
        $max_reservations = $this->student->getSlotLimitofStudentID($studentid);

        if ($max_reservations > 0)
            return intval($max_reservations);
        else {
            $defaultLimit = $this->student->getSlotLimitOfDepartment($departmentid);

            return intval($defaultLimit);
        }
    }
}
