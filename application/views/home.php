<?php

include_once ("/InnerViews/InnerViewList.php");

/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 1:44 PM
 */

$stepsList = new InnerViewList();

$stepsList -> addInnerView('InnerViews/step1.php');
$stepsList -> addInnerView('InnerViews/step2.php');
$stepsList -> addInnerView('InnerViews/step3.php');

function addSteps ($stepsList) {
    for ($i = 0; $i < $stepsList->getSize(); $i++ ) {
        include $stepsList->getInnerView ($i);
    }
}

?>

<div class="container">

    <div id = "steps" class="panel panel-default">
        <div id = "tabs" class="row" style="margin-bottom: 20px">
            <div class = "col-md-12">
                <ul class="nav nav-tabs nav-justified">
                   <li role="presentation" class="active"><a href="#">Step 1 : Choose a time slot</a></li>
                   <li role="presentation" class="disabled"><a href="#">Step 2 : Provide your personal information</a></li>
                    <li role="presentation" class="disabled"><a href="#">Final Step : Email Confirmation</a></li>
                </ul>
            </div>
        </div>

        <?php
            addSteps($stepsList);
        ?>

    </div>

</div>