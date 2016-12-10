<?php header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: patricktobias
 * Date: 29/11/2016
 * Time: 2:49 PM
 */
class ModeratorController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    public function index()
    {
        $this->home();
    }

    public function home()
    {
        date_default_timezone_set('Asia/Hong_Kong');

        $this->loadAction("");
    }

    public function loadAction($action) {

        if(!isset($_SESSION['email']) && $action != MODERATOR_SIGN_IN) {
            $this->signInView("");
        } else {
            switch ($action) {

                case MODERATOR_SIGN_IN:
                    $this->signIn();
                    break;
                case MODERATOR_GET_TIMES:
                    $this->getTimes();
                    break;
                case MODERATOR_GET_COMPUTERS:
                    $this->getComputers();
                    break;
                case MODERATOR_SIGN_OUT:
                    $this->signOut();
                    break;
                case MODERATOR_DECODE_SLOTS:
                    $this->decodeSlots();
                    break;
                case MODERATOR_SET_RESERVATIONS_PRESENT:
                    $this->markPresentReservations();
                    break;
                case MODERATOR_VERIFY_RESERVATION:
                    $this->verifyReservations();
                    break;
                default:
                    $this->initModerator();
                    break;

            }
        }

    }

    public function getBusinessRules() { // roomid
        $getData = array(
            'roomid' => $this->input->get('roomid'),
        );

        $data = $this->moderator->queryBusinessRulesByRoomID($getData['roomid']);

        echo json_encode($data);
    }

    public function getTimes () {
        date_default_timezone_set('America/New_York'); // set to Hong Kong's/Philippines' Timezone

        $getData = array(
            'interval' => $this->input->get('interval'),
        );

        $currentHour = date("H");
        $currentMinute = date("i");

        $times_today = $this->admin->getTimes($currentHour, $currentMinute, $getData['interval'], $this->admin->getMinimumHour(), $this->admin->getMaximumHour(), false);

        $data['times_today'] = null;
        $data['times_today_DISPLAY'] = null;

        foreach ($times_today as $time)
            $data['times_today'][] = date("H:i:s", $time);

        foreach ($times_today as $time)
            $data['times_today_DISPLAY'][] = date("h:i A", $time);

        echo json_encode($data);
    }

    public function decodeSlots(){
        $slots = $this->input->get('slots');
        $data = [];


        foreach ($slots as $slot) {
            $arr = explode('_', $slot);

            $roomName = $this->moderator->queryRoomAndCompNoAtComputerID($arr[0]);

            $date = date('M', mktime(0, 0, 0, explode('-',$arr[1])[1], 10))." ".explode('-',$arr[1])[2].", ".explode('-',$arr[1])[0];
            $timeStart = date('h:iA',mktime(explode(':',$arr[2])[0],explode(':',$arr[2])[1]));
            $timeEnd =  date('h:iA',mktime(explode(':',$arr[3])[0],explode(':',$arr[3])[1]));

            $idnumber = $this->moderator->queryUserIDwithReservationID($arr[4]);

            $arr2 = array('id' => $slot,'roomName' => $roomName[0]->name, 'compNo' => $roomName[0]->computerno, 'date' => $date, 'start' => $timeStart, 'end' => $timeEnd, 'userid' => $idnumber);
            array_push($data, $arr2);
        }
        /*$data = array(
          'result' => $this->student->queryAllRoomsAtBuildingID($getData['buildingid']),
        );*/
        echo json_encode($data);
    }

    public function getComputers()
    {
        $getData = array(
            'roomid' => $this->input->get('roomid'),
            'date' => $this->input->get('currdate'),
        );

        $date = $getData['date'];

        $data = array(
            'computers' => $this->moderator->queryComputersAtRoomID($getData['roomid']),
            'reservations' => $this->moderator->queryReservationsAtRoomIDOnDate($getData['roomid'], $date),
            'date' => $date,
        );
        
        echo json_encode($data);
    }

    public function markPresentReservations() {
        $slots = $this->input->get('slots');

        foreach ($slots as $slot) {
            $arr = explode('_', $slot);

            $this->moderator->updateAttendance (1, intval($arr[4]));
        }

        $result = array(
            'result' => "success"
        );

        echo json_encode($result);
    }

    public function verifyReservations() {
        $slots = $this->input->get('slots');

        foreach ($slots as $slot) {
            $arr = explode ('_', $slot);

            $this->moderator->updateVerification(1, intval($arr[4]));
        }

        $result = array(
            'result' => "success"
        );

        echo json_encode($result);
    }

    public function removeReservations() {
        $slots = $this->input->get('slots');

        foreach ($slots as $slot) {
            $arr = explode ('_', $slot);

            $this->moderator->removeReservation(intval($arr[4]));
        }

        $result = array(
            'result' => "success"
        );

        echo json_encode($result);
    }

    public function signIn() {
        $e = $_POST["modEmail"];
        $p = $_POST["modPassword"];
        // = $this->input->post('#adminPassword');



        if ($this->moderator->isValidUser($e,$p)) {
            $moderator = $this->moderator->queryModeratorAccount($e);
            $this->session->set_userdata($moderator);
            $this->index();
        }
        else {
            $errorMessage = "Invalid email or password.";
            $this->signInView($errorMessage);
        }
    }

    public function signOut() {
        $this->session->sess_destroy();
        $this->session->unset_userdata('email');
        $this->index();
    }

    public function signInView($errorMessage){
        //$data['login'] = $this->admin->queryAllModerators();

        $data['errorMessage'] = $errorMessage;

        $this->load->view('moderator/m_header'); // include bootstrap 3 header -> included in home
        $this->load->view('moderator/signIn', $data); // $this->load->view('admin', $data); set to this if data is set
        //$this->load->view('template/footer'); // include bootstrap 3 footer
    }

    public function initModerator() {
        $data['roomid'] = $this->moderator->queryRoomIDwithModeratorID($_SESSION['moderatorid']);

        $this->load->view('moderator/m_header');
        $this->load->view('moderator/home', $data);
    }
}