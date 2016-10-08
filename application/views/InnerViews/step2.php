<?php
/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 3:41 PM
 */?>

<div id = "step2" class="step">

    <div class = "row">
        <div class = "panel-body">
            <div class = "col-md-10 col-md-offset-1">
                <form>
                    <div class="form-group">
                        <label for="idno">ID Number:</label>
                        <input type="number" class="form-control" id="idno">
                    </div>
                    <div class="form-group">
                        <label for="college">College:</label>
                        <input type="college" class="form-control" id="college">
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <input type="type" class="form-control" id="type">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email">
                    </div>

                    <b>Date:</b> mm/dd/yyyy
                    <br /><br />
                    <b>Time Slots:</b>
                    <div class = "row">
                        <div class = "col-md-6">
                            <div class="form-group">
                                <label for="starttime">Start:</label>
                                <input type="starttime" class="form-control" id="starttime">
                            </div>
                        </div>
                        <div class = "col-md-6">
                            <div class="form-group">
                                <label for="endtime">End:</label>
                                <input type="endtime" class="form-control" id="endtime">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class ="row">

        <div class = "col-md-10 col-md-offset-1">
            <ul class="pager">
                <li rel = "step2" class="previous"><a href="#step1"><span aria-hidden="true">&larr;</span> Previous step</a></li>
                <li rel = "step2" class="next"><a href="#step3">Proceed to next step <span aria-hidden="true">&rarr;</span></a></li>
            </ul>
        </div>

    </div>

</div>
