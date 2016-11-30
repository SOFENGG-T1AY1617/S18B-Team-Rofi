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
    public function index()
    {
        $this->home();
    }

    public function getBusinessRules() { // roomid
        $getData = array(
            'roomid' => $this->input->get('roomid'),
        );

        $data = $this->moderator->queryBusinessRulesByRoomID($getData['roomid']);

        echo json_encode($data);
    }

    public function getTimes () {
        date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

        $getData = array(
            'interval' => $this->input->get('interval'),
        );

        $currentHour = date("H");
        $currentMinute = date("i");

        $times_today = $this->admin->getTimes($currentHour, $currentMinute, $getData['interval'], $this->admin->getMinimumHour(), $this->admin->getMaximumHour(), false);
        $times_tomorrow = $this->admin->getTimes(null, $currentMinute, $getData['interval'], $this->admin->getMinimumHour(), $this->admin->getMaximumHour(), true);

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

    public function getRoomsByBuilding() {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),
        );

        $data = $this->moderator->queryRoomsByBuildingID($getData['buildingid']);

        echo json_encode($data);
    }

    public function getComputers()
    {
        $getData = array(
            'buildingid' => $this->input->get('buildingid'),

            'roomid' => $this->input->get('roomid'),
            'date' => $this->input->get('currdate'),
        );

        //$date = date("Y-m-d", strtotime($getData['date']));
        $date = $getData['date'];

        if ($getData['roomid'] == 0)
            $data = array(
                'computers' => $this->admin->queryAllComputersAtBuildingIDByDepartmentID($getData['buildingid'], $_SESSION['admin_departmentid']),
                'reservations' => $this->admin->queryReservationsAtBuildingIDOnDate($getData['buildingid'], $date),
                'date' => $date,
            );
        else
            $data = array(
                'computers' => $this->admin->queryComputersAtBuildingIDAndRoomID($getData['buildingid'], $getData['roomid']),
                'reservations' => $this->admin->queryReservationsAtRoomIDOnDate($getData['roomid'], $date),
                'date' => $date,
            );
        /*$data = array(
          'result' => $this->student->queryAllRoomsAtBuildingID($getData['buildingid']),
        );*/
        echo json_encode($data);
    }

    public function home()
    {

        //$maxNumberOfSlots = $this->student->getMaxNumberOfSlots();
        $data['buildings'] = $this->moderator->queryAllBuildings();

        $this->load->view('moderator/m_header'); // include bootstrap 3 header
        $this->load->view('moderator/home', $data); // $this->load->view('home', $data); set to this if data is set
        //$this->load->view('m_footer'); // include bootstrap 3 footer
    }
}