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

    public function getMinuteInterval() {
        return 15; // TODO retrieve from Settings
    }

    public function getMaxNumberOfSlots() {
        return 4; // TODO retrieve from Settings
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

    function queryRoomAndCompNoAtComputerID($id){
        $sql = "SELECT name, computerno
                FROM rooms NATURAL JOIN 
                  (SELECT roomid, computerno
                   FROM computers
                   WHERE computerid = ?) b";
        return $this->db->query($sql, array($id))->result();
    }

    function queryRoomsWithDepartmentID($id) {
        //return $this->db->get(TABLE_ROOMS)->result();
        $sql = "SELECT roomid, name, buildingid, departmentid, COUNT(computerid) as capacity
                FROM (SELECT * 
                      FROM rooms
                      WHERE departmentid = ?) r NATURAL JOIN computers
                GROUP BY roomid
                ORDER BY name";
        return $this->db->query($sql, array($id))->result();
    }

    function queryRoomsByBuildingID($id) {
        //return $this->db->get(TABLE_ROOMS)->result();
        $sql = "SELECT * 
                FROM rooms
                WHERE buildingid = ?";
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

    function queryLatestRoomID() {
        return $this->db->insert_id();
    }

    function isExistingModerator($email) {
        $this->db->select('*');
        $this->db->from(TABLE_MODERATORS);
        $this->db->where(COLUMN_EMAIL, $email);
        $query = $this->db->get();
        $result = $query->result();

        return count($result)>=1;
    }

    function getLastComputerIDAtRoomID($id) {
        $this->db->select_max(COLUMN_COMPUTERID);
        $this->db->from(TABLE_COMPUTERS);
        $this->db->where(COLUMN_ROOMID, $id);
        $result = $this->db->get()->row();
        return $result->computerid;
    }

    function getLastComputerNoAtRoomID($id) {
        $this->db->select_max(COLUMN_COMPUTERNO);
        $this->db->from(TABLE_COMPUTERS);
        $this->db->where(COLUMN_ROOMID, $id);
        $result = $this->db->get()->row();
        return $result->computerno;
    }

    function updateRoomName($room) {
        $this->db->where(COLUMN_ROOMID, $room['roomid']);
        $this->db->update(TABLE_ROOMS, array('name' => $room['name']));
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

        return $roomid[0]->roomid;
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
}