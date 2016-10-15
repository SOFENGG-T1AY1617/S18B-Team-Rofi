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

    public function getTimes() {
        $times = array();
        $minute_interval = 15; // minute intervals per hour

        for ($hour = 0; $hour < 24 ; $hour++) {
            for ($minute = 0; $minute < 60; $minute += $minute_interval) {
                $times[] = mktime($hour, $minute, 0, 0, 0, 0);
            }
        }

        return $times;
    }

    function queryRooms() {
        return $this->db->get(TABLE_ROOMS)->result();
    }

    function queryAllComputers() {
        return $this->db->get(TABLE_COMPUTERS)->result();
    }

    function queryComputersAtRoomName($name) {
        return $this->db->query(
            "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE name = " . $name . ") t1")->result();
    }

    function queryComputersAtRoomID($id) {
        return $this->db->query(
            "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE roomid = " . $id . ") t1")->result();
    }

    function queryAllBuildings() {
        return $this->db->get(TABLE_BUILDINGS)->result();
    }

    function queryAllRoomsAtBuildingID($id) {
        return $this->db->query(
            "SELECT * 
             FROM rooms NATURAL JOIN 
              (SELECT buildingid
               FROM buildings
               WHERE buildingid = " . $id . ") t1")->result();
    }

    function queryAllRoomsAtBuildingName($name) {
        return $this->db->query(
            "SELECT * 
             FROM rooms NATURAL JOIN 
              (SELECT buildingid
               FROM buildings
               WHERE name = " . $name . ") t1")->result();
    }

    function queryColleges() {
        return $this->db->get(TABLE_COLLEGES)->result();
    }

    function queryTypes() {
        return $this->db->get(TABLE_TYPES)->result();
    }
}