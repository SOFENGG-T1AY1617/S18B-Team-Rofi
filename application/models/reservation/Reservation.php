<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/18/2016
 * Time: 10:32 PM
 */
class Reservation
{

    private $reservationID;
    private $computerID;
    private $userIDNo;
    private $email;
    private $slots;
    private $collegeID;
    private $typeID;
    private $verified;
    private $verificationCode;

    /**
     * Reservation constructor.
     * @param $computerID
     * @param $userIDNo
     * @param $email
     * @param $slots
     * @param $collegeID
     * @param $typeID
     */
    public function __construct($computerID, $userIDNo, $email, $slots, $collegeID, $typeID)
    {
        $this->computerID = $computerID;
        $this->userIDNo = $userIDNo;
        $this->email = $email;
        $this->slots = $slots;
        $this->collegeID = $collegeID;
        $this->typeID = $typeID;

        $this->load->helper('string');
        $this->verificationCode = random_string('sha1');
    }


    /**
     * @return mixed
     */
    public function getReservationID()
    {
        return $this->reservationID;
    }

    /**
     * @param mixed $reservationID
     */
    public function setReservationID($reservationID)
    {
        $this->reservationID = $reservationID;
    }

    /**
     * @return mixed
     */
    public function getComputerID()
    {
        return $this->computerID;
    }

    /**
     * @param mixed $computerID
     */
    public function setComputerID($computerID)
    {
        $this->computerID = $computerID;
    }

    /**
     * @return mixed
     */
    public function getUserIDNo()
    {
        return $this->userIDNo;
    }

    /**
     * @param mixed $userIDNo
     */
    public function setUserIDNo($userIDNo)
    {
        $this->userIDNo = $userIDNo;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * @param mixed $slots
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;
    }

    /**
     * @return mixed
     */
    public function getCollegeID()
    {
        return $this->collegeID;
    }

    /**
     * @param mixed $collegeID
     */
    public function setCollegeID($collegeID)
    {
        $this->collegeID = $collegeID;
    }

    /**
     * @return mixed
     */
    public function getTypeID()
    {
        return $this->typeID;
    }

    /**
     * @param mixed $typeID
     */
    public function setTypeID($typeID)
    {
        $this->typeID = $typeID;
    }

    /**
     * @return mixed
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * @param mixed $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    /**
     * @return mixed
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * @param mixed $verificationCode
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }



}