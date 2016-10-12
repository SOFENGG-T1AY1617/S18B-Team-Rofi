<?php
/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 3:41 PM
 */

$stepNo = 1;

?>

<script type = "text/javascript">

    $(function () { // put functions in respective buttons

        $('.pager li.nextStep_<?php echo $stepNo ?>').on('click', function () { // for next step
            if ($(this).hasClass('active'))
                $(this).removeClass('active');

            $("#tabs li.tab_<?php echo $stepNo ?>").removeClass('active');
            $("#tabs li.tab_<?php echo $stepNo ?>").addClass('disabled');

            $("#tabs li.tab_<?php echo $stepNo+1 ?>").addClass('active');
        });

    });

    function selectBuilding(val){
        console.log(val);
    }

</script>

<div id = "tab_1_<?php echo $stepNo ?>" class="tab-pane fade in <?php echo ($tab == $stepNo) ? 'active' : ''; ?>">

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
                    Building:
                    <select name="form-building" onchange="selectBuilding(this.value)">
                        <option value="" disabled selected>Select...</option>
                        <?php foreach($buildings as $row):?>
                            <option value="<?=$row->buildingid?>"><?=$row->name?></option>
                        <?php endforeach;?>
                    </select>

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
                <!--<li class="previous prevStep_</?php echo $stepNo ?>">
                    <a href="#tab_1_</?php echo $stepNo-1 ?>" data-toggle="tab"><span aria-hidden="true">&larr;</span> Go back to previous step</a>
                </li>-->
                <li class="nextStep_<?php echo $stepNo ?>">
                    <a href="#tab_1_<?php echo $stepNo+1 ?>" data-toggle="tab">Proceed to next step <span aria-hidden="true">&rarr;</span></a>
                </li>
            </ul>
        </div>

    </div>

</div>
