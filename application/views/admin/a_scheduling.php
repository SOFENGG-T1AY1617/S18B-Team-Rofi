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
    var allSlotsDisplayed = []; // for enabling and disabling all slots
    var slotsPicked = [];
    var computers = [];
    var reservations = [];

    var disabledSlots = []; // for backend

    var request;
    var dateToday = "<?=date("Y-m-d")?>";
    var dateSelected = dateToday;
    var currentDeptID = 0;

    var currentTime = "<?=date("H:i:s"); ?>";

    var times;
    var times_DISPLAY;

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
            updateTimesHeader();

            if($("#form_building").val()!=null){
                selectRoom($("#form_room").val());
            }

        });

        $("#enable-btn").click(function () {
            enableSlots(slotsPicked);
        });

        $("#disable-btn").click(function () {
            var hour = $("#endHour").val();
            var minute = $("#endMinute").val();

            disableSlots(slotsPicked, hour, minute);
        });

        $("#enableAll-btn").click(function () {
            enableSlots(allSlotsDisplayed);
        });

        $("#disableAll-btn").click(function () {
            var hour = $("#endHourAll").val();
            var minute = $("#endMinuteAll").val();

            disableSlots(allSlotsDisplayed, hour, minute);
        });

        updateDisplayTimeInModal();

        var enable_button = $("#enable-modal-btn");
        var disable_button = $("#disable-modal-btn");
        var enableAll_button = $("#enableAll-all-modal-btn");
        var disableAll_button = $("#disable-all-modal-btn");

        enable_button.click(function() {
            if ($(this).hasClass("disabled"))
                toastr.info("You must view a room and select slots before performing this action", "Hold On!");
        });

        disable_button.click(function() {
            if ($(this).hasClass("disabled"))
                toastr.info("You must view a room and select slots before performing this action", "Hold On!");
        });

        enableAll_button.click(function() {
            if ($(this).hasClass("disabled"))
                toastr.info("You must view a room with slots before performing this action", "Hold On!");
        });

        disableAll_button.click(function() {
            if ($(this).hasClass("disabled"))
                toastr.info("You must view a room with slots before performing this action", "Hold On!");
        });


    });

    function disableSlots (slotArray, hour, minute) {
        if (slotArray.length == 0) {

            toastr.info("You must select slots before performing actions.", "Hold on!");

        } else {
            var chunk = 10;

            var isLastChunk = false;

            var slotsChunk = [];
            var updatedSlots = 0;

            $("#processing_message").css("visibility", "visible");
            $("#tableHead").css("visibility", "hidden");
            for (var i = 0; i < slotArray.length; i += chunk) {
                slotsChunk = slotArray.slice(i, i + chunk);

                $.ajax({
                    url: '<?php echo base_url('admin/' . ADMIN_DISABLE_SLOTS) ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        slots: slotsChunk,
                        currentDate: dateSelected,
                        hour: hour,
                        minute: minute
                    }
                })
                    .done(function (result) {

                        updatedSlots += result['updated'];

                        var newIDs = result['newIDs'];
                        var updatedIDs = result['updatedIDs'];

                        isLastChunk = updatedIDs[ updatedIDs.length - 1 ] == slotArray[ slotArray.length - 1 ];

                        for (var j = 0; j < updatedIDs.length; j++) {
                            var currentSlot = $("[id = '" + updatedIDs[j] + "']");

                            currentSlot.attr("id", currentSlot.attr("id") + "_" + newIDs[j]);

                            var existIndex = slotArray.indexOf(updatedIDs[j]);

                            slotArray[existIndex] = currentSlot.attr("id");

                            disableSlot(currentSlot);
                        }

                        if (isLastChunk) {
                            $("#processing_message").css("visibility", "hidden");
                            $("#tableHead").css("visibility", "visible");
                            toastr.success(updatedSlots + " slots were updated!", updatedSlots + " slot/s is/are now disabled");
                        }

                        //updateSelectedSlots();

                    })
                    .fail(function () {

                        toastr.error("Slots were not updated.", "Oops!");


                        $("#processing_message").css("visibility", "hidden");
                        $("#tableHead").css("visibility", "visible");
                        console.log("fail");

                    })
                    .always(function () {

                        console.log("complete");

                        syncArrayWithAllSlotsArray(slotArray);
                        syncArrayWithSlotsPickedArray(slotArray);

                        updateAllButtons();

                    });
            }

        }


    }

    function enableSlots (slotArray) {
        if (slotArray.length == 0) {

            toastr.info("You must select slots before performing actions.", "Hold on!");

        } else {
            var chunk = 10;
            var slotsChunk = [];
            var updatedSlots = 0;

            var isLastChunk = false;


            $("#processing_message").css("visibility", "visible");
            $("#tableHead").css("visibility", "hidden");
            for (var i = 0; i < slotArray.length; i += chunk) {
                slotsChunk = slotArray.slice(i, i + chunk);

                $.ajax({
                    url: '<?php echo base_url('admin/' . ADMIN_ENABLE_SLOTS) ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        slots: slotsChunk
                    }
                })
                    .done(function (result) {

                        updatedSlots+=result['updated'];

                        var updatedIDs = result['updatedIDs'];

                        isLastChunk = updatedIDs[ updatedIDs.length - 1 ] == slotArray[ slotArray.length - 1 ];

                        for (var j = 0; j < updatedIDs.length; j++) {
                            var currentSlot = $("[id = '" + updatedIDs[j] + "']");

                            var existIndex = slotArray.indexOf(updatedIDs[j]);

                            var IDArray = updatedIDs[j].split('_');

                            currentSlot.attr("id", IDArray[0] + "_" + IDArray[1] + "_" + IDArray[2] + "_" + IDArray[3]);

                            enableSlot(currentSlot);

                            slotArray[existIndex] = currentSlot.attr("id");

                        }

                        if (isLastChunk) {
                            $("#processing_message").css("visibility", "hidden");
                            $("#tableHead").css("visibility", "visible");
                            toastr.success(updatedSlots + " slots were updated!", updatedSlots + " slot/s is/are now enabled");
                        }

                        //updateSelectedSlots();

                    })
                    .fail(function () {

                        toastr.error("Slots were not updated.", "Oops!");

                        $("#processing_message").css("visibility", "hidden");
                        $("#tableHead").css("visibility", "visible");
                        console.log("fail");

                    })
                    .always(function () {

                        console.log("complete");
                        syncArrayWithAllSlotsArray(slotArray);
                        syncArrayWithSlotsPickedArray(slotArray);

                        updateAllButtons();

                    });
            }

        }
    }

    function syncArrayWithAllSlotsArray (slotArray) {
        for (var i = 0; i < slotArray.length; i++) {

            var splitID = slotArray[i].split("_");

            var comparisonKey = splitID[0] + "_" + splitID[1] + "_" + splitID[2] + "_" + splitID[3];

            for (var j = 0; j < allSlotsDisplayed.length; j++) {

                var splitTargetID = allSlotsDisplayed[j].split("_");

                var targetKey = splitTargetID[0] + "_" + splitTargetID[1] + "_" + splitTargetID[2] + "_" + splitTargetID[3];

                if (targetKey == comparisonKey) {
                    allSlotsDisplayed[j] = slotArray[i];
                    break;
                }
            }

        }

    }

    function syncArrayWithSlotsPickedArray (slotArray) {
        for (var i = 0; i < slotArray.length; i++) {

            var splitID = slotArray[i].split("_");

            var comparisonKey = splitID[0] + "_" + splitID[1] + "_" + splitID[2] + "_" + splitID[3];

            for (var j = 0; j < slotsPicked.length; j++) {

                var splitTargetID = slotsPicked[j].split("_");

                var targetKey = splitTargetID[0] + "_" + splitTargetID[1] + "_" + splitTargetID[2] + "_" + splitTargetID[3];

                if (targetKey == comparisonKey) {
                    slotsPicked[j] = slotArray[i];
                    break;
                }

            }

        }
    }

    function updateDisplayTimeInModal () {
        var currentTimeContainer = $(".currentTimeContainer");

        currentTimeContainer.empty();

        currentTimeContainer.append(dateSelected);
    }

    function updateButton (button, enable) {

        if (!enable) {
            button.addClass("disabled");
            button.attr("data-toggle", "");
        } else {
            button.removeClass("disabled");
            button.attr("data-toggle", "modal");
        }

    }

    function updateAllButtons() {
        var enable_button = $("#enable-modal-btn");
        var disable_button = $("#disable-modal-btn");
        var enableAll_button = $("#enableAll-all-modal-btn");
        var disableAll_button = $("#disable-all-modal-btn");

        updateButton(enable_button, hasDisabledSlot(slotsPicked));
        updateButton(disable_button, hasEnabledSlot(slotsPicked));
        updateButton(enableAll_button, hasDisabledSlot(allSlotsDisplayed));
        updateButton(disableAll_button, hasEnabledSlot(allSlotsDisplayed));

        if (allSlotsDisplayed.length != 0) {
            enableAll_button.unbind();
            disableAll_button.unbind();

            if (enableAll_button.hasClass("disabled")) {
                enableAll_button.click(function () {
                    toastr.info ("All slots are currently enabled. There is no use in enabling them all.", "Hold on!");
                });
            }

            if (disableAll_button.hasClass("disabled")) {
                disableAll_button.click(function () {
                    toastr.info ("All slots are currently disabled. There is no use in disabling them all.", "Hold on!");
                });
            }
        }

        enable_button.unbind();
        disable_button.unbind();

        if (slotsPicked.length == 0) {

            if (enable_button.hasClass("disabled")) {
                enable_button.click(function () {
                    toastr.info ("There are no slots selected. Please select slots to perform this action.", "Hold on!");
                });
            }

            if (disable_button.hasClass("disabled")) {
                disable_button.click(function () {
                    toastr.info ("There are no slots selected. Please select slots to perform this action.", "Hold on!");
                });
            }
        }
    }

    function hasDisabledSlot(slotArray) {

        for (var i = 0; i < slotArray.length; i++) {
            var currentSlot = $("[id='" + slotArray[i] + "']");

            if (isDisabled(currentSlot))
                return true;
        }

        return false;

    }

    function hasEnabledSlot(slotArray) {

        for (var i = 0; i < slotArray.length; i++) {
            var currentSlot = $("[id='" + slotArray[i] + "']");

            if (isEnabled(currentSlot))
                return true;
        }

        return false;

    }

    function isDisabled(slot) {
        return slot.hasClass("disabled");
    }

    function isEnabled(slot) {
        return slot.hasClass("enabled");
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

    function selectSlot (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selected');

        updateAllButtons();
    }

    function selectSlotX (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selectedX');

        if (slot.hasClass('selected'))
            slot.removeClass('selected');

        updateAllButtons();
    }

    function selectSlotY (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selectedY');

        if (slot.hasClass('selected'))
            slot.removeClass('selected');

        updateAllButtons();
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

        updateAllButtons();
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

        updateAllButtons();
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

        updateAllButtons();
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

    function updateTimesHeader() {

        var slotTable = $('#slotTable');

        slotTable.floatThead('destroy');

        var currentTimeArray = times_DISPLAY;
        var currentTimeArrayForIDs = times;

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
            var start_time;
            var end_time;

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
                    interval = result['interval'];
                    start_time = result['start_time'];
                    end_time = result['end_time'];

                    console.log("START TIME: " + start_time);
                    console.log("END TIME: " + end_time);

                    currentDeptID = result['departmentid'];
                })
                .fail(function() {
                    console.log("get business rules fail");
                })
                .always(function() {
                    console.log("complete");
                })
                .then(function () {
                    console.log("PROMISE FOR TIMES FULFILL");

                    return $.ajax({ // PROCEED TO PROMISE
                        url: '<?php echo base_url('admin/' . ADMIN_GET_TIMES) ?>',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            interval: interval,
                            start_time: start_time,
                            end_time: end_time,
                            date: dateSelected
                        }
                    })
                })

                // FOR PROMISE
                .done (function (result) {
                    times = result['times'];
                    times_DISPLAY = result['times_DISPLAY'];

                    updateTimesHeader();
                })
                .fail (function () {

                })
                .then (function () {
                    // get computers
                    console.log("PROMISE for COMPUTERS FULFILL");

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
                    console.log("failed to get computers");
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
            allSlotsDisplayed = [];

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

            var currentTimeArray = times;

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

                            allSlotsDisplayed.push(clickableSlot1.getAttribute("id"));

                            slotCell.appendChild(clickableSlot1);

                            newTableRow.appendChild(slotCell);
                        }

                        $('#tableBody').append(newTableRow);
                    }

                }

            }

            updateAllButtons();
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
                <h4 class="modal-title">Disabling All Slots</h4>
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
                <button id = "disableAll-btn" type="button" class="btn btn-success" data-dismiss="modal">Continue</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>

    </div>
