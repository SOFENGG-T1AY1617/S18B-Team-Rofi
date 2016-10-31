<?php

/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 1:44 PM
 */

$defaultTab = 1;

?>

<div class="container">

    <div id = "steps" class="panel panel-default">
        <div id = "tabs" class="row" style="margin-bottom: 20px">
            <div class = "col-md-12">
                <ul class="nav nav-pills nav-justified">

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

            $tab = (isset($tab)) ? $tab : 'tab' . $defaultTab;

            /**
             * Created by PhpStorm.
             * User: Patrick
             * Date: 10/8/2016
             * Time: 3:41 PM
             */

            $stepNo = 1;

            ?>

            <script type = "text/javascript">

                var slotsPicked = [];
                var computers = [];
                var reservations = [];
                var request;
                var dateSelected = "<?=date("Y-m-d")?>";


                $(document).ready(function() {

                    var $table = $('#slotTable');
                    $table.floatThead({
                        scrollContainer: function($table){
                            return $table.closest('#slots');
                        }
                    });

                    $(".pager li.nextStep_<?php echo $stepNo ?> a").attr("data-toggle", "");

                    $(".pager li.nextStep_<?php echo $stepNo ?> a").click(function() {
                        if (slotsPicked == 0) {
                            toastr.info("You must choose up to 4 slots before proceeding.", "Info");
                            $(this).attr("data-toggle", "");
                        } else {
                            $(this).attr("data-toggle", "tab");

                            $("#tabs li.tab_<?php echo $stepNo ?>").removeClass('active');
                            $("#tabs li.tab_<?php echo $stepNo ?>").addClass('disabled');

                            $("#tabs li.tab_<?php echo $stepNo+1 ?>").removeClass('disabled');
                            $("#tabs li.tab_<?php echo $stepNo+1 ?>").addClass('active');
                        }

                        var date_selected = $("input[name=optradio]:checked").val();
                        console.log(date_selected);
                        if (date_selected == "today") {
                            dateSelected = "<?=date("Y-m-d")?>";
                            $("#text-date").text("<?=date("F d, Y")?>");
                        }
                        else {
                            dateSelected = "<?=date("Y-m-d", strtotime("tomorrow"))?>";
                            $("#text-date").text("<?=date('F d, Y', strtotime('tomorrow'))?>");
                        }

                        if(slotsPicked!=null){
                            $.ajax({
                                url: '<?php echo base_url('getMyReservations') ?>',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    slots: slotsPicked
                                }
                            })
                                .done(function(result) {
                                    console.log(result);

                                    var roomOut=[];
                                    var startOut=[];
                                    var endOut=[];

                                    for(i=0;i<result.length;i++){
                                        roomOut[i]= result[i].roomName + " PC"+result[i].compNo +"<br>";
                                        startOut[i]= result[i].start+"<br>";
                                        endOut[i]= result[i].end+"<br>";
                                    };

                                    //$("#form_room").empty().append(out);
                                    $("#computerColumn").empty().append(roomOut);
                                    $("#startColumn").empty().append(startOut);
                                    $("#endColumn").empty().append(endOut);
                                    console.log(roomOut);

                                })
                                .fail(function() {
                                    console.log("fail");
                                })
                                .always(function() {
                                    console.log("complete");
                                });
                        }

                    });

                    $(".pager li.nextStep_<?php echo $stepNo ?>").click(function() {
                        if ($(this).hasClass('active'))
                            $(this).removeClass('active');
                    });

                    $("#finish").click(function() {
                        console.log($("#select-college").val());
                        $.ajax({
                            url: '<?php echo base_url('submitReservation') ?>',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                idnumber: $("#id-number").val(),
                                collegeid: $("#select-college").val(),
                                typeid: $("#select-type").val(),
                                email: $("#email").val(),
                                date: $("#text-date").val(),
                                slots: slotsPicked,
                            }
                        })
                            .done(function(result) {
                                console.log("done");
                                if (result['status'] == "fail") {
                                    errors = result['errors'];
                                    toast = "You have an error in the following input ";
                                    if (errors.length > 1) {
                                       toast = toast + "s: ";
                                    }
                                    else {
                                        toast = toast + ": ";
                                    }

                                    for (i = 0; i < errors.length; i++) {
                                        toast = toast + errors[i] + ", ";
                                    }
                                    toast = toast + errors[errors.length - 1];
                                    toastr.error(toast, "Submission failed");
                                }
                            })
                            .fail(function() {
                                console.log("Submission: fail");
                            })
                            .always(function() {
                                console.log("complete");
                            });
                        
                    });


                    $(document).on( "click", ".slotCell.free:not(.disabled)",function() {
                        var slotID = $(this).attr('id');

                        if (slotsPicked.length < 4 && (($.inArray(slotID, slotsPicked)) == -1)) {
                            slotsPicked.push(slotID);
                            this.setAttribute("class", "slotCell selected");
                        }
                        else {
                            toastr.error("You cannot select more than 4 slots at once!", "Error");
                        }

                        console.log(slotsPicked);
                        outputSlots();

                    });

                    $(document).on( "click", ".slotCell.selected",function() {
                        var slotID = $(this).attr('id');

                        if (($.inArray(slotID, slotsPicked)) > -1) {
                            var existIndex = slotsPicked.indexOf(slotID);
                            slotsPicked.splice(existIndex, 1);

                           // this.setAttribute("class", "slotCell free");

                        }

                        console.log(slotsPicked);
                        outputSlots();
                    });

                    $(document).on( "click", ".unSelectSlot",function() {
                        var slotID = $(this).attr('id');

                        if (($.inArray(slotID, slotsPicked)) > -1) {
                            var existIndex = slotsPicked.indexOf(slotID);
                            slotsPicked.splice(existIndex, 1);
                        }

                        ;
                        outputSlots();

                        //console.log("slotCell.selected"+" #"+slotID);
                        console.log(slotsPicked);

                    });

                    $("input[name=optradio]:radio").change(function () {
                        if($("#form_building").val()!=null){

                            var date_selected = $("input[name=optradio]:checked").val();
                            console.log(date_selected);
                            if (date_selected == "today") {
                                dateSelected = "<?=date("Y-m-d")?>";
                                $("#text-date").text("<?=date("F d, Y")?>");
                            }
                            else {
                                dateSelected = "<?=date("Y-m-d", strtotime("tomorrow"))?>";
                                $("#text-date").text("<?=date('F d, Y', strtotime('tomorrow'))?>");
                            }

                            selectRoom($("#form_room").val());


                        }
                    });

                });

                function updateSelectedSlots(){

                        $.ajax({
                            url: '<?php echo base_url('getMyReservations') ?>',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                slots: slotsPicked
                            }
                        })
                            .done(function(result) {
                                console.log(result);

                                var out = [];

                                for(i=0;i<result.length;i++){
                                    out[i]= "<div class='selectedSlot'>";
                                    out[i]+= "<div><b>"+result[i].roomName + " PC"+result[i].compNo +"</b></div>";
                                    out[i]+= "<div>" + result[i].start + " to "+result[i].end+"</div>";
                                    out[i]+="<div class='unSelectSlot' id='"+result[i].id+"'><span aria-hidden='true' >x</span></div>";
                                    out[i]+="</div>";
                                };

                                //$("#form_room").empty().append(out);
                                $("#my_slots").html(out);

                                console.log(out);

                            })
                            .fail(function() {
                                console.log("fail");

                                $("#my_slots").html(null);
                            })
                            .always(function() {
                                console.log("complete");
                            });

                }

                function selectBuilding(buildingid) {


                    // Abort any pending request
                    if (request) {
                        request.abort();
                    }
                    // setup some local variables
                    var $form = $(this);

                    // Let's select and cache all the fields
                    var $inputs = $form.find("input, select, button, textarea");

                    // Serialize the data in the form
                    var serializedData = $form.serialize();

                    // Let's disable the inputs for the duration of the Ajax request.
                    // Note: we disable elements AFTER the form data has been serialized.
                    // Disabled form elements will not be serialized.
                    $inputs.prop("disabled", true);


                    if (buildingid != "") {
                        console.log(buildingid);

                        $.ajax({
                            url: '<?php echo base_url('getRooms') ?>',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                buildingid: buildingid
                            }
                        })
                        .done(function(result) {
                            console.log(result);
                            console.log("done");

                            var out=[];

                            out[0]= '<option value="0" selected >All Rooms</option>';

                            for(i=1;i<=result.length;i++){
                                out[i]= '<option value="'+result[i-1].roomid+'" >'+result[i-1].name+'</option>';
                            };

                            $("#form_room").empty().append(out);

                            selectRoom("0");

                            numOfRooms = result.length;

                        })
                        .fail(function() {
                            console.log("fail");
                        })
                        .always(function() {
                            console.log("complete");
                        });

                        /*$.post('application/controllers/ajax/foo', function(data) {
                            console.log(data)
                        }, 'json');*/

                    }
                }

                function selectRoom(roomid) {

                    var buildingid = $("#form_building").val();

                    computers = [];
                    reservations = [];

                    // Abort any pending request
                    if (request) {
                        request.abort();
                    }
                    // setup some local variables
                    var $form = $(this);

                    // Let's select and cache all the fields
                    var $inputs = $form.find("input, select, button, textarea");

                    // Serialize the data in the form
                    var serializedData = $form.serialize();

                    // Let's disable the inputs for the duration of the Ajax request.
                    // Note: we disable elements AFTER the form data has been serialized.
                    // Disabled form elements will not be serialized.
                    $inputs.prop("disabled", true);

                    $("#form_room").attr('disabled', false);

                    if (buildingid!=""&&roomid != "") {
                        console.log(buildingid+"-"+roomid);

                        $.ajax({
                            url: '<?php echo base_url('getComputers') ?>',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                buildingid: buildingid,
                                roomid:roomid,
                                currdate: dateSelected,
                            }
                        })
                            .done(function(result) {
                                console.log(result['date']);
                                console.log(result);
                                console.log("done");

                                queriedComputers = result['computers'];
                                queriedReservations = result['reservations'];

                                for(i=0;i<queriedComputers.length;i++){ // retrieve all computers from result
                                    computers[i]=queriedComputers[i];
                                }

                                for(i=0;i<queriedReservations.length;i++){ // retrieve all reservations from result
                                    reservations[i]=queriedReservations[i];
                                }

                                outputSlots();
                            })
                            .fail(function() {
                                console.log("fail");
                            })
                            .always(function() {
                                console.log("complete");
                            });

                        /*$.post('application/controllers/ajax/foo', function(data) {
                         console.log(data)
                         }, 'json');*/

                    }

                }

                function outputSlots() {

                    if(computers!=null){

                        $("#slotTable").find("tr:gt(0)").remove(); // remove all cells except first row

                        <?php
                        $tm15_today = [];

                        foreach ($times15_today as $key => $time)
                            $tm15_today[] = date("H:i:s", $time);

                        echo "var times15_today = " . json_encode($tm15_today) . ";";

                        $tm15_tomorrow = [];

                        foreach ($times15_tomorrow as $time)
                            $tm15_tomorrow[] = date("H:i:s", $time);

                        echo "var times15_tomorrow = " . json_encode($tm15_tomorrow) . ";";

                        $tm30_today = [];

                        foreach ($times30 as $time)
                            $tm30_today[] = date("Hi", $time);

                        echo "var times30 = " . json_encode($tm30_today) . ";";
                        ?>

                        var roomIDs = [];
                        var roomNames = [];

                        // index of ID corresponds with index of NAME

                        for (var i = 0; i < computers.length; i++) // retrieve all room numbers and room names
                        {
                            if (($.inArray(computers[i].roomid, roomIDs)) == -1)
                            {
                                roomIDs.push(computers[i].roomid);
                                roomNames.push(computers[i].name);
                            }
                        }

                        /*
                         * IF : OPTION 0
                         *   MAKE <tr> dedicated for room number first before proceeding
                         *
                         * MAKE <tr> for each PC
                         * APPEND <td>'s
                         *   - FIRST <td> having the PC NUMBER
                         *   - THE REST OF THE <td> having the SLOTS
                         * APPEND all <td>'s to <tr> made earlier
                         * APPEND <tr> to <table> with ID = slotTable
                         */


                        for (var i = 0; i < roomIDs.length; i++) {
                            var roomTitleRow = document.createElement("tr");
                            var roomTitleCell = document.createElement("th");

                            roomTitleCell.appendChild(document.createTextNode("Room: " + roomNames[i]));
                            roomTitleCell.setAttribute("colspan", times30.length + 1);

                            roomTitleRow.appendChild(roomTitleCell);

                            $('#tableBody').append(roomTitleRow);

                            var $table = $('#slotTable');
                            $table.floatThead({
                                scrollContainer: function ($table) {
                                    return $table.closest('#slots');
                                }
                            });

                            for (var k = 0; k < computers.length; k++) {

                                if (computers[k].roomid == roomIDs[i]) {

                                    var newTableRow = document.createElement("tr");
                                    var newPCNoCell = document.createElement("th");

                                    newPCNoCell.appendChild(document.createTextNode("PC No. " + computers[k].computerno));

                                    newTableRow.appendChild(newPCNoCell);

                                    var n = 0; // counter for chosenDateTimes
                                    var chosenDateTimes;

                                    if (dateSelected == "<?php echo date("Y-m-d") ?>") {
                                        chosenDateTimes = times15_today;
                                    } else {
                                        chosenDateTimes = times15_tomorrow;
                                    }

                                    for (var m = 0; m < times30.length - 1; m++) { // generate time slot cells
                                        var slotCell = document.createElement("td");
                                        var clickableSlot1 = document.createElement("div");
                                        var clickableSlot2 = document.createElement("div");
                                        var leftSpacer = document.createElement("div");
                                        var rightSpacer = document.createElement("div");

                                        slotCell.className = "nopadding";

                                        var taken = false;
                                        for (var p = 0; p < reservations.length; p++) {
                                            if ((reservations[p].start_restime == chosenDateTimes[n]) && (reservations[p].date == dateSelected) && (reservations[p].computerid == computers[k].computerid))
                                                taken = true;
                                        }

                                        var chosenTime1 = chosenDateTimes[n++];
                                        var chosenTime2 = chosenDateTimes[n];

                                        if (!taken) {
                                            var computerID = computers[k].computerid;

                                            var strID = computerID + "_" + dateSelected + "_" + chosenTime1 + "_" + chosenTime2;

                                            /*var selected = false;

                                             for(var s=0;s<slotsPicked.length;s++)
                                             {
                                             if(strID==slotsPicked[s])
                                             selected = true;
                                             }*/
                                            clickableSlot1.setAttribute("id", strID);

                                            if (($.inArray(clickableSlot1.getAttribute("id"), slotsPicked)) > -1)
                                                clickableSlot1.className = "slotCell pull-left selected";
                                            else
                                                clickableSlot1.className = "slotCell pull-left free";
                                        } else {
                                            clickableSlot1.className = "slotCell pull-left taken";
                                            n++;
                                        }

                                        for (var x in slotsPicked) {
                                            if (slotsPicked[x].includes(chosenTime1) && slotsPicked[x].includes(chosenTime2) && !(($.inArray(clickableSlot1.getAttribute("id"), slotsPicked)) > -1))
                                                disableSlot(clickableSlot1);
                                        }

                                        taken = false;
                                        for (var p = 0; p < reservations.length; p++) {
                                            if ((reservations[p].start_restime == chosenDateTimes[n]) && (reservations[p].date == dateSelected) && (reservations[p].computerid == computers[k].computerid))
                                                taken = true;
                                        }

                                        chosenTime1 = chosenDateTimes[n++];
                                        chosenTime2 = chosenDateTimes[n];

                                        if (!taken) {
                                            var computerID = computers[k].computerid;

                                            var strID = computerID + "_" + dateSelected + "_" + chosenTime1 + "_" + chosenTime2;

                                            /*var selected = false;

                                             for(var s=0;s<slotsPicked.length;s++)
                                             {
                                             if(strID==slotsPicked[s])
                                             selected = true;
                                             }*/
                                            clickableSlot2.setAttribute("id", strID);

                                            if (($.inArray(clickableSlot2.getAttribute("id"), slotsPicked)) > -1)
                                                clickableSlot2.className = "slotCell pull-left selected";
                                            else
                                                clickableSlot2.className = "slotCell pull-left free";
                                        } else {
                                            clickableSlot2.className = "slotCell pull-left taken";
                                            n++;
                                        }

                                        for (var x in slotsPicked) {
                                            if (slotsPicked[x].includes(chosenTime1) && slotsPicked[x].includes(chosenTime2) && !(($.inArray(clickableSlot2.getAttribute("id"), slotsPicked)) > -1))
                                                disableSlot(clickableSlot2);
                                        }

                                        leftSpacer.className = "slotDivider pull-left";
                                        rightSpacer.className = "slotDivider pull-right";

                                        slotCell.appendChild(leftSpacer);
                                        leftSpacer.appendChild(clickableSlot1);
                                        slotCell.appendChild(rightSpacer);
                                        rightSpacer.appendChild(clickableSlot2);

                                        newTableRow.appendChild(slotCell);
                                    }

                                    $('#tableBody').append(newTableRow);
                                }

                            }
                            updateSelectedSlots();
                        }
                    }

                }

                function disableSlot (slot) {
                    slot.className += ' disabled';
                }

            </script>
