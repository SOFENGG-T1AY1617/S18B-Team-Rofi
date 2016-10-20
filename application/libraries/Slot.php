<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/18/2016
 * Time: 10:25 PM
 */
class Slot
{

    private $computerID;
    private $date;
    private $startTime;
    private $endTime;

    function __construct($computerID, $date, $startTime, $endTime)
    {
        $this->computerID = $computerID;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getComputerID() {
        return $this->computerID;
    }

    public function getDate() {
        return $this->date;
    }

    public function getStartTime() {
        return $this->startTime;
    }

    public function getEndTime() {
        return $this->endTime;
    }
}