</div>

<div id="enableModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Enabling Selected Slots</h4>
            </div>
            <div class="modal-body">
                Are you sure you want to enable the slots selected?
            </div>
            <div class="modal-footer">
                <button id = "enable-btn" type="button" class="btn btn-success" data-dismiss="modal">Continue</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>

    </div>
</div>

<div id="enableAllModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Enabling All Slots</h4>
            </div>
            <div class="modal-body">
                Are you sure you want to enable all slots located in the currently selected room?
            </div>
            <div class="modal-footer">
                <button id = "enableAll-btn" type="button" class="btn btn-success" data-dismiss="modal">Continue</button>
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
                            <button id = "enableAll-all-modal-btn" class = "btn btn-success btn-block disabled" data-toggle = "" data-target = "#enableAllModal">Enable all slots in room</button>
                        </div>

                        <div class = "row clsSlotsBtn-container">
                            <button id = "disable-all-modal-btn" class = "btn btn-danger btn-block disabled" data-toggle = "" data-target = "#disableAllModal">Disable all slots in room</button>
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
                    <button id = "enable-modal-btn" class = "btn btn-success disabled" data-toggle = "" data-target = "#enableModal">Enable slot(s)</button>
                    <button id = "disable-modal-btn" class = "btn btn-danger disabled" data-toggle = "" data-target = "#disableModal">Disable slot(s)</button>
                </div>
            </div>
        </div>

    </div>



</div>
<div class = "message parent" id = "processing_message">
    Processing...
</div>
