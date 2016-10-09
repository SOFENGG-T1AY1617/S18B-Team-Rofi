<?php
/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 3:41 PM
 */

$stepNo = 2;

?>

<script type = "text/javascript">

    $(function () { // put functions in respective buttons

        $('.pager li.nextStep_<?php echo $stepNo ?>').on('click', function () { // for next step
            if ($(this).hasClass('active'))
                $(this).toggleClass('active');

            $("#tabs li.tab_<?php echo $stepNo ?>").removeClass('active');
            $("#tabs li.tab_<?php echo $stepNo ?>").addClass('disabled');

            $("#tabs li.tab_<?php echo $stepNo+1 ?>").addClass('active');
        });

        $('.pager li.prevStep_<?php echo $stepNo ?>').on('click', function () { // for next step
            if ($(this).hasClass('active'))
                $(this).toggleClass('active');

            $("#tabs li.tab_<?php echo $stepNo ?>").removeClass('active');
            $("#tabs li.tab_<?php echo $stepNo ?>").addClass('disabled');

            $("#tabs li.tab_<?php echo $stepNo-1 ?>").addClass('active');
        });

    });

</script>

<div id = "tab_1_<?php echo $stepNo ?>" class="tab-pane fade in <?php echo ($tab == $stepNo) ? 'active' : ''; ?>">

    <div class = "row">
        <div class = "panel-body">
            <div class = "col-md-3 col-md-offset-2">
                <form>
                    <div class="form-group">
                        <label for="idno">ID Number:</label>
                        <input type="number" class="form-control" id="idno">
                    </div>

                    <div class="form-group">
                        <label for="college">College:</label> <!-- THIS SHOULDN'T BE HARDCODED -->
                        <select class="form-control" id="college">
                            <option selected disabled>Choose your college...</option>
                            <option>CCS</option>
                            <option>COB</option>
                            <option>COS</option>
                            <option>CLA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type">Type:</label>
                        <select class="form-control" id="type"> <!-- THIS SHOULDN'T BE HARDCODED -->
                            <option selected disabled>Choose your type...</option>
                            <option>Student</option>
                            <option>Faculty</option>
                        </select>
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
                <li class="previous prevStep_<?php echo $stepNo ?>">
                    <a href="#tab_1_<?php echo $stepNo-1 ?>" data-toggle="tab"><span aria-hidden="true">&larr;</span> Go back to previous step</a>
                </li>
                <li class="next nextStep_<?php echo $stepNo ?>">
                    <a href="#tab_1_<?php echo $stepNo+1 ?>" data-toggle="tab">Proceed to next step <span aria-hidden="true">&rarr;</span></a>
                </li>
            </ul>
        </div>

    </div>

</div>
