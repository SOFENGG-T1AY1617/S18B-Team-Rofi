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

    public function getTimes($date, $minute_interval, $minimum_time, $maximum_time, $adaptToCurrentTime) {
        $times = array();
        $startMinute = 0;
        $daysForward = 0;

        $dateArray = explode("-", $date); // Y-m-d

        $minTimeArray = explode(":", $minimum_time);
        $maxTimeArray = explode(":", $maximum_time);

        $minimum_hour = null;
        $minimum_minute = null;
        $maximum_hour = null;
        $maximum_minute = null;

        if ($adaptToCurrentTime) {

            if (intval(date("H")) < intval($minTimeArray[0])) {
                $minimum_hour = $minTimeArray[0];
                $minimum_minute = $minTimeArray[1];
            } else {
                $minimum_hour = intval(date("H"));
                $minimum_minute = intval(date("i"));
            }

        } else {

            $minimum_hour = $minTimeArray[0];
            $minimum_minute = $minTimeArray[1];

        }

        $maximum_hour = $maxTimeArray[0];
        $maximum_minute = $maxTimeArray[1];

        if ($adaptToCurrentTime)
            $startMinute = intval($minimum_minute / $minute_interval) * $minute_interval; // calculate first_minute to suit current time

        for ($hour = $minimum_hour; $hour <= $maximum_hour; $hour++) {
            for ($minute = $startMinute; $minute < 60; $minute += $minute_interval) {

                if ($hour == $maximum_hour) {

                    if ($minute <= $maximum_minute)
                        $times[] = mktime($hour, $minute, 0, $dateArray[1], $dateArray[2] + $daysForward, $dateArray[0]);

                } else {

                    $times[] = mktime($hour, $minute, 0, $dateArray[1], $dateArray[2] + $daysForward, $dateArray[0]);

                }

            }

            $startMinute = 0; // reset to 0 to suit the succeeding hours
        }

        $times[] = mktime($hour, 0, 0, $dateArray[1], $dateArray[2] + $daysForward, $dateArray[0]);

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

    function queryNonEmptyBuildings() {
        $sql = "SELECT b.buildingid, b.name
                FROM buildings b INNER JOIN rooms r 
                  ON b.buildingid = r.buildingid
                GROUP BY b.buildingid
                HAVING COUNT(roomid) > 0";
        return $this->db->query($sql)->result();

    }

    function queryAllRoomsAtBuildingID($id) {
        $sql = "SELECT * 
                FROM rooms NATURAL JOIN 
                  (SELECT buildingid
                   FROM buildings
                   WHERE buildingid = ?) b";
        return $this->db->query($sql, array($id))->result();
    }

    function queryNonEmptyRoomsAtBuildingID($id) {
        $sql = "SELECT r.roomid, r.name
                FROM rooms r NATURAL JOIN 
                  (SELECT buildingid
                   FROM buildings
                   WHERE buildingid = ?) b NATURAL JOIN computers
                GROUP BY r.roomid
                HAVING COUNT(computerid) > 0";
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

    function hasRelativeReservations($userid, $date, $start) {

        $sql = "SELECT *
                FROM reservations
                WHERE userid = ? AND date = ? AND start_restime = ?";

        $result = $this->db->query($sql, array($userid, $date, $start))->result();

        return count($result) > 0;

    }

    function queryOngoingReservationsByStudentID($id) {
        $sql = "SELECT * 
                FROM reservations 
                WHERE userid = ? AND 
                concat_ws(' ', date, start_restime) >= NOW()";
        return $this->db->query($sql, array($id))->result();
    }

    function createReservation($data) {
        $slots = $data['slots'];

        /*if ($data['collegeid'] != 0) {
            $insertData = array(
                'computerid' => '',
                'userid' => $data['idnumber'],
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
                'userid' => $data['idnumber'],
                'email' => $data['email'],
                'date' => '',
                'start_restime' => '',
                'end_restime' => '',
                'typeid' => $data['typeid'],
                'verificationcode' => $data['verificationCode'],
            );
        }*/

        $insertData = array(
            'computerid' => '',
            'userid' => $data['userid'],
            'date' => '',
            'start_restime' => '',
            'end_restime' => '',
            'verificationcode' => $data['verificationCode'],
        );

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
        $this->db->query($sql, array($verificationCode));
        return $this->db->affected_rows();
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
                                                                                                                                             WHERE userid = ? AND 
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

    function queryDisabledSlotsAtRoomIDOnDateTime($id, $date, $time) {
        $sql = "SELECT *
                FROM (SELECT * 
                      FROM disabled_slots
                      WHERE (STR_TO_DATE(?, '%Y-%m-%d') = CAST(date_time_duration AS DATE)) AND (STR_TO_DATE(?, '%H:%i:%s') <= CAST(date_time_duration AS TIME))) d NATURAL JOIN 
                      computers NATURAL JOIN 
                      (SELECT roomid
                      FROM rooms
                      WHERE roomid = ?) ro";

        return $this->db->query($sql, array($date, $time, $id))->result();
    }

    function queryDisabledSlotsAtBuildingIDOnDateTime($id, $date, $time) {

        $sql = "SELECT *
                FROM rooms r NATURAL JOIN 
                  (SELECT buildingid
                   FROM  buildings
                   WHERE buildingid = ?) b NATURAL JOIN 
                  computers NATURAL JOIN 
                  (SELECT * 
                      FROM disabled_slots
                      WHERE (STR_TO_DATE(?, '%Y-%m-%d') = CAST(date_time_duration AS DATE)) AND (STR_TO_DATE(?, '%H:%i:%s') <= CAST(date_time_duration AS TIME))) d";

        return $this->db->query($sql, array($id, $date, $time))->result();
    }

    function isValidUserID($userid) {
        $result = $this->db->get_where(TABLE_USERS, array('userid' => $userid));

        return $result->num_rows() == 1;
    }

    function isValidUser($userData) {
        $result = $this->db->get_where(TABLE_USERS, $userData);

        return $result->num_rows() == 1;
    }

    function getUserData($userid) {
        $result = $this->db->get_where(TABLE_USERS, array('userid' => $userid));

        return $result->row_array();
    }
}