<!--Step 1-->
            <div id = "tab_1_<?php echo $stepNo ?>" class="tab-pane fade in <?php echo ($tab == $stepNo) ? 'active' : ''; ?>">

                <div class = "row">
                    <div class = "col-md-3 col-md-offset-1">
                        <div class = "panel panel-default">
                            <div class = "panel-body">
                                <div class="radio">
                                    <div class="radio" id="radio-date" name="form-date">
                                        <label><input type="radio" id="radio-today" name="optradio" value="today" checked>
                                            Today
                                            <div class = "date-font"> (<?=date("F d, Y")?>) </div>
                                        </label>
                                        <label><input type="radio" id="radio-tomorrow" name="optradio" value="tomorrow">
                                            Tomorrow
                                            <div class = "date-font"> (<?=date("F d, Y", strtotime("tomorrow"))?>) </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "col-md-5">
                        <div class = "panel panel-default">
                            <div class = "panel-body">
                                <div class = "form-group col-md-7">
                                    Building:
                                    <select class="form-control" id="form_building" name="form-building" onchange="selectBuilding(this.value)">
                                        <option value="" selected disabled>Choose a building...</option>
                                        <?php foreach($buildings as $row):?>
                                            <option value="<?=$row->buildingid?>"><?=$row->name?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class = "form-group col-md-5">
                                    Room:
                                    <select class="form-control" id="form_room" name="form-room" onchange="selectRoom(this.value)" disabled=true>
                                        <option value="" selected></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class = "row">
                    <div class = "col-md-8 col-md-offset-1">
                        <div id = "slots" class = "panel panel-default">
                            <div class = "panel-body nopadding">
                                <table id = "slotTable" class = "table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>PC Numbers</th>
                                            <?php

                                            foreach ($times30 as $key => $time) {
                                                if ($key != count($times30) - 1)
                                                    echo "<th>" . date("h:i A", $time) . "</th>";
                                            }

                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class = "col-md-2">

                        <div id = "slots_selected" class = "panel panel-default">

                            <div  class = "panel-body">
                                SELECTED SLOTS <br>
                                <p id = "my_slots"></p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class ="row">

                    <div class = "col-md-10 col-md-offset-1">
                        <ul class="pager">
                            <li class="previous pull-left">
                                LEGEND:
                            </li>
                            <li class="next nextStep_<?php echo $stepNo ?>">
                                <a href="#tab_1_<?php echo $stepNo+1 ?>" data-toggle="tab">Proceed to next step <span aria-hidden="true">&rarr;</span></a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
