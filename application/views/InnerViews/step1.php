<?php
/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 3:41 PM
 */?>

<div id = "step1" class="step">

    <div class = "row">
        <div class = "col-md-3 col-md-offset-1">
            <div class = "panel panel-default">
                <div class = "panel-body">
                    <div class="radio">
                        <div class="radio">
                            <label><input type="radio" name="optradio">Today (<?=date("m-d-Y")?>)</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Tomorrow (<?=date("m-d-Y", strtotime("tomorrow"))?>)</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class = "panel panel-default">
                <div class = "panel-body">
                    BLDGS AND ROOMS
                </div>
            </div>
        </div>

        <div class = "col-md-7">
            <div class = "panel panel-default">
                <div class = "panel-body">
                    SLOTS
                </div>
            </div>
        </div>
    </div>

    <div class ="row">

        <div class = "col-md-3 col-md-offset-8">
            <ul class="pager">
                <!--<li class="previous"><a href="#"><span aria-hidden="true">&larr;</span> Older</a></li>-->
                <li rel = "step1" class="next"><a href="#">Proceed to next step <span aria-hidden="true">&rarr;</span></a></li>
            </ul>
        </div>

    </div>

</div>
