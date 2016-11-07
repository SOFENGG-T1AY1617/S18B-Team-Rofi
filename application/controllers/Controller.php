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
        date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

        $minuteInterval = $this->student->getMinuteInterval();
        $maxNumberOfSlots = $this->student->getMaxNumberOfSlots();
        $currentHour = date("H");
        $currentMinute = date("i");

        $data['buildings'] = $this->student->queryAllBuildings();
        $data['colleges'] = $this->student->queryColleges();
        $data['types'] = $this->student->queryTypes();
        $data['times_today'] = $this->student->getTimes($currentHour, $currentMinute, $minuteInterval, false);
        $data['times_tomorrow'] = $this->student->getTimes(6, $currentMinute, $minuteInterval, true);

        $data['tab'] = 1; // set to first tab on open
        $data['maxNumberOfSlots'] = $maxNumberOfSlots;

        $this->load->view('template/header'); // include bootstrap 3 header
        $this->load->view('home', $data); // $this->load->view('home', $data); set to this if data is set
        $this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function getRooms() {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),
        );

        $data = $this->student->queryAllRoomsAtBuildingID($getData['buildingid']);
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
        else if (count($getData['slots']) +
            ($numReservations = $this->numReservations($getData['idnumber'])) > MAX_RESERVATIONS) {
            $data = array(
                'status' => 'fail',
                'errors' => $errors,
                'numReservations_status' => 'fail',
                'reserved' => $numReservations,
                'remaining' => MAX_RESERVATIONS - $numReservations,
            );
        }
        else if (count($errors) == 0){ // Add to database
            $slots = $this->parseSlots($getData['slots']);
            $getData['slots'] = $slots;

            $reservations = $this->getSameReservations($slots);

            if (count($reservations) > 0) {
                $data = array(
                    'status' => 'fail',
                    'errors' => $errors,
                    'reservation_status' => 'fail',
                    'sameReservations' => $reservations,
                );
            }
            else {
                $getData['verificationCode'] = $this->getVerificationCode();
                if ($this->sendVerificationEmail($getData['email'], $getData['verificationCode'])) {
                    $this->student->createReservation($getData);
                    $data = array(
                        'status' => 'success',
                        'data' => $getData,
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


            /*$data = array(
                'status' => 'success',
                'data' => $getData,
            );*/
        }

        echo json_encode($data);
    }

    private function numReservations($id) {
        $reservations = $this->student->queryOngoingReservationsByStudentID($id);

        return count($reservations);
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

        if ($getData['collegeid'] == 0) {
            $errors[] = "College";
        }
        if ($getData['typeid'] == 0) {
            $errors[] = "Type";
        }
        if (strlen($getData['email']) < 4) {
            $errors[] = "Email Address";
        }
        else { // Check if valid email address
            $emailArray = explode("@", $getData['email']);
            if ($emailArray[1] != "dlsu.edu.ph") {
                $errors[] = "Email Address";
            }
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
}
