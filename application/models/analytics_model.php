<?php

/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 11/29/2016
 * Time: 15:30
 */
class Analytics_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function queryBusinessRulesByRoomID($id) {
        $sql = "SELECT *
                FROM business_rules NATURAL JOIN (SELECT DISTINCT departmentid
                                                  FROM rooms
                                                  WHERE roomid = ?) d";

        return $this->db->query($sql, array($id))->result();
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

            $minimum_hour = intval(date("H"));
            $minimum_minute = intval(date("i"));

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

    function queryComputersAtRoomID($id) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE roomid = ?) t1";
        return $this->db->query($sql, array($id))->result();
    }

    function queryAllArchiveReservationsAtRoomByTime($roomid,$date,$interval) {
        $sql = "SELECT start_restime as 'time', COUNT(start_restime) as uses
                FROM archive_reservations NATURAL JOIN( SELECT name room_name
                                                              from rooms
                                                              where roomid = ?) t1
                WHERE date >= DATE_SUB(?,INTERVAL ? DAY) 
                GROUP BY time";

        $result = $this->db->query($sql, array($roomid,$date,$interval))->result();
        return $result;
    }

    function queryAllArchiveReservationsAtRoomByDay($roomid,$date,$interval) {
        $sql = "SELECT date as 'time', COUNT(start_restime) as uses
                FROM archive_reservations NATURAL JOIN( SELECT name room_name
                                                          from rooms
                                                          where roomid = ?) t1
                WHERE date >= DATE_SUB(?,INTERVAL ? DAY) 
                GROUP BY time";

        $result = $this->db->query($sql, array($roomid,$date,$interval))->result();
        return $result;
    }

    function queryAllArchiveReservationsAtRoom($roomid,$date,$interval) {
        $sql = "SELECT computerno, COUNT(archive_reservationid) as uses, room_name
                      FROM archive_reservations NATURAL JOIN(
                      SELECT name room_name
                      from rooms
                      where roomid = ? ) t1
                      WHERE date >= DATE_SUB(?,INTERVAL ? DAY) 
                      GROUP BY computerno";

        $result = $this->db->query($sql, array($roomid,$date,$interval))->result();
        return $result;
    }

    function queryComputersAtRoomName($name) {
        $sql = "SELECT * 
             FROM computers NATURAL JOIN 
              (SELECT roomid
               FROM rooms
               WHERE name = ?) t1";
        return $this->db->query($sql, array($name))->result();
    }

    function queryIntervalsAtRoomID($id) {
        $sql = "SELECT business_rules.interval as 'interval'
                FROM business_rules NATURAL JOIN (SELECT DISTINCT departmentid
                                                  FROM rooms
                                                  WHERE roomid = ?) d";

        return $this->db->query($sql, array($id))->row_array();
    }

}