<!--End of Step 1-->

            <?php
            /**
             * Created by PhpStorm.
             * User: Patrick
             * Date: 10/8/2016
             * Time: 3:41 PM
             */

            $stepNo++; // make step into 2

            ?>

            <script type = "text/javascript">
                // js functions for step no 2
                $(function () { // put functions in respective buttons

                    $('.pager li.nextStep_<?php echo $stepNo ?>').on('click', function () { // for next step
                        if ($(this).hasClass('active'))
                            $(this).toggleClass('active');

                        $("#tabs li.tab_<?php echo $stepNo ?>").removeClass('active');
                        $("#tabs li.tab_<?php echo $stepNo ?>").addClass('disabled');

                        $("#tabs li.tab_<?php echo $stepNo+1 ?>").removeClass('disabled');
                        $("#tabs li.tab_<?php echo $stepNo+1 ?>").addClass('active');
                    });

                    $('.pager li.prevStep_<?php echo $stepNo ?>').on('click', function () { // for next step
                        if ($(this).hasClass('active'))
                            $(this).toggleClass('active');

                        $("#tabs li.tab_<?php echo $stepNo ?>").removeClass('active');
                        $("#tabs li.tab_<?php echo $stepNo ?>").addClass('disabled');

                        $("#tabs li.tab_<?php echo $stepNo-1 ?>").removeClass('disabled');
                        $("#tabs li.tab_<?php echo $stepNo-1 ?>").addClass('active');
                    });

                });

            </script>

            <div id = "tab_1_<?php echo $stepNo ?>" class="tab-pane fade in <?php echo ($tab == $stepNo) ? 'active' : ''; ?>">

                <div class = "row">
                    <div class = "panel-body">
                        <div class = "col-md-4 col-md-offset-2">
                            <form>
                                <div class="form-group">
                                    <label for="id-number">ID Number:</label>
                                    <input type="text" class="form-control" name="form-id" id="id-number"
                                           onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                                </div>

                                <div class="form-group">
                                    <label for="college">College:</label>
                                    <select class="form-control" name="form-college" id="select-college">
                                        <option selected disabled>Choose your college...</option>
                                        <?php foreach($colleges as $row):?>
                                            <option value="<?=$row->collegeid?>"><?=$row->name?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="type">Type:</label>
                                    <select class="form-control" name="form-type" id="select-type">
                                        <option selected disabled>Choose your type...</option>
                                        <?php foreach($types as $row):?>
                                            <option value="<?=$row->typeid?>"><?=$row->type?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" name="form-email" id="email">
                                </div>

                                <b>Date:</b> <span id="text-date"></span>
                                <br />
                                <b>Time Slots:</b>
                                <br/>
                                <div class = "row">
                                    <div class = "col-md-4">
                                        <div class="form-group">
                                            <label>Room & PC#:</label>
                                            <div id="computerColumn">

                                            </div>
                                        </div>
                                    </div>
                                    <div class = "col-md-4">
                                        <div class="form-group">
                                            <label>Start:</label>
                                            <div id="startColumn">

                                            </div>
                                        </div>
                                    </div>
                                    <div class = "col-md-4">
                                        <div class="form-group">
                                            <label>End:</label>
                                            <div id="endColumn">

                                            </div>
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
                                <a href="#tab_1_<?php echo $stepNo+1 ?>" data-toggle="tab" id="finish">Proceed to next step <span aria-hidden="true">&rarr;</span></a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

            <?php
            /**
             * Created by PhpStorm.
             * User: Patrick
             * Date: 10/8/2016
             * Time: 3:41 PM
             */

            $stepNo++; // make step into 3

            ?>

            <script>
                var reset = function () {
                    location.reload(true);
                };

                $(document).ready(function(){
                    $("#ok-button").click(reset);
                });
            </script>

            <div id = "tab_1_<?php echo $stepNo ?>" class="tab-pane fade in <?php echo ($tab == $stepNo) ? 'active' : ''; ?>">

                <div class = "row">
                    <div class = "col-md-10 col-md-offset-1">
                        <div class="panel-body">
                            <div class = "row">
                                <div class = "col-md-12">
                                    A URL with the confirmation code has been sent to your email address! Please click the link to confirm your reservation! Thank You!
                                </div>
                            </div>
                            <div class = "row">
                                <div class = "col-md-3 col-md-offset-5">
                                    <button id = "ok-button" type="button" class="btn btn-success">OK!</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--<div class ="row">

                    <div class = "col-md-3 col-md-offset-8">
                        <ul class="pager">
                            <li class="previous"><a href="#"><span aria-hidden="true">&larr;</span> Older</a></li>
                            <li class="next"><a href="#">Proceed to next step <span aria-hidden="true">&rarr;</span></a></li>
                        </ul>
                    </div>

                </div>-->

                <div class ="row">

                    <div class = "col-md-10 col-md-offset-1">
                        <ul class="pager">
                            <!--<li class="previous prevStep_<?//php echo $stepNo ?>">
                                <a href="#tab_1_<?//php echo $stepNo-1 ?>" data-toggle="tab"><span aria-hidden="true">&larr;</span> Go back to previous step</a>
                            </li>
                            <li class="next nextStep_<?//php echo $stepNo ?>">
                                <a href="#tab_1_<?//php echo $stepNo+1 ?>" data-toggle="tab" id="finish">Proceed to next step <span aria-hidden="true">&rarr;</span></a>
                            </li>-->
                        </ul>
                    </div>

                </div>

            </div>

        </div> <!-- EOF -->

    </div>

</div>
