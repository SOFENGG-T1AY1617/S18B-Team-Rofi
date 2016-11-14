<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 8:11 PM
 */

class Student_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getMinimumHour() { // TODO parameter must be department ID
        return 6; // TODO retrieve value depending on department
    }

    public function getMaximumHour() { // TODO parameter must be department ID
        return 20; // TODO retrieve value depending on department
    }

    public function getTimes($first_hour, $first_minute, $minute_interval, $minimum_hour, $maximum_hour, $tomorrow) {
        $times = array();
        $startMinute = 0;
        $daysForward = 0;

        if ($first_hour < $minimum_hour || $first_hour == null) // set to minimum_hour if first_hour is below the minimum_hour or if first_hour is null
            $first_hour = $minimum_hour;

        if (!$tomorrow && $first_hour != $minimum_hour)
            $startMinute = intval($first_minute / $minute_interval) * $minute_interval; // calculate first_minute to suit current time
        else
            $daysForward++; // plus 1 to day if tomorrow is true

        for ($hour = $first_hour; $hour < $maximum_hour ; $hour++) {
            for ($minute = $startMinute; $minute < 60; $minute += $minute_interval) {

                $time = mktime($hour, $minute, 0, date("m"), date("d") + $daysForward, date("Y"));

                $times[] = $time;

            }

            $startMinute = 0; // reset to 0 to suit the succeeding hours
        }

        $times[] = mktime($hour, 0, 0, date("m"), date("d") + $daysForward, date("Y"));

        return $times;
    }

    function queryRooms() {
        return $this->db->get(TABLE_ROOMS)->result();
    }

    function queryAllComputers() {
        return $this->db->get(TABLE_COMPUTERS)->result();
    }

    function queryComputersAtRoomName($name) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE name = ?) t1";
        return $this->db->query($sql, array($name))->result();
    }

    function queryTypeAtTypeID($id) {
        $this->db->select('*');
        $this->db->from(TABLE_TYPES);
        $this->db->where(COLUMN_TYPEID, $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function queryComputersAtRoomID($id) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE roomid = ?) t1";
        return $this->db->query($sql, array($id))->result();
    }

    function queryAllComputersAtBuildingID($id) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid, name
               FROM rooms
               WHERE buildingid = ?) t1";
        return $this->db->query($sql, array($id))->result();
    }

    function queryComputersAtBuildingIDAndRoomID($bid,$id) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid, name
               FROM rooms
               WHERE roomid = ? AND buildingid = ?) t1";
        return $this->db->query($sql, array($id, $bid))->result();
    }

    function queryAllBuildings() {
        return $this->db->get(TABLE_BUILDINGS)->result();
    }

    function queryAllRoomsAtBuildingID($id) {
        $sql = "SELECT * 
                FROM rooms NATURAL JOIN 
                  (SELECT buildingid
                   FROM buildings
                   WHERE buildingid = ?) b";
        return $this->db->query($sql, array($id))->result();
    }

    function queryAllRoomsAtBuildingName($name) {
        $sql = "SELECT * 
                FROM rooms NATURAL JOIN 
                  (SELECT buildingid
                   FROM buildings
                   WHERE name = ?) b";
        return $this->db->query($sql, array($name))->result();
    }

    function queryColleges() {
        return $this->db->get(TABLE_COLLEGES)->result();
    }

    function queryTypes() {
        return $this->db->get(TABLE_TYPES)->result();
    }

    function queryReservationsAtBuildingIDOnDate($id, $date) {
        $sql = "SELECT *
                FROM rooms r NATURAL JOIN 
                  (SELECT buildingid
                   FROM  buildings
                   WHERE buildingid = ?) b NATURAL JOIN 
                  computers NATURAL JOIN 
                  (SELECT *
                   FROM reservations
                   WHERE date = ?) r";
        return $this->db->query($sql, array($id, $date))->result();
    }

    function queryReservationsAtRoomIDOnDate($id, $date) {
        $sql = "SELECT *
                FROM (SELECT *
                      FROM reservations
                      WHERE date = ?) r NATURAL JOIN
                  computers NATURAL JOIN
                  (SELECT roomid
                   FROM rooms
                   WHERE roomid = ?) ro";
        return $this->db->query($sql, array($date, $id))->result();
    }

    function queryReservationsOfComputerIDOnDate($id, $date) {
        $sql = "SELECT *
                FROM (SELECT *
                      FROM reservations
                      WHERE date = ?) r NATURAL JOIN
                  (SELECT computerid
                   FROM computers
                   WHERE computerid = ?) c";
        return $this->db->query($sql, array($date, $id))->result();
    }

    function queryOngoingReservationsByStudentID($id) {
        $sql = "SELECT * 
                FROM reservations 
                WHERE useridno = ? AND 
                concat_ws(' ', date, start_restime) >= NOW()";
        return $this->db->query($sql, array($id))->result();
    }

    function createReservation($data) {
        $slots = $data['slots'];

        if ($data['collegeid'] != 0) {
            $insertData = array(
                'computerid' => '',
                'useridno' => $data['idnumber'],
                'email' => $data['email'],
                'date' => '',
                'start_restime' => '',
                'end_restime' => '',
                'collegeid' => $data['collegeid'],
                'typeid' => $data['typeid'],
                'verificationcode' => $data['verificationCode'],
            );
        }
        else {
            $insertData = array(
                'computerid' => '',
                'useridno' => $data['idnumber'],
                'email' => $data['email'],
                'date' => '',
                'start_restime' => '',
                'end_restime' => '',
                'typeid' => $data['typeid'],
                'verificationcode' => $data['verificationCode'],
            );
        }

        foreach($slots as $slot) {
            $insertData['computerid'] = $slot['computerid'];
            $insertData['date'] = $slot['date'];
            $insertData['start_restime'] = $slot['startTime'];
            $insertData['end_restime'] = $slot['endTime'];

            $this->db->insert(TABLE_RESERVATIONS, $insertData);
        }

    }

    function isExistingVerificationCode($verificationCode) {
        $result = $this->db->get_where(TABLE_RESERVATIONS, $verificationCode)->result;

        // Check if there is existing row
        return $result->num_rows() > 0;
    }


    function queryReservationsAtSlotOnDate($slot, $date){

    }

    function queryRoomAndCompNoAtComputerID($id){
        $sql = "SELECT name, computerno
                FROM rooms NATURAL JOIN 
                  (SELECT roomid, computerno
                   FROM computers
                   WHERE computerid = ?) b";
        return $this->db->query($sql, array($id))->result();
    }
    function verifyReservation($verificationCode) {
        $sql = "UPDATE reservations SET verified = 1 
                  WHERE verificationcode = ?";
        return $this->db->query($sql, array($verificationCode));
    }

    function querySameReservations($reservations) {
        $this->db->select('*');
        $this->db->from(TABLE_RESERVATIONS);
        $this->db->where_in(COLUMN_DATE, $reservations['date']);
        $this->db->where_in(COLUMN_STARTRESTIME, $reservations['startTime']);
        $this->db->where_in(COLUMN_ENDRESTIME, $reservations['endTime']);
        $query = $this->db->get();
        return $query->result();
    }

    function queryBusinessRulesAtRoomID($id) {
        $sql = "SELECT *
                FROM business_rules NATURAL JOIN (SELECT DISTINCT departmentid
                                                  FROM rooms
                                                  WHERE roomid = ?) d";

        return $this->db->query($sql, array($id))->result();
    }

    function getSlotLimitofStudentID($id) {
        $sql = "SELECT MIN(b.limit) as 'slotLimit'
                FROM (SELECT br.limit
                      FROM business_rules br NATURAL JOIN (SELECT DISTINCT departmentid
                                                           FROM departments NATURAL JOIN (SELECT DISTINCT departmentid
                                                                                       FROM rooms NATURAL JOIN (SELECT DISTINCT roomid 
                                                                                                                FROM computers NATURAL JOIN (SELECT DISTINCT computerid
                                                                                                                                             FROM reservations
                                                                                                                                             WHERE useridno = ? AND 
                                                                                                                                             date >= CURRENT_DATE) res ) c ) ro ) d) b";

        $slotLimit = $this->db->query($sql, array($id))->result();

        return $slotLimit[0]->slotLimit;
    }

    function getSlotLimitOfDepartment($id) {
        $sql = "SELECT br.limit as 'slotLimit'
                FROM business_rules br 
                WHERE departmentid = ?";

        $slotLimit = $this->db->query($sql, array($id))->result();

        return $slotLimit[0]->slotLimit;
    }
}