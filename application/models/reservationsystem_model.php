<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 8:11 PM
 */


class ReservationSystem_Model extends CI_Model
{
    /*const TABLE_ROOMS = "rooms";
    const COLUMN_ROOMID = "roomid";
    const COLUMN_NAME = "name";
    const COLUMN_BUILDING = "building";

    const TABLE_COMPUTERS =  "computers";
    const COLUMN_COMPUTERID = "computerid";
    const COLUMN_COMPUTERNO = "computerno";

    const TABLE_RESERVATIONS = "reservations";
    const COLUMN_RESERVATIONID = "reservationid";
    const COLUMN_USERIDNO = "useridno";
    const COLUMN_RESERVEDATETIME = "reservedatetime";
    const COLUMN_COLLEGE = "college";
    const COLUMN_VERIFIED = "verified";
    const COLUMN_VERFICATIONCODE = "verificationcode";*/

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function queryRooms() {
        return $this->db->get("rooms")->result();
    }

    function queryAllComputers() {
        return $this->db->get("computers")->result();
    }

    function queryComputersAtRoomName($name) {
        return $this->db->query(
            "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE name = $name) t1")->result();
    }

    function queryComputersAtRoomID($id) {
        return $this->db->query(
            "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE roomid = $id) t1")->result();
    }

    function queryAllBuildings() {
        return $this->db->get("buildings")->result();
    }

    function queryAllRoomsAtBuildingID($id) {
        return $this->db->query(
            "SELECT * 
             FROM rooms NATURAL JOIN 
              (SELECT buildingid
               FROM buildings
               WHERE buildingid = $id) t1")->result();
    }

    function queryAllRoomsAtBuildingName($name) {
        return $this->db->query(
            "SELECT * 
             FROM rooms NATURAL JOIN 
              (SELECT buildingid
               FROM buildings
               WHERE name = $name) t1")->result();
    }
}