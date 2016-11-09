<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 11/6/2016
 * Time: 7:07 PM
 */
class Admin_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function queryAllAdministators() {
        $this->db->select('*');
        $this->db->from(TABLE_ADMINISTRATORS);
        $this->db->join(TABLE_DEPARTMENTS, 'admin_departmentid = departmentid');
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

    function queryAllRooms() {
        //return $this->db->get(TABLE_ROOMS)->result();
        $sql = "SELECT roomid, name, buildingid, departmentid, COUNT(computerid) as capacity
             FROM rooms NATURAL JOIN computers
             GROUP BY roomid";
        return $this->db->query($sql)->result();
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

    function queryAdminAccount($email, $password) {
        $this->db->select("*");
        $this->db->from(TABLE_ADMINISTRATORS);
        $this->db->where(COLUMN_EMAIL, $email);
        $this->db->where(COLUMN_PASSWORD, $password);
        $query = $this->db->get();

        return $query->row_array();
    }
}