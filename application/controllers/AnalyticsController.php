<?php

/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 11/29/2016
 * Time: 15:17
 */
class AnalyticsController extends CI_Controller
{
    public function testFunction() {

        $data = $this->analytics->queryAllArchiveReservations();
        echo json_encode($data);
    }
}