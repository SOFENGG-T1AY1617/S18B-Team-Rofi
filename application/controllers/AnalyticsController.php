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





        $times = [];
        if($dateType == "today"){

            $reservations = $this->analytics->queryAllArchiveReservationsAtRoom($roomid,date("Y-m-d"),0);
            $reservationsTime = $this->analytics->queryAllArchiveReservationsAtRoomByTime($roomid,date("Y-m-d"),0);
            $computers = $this->analytics->queryComputersAtRoomID($roomid);
            $times = $this->getTimes($roomid);
        }
        else{
            $reservations = $this->analytics->queryAllArchiveReservationsAtRoom($roomid,date("Y-m-d"),7);
            $reservationsTime = $this->analytics->queryAllArchiveReservationsAtRoomByDay($roomid,date("Y-m-d"),7);
            $computers = $this->analytics->queryComputersAtRoomID($roomid);
            $times = $this->getDates(7);
        }

        $data = ["computers"=>$computers, "reservations"=>$reservations,"times"=>$times, "reservationsTime" =>$reservationsTime];


        echo json_encode($data);
    }

    public function getTimes($roomid) {

        date_default_timezone_set('Asia/Hong_Kong'); // set to Hong Kong's/Philippines' Timezone

        $getData = array(
            'interval' => intval($this->analytics->queryIntervalsAtRoomID($roomid)['interval'])
        );



        $currentMinute = date("i");

        $times_today = $this->analytics->getTimes(null, $currentMinute, $getData['interval'], $this->analytics->getMinimumHour(), $this->analytics->getMaximumHour(), false);

        $data['times_today'] = null;

        $data['times_today_DISPLAY'] = null;


        foreach ($times_today as $time)
            $data['times_today'][] = date("H:i:s", $time);



        foreach ($times_today as $time)
            $data['times_today_DISPLAY'][] = date("h:i A", $time);


        return $data;

    }

    public function getDates($daysAgo){
        $data['times_today'] = null;
        $data['times_today_DISPLAY'] = null;
        for($i = 0; $i<$daysAgo;$i++){
            $data['times_today'][]=date('Y-m-d',time()-86400*($i-1));
            $data['times_today_DISPLAY'][]=date('D M d',time()-86400*($i-1));
        }
        return $data;
    }


}