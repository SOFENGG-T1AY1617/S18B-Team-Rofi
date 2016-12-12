<?php
include 'a_navbar.php';
?>


<ol class="breadcrumb  col-md-offset-2 col-md-10">
    <li><a href="#">Admin</a></li>
    <li><a href="#">Application Settings</a></li>
    <li class="active">Modify Schedule</li>
</ol>

<link href="<?=base_url()?>assets/css/admin_reservation_system_style.css" rel="stylesheet">

<script src="<?=base_url()?>assets/js/floatThread.js"></script>

<style>
    body {
        padding-top: 0px;
    }
</style>

<script>
    var slotsPicked = [];
    var computers = [];
    var reservations = [];

    var disabledSlots = []; // for backend

    var request;
    var dateToday = "<?=date("Y-m-d")?>";
    var dateSelected = dateToday;
    var currentDeptID = 0;

    var currentTime = "<?=date("H:m:s"); ?>";

    var times_today;
    var times_tomorrow;
    var times_today_DISPLAY;
    var times_tomorrow_DISPLAY;

    $(document).ready(function() {

        $(document).on( "click", ".slotCell:not(.selected)",function() {
            selectSlot($(this));
        });

        $(document).on( "click", ".slotCell.selected, .slotCell.selectedY, .slotCell.selectedX",function() {
            deselectSlot($(this));
        });

        $(document).on( "click", ".horizSelect", function () {
            toggleSlotsHorizontal($(this));
        });

        $(document).on( "click", ".vertSelect", function () {
            toggleSlotsVertical($(this));
        });

        $(document).on( "mouseover", ".horizSelect", function () {
            highlightHorizontal($(this));
        });

        $(document).on( "mouseover", ".vertSelect", function () {
            highlightVertical($(this));
        });

        $(document).on( "mouseleave", ".horizSelect", function () {
            unhighlightHorizontal($(this));
        });

        $(document).on( "mouseleave", ".vertSelect", function () {
            unhighlightVertical($(this));
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

            updateDisplayTimeInModal();
            updateTimesHeader(date_selected == "today");

            if($("#form_building").val()!=null){
                selectRoom($("#form_room").val());
            }

        });

        $("#enable-btn").click(function () {
            enableSlots();
        });

        $("#disable-btn").click(function () {
            disableSlots();
        });

        $("#toggle-btn").click(function () {
            toggleSelectedSlots();
        });

        $("#enableAll-btn").click(function () {
            enableAllSlotsInRoom();
        });

        $("#disableAll-btn").click(function () {
            disableAllSlotsInRoom();
        });

        updateDisplayTimeInModal();

    });

    function disableSlots () {
        if (slotsPicked.length == 0) {

            toastr.info("You must select slots before performing actions.", "Hold on!");

        } else {

            console.log("Disabling slots on: " + dateSelected);

            $.ajax({
                url: '<?php echo base_url('admin/' . ADMIN_DISABLE_SLOTS) ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    slots: slotsPicked,
                    currentDate: dateSelected,
                    hour: $("#endHour").val(),
                    minute: $("#endMinute").val()

                }
            })
                .done(function (result) {

                    toastr.success(result['updated'] + " slots were updated!", result['updated'] + " slot/s is/are now disabled");

                    disableSelectedSlots();

                    var newIDs = result['newIDs'];
                    var updatedIDs = result['updatedIDs'];

                    for (var i = 0; i < newIDs.length; i++) {
                        var currentSlot = $("[id = '" + updatedIDs[i] + "']");

                        currentSlot.attr("id", currentSlot.attr("id") + "_" + newIDs[i]);

                        var existIndex = slotsPicked.indexOf(updatedIDs);

                        slotsPicked[existIndex] = currentSlot.attr("id");
                    }

                    //updateSelectedSlots();

                })
                .fail(function () {

                    toastr.error("Slots were not updated.", "Oops!");

                    console.log("fail");

                })
                .always(function () {

                    console.log("complete");

                });
        }
    }

    function enableSlots () {
        if (slotsPicked.length == 0) {

            toastr.info("You must select slots before performing actions.", "Hold on!");

        } else {
            $.ajax({
                url: '<?php echo base_url('admin/' . ADMIN_ENABLE_SLOTS) ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    slots: slotsPicked
                }
            })
                .done(function (result) {

                    toastr.success(result['updated'] + " slots were updated!", result['updated'] + " slots/s is/are now enabled");

                    for (var i = 0; i < slotsPicked.length; i++) {
                        var currentSlot = $("[id = '" + slotsPicked[i] + "']");

                        var IDArray = slotsPicked[i].split('_');

                        currentSlot.attr("id", IDArray[0] + "_" + IDArray[1] + "_" + IDArray[2] + "_" + IDArray[3]);

                        enableSlot(currentSlot);

                        slotsPicked[i] = currentSlot.attr("id");
                    }

                    //updateSelectedSlots();

                })
                .fail(function () {

                    toastr.error("Slots were not updated.", "Oops!");

                    console.log("fail");

                })
                .always(function () {

                    console.log("complete");

                });
        }
    }

    function updateDisplayTimeInModal () {
        var currentTimeContainer = $(".currentTimeContainer");

        currentTimeContainer.empty();

        currentTimeContainer.append(dateSelected);
    }

    function highlightHorizontal (cell) {
        var cellID = cell.attr("id");

        var splittedCellID = cellID.split('_');

        var PCID = splittedCellID[1];

        var jQuerySelector = "[id^='" + PCID + "_']";

        $(jQuerySelector).addClass('slot-hover');
    }

    function highlightVertical (cell) {
        var cellID = cell.attr("id");

        var splittedCellID = cellID.split('_');

        var time1 = splittedCellID[1];
        var time2 = splittedCellID[2];

        var jQuerySelector = "[id*='" + time1 + "_" + time2 +"']:not([id = '" + cellID + "'])";

        $(jQuerySelector).addClass('slot-hover');
    }

    function unhighlightHorizontal (cell) {
        var cellID = cell.attr("id");

        var splittedCellID = cellID.split('_');

        var PCID = splittedCellID[1];

        var jQuerySelector = "[id^='" + PCID + "_']";

        $(jQuerySelector).removeClass('slot-hover');
    }

    function unhighlightVertical (cell) {
        var cellID = cell.attr("id");

        var splittedCellID = cellID.split('_');

        var time1 = splittedCellID[1];
        var time2 = splittedCellID[2];

        var jQuerySelector = "[id*='" + time1 + "_" + time2 +"']:not([id = '" + cellID + "'])";

        $(jQuerySelector).removeClass('slot-hover');
    }

    function enableSlot (slot) {
        if (slot.hasClass('disabled'))
            slot.removeClass('disabled');

        slot.addClass('enabled');
    }

    function disableSlot (slot) {
        if (slot.hasClass('enabled'))
            slot.removeClass('enabled');

        slot.addClass('disabled');
    }

    function enableSelectedSlots () {
        console.log ("enable");

        for (var i = 0; i < slotsPicked.length; i++) {
            var jQuerySelector = "[id='" + slotsPicked[i] + "']";

            enableSlot($(jQuerySelector));
        }
    }

    function disableSelectedSlots () {
        console.log ("disable");

        for (var i = 0; i < slotsPicked.length; i++) {
            var jQuerySelector = "[id='" + slotsPicked[i] + "']";

            disableSlot($(jQuerySelector));
        }
    }

    function toggleSelectedSlots () {
        console.log ("toggle");

        for (var i = 0; i < slotsPicked.length; i++) {
            var jQuerySelector = "[id='" + slotsPicked[i] + "']";

            if ($(jQuerySelector).hasClass('disabled')) {
                enableSlot($(jQuerySelector));

            } else if ($(jQuerySelector).hasClass('enabled')) {
                disableSlot($(jQuerySelector));
            }
        }
    }

    function enableAllSlotsInRoom () {
        console.log ("enable-all");

    }

    function disableAllSlotsInRoom () {
        console.log ("disable-all");

    }

    function selectSlot (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selected');
    }

    function selectSlotX (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selectedX');

        if (slot.hasClass('selected'))
            slot.removeClass('selected');
    }

    function selectSlotY (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selectedY');

        if (slot.hasClass('selected'))
            slot.removeClass('selected');
    }

    function deselectSlot (slot) {
        var slotID = slot.attr("id");

        if (($.inArray(slotID, slotsPicked)) > -1) {
            var existIndex = slotsPicked.indexOf(slotID);
            slotsPicked.splice(existIndex, 1);

            if (slot.hasClass('selected'))
                slot.removeClass('selected');

            if (slot.hasClass('selectedY'))
                slot.removeClass('selectedY');

            if (slot.hasClass('selectedX'))
                slot.removeClass('selectedX');
        }

        var splittedID = slotID.split("_");

        var removeUseSelectorHoriz = "#PC_" + splittedID[0];
        var removeUseSelectorVert = "[id = 'TIME" + "_" + splittedID[2] + "_" + splittedID[3] + "']";

        if ($(removeUseSelectorHoriz).hasClass('used'))
            $(removeUseSelectorHoriz).removeClass('used');

        if ($(removeUseSelectorVert).hasClass('used'))
            $(removeUseSelectorVert).removeClass('used');
    }

    function deselectSlotX (slot) {
        var slotID = slot.attr("id");

        if (($.inArray(slotID, slotsPicked)) > -1) {

            if (!slot.hasClass('selectedY')) {
                var existIndex = slotsPicked.indexOf(slotID);
                slotsPicked.splice(existIndex, 1);
            }

            if (slot.hasClass('selectedX'))
                slot.removeClass('selectedX');
        }
    }

    function deselectSlotY (slot) {
        var slotID = slot.attr("id");

        if (($.inArray(slotID, slotsPicked)) > -1) {
            if (!slot.hasClass('selectedX')) {
                var existIndex = slotsPicked.indexOf(slotID);
                slotsPicked.splice(existIndex, 1);
            }

            if (slot.hasClass('selectedY'))
                slot.removeClass('selectedY');
        }
    }

    function toggleSlotsVertical (cell) {
        var cellID = cell.attr("id");

        var splittedCellID = cellID.split('_');

        var time1 = splittedCellID[1];
        var time2 = splittedCellID[2];

        var jQuerySelector = "[id*='" + time1 + "_" + time2 +"']:not([id = '" + cellID + "'])";

        $(jQuerySelector).each(function () {
            selectSlotY($(this));
        });

        if(cell.hasClass('used')) {
            $(jQuerySelector).each(function () {
                deselectSlotY($(this));
            });
            cell.removeClass('used');
        } else
            cell.addClass('used');
    }

    function toggleSlotsHorizontal (cell) {
        var cellID = (cell.attr("id")).split('_');

        var PCID = cellID[1];

        var jQuerySelector = "[id^='" + PCID + "_']";

        $(jQuerySelector).each(function () {
            selectSlotX($(this));
        });

        if($(cell).hasClass('used')) {
            $(jQuerySelector).each(function () {
                deselectSlotX($(this));
            });
            $(cell).removeClass('used');
        } else
            $(cell).addClass('used');
    }

    function updateTimesHeader(isToday) {

        var slotTable = $('#slotTable');

        slotTable.floatThead('destroy');

        var currentTimeArray = (isToday ? times_today_DISPLAY : times_tomorrow_DISPLAY);
        var currentTimeArrayForIDs = (isToday ? times_today : times_tomorrow);

        var timesRow = document.createElement("tr");
        var PCNumbersTH = document.createElement("th");

        PCNumbersTH.appendChild(document.createTextNode("PC Numbers"));

        timesRow.appendChild(PCNumbersTH);

        var n = 0; // use to traverse for creating IDs
        for (var i = 0; i < currentTimeArray.length - 1; i++) {
            var th = document.createElement("th");

            th.appendChild(document.createTextNode(currentTimeArray[i]));

            th.setAttribute("id", "TIME_" + currentTimeArrayForIDs[n++] + "_" + currentTimeArrayForIDs[n] );
            th.className = 'vertSelect';
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
                url: '<?php echo base_url('admin/' . ADMIN_GET_ROOMS) ?>',
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

                    if (result.length != 0) {
                        out[0] = '<option value="0" selected >All Rooms</option>';

                        for (i = 1; i <= result.length; i++) {
                            out[i] = '<option value="' + result[i - 1].roomid + '" >' + result[i - 1].name + '</option>';
                        }
                        ;

                        $("#form_room").empty().append(out);

                        selectRoom("0");
                    }

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
            var interval;

            console.log(buildingid+"-"+roomid);

            $.ajax({
                url: '<?php echo base_url('admin/' . ADMIN_GET_BUSINESS_RULES) ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    roomid: roomid
                }
            })
                .done(function(result) {
                    interval = result[0].interval;

                    if (currentDeptID != result[0].departmentid) {
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
                        url: '<?php echo base_url('admin/' . ADMIN_GET_TIMES) ?>',
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
                        url: '<?php echo base_url('admin/' . ADMIN_GET_COMPUTERS) ?>',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            buildingid: buildingid,
                            roomid:roomid,
                            currdate: dateSelected,
                            currtime: currentTime
                        }
                    })
                })

                .done(function(result) {

                    computers = [];
                    reservations = [];
                    disabledSlots = [];


                    console.log(currentTime);

                    console.log(result['date']);
                    console.log(result);
                    console.log("done");

                    queriedComputers = result['computers'];
                    queriedReservations = result['reservations'];
                    queriedDisabledSlots = result['disabledslots'];

                    for(i=0;i<queriedComputers.length;i++){ // retrieve all computers from result
                        computers[i]=queriedComputers[i];
                    }

                    for(i=0;i<queriedReservations.length;i++){ // retrieve all reservations from result
                        reservations[i]=queriedReservations[i];
                    }

                    for(i=0;i<queriedDisabledSlots.length;i++){ // retrieve all reservations from result
                        disabledSlots[i]=queriedDisabledSlots[i];
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

            for (var i = 0; i < roomIDs.length; i++) {
                var roomTitleRow = document.createElement("tr");
                var roomTitleCell = document.createElement("th");

                roomTitleCell.appendChild(document.createTextNode("Room: " + roomNames[i]));
                roomTitleCell.setAttribute("colspan", currentTimeArray.length + 1);

                roomTitleRow.appendChild(roomTitleCell);

                $('#tableBody').append(roomTitleRow);

                for (var k = 0; k < computers.length; k++) {

                    if (computers[k].roomid == roomIDs[i]) {

                        var newTableRow = document.createElement("tr");
                        var newPCNoCell = document.createElement("th");

                        newPCNoCell.appendChild(document.createTextNode("PC No. " + computers[k].computerno));

                        newPCNoCell.setAttribute("id", "PC_" + computers[k].computerid);
                        newPCNoCell.className += 'horizSelect';

                        newTableRow.appendChild(newPCNoCell);

                        var n = 0; // counter for traversing through currentTimeArray

                        for (var m = 0; m < currentTimeArray.length - 1; m++) { // generate time slot cells
                            var slotCell = document.createElement("td");
                            var clickableSlot1 = document.createElement("div");

                            slotCell.className = "nopadding";

                            var taken = false;
                            var isDisabled = false;

                            var corresDisabled = null;

                            for (var p = 0; p < reservations.length; p++) {
                                if ((reservations[p].start_restime == currentTimeArray[n]) && (reservations[p].date == dateSelected) && (reservations[p].computerid == computers[k].computerid))
                                    taken = true;
                            }

                            for (var q = 0; q < disabledSlots.length; q++) {
                                if ((disabledSlots[q].start_time == currentTimeArray[n]) && (disabledSlots[q].computerid == computers[k].computerid)) {
                                    isDisabled = true;
                                    corresDisabled = disabledSlots[q];
                                    break;
                                }
                            }

                            var chosenTime1 = currentTimeArray[n++];
                            var chosenTime2 = currentTimeArray[n];

                            var computerID = computers[k].computerid;

                            var strID = computerID + "_" + dateSelected + "_" + chosenTime1 + "_" + chosenTime2;

                            clickableSlot1.setAttribute("id", strID);

                            clickableSlot1.className = "slotCell pull-left";

                            var currentSlotID = "#" + strID;

                            if (!taken) {

                                if (isDisabled) {
                                    clickableSlot1.className = clickableSlot1.className + " disabled";
                                    clickableSlot1.setAttribute("id", strID + "_" + corresDisabled.<?=COLUMN_DISABLED_SLOT_ID?>);
                                } else {
                                    clickableSlot1.className = clickableSlot1.className + " enabled";
                                }

                            } else {
                                clickableSlot1.className = clickableSlot1.className + " taken";
                            }

                            if (($.inArray(clickableSlot1.getAttribute("id"), slotsPicked)) > -1) {
                                clickableSlot1.className = clickableSlot1.className + " selected";
                            }

                            slotCell.appendChild(clickableSlot1);

                            newTableRow.appendChild(slotCell);
                        }

                        $('#tableBody').append(newTableRow);
                    }

                }

            }
        }
    }
</script>

<div id="disableModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Disabling Selected Slots</h4>
            </div>
            <div class="modal-body">
                <form>
                    <label>Disable slots until: (Selected date: <span class = "currentTimeContainer"></span>)</label>
                    <div class = "row">

                        <div class = "col-md-2 col-md-offset-4">
                            <div class="form-group">
                                <small><label for="startHour">End Hour:</label></small>
                                <select class="form-control" id="endHour">
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        echo "<option value=" . $i .">" . $i . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class = "col-md-2">
                            <div class = "form-group">
                                <small><label for="startHour">End Min:</label></small>
                                <select class="form-control" id="endMinute">
                                    <?php
                                    for ($i = 0; $i < 60; $i++) {
                                        echo "<option value=" . $i .">" . $i . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id = "disable-btn" type="button" class="btn btn-success" data-dismiss="modal">Continue</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>

    </div>
</div>

<div id="disableAllModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Disabling Selected Slots</h4>
            </div>
            <div class="modal-body">
                <form>
                    <label>Disable slots until: (Selected date: <span class = "currentTimeContainer"></span>)</label>
                    <div class = "row">

                        <div class = "col-md-2 col-md-offset-4">
                            <div class="form-group">
                                <small><label for="startHour">End Hour:</label></small>
                                <select class="form-control" id="endHourAll">
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        echo "<option value=" . $i .">" . $i . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class = "col-md-2">
                            <div class = "form-group">
                                <small><label for="startHour">End Min:</label></small>
                                <select class="form-control" id="endMinuteAll">
                                    <?php
                                    for ($i = 0; $i < 60; $i++) {
                                        echo "<option value=" . $i .">" . $i . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Continue</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>

    </div>
</div>

<div class = "container">

    <div class = "col-md-12">

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

            <div class = "col-md-4">
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

            <div class = "col-md-3">
                <div class = "panel">
                    <div class = "panel-body">
                        <div class = "row opnSlotsBtn-container">
                            <button id = "enableAll-btn" class = "btn btn-success btn-block">Enable all slots in room</button>
                        </div>

                        <div class = "row clsSlotsBtn-container">
                            <button id = "disable-all-modal-btn" class = "btn btn-danger btn-block" data-toggle = "modal" data-target = "#disableAllModal">Disable all slots in room</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class = "row">
            <div class = "col-md-10 col-md-offset-1">
                <div id = "slots" class = "panel panel-default">
                    <div class = "panel-body nopadding">
                        <table id = "slotTable" class = "table table-bordered">
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class = "row">
            <div class = "col-md-10 col-md-offset-1">
                <div class = "col-md-6 col-md-offset-6 text-right">
                    <button id = "enable-btn" class = "btn btn-success">Enable slot(s)</button>
                    <button id = "disable-modal-btn" class = "btn btn-danger" data-toggle = "modal" data-target = "#disableModal">Disable slot(s)</button>
                    <button id = "toggle-btn" class = "btn btn-default">Toggle slot(s)</button>
                </div>
            </div>
        </div>

    </div>

</div>