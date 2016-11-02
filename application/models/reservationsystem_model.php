<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 8:11 PM
 */

class ReservationSystem_Model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getTimes($first_hour, $minute_interval, $tomorrow) {
        $times = array();

        for ($hour = $first_hour; $hour < 20 ; $hour++) {
            for ($minute = 0; $minute < 60; $minute += $minute_interval) {

                if ($tomorrow == 1)
                    $time = mktime($hour, $minute, 0, date("m"), date("d") + 1, date("Y"));
                else
                    $time = mktime($hour, $minute, 0, date("m"), date("d"), date("Y"));

                $times[] = $time;

            }
        }

        if ($tomorrow == 1)
            $times[] = mktime($hour, 0, 0, date("m"), date("d") + 1, date("Y"));
        else
            $times[] = mktime($hour, 0, 0, date("m"), date("d"), date("Y"));

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
                  date >= NOW() AND
                  start_restime >= NOW()";
        return $this->db->query($sql, array($id))->result();
    }

    function createReservation($data) {
        $slots = $data['slots'];

        foreach($slots as $slot) {
            $insertData = array(
                'computerid' => $slot['computerid'],
                'useridno' => $data['idnumber'],
                'email' => $data['email'],
                'date' => $slot['date'],
                'start_restime' => $slot['startTime'],
                'end_restime' => $slot['endTime'],
                'collegeid' => $data['collegeid'],
                'typeid' => $data['typeid'],
                'verificationcode' => $data['verificationCode'],
            );

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
}