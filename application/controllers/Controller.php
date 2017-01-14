<?php header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

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

        $result = array(
            'interval' => intval($data[0]->interval),
            'start_time' => $data[0]->start_time,
            'end_time' => $data[0]->end_time,
            'departmentid' => $data[0]->departmentid,
            'slotlimit' => intval($data[0]->limit)
        );

        echo json_encode($result);
    }

    public function getTimes() {

        $getData = array(
            'interval' => $this->input->get('interval'),
            'starttime' => $this->input->get('start_time'),
            'endtime' => $this->input->get('end_time'),
            'date' => $this->input->get('date')
        );

        if (date('l', strtotime($getData['date'])) != 'Sunday')
            $times = $this->student->getTimes($getData['date'], $getData['interval'], $getData['starttime'], $getData['endtime'], strcmp($getData['date'], date("Y-m-d")) == 0);
        else
            $times = null;

        $data['times'] = null;
        $data['times_DISPLAY'] = null;

        foreach ($times as $time)
            $data['times'][] = date("H:i:s", $time);

        foreach ($times as $time)
            $data['times_DISPLAY'][] = date("h:i A", $time);

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
            'time' => $this->input->get('currtime')
        );

        //$date = date("Y-m-d", strtotime($getData['date']));
        $date = $getData['date'];

        if (strcmp($getData['date'], date("Y-m-d")."") == 0)
            $time = $getData['time'];
        else
            $time = "00:00:00";

        if($getData['roomid']==0)
            $data = array(
                'computers' => $this->student->queryAllComputersAtBuildingID($getData['buildingid']),
                'reservations' => $this->student->queryReservationsAtBuildingIDOnDate($getData['buildingid'], $date),
                'disabledslots' => $this->student->queryDisabledSlotsAtBuildingIDOnDateTime($getData['buildingid'], $date, $time),
                'date' => $date,
            );
        else
            $data = array(
                'computers' => $this->student->queryComputersAtBuildingIDAndRoomID($getData['buildingid'],$getData['roomid']),
                'reservations' => $this->student->queryReservationsAtRoomIDOnDate($getData['roomid'], $date),
                'disabledslots' => $this->student->queryDisabledSlotsAtRoomIDOnDateTime($getData['roomid'], $date, $time),
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
            'userid' => $this->input->get('userid'),
            'collegeid' => $this->input->get('collegeid'),
            'typeid' => $this->input->get('typeid'),
            'email' => $this->input->get('email'),
            'slots' => $this->input->get('slots'),
            'verificationCode' => "",
            'departmentid' => $this->input->get('departmentid')
        );*/

        $reservationData = array(
            'userid' => $this->input->post('userid'),
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
        else if ($this->hasRelativeReservations($reservationData['userid'], $reservationData['slots'])) {

            $data = array(
                'status' => 'fail',
                'errors' => $errors,
                'relativeReservations' => 'fail'
            );

        }
        else if ((count($reservationData['slots']) +
            ($numReservations = $this->numReservations($reservationData['userid']))) > $this->getMaxReservations($reservationData['userid'], $reservationData['departmentid'])) {
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
            $userData = $this->student->getUserData($reservationData['userid']);

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
        if (!$this->student->isValidUserID($reservationData['userid'])) {
            // ID Number was not found
            $errors[] = "UserID";
        }
        else if (!$this->student->isValidUser(array(
            'userid' => $reservationData['userid'],
            'password' => $reservationData['password']))) {
            // ID Number was found but password does not match

            $errors[] = "Password";
        }

        /*// Check if data is valid
        $userid = $reservationData['userid'];
        if (strlen($userid) > 8 || strlen($userid < 8) ||
            substr($userid, 0, 1) != '1') { // Invalid id number
            $errors[] = "ID Number";
        }
        else { // Check if valid id number start
            $year = substr($userid, 1, 2);

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
                'numResult' => $numResult,
                'message' => "Reservation confirmed successfully!",
            );
        }
        else {
            $data = array(
                'result' => "fail",
                'numResult' => $numResult,
                'message' => "Sorry, unable to verify your reservation. Your slot has expired.",
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

    private function hasRelativeReservations($studentid, $slots) {

        $parsedSlots = $this->parseSlots($slots);

        foreach ($parsedSlots as $slot) {

            if ($this->student->hasRelativeReservations($studentid, $slot['date'], $slot['startTime']))
                return true;

        }

        return false;

    }
}
