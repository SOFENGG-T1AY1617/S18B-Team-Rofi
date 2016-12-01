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

    function queryAllAdministators() {
        $this->db->select('*');
        $this->db->from(TABLE_ADMINISTRATORS);
        $this->db->join(TABLE_DEPARTMENTS, 'admin_departmentid = departmentid');
        $this->db->where(COLUMN_ADMIN_TYPEID . " != ", '1');
        $this->db->order_by(COLUMN_FIRST_NAME, COLUMN_LAST_NAME);
        $query = $this->db->get();
        return $query->result();
    }

    function queryAllModerators() {
        $this->db->select('*');
        $this->db->from(TABLE_MODERATORS);
        $this->db->join(TABLE_DEPARTMENTS, 'mod_departmentid = departmentid');
        $this->db->order_by(COLUMN_FIRST_NAME, COLUMN_LAST_NAME);
        $query = $this->db->get();
        return $query->result();
    }

    function queryAllDepartments() {
        $this->db->select('*');
        $this->db->from(TABLE_DEPARTMENTS);
        $query = $this->db->get();
        return $query->result();
    }

    function queryModeratorsWithDepartmentID($id) {
        $this->db->select('*');
        $this->db->from(TABLE_MODERATORS);
        $this->db->join(TABLE_DEPARTMENTS, 'mod_departmentid = departmentid');
        $this->db->where(COLUMN_MOD_DEPARTMENTID, $id);
        $this->db->order_by(COLUMN_FIRST_NAME, COLUMN_LAST_NAME);
        $query = $this->db->get();
        return $query->result();
    }

    function queryAllRooms() {
        //return $this->db->get(TABLE_ROOMS)->result();
        $sql = "SELECT rooms.roomid, name, buildingid, departmentid, COUNT(computerid) as capacity
                FROM rooms LEFT JOIN computers ON rooms.roomid = computers.roomid
                GROUP BY rooms.roomid
                ORDER BY name";
        return $this->db->query($sql)->result();
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

    function queryAllComputersAtBuildingID($id) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid, name
               FROM rooms
               WHERE buildingid = ?) t1";
        return $this->db->query($sql, array($id))->result();
    }

    function queryAllComputersAtBuildingIDAndDepartmentID($id, $did) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid, name
               FROM rooms
               WHERE buildingid = ? AND department_id = ?) t1";
        return $this->db->query($sql, array($id, $did))->result();
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

    function queryBuildingsByDepartmentID($id) {
        $sql = "SELECT *
                FROM buildings NATURAL JOIN (SELECT DISTINCT buildingid
                            FROM rooms
                            WHERE departmentid = ?) rooms";

        return $this->db->query($sql, array($id))->result();
    }

    function queryBuildingIDFromBuildingName($name) {
        $sql = "SELECT buildingid
                   FROM buildings
                   WHERE name = ?";
        return $this->db->query($sql, $name)->result();

    }
    function queryAllRoomTypes(){
        $sql = "SELECT area_typeid, type FROM area_types";
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