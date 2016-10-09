<?php

//include_once ("/InnerViews/InnerViewList.php");

/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 1:44 PM
 */

/*$stepsList = new InnerViewList();

$stepsList -> addInnerView('InnerViews/step1.php');
$stepsList -> addInnerView('InnerViews/step2.php');
$stepsList -> addInnerView('InnerViews/step3.php');

function addSteps ($stepsList) {
    for ($i = 0; $i < $stepsList->getSize(); $i++ ) {
        include $stepsList->getInnerView ($i);
    }
}*/

$defaultTab = 1;

?>

<div class="container">

    <div id = "steps" class="panel panel-default">
        <div id = "tabs" class="row" style="margin-bottom: 20px">
            <div class = "col-md-12">
                <ul class="nav nav-tabs nav-justified">

                   <li role="presentation" class="tab_1 active">
                       <a href="#">Step 1 : Choose a time slot</a>
                   </li>

                   <li role="presentation" class="tab_2 disabled">
                       <a href="#">Step 2 : Provide your personal information</a>
                   </li>

                    <li role="presentation" class="tab_3 disabled">
                        <a href="#">Final Step : Email Confirmation</a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="tab-content">
            <?php
            //addSteps($stepsList);

            $tab = (isset($tab)) ? $tab : 'tab' . $defaultTab;

            $this->load->view('InnerViews/step1.php', $data);
            $this->load->view('InnerViews/step2.php', $data);
            $this->load->view('InnerViews/step3.php', $data);
            ?>
        </div>

    </div>

</div>