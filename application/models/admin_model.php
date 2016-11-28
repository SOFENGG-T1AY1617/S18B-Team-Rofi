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

    function queryRoomsWithDepartmentIDAndBuildingID($id, $bid) {
        //return $this->db->get(TABLE_ROOMS)->result();
        $sql = "SELECT * 
                FROM rooms
                WHERE departmentid = ? AND buildingid = ?";
        return $this->db->query($sql, array($id, $bid))->result();
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
        $sql = "SELECT * FROM area_types";
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
                      FROM administrators
                      WHERE email = ? AND password = ?";

        $result = $this->db->query($sql, array($email, $pass))->result();
        // If credentials is found.


        return count($result)>=1;
    }

    function queryAdminAccount($email) {
        $this->db->select("*");
        $this->db->from(TABLE_ADMINISTRATORS);
        $this->db->where(COLUMN_EMAIL, $email);
        $query = $this->db->get();

        return $query->row_array();
    }

    function queryLatestRoomID() {
        return $this->db->insert_id();
    }

    function insertRoomsAndComputers($data) {
        $rooms = $data['rooms'];
        $numAdded = 0;
        $notAdded = [];

        foreach($rooms as $room) {
            if ($this->isExistingRoom($room[0])) {
                $notAdded[] = $room[0];
                continue;
            }

            $insertRoomData = array(
                'name' => $room[0],
                'buildingid' => $data['buildingid'],
                'departmentid' => $data['departmentid']
            );

            $this->insertRoom($insertRoomData);

            $insertComputersData = array(
                'computerCount' => $room[1],
                'roomid' => $this->queryLatestRoomID(),
            );

            $this->insertComputersAtRoom($insertComputersData);
            $numAdded++;
        }

        $result = array(
            'numAdded' => $numAdded,
            'notAdded' => $notAdded,
        );


        return $result;
    }

    function insertModerators($data) {
        $moderators = $data['moderators'];
        $numAdded = 0;
        $notAdded = [];

        foreach($moderators as $mod) {

            $insertModData = array(
                'first_name' => $mod[0],
                'last_name' => $mod[1],
                'password' => "password",
                'email' => $mod[2],
                'mod_departmentid' => $data['departmentid']
            );

            if(!($this->isExistingModerator($insertModData[COLUMN_EMAIL]))) {
                $this->db->insert(TABLE_MODERATORS, $insertModData);
                $numAdded++;
            } else {
                $notAdded[] = $mod[0] . ' ' . $mod[1];
            }

        }

        $returnData = array(
            'numAdded' => $numAdded,
            'notAdded' => $notAdded
        );

        return $returnData;
    }



    function insertRoom($room) {
        $this->db->insert(TABLE_ROOMS, $room);
    }

    function insertComputersAtRoom($data) {
        $computerCount = $data['computerCount'];

        for ($i = 1; $i <= $computerCount; $i++) {
            $insertComputerData = array(
                'computerno' => $i,
                'roomid' => $data['roomid'],
            );
            $this->insertComputer($insertComputerData);
        }
    }

    function insertComputer($computer) {
        $this->db->insert(TABLE_COMPUTERS, $computer);
    }

    function isExistingRoom($roomName) {
        $this->db->select('*');
        $this->db->from(TABLE_ROOMS);
        $this->db->where(COLUMN_NAME, $roomName);
        $query = $this->db->get();
        $result = $query->result();

        return count($result)>=1;
    }

    function isExistingModerator($email) {
        $this->db->select('*');
        $this->db->from(TABLE_MODERATORS);
        $this->db->where(COLUMN_EMAIL, $email);
        $query = $this->db->get();
        $result = $query->result();

        return count($result)>=1;
    }

    function queryRoomAtID($id) {
        $sql = "SELECT roomid, name, buildingid, departmentid, COUNT(computerid) as capacity
                FROM (SELECT * 
                      FROM rooms
                      WHERE roomid = ?) r NATURAL JOIN computers
                GROUP BY roomid";
        return $this->db->query($sql, array($id))->row_array();
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

    function insertAdmin($data) {
        $this->db->insert(TABLE_ADMINISTRATORS, $data);
    }

    function insertAdmins($data) {
        $admins = $data['admins'];
        $numAdded = 0;
        $notAdded = [];

        foreach($admins as $admin) {
            $insertAdminData = array(
                COLUMN_EMAIL => $admin[2],
                COLUMN_PASSWORD => 'default', // to be set by the admin on email confirmation
                COLUMN_FIRST_NAME => $admin[0],
                COLUMN_LAST_NAME => $admin[1],
                COLUMN_ADMIN_DEPARTMENTID => $admin[3],
                COLUMN_ADMIN_TYPEID => 2
            );

            if ( !($this->isExistingAdmin($insertAdminData[COLUMN_EMAIL])) ) {
                $this->insertAdmin($insertAdminData);
                $numAdded++;
            } else {
                $notAdded[] = $admin[0] . ' ' . $admin[1];
            }
        }

        $returnData = array(
            'numAdded' => $numAdded,
            'notAdded' => $notAdded
        );

        return $returnData;
    }

    function isExistingDepartment($departmentName) {
        $this->db->select('*');
        $this->db->from(TABLE_DEPARTMENTS);
        $this->db->where(COLUMN_NAME, $departmentName);
        $query = $this->db->get();
        $result = $query->result();

        return count($result)>=1;
    }

    function insertDepartment($department) {
        $this->db->insert(TABLE_DEPARTMENTS, $department);
    }

    function insertBusinessRules($rules) {
        $this->db->insert(TABLE_BUSINESS_RULES, $rules);
    }


    function removeComputersFromRoom($data) {
        $roomid = $data['roomid'];
        $numToRemove = $data['count'];

        for ($i = 0; $i < $numToRemove; $i++) {
            // Find last computer in roomid
            $computerid = $this->getLastComputerIDAtRoomID($roomid);
            $this->db->delete(TABLE_COMPUTERS, array('computerid' => $computerid));
        }
    }

    function addComputersToRoom($data) {
        $roomid = $data['roomid'];
        $numToAdd = $data['count'];

        for ($i = 0; $i < $numToAdd; $i++) {
            // Get last computer number

            $computerno = $this->getLastComputerNoAtRoomID($roomid) + 1;

            $insertComputerData = array(
                'computerno' => $computerno,
                'roomid' => $roomid,
            );

            $this->insertComputer($insertComputerData);
        }
    }

    function deleteRoom($roomid) {
        // Delete all computers in room
        $this->removeAllComputersFromRoom($roomid);

        // Delete room
        $this->db->delete(TABLE_ROOMS, array('roomid' => $roomid));
    }

    function removeAllComputersFromRoom($roomid) {
        $this->db->delete(TABLE_COMPUTERS, array('roomid' => $roomid));
    }

    function insertBuilding($data) {

            if(!$this->isExistingBuilding($data['name'])) {
                $this->db->insert(TABLE_BUILDINGS, $data);
                return true;
            }
            else
                return false;
    }

    function isExistingBuilding($name) {
        $this->db->select('*');
        $this->db->from(TABLE_BUILDINGS);
        $this->db->where(COLUMN_NAME, $name);
        $query = $this->db->get();
        $result = $query->result();

        return count($result)>=1;
    }

    function updateBusinessRules($id, $updateData) {
        $this->db->where(COLUMN_BUSINESS_RULESID, $id);
        $this->db->update(TABLE_BUSINESS_RULES, $updateData);
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

    function deleteModerator($email) {
        // Delete all computers in room
        // Delete room
        $this->db->delete(TABLE_MODERATORS, array('email' => $email));
    }

    function updateModEmail($data) {
        $this->db->where(COLUMN_MODERATORID, $data['id']);
        $this->db->update(TABLE_MODERATORS, array('email' => $data['email']));
    }

    function updateModFirstName($data) {
        $this->db->where(COLUMN_MODERATORID, $data['id']);
        $this->db->update(TABLE_MODERATORS, array('first_name' => $data['fName']));
    }

    function updateModLastName($data) {
        $this->db->where(COLUMN_MODERATORID, $data['id']);
        $this->db->update(TABLE_MODERATORS, array('last_name' => $data['lName']));
    }

    function updateModDepartment($data) {
        $this->db->where(COLUMN_MODERATORID, $data['id']);
        $this->db->update(TABLE_MODERATORS, array('mod_departmentid' => $data['dept']));
    }


    function queryModeratorAtID($id) {
        $sql = "SELECT * 
                      FROM Moderators
                      WHERE moderatorid = ?";
        return $this->db->query($sql, array($id))->row_array();
    }


    function queryAdminsAtEmail($email) {
        $sql = "SELECT * 
                      FROM Administrators
                      WHERE email = ?";
        return $this->db->query($sql, array($email))->row_array();
    }

    function deleteAdmin($email) {
        // Delete all computers in room
        // Delete room
        $this->db->delete(TABLE_ADMINISTRATORS, array('email' => $email));
    }

    function deleteAdminAtID($ID) {
        // Delete all computers in room
        // Delete room
        $this->db->delete(TABLE_ADMINISTRATORS, array(COLUMN_ADMINISTRATORID => $ID));
    }

    function updateAdminEmail($data) {
        $this->db->where(COLUMN_ADMINISTRATORID, $data['id']);
        $this->db->update(TABLE_ADMINISTRATORS, array('email' => $data['email']));
    }

    function updateAdminFirstName($data) {
        $this->db->where(COLUMN_ADMINISTRATORID, $data['id']);
        $this->db->update(TABLE_ADMINISTRATORS, array('first_name' => $data['fName']));
    }

    function updateAdminLastName($data) {
        $this->db->where(COLUMN_ADMINISTRATORID, $data['id']);
        $this->db->update(TABLE_ADMINISTRATORS, array('last_name' => $data['lName']));
    }

    function updateAdminDepartment($data) {
        $this->db->where(COLUMN_ADMINISTRATORID, $data['id']);
        $this->db->update(TABLE_ADMINISTRATORS, array('admin_departmentid' => $data['dept']));
    }


    function queryAdminAtID($id) {
        $sql = "SELECT * 
                      FROM Administrators
                      WHERE administratorid = ?";
        return $this->db->query($sql, array($id))->row_array();
    }

    function isExistingAdmin($email) {
        $this->db->select('*');
        $this->db->from(TABLE_ADMINISTRATORS);
        $this->db->where(COLUMN_EMAIL, $email);
        $query = $this->db->get();
        $result = $query->result();

        return count($result)>=1;
    }

    function archivePastReservations($date, $time) {
        $sql = "call archive_past_reservations(?, ?)";

        $this->db->query($sql, array($date, $time));
    }

}