<?php

/**
 * Created by PhpStorm.
 * User: patricktobias
 * Date: 29/11/2016
 * Time: 2:49 PM
 */
class moderator_model extends CI_Model
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

    function queryRoomAndCompNoAtComputerID($id){
        $sql = "SELECT name, computerno
                FROM rooms NATURAL JOIN 
                  (SELECT roomid, computerno
                   FROM computers
                   WHERE computerid = ?) b";
        return $this->db->query($sql, array($id))->result();
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
              (SELECT roomid, name
               FROM rooms
               WHERE roomid = ?) t1";
        return $this->db->query($sql, array($id))->result();
    }

    function queryAllBusinessRules() {
        $this->db->select('*');
        $this->db->from(TABLE_BUSINESS_RULES);
        $this->db->join(TABLE_DEPARTMENTS, 'business_rules.departmentid = departments.departmentid');
        $query = $this->db->get();
        return $query->result();
    }

    function queryBusinessRulesByDepartmentID($id) {
        $sql = "SELECT *
                FROM business_rules NATURAL JOIN
                  (SELECT *
                   FROM departments
                   WHERE departmentid = ?) d";
        return $this->db->query($sql, array($id))->result();
    }

    function queryBusinessRulesByRoomID($id) {
        $sql = "SELECT *
                FROM business_rules NATURAL JOIN (SELECT DISTINCT departmentid
                                                  FROM rooms
                                                  WHERE roomid = ?) d";

        return $this->db->query($sql, array($id))->result();
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

    function hasOngoingReservations($id) {
        $sql = "SELECT *
                FROM (SELECT *
                      FROM reservations
                      WHERE concat_ws(' ', date, start_restime) >= NOW()) r NATURAL JOIN
                  (SELECT roomid
                   FROM rooms
                   WHERE roomid = ?) ro";
        $result = $this->db->query($sql, array($id))->result();

        return count($result)>=1;
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

    function isValidUser($email, $pass) {
        $sql = "SELECT *
                      FROM moderators
                      WHERE email = ? AND password = ?";

        $result = $this->db->query($sql, array($email, $pass))->result();
        // If credentials is found.


        return count($result)>=1;
    }

    function queryModeratorAccount($email) {
        $this->db->select("*");
        $this->db->from(TABLE_MODERATORS);
        $this->db->where(COLUMN_EMAIL, $email);
        $query = $this->db->get();

        return $query->row_array();
    }

    function isExistingModerator($email) {
        $this->db->select('*');
        $this->db->from(TABLE_MODERATORS);
        $this->db->where(COLUMN_EMAIL, $email);
        $query = $this->db->get();
        $result = $query->result();

        return count($result)>=1;
    }

    function queryAttendance($reservationid) {
        $sql = "SELECT " . COLUMN_ATTENDANCE . " " .
            "FROM " . TABLE_RESERVATIONS . " " .
            "WHERE " . COLUMN_RESERVATIONID . " = ?;";

        $attendance = $this->db->query($sql, array($reservationid))->result();

        return intval ($attendance[0]->attendance);
    }

    function queryVerification($reservationid) {
        $sql = "SELECT " . COLUMN_VERIFIED . " " .
            "FROM " . TABLE_RESERVATIONS . " " .
            "WHERE " . COLUMN_RESERVATIONID . " = ?;";

        $verification = $this->db->query($sql, array($reservationid))->result();

        return intval ($verification[0]->verified);
    }

    function updateAttendance($attendance, $reservationid) {
        $this->db->where(COLUMN_RESERVATIONID, $reservationid);
        $this->db->update(TABLE_RESERVATIONS, array(COLUMN_ATTENDANCE => $attendance));
    }

    function updateVerification($verification, $reservationid) {
        $this->db->where(COLUMN_RESERVATIONID, $reservationid);
        $this->db->update(TABLE_RESERVATIONS, array(COLUMN_VERIFIED => $verification));
    }

    function removeReservation($reservationid) {

        $this->archiveReservationID($reservationid);

        $this->db->delete(TABLE_RESERVATIONS, array(COLUMN_RESERVATIONID => $reservationid));

    }

    function queryModDeptIDAtEmail($email){
        $sql = "SELECT mod_departmentid 
                      FROM moderators
                      WHERE email = ?";

        $deptid = $this->db->query($sql, array($email))->result();

        return $deptid[0]->mod_departmentid;
    }

    function queryModeratorAtEmail($email) {
        $sql = "SELECT * 
                      FROM Moderators
                      WHERE email = ?";
        return $this->db->query($sql, array($email))->row_array();
    }

    function queryModeratorAtID($id) {
        $sql = "SELECT * 
                      FROM Moderators
                      WHERE moderatorid = ?";
        return $this->db->query($sql, array($id))->row_array();
    }

    function queryRoomIDwithModeratorID ($id) {
        $sql = "SELECT roomid
                FROM tag_mod_rooms
                WHERE moderatorid = ?";

        $roomid = $this->db->query($sql, array($id))->result();

        if (!empty($roomid))
            return $roomid[0]->roomid;
        else
            return 0;
    }

    function queryRoomNamewithRoomID ($id) {
        $sql = "SELECT name
                FROM rooms
                WHERE roomid = ?";

        $roomname = $this->db->query($sql, array($id))->result();

        return $roomname[0]->name;

    }

    function queryReservationDetailswithReservationID ($id) {
        $sql = "SELECT userid, verified, attendance
                FROM reservations
                WHERE reservationid = ?";

        $result = $this->db->query($sql, array($id))->result();

        return $result;
    }

    function archivePastReservations($date, $time) {
        $sql = "call archive_past_reservations(?, ?)";

        $this->db->query($sql, array($date, $time));
    }

    function archiveUnconfirmedReservations() {
        $sql = "call archive_unconfirmed_reservations(?)";

        $this->db->select(COLUMN_CONFIRMATION_EXPIRY);
        $this->db->from(TABLE_BUSINESS_RULES);
        $query = $this->db->get();

        foreach($query->result() as $row) {
            $this->db->query($sql, array($row->confirmation_expiry));
        }

    }

    function archiveReservationID($reservationid) {
        $sql = "call archive_reservation(?)";

        $this->db->query($sql, array($reservationid));
    }
}