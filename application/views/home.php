<?php

/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 10/8/2016
 * Time: 1:44 PM
 */

$defaultTab = 1;

?>

<script>

    function nextStep(currentStepNo) {

        var currentStepTabID = "#tabs li.tab_" + currentStepNo;

        $(currentStepTabID).removeClass('active');
        $(currentStepTabID).addClass('disabled');

        $(currentStepTabID).next("li").removeClass('disabled');
        $(currentStepTabID).next("li").find("a").attr('data-toggle', 'tab');
        $(currentStepTabID).next("li").find("a").trigger('click');
        $(currentStepTabID).next("li").find("a").attr('data-toggle', '');

    }

    function prevStep(currentStepNo) {

        var currentStepTabID = "#tabs li.tab_" + currentStepNo;

        $(currentStepTabID).removeClass('active');
        $(currentStepTabID).addClass('disabled');

        $(currentStepTabID).prev("li").removeClass('disabled');
        $(currentStepTabID).prev("li").find("a").attr('data-toggle', 'tab');
        $(currentStepTabID).prev("li").find("a").trigger('click');
        $(currentStepTabID).prev("li").find("a").attr('data-toggle', '');

    }

</script>

<div class="container">

    <div id = "steps" class="panel panel-default">
        <div id = "tabs" class="row" style="margin-bottom: 20px">
            <div class = "col-md-12">
                <ul class="nav nav-pills nav-justified">

                   <li role="presentation" class="tab_1 active">
                       <a href="#tab_1_1">Step 1 : Choose a time slot</a>
                   </li>

                   <li role="presentation" class="tab_2 disabled">
                       <a href="#tab_1_2">Step 2 : Provide your personal information</a>
                   </li>

                    <li role="presentation" class="tab_3 disabled">
                        <a href="#tab_1_3">Final Step : Email Confirmation</a>
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
                var dateToday = "<?=date("Y-m-d")?>";
                var dateSelected = dateToday;
                var maxNumberOfSlots = 0;
                var currentDeptID = 0;

                var times_today;
                var times_tomorrow;
                var times_today_DISPLAY;
                var times_tomorrow_DISPLAY;

                $(document).ready(function() {

                    $(".pager li.nextStep_<?php echo $stepNo ?> a").click(function() {
                        if (slotsPicked == 0) {
                            toastr.info("You must choose up to "+maxNumberOfSlots+" slots before proceeding.", "Info");
                        } else {
                            nextStep(<?php echo $stepNo ?>);
                        }

                        var date_selected = $("input[name=optradio]:checked").val();
                        console.log(date_selected);
                        if (date_selected == "today") {
                            dateSelected = "<?=date("Y-m-d")?>";
                            //$("#text-date").text("<?=date("F d, Y")?>");
                        }
                        else {
                            dateSelected = "<?=date("Y-m-d", strtotime("tomorrow"))?>";
                            //$("#text-date").text("<?=date('F d, Y', strtotime('tomorrow'))?>");
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
                                    var dateOut=[];
                                    var startOut=[];
                                    var endOut=[];

                                    for(i=0;i<result.length;i++){
                                        roomOut[i]= result[i].roomName + " PC"+result[i].compNo +"<br>";
                                        dateOut[i]= result[i].date+"<br>";
                                        startOut[i]= result[i].start+"<br>";
                                        endOut[i]= result[i].end+"<br>";
                                    };

                                    //$("#form_room").empty().append(out);
                                    $("#computerColumn").empty().append(roomOut);
                                    $("#dateColumn").empty().append(dateOut);
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

                    $(document).on( "click", ".slotCell.free:not(.disabled)",function() {
                        var slotID = $(this).attr('id');

                        if (slotsPicked.length < maxNumberOfSlots && (($.inArray(slotID, slotsPicked)) == -1)) {
                            slotsPicked.push(slotID);
                            this.setAttribute("class", "slotCell selected");

                            disableAllRelativeSlots(slotID);
                        }
                        else {
                            toastr.error("You cannot select more than "+ maxNumberOfSlots +" slots at once!", "Error");
                        }

                        console.log(slotsPicked);
                        updateSelectedSlots();

                    });

                    $(document).on( "click", ".slotCell.selected, .unSelectSlot",function() {
                        var slotID = $(this).attr('id');

                        if (($.inArray(slotID, slotsPicked)) > -1) {
                            var existIndex = slotsPicked.indexOf(slotID);
                            slotsPicked.splice(existIndex, 1);

                            enableAllRelativeSlots(slotID);
                            deselectSlot($("[id='" + slotID + "']"));
                        }

                        console.log(slotsPicked);
                        updateSelectedSlots();
                    });


                    $("input[name=optradio]:radio").change(function () {

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

                        if($("#form_building").val()!=null){
                            selectRoom($("#form_room").val());
                        }

                    });

                });

                function deselectSlot (slot) {
                    if (slot.hasClass('selected'))
                        slot.removeClass('selected');

                    slot.addClass('free');
                }

                function enableSlot (slot) {
                    if (slot.hasClass('disabled'))
                        slot.removeClass('disabled');
                }

                function disableSlot (slot) {
                    slot.addClass('disabled');
                }

                function disableSlotObject (slotObject) {
                    slotObject.className += ' disabled';
                }

                function disableAllRelativeSlots(id) {

                    var splittedID = id.split("_");
                    var pcID = splittedID[0];
                    var relativeDateTime = splittedID[1] + "_" + splittedID[2] + "_" + splittedID[3];

                    $("[id*='" + relativeDateTime + "']:not([id^='" + pcID + "_'])").each(function () {
                        disableSlot($(this));
                    });

                }

                function enableAllRelativeSlots(id) {

                    var splittedID = id.split("_");
                    var pcID = splittedID[0];
                    var relativeDateTime = splittedID[1] + "_" + splittedID[2] + "_" + splittedID[3];

                    $("[id*='" + relativeDateTime + "']").each(function () {
                        enableSlot($(this));
                    });

                }

                function updateTimesHeader(isToday) {

                    var slotTable = $('#slotTable');

                    slotTable.floatThead('destroy');

                    var currentTimeArray = (isToday ? times_today_DISPLAY : times_tomorrow_DISPLAY);

                    var timesRow = document.createElement("tr");
                    var PCNumbersTH = document.createElement("th");

                    PCNumbersTH.appendChild(document.createTextNode("PC Numbers"));

                    timesRow.appendChild(PCNumbersTH);

                    for (var i = 0; i < currentTimeArray.length - 1; i++) {
                        var th = document.createElement("th");

                        th.appendChild(document.createTextNode(currentTimeArray[i]));

                        timesRow.appendChild(th);
                    }

                    slotTable.empty();

                    slotTable.append(timesRow);

                    var tableHead = document.createElement("thead");
                    tableHead.id = "tableHead";

                    slotTable.prepend(tableHead);
                    slotTable.find('thead').append(slotTable.find("tr:eq(0)"));

                    var tableBody = document.createElement("tbody");
                    tableBody.id = "tableBody";

                    slotTable.append(tableBody);

                    slotTable.floatThead({
                        scrollContainer: function(slotTable){
                            return slotTable.closest('#slots');
                        }
                    });

                }

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
                                    out[i]+= "<div>" + result[i].date +"</div>";
                                    out[i]+= "<div>" + result[i].start + " - "+result[i].end+"</div>";
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
                                if (maxNumberOfSlots != 0)
                                    $("#my_number_of_slots").html("Selected Slots ("+slotsPicked.length+"/"+maxNumberOfSlots+"):");
                                else
                                    $("#my_number_of_slots").html("Selected Slots ("+slotsPicked.length+"/X):");

                                console.log("complete");
                            });

                }

                function clearSelectedSlots() {
                    slotsPicked = [];
                    $("#my_slots").html("");
                }

                function setSlotLimit(limit) {
                    maxNumberOfSlots = limit;
                    $("#my_number_of_slots").html("Selected Slots ("+slotsPicked.length+"/"+maxNumberOfSlots+"):");
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

                            $("#form_room").empty();

                            var out=[];

                            //out[0]= '<option value="0" selected >All Rooms</option>';

                            var firstRoomID;

                            if (result[0] != null)
                                firstRoomID = result[0].roomid;
                            else
                                firstRoomID = "";

                            for(var i=0;i<result.length;i++){
                                out[i] = '<option value="'+result[i].roomid+'" >'+result[i].name+'</option>';
                            }

                            if (out.length > 0)
                                $("#form_room").append(out);

                            selectRoom(firstRoomID);

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
                    
                    //updateTimesHeader(dateSelected == dateToday);

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
                        var interval;

                        console.log(buildingid+"-"+roomid);

                        $.ajax({
                            url: '<?php echo base_url('getBusinessRules') ?>',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                roomid: roomid
                            }
                        })
                            .done(function(result) {
                                interval = result[0].interval;

                                if (currentDeptID != result[0].departmentid) {
                                    clearSelectedSlots();
                                    setSlotLimit(result[0].limit);
                                    if (currentDeptID !=0)
                                        toastr.info("The slots have been cleared and limit has been changed.", "Department has changed!");
                                }

                                currentDeptID = result[0].departmentid;
                            })
                            .fail(function() {
                                console.log("fail");
                            })
                            .always(function() {
                                console.log("complete");
                            })
                            .then(function () {
                            console.log("PROMISE FULFILL");

                            return $.ajax({ // PROCEED TO PROMISE
                                url: '<?php echo base_url('getTimes') ?>',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    interval: interval
                                }
                            })
                        })

                        // FOR PROMISE
                        .done (function (result) {
                            times_today = result['times_today'];
                            times_tomorrow = result['times_tomorrow'];
                            times_today_DISPLAY = result['times_today_DISPLAY'];
                            times_tomorrow_DISPLAY = result['times_tomorrow_DISPLAY'];

                            updateTimesHeader(dateSelected == dateToday);
                        })
                        .fail (function () {

                        })
                        .then (function () {
                            // get computers

                            return $.ajax({
                                url: '<?php echo base_url('getComputers') ?>',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    buildingid: buildingid,
                                    roomid:roomid,
                                    currdate: dateSelected,
                                }
                            })
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

                    } else {
                        outputSlots();
                    }

                }

                function outputSlots() {

                    if(computers!=null){

                        $("#tableBody").empty();

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

                        var currentTimeArray = (dateSelected == dateToday ? times_today : times_tomorrow);

                        if(currentTimeArray.length>0) {
                            for (var i = 0; i < roomIDs.length; i++) {
                                var roomTitleRow = document.createElement("tr");
                                var roomTitleCell = document.createElement("th");

                                roomTitleCell.appendChild(document.createTextNode("Room: " + roomNames[i]));
                                roomTitleCell.setAttribute("colspan", currentTimeArray.length + 1);

                                roomTitleRow.appendChild(roomTitleCell);

                                $('#tableBody').append(roomTitleRow);

                                /*var $table = $('#slotTable');
                                 $table.floatThead({
                                 scrollContainer: function ($table) {
                                 return $table.closest('#slots');
                                 }
                                 });*/

                                for (var k = 0; k < computers.length; k++) {

                                    if (computers[k].roomid == roomIDs[i]) {

                                        var newTableRow = document.createElement("tr");
                                        var newPCNoCell = document.createElement("th");

                                        newPCNoCell.appendChild(document.createTextNode("PC No. " + computers[k].computerno));

                                        newTableRow.appendChild(newPCNoCell);

                                        var n = 0; // counter for traversing through currentTimeArray

                                        for (var m = 0; m < currentTimeArray.length - 1; m++) { // generate time slot cells
                                            var slotCell = document.createElement("td");
                                            var clickableSlot1 = document.createElement("div");

                                            slotCell.className = "nopadding";

                                            var taken = false;
                                            for (var p = 0; p < reservations.length; p++) {
                                                if ((reservations[p].start_restime == currentTimeArray[n]) && (reservations[p].date == dateSelected) && (reservations[p].computerid == computers[k].computerid))
                                                    taken = true;
                                            }

                                            var chosenTime1 = currentTimeArray[n++];
                                            var chosenTime2 = currentTimeArray[n];

                                            if (!taken) {
                                                var computerID = computers[k].computerid;

                                                var strID = computerID + "_" + dateSelected + "_" + chosenTime1 + "_" + chosenTime2;

                                                clickableSlot1.setAttribute("id", strID);

                                                if (($.inArray(clickableSlot1.getAttribute("id"), slotsPicked)) > -1)
                                                    clickableSlot1.className = "slotCell pull-left selected";
                                                else
                                                    clickableSlot1.className = "slotCell pull-left free";
                                            } else {
                                                clickableSlot1.className = "slotCell pull-left taken";
                                            }

                                            for (var x in slotsPicked) {
                                                if (slotsPicked[x].includes(chosenTime1) && slotsPicked[x].includes(chosenTime2) && !(($.inArray(clickableSlot1.getAttribute("id"), slotsPicked)) > -1)) {
                                                    disableSlotObject(clickableSlot1);
                                                }

                                            }

                                            slotCell.appendChild(clickableSlot1);

                                            newTableRow.appendChild(slotCell);
                                        }

                                        $('#tableBody').append(newTableRow);

                                    }

                                }

                            }
                        }else{
                            var tableMessageRow = document.createElement("tr");
                            var tableMessageCell = document.createElement("th");

                            tableMessageCell.appendChild(document.createTextNode("No slots left for today"));
                            tableMessageCell.setAttribute("colspan", 100);

                            tableMessageRow.appendChild(tableMessageCell);

                            $('#tableBody').append(tableMessageRow);

                        }
                    }

                    //updateSelectedSlots();
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

                                </table>
                            </div>
                        </div>
                    </div>

                    <div class = "col-md-2">

                        <div id = "slots_selected" class = "panel panel-default">

                            <div  class = "panel-body">
                                <p id="my_number_of_slots"></p>
                                <div id = "my_slots"></div>
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
                            <li class="previous pull-left">

                                <div class="legend free pull-left"></div>Free
                            </li>
                            <li class="previous pull-left">
                                <div class="legend selected pull-left"></div>Selected
                            </li>
                            <li class="previous pull-left">
                                <div class="legend taken pull-left"></div>Taken
                            </li>


                            <li class="next nextStep_<?php echo $stepNo ?>">
                                <a href="#">Proceed to next step <span aria-hidden="true">&rarr;</span></a>
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
                    $('.pager li.prevStep_<?php echo $stepNo ?>').on('click', function () { // for next step
                        prevStep(<?php echo $stepNo ?>);
                    });
                });

                function checkType(typeid) {
                    $.ajax({
                        url: '<?php echo base_url('checkType') ?>',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            typeid: typeid,
                        }
                    })
                        .done(function(result) {
                            console.log("done");
                            console.log(result);
                            if (result.toLowerCase().indexOf("graduate") >= 0) {
                                $("#div-college").show();
                            }
                            else {
                                $("#div-college").hide();
                                $("#select-college").val("0");
                            }

                        })
                        .fail(function() {
                            console.log("fail");
                        })
                        .always(function() {
                            console.log("complete");
                        });

                }

                $(document).ready(function() {

                    $("#div-college").hide();

                    $(".pager li.nextStep_<?php echo $stepNo ?> a").click(function () {



                        $("#email_message").css("visibility", "visible");
                        $.ajax({
                            url: '<?php echo base_url('submitReservation') ?>',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                idnumber: $("#id-number").val(),
                                typeid: $("#select-type").val(),
                                collegeid: $("#select-college").val(),
                                email: $("#email").val(),
                                date: $("#text-date").val(),
                                slots: slotsPicked,
                                departmentid: currentDeptID
                            }
                        })
                            .done(function(result) {
                                console.log("done");
                                if (result['status'] == "fail") {
                                    $("#email_message").css("visibility", "hidden");
                                    console.log("Count: " + result['count']);
                                    console.log("Num Reservations: " + result['num']);
                                    errors = result['errors'];
                                    console.log(errors);
                                    if (errors.length > 0) {
                                        toast = "You have an error in the following input";

                                        console.log ("NUMBER OF ERRORS: " + errors.length);

                                        if (errors.length > 1) {
                                            toast = toast + "s: ";
                                        }
                                        else {
                                            toast = toast + ": ";
                                        }

                                        for (i = 0; i < errors.length - 1; i++) {
                                            toast = toast + errors[i] + ", ";
                                        }
                                        toast = toast + errors[errors.length - 1];
                                        toastr.error(toast, "Submission failed");
                                    }
                                    if (result['email_status'] == "fail") {
                                        toastr.error("An error occurred while trying to reserve. Please try again.", "Submission failed");
                                    }
                                    if (result['numReservations_status'] == "fail") {
                                        toast = "You've already reserved " + result['reserved'] +
                                            " slots! You can only have a maximum of " + maxNumberOfSlots + ".";
                                        toastr.error(toast, "Too many reservations");
                                    }
                                    if (result['reservation_status'] == 'fail') {
                                        toast = "Sorry, but a slot you picked was already selected: ";
                                        var reservations = result['sameReservations'];
                                        for (var i = 0; i < reservations.length - 1; i++) {
                                            var message = "[" + reservations[i]['date'] + " " +
                                                reservations[i]['startTime'] + " - " +
                                                reservations[i]['endTime'] + "], ";
                                            toast = toast + message;
                                        }

                                        var message = "[" + reservations[reservations.length - 1]['date'] + " " +
                                            reservations[reservations.length - 1]['startTime'] + " - " +
                                            reservations[reservations.length - 1]['endTime'] + "]";
                                        toast = toast + message;

                                        toastr.error(toast, "Oops!");
                                    }
                                }
                                else {

                                    nextStep(<?php echo $stepNo ?>);
                                }

                            })
                            .fail(function() {
                                console.log("Submission: fail");
                                toastr.error("Failed to send email. Please check your connection and try again.", "Submission failed");
                            })
                            .always(function() {
                                console.log("complete");
                                $("#email_message").css("visibility", "hidden");
                            });
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
                                    <label for="type">Type:</label>
                                    <select class="form-control" name="form-type" id="select-type" onchange="checkType(this.value)">
                                        <option value="0" selected disabled>Choose your type...</option>
                                        <?php foreach($types as $row):?>
                                            <option value="<?=$row->typeid?>"><?=$row->type?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                                <div class="form-group" id="div-college">
                                    <label for="college">College:</label>
                                    <select class="form-control" name="form-college" id="select-college">
                                        <option value="0" selected disabled>Choose your college...</option>
                                        <?php foreach($colleges as $row):?>
                                            <option value="<?=$row->collegeid?>"><?=$row->name?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                                <label for="email">Email:</label>
                                <i><small class = "text-primary pull-right">Only DLSU Email is allowed (dlsu.edu.ph)</small></i>

                                <div class = "row">
                                    <div class = "col-md-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="form-email" id="email" required />
                                            <span class = "input-group-addon">@</span>
                                            <select class="form-control" name="form-email-extension" id="select-email-extension">
                                                <?php foreach($email_extensions as $row):?>
                                                    <option value="<?=$row->email_extension?>"><?=$row->email_extension?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id = "second-step-slots" class = "row col-md-10 col-md-offset-2">
                            <b>Time Slots:</b>
                            <br/>
                            <div class = "row">
                                <div class = "col-md-2">
                                    <div class="form-group">
                                        <label>Room & PC#:</label>
                                        <div id="computerColumn">

                                        </div>
                                    </div>
                                </div>
                                <div class = "col-md-2">
                                    <div class="form-group">
                                        <label>Date:</label>
                                        <div id="dateColumn">

                                        </div>
                                    </div>
                                </div>
                                <div class = "col-md-1">
                                    <div class="form-group">
                                        <label>Start:</label>
                                        <div id="startColumn">

                                        </div>
                                    </div>
                                </div>
                                <div class = "col-md-1">
                                    <div class="form-group">
                                        <label>End:</label>
                                        <div id="endColumn">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class ="row">

                    <div class = "col-md-10 col-md-offset-1">
                        <ul class="pager">
                            <li class="previous prevStep_<?php echo $stepNo ?>">
                                <a href="#tab_1_<?php echo $stepNo-1 ?>"><span aria-hidden="true">&larr;</span> Go back to previous step</a>
                            </li>
                            <li class="next nextStep_<?php echo $stepNo ?>">
                                <a href="#tab_1_<?php echo $stepNo+1 ?>">Proceed to next step <span aria-hidden="true">&rarr;</span></a>
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
                updateSelectedSlots();
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
            <div class = "message parent" id = "email_message">
                <div class="message child">Processing...</div>
            </div>

        </div> <!-- EOF -->

    </div>

</div>
