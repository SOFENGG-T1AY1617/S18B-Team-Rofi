<?php

/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 4:29 PM
 */
class InnerViewList
{
    private $innerViewList;

    public function __construct () {
        $this->innerViewList = array();
    }

    public function withInnerViews ($innerViews) {
        foreach ($innerViews as $innerView) {
            $this->addInnerView($innerView);
        }
    }

    public function getSize () {
        return count($this->innerViewList);
    }

    public function getInnerView ($index) {
        return $this->innerViewList[$index];
    }

    public function addInnerView ($innerView) {
        $this->innerViewList[] = $innerView;
    }

    public function removeInnerView () {

    }
}

?>