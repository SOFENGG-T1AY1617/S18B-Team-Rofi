<?php

/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 11/29/2016
 * Time: 15:17
 */
class AnalyticsController extends CI_Controller
{
    public function getData() {
        $roomid = $this->input->get('roomid');
        $dateType = $this->input->get('dateType');

        $data = $this->analytics->queryAllArchiveReservationsAtRoom($roomid);
        echo json_encode($data);
    }
}