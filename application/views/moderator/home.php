<?php
/**
 * Created by PhpStorm.
 * User: patricktobias
 * Date: 29/11/2016
 * Time: 2:46 PM
 */


?>

<body>

<script>

    var slotsPicked = [];
    var computers = [];
    var reservations = [];
    var disabledSlots = [];
    var request;
    var dateToday = "<?=date("Y-m-d")?>";
    var dateSelected = dateToday;

    var currentTime = "<?=date("H:m:s"); ?>";

    var currentDeptID = 0;
    var roomName;

    var times;
    var times_DISPLAY;

    $(document).ready(function() {

        $(document).on( "click", ".slotCell.reserved",function() {
            selectSlot($(this));
        });

        $(document).on( "click", ".slotCell.reserved.selected",function() {
            deselectSlot($(this));
        });

        $(document).on( "click", ".delete-button",function() {
            var slotID = $(this).attr('id');

            deselectSlot($("[id = '" + slotID + "']"));
        });

        $("#markPresent").click( function() {
            markSlotsPresent();
        } );

        $("#verifySlot").click(function () {
            verifySlots();
        });

        $("#removeReservation").click(function () {
            removeReservations();
        });

        $("#presentButton").click (function() {

            if (slotsPicked.length == 0)
                toastr.info("You must select slots before performing actions.", "Hold on!");
            else if ($(this).hasClass('disabled'))
                toastr.error("One or more of the selected slots is/are still unverified", "Oops!");

        });

        $("#verifyButton").click (function() {

            if (slotsPicked.length == 0)
                toastr.info("You must select slots before performing actions.", "Hold on!");

        });

        $("#removeButton").click (function() {

            if (slotsPicked.length == 0)
                toastr.info("You must select slots before performing actions.", "Hold on!");

        });

        selectRoom (<?php echo $roomid;?>);
        updateAllButtons();
    });

    function markSlotsPresent () {
        if (slotsPicked.length == 0) {

            toastr.info("You must select slots before performing actions.", "Hold on!");

        } else if (!existsAnUnverifiedSelectedSlot()) {
            $.ajax({
                url: '<?php echo base_url('moderator/' . MODERATOR_SET_RESERVATIONS_PRESENT) ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    slots: slotsPicked
                }
            })
                .done(function (result) {

                    toastr.success(result.updated + " reservations were updated!", result.updated + " reservation/s is/are now present");

                    for (var i = 0; i < slotsPicked.length; i++)
                        GUImarkSlotPresent($("[id = '" + slotsPicked[i] + "']"));

                    updateSelectedSlots();

                })
                .fail(function () {

                    console.log("fail");

                })
                .always(function () {

                    console.log("complete");

                });
        } else {
            toastr.error("One or more of the selected slots is/are still unverified", "Oops!");
        }
    }

    function verifySlots () {
        if (slotsPicked.length == 0) {

            toastr.info("You must select slots before performing actions.", "Hold on!");

        } else {
            $.ajax({
                url: '<?php echo base_url('moderator/' . MODERATOR_VERIFY_RESERVATION) ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    slots: slotsPicked
                }
            })
                .done(function (result) {

                    toastr.success(result['updated'] + " reservations were updated!", result['updated'] + " reservation/s is/are now verified");

                    for (var i = 0; i < slotsPicked.length; i++)
                        GUIverifySlot($("[id = '" + slotsPicked[i] + "']"));

                    updateSelectedSlots();
                    updateAllButtons();

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

    function removeReservations () {
        if (slotsPicked.length == 0) {

            toastr.info("You must select slots before performing actions.", "Hold on!");

        } else {
            $.ajax({
                url: '<?php echo base_url('moderator/' . MODERATOR_REMOVE_RESERVATION) ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    slots: slotsPicked
                }
            })
                .done(function (result) {

                    toastr.success(slotsPicked.length + " reservations were removed!", "Selected reservation/s is/are removed");

                    for (var i = 0; i < slotsPicked.length; i++)
                        GUIremoveReservation($("[id = '" + slotsPicked[i] + "']"));

                    slotsPicked = [];

                    updateSelectedSlots();
                    updateAllButtons();


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

    function GUImarkSlotPresent (slot) {

        if (!slot.hasClass('present'))
            slot.addClass('present');

    }

    function GUIverifySlot (slot) {

        if (!slot.hasClass('verified'))
            slot.addClass('verified');

    }

    function GUIremoveReservation (slot) {

        slot.removeClass('present');
        slot.removeClass('verified');
        slot.removeClass('reserved');

    }

    function disableButton (button) {

        if (!button.hasClass('disabled')) {
            console.log("DISABLED PRESENT");
            button.addClass('disabled');

            button.attr('data-toggle', '');
        }

    }

    function enableButton (button) {

        if (button.hasClass('disabled')) {
            console.log("ENABLED PRESENT");
            button.removeClass('disabled');

            button.attr('data-toggle', 'modal');
        }

    }

    function selectSlot (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selected');

        updateAllButtons();
        updateSelectedSlots();
    }

    function deselectSlot (slot) {
        var slotID = slot.attr("id");

        if (($.inArray(slotID, slotsPicked)) > -1) {
            var existIndex = slotsPicked.indexOf(slotID);
            slotsPicked.splice(existIndex, 1);

            if (slot.hasClass('selected'))
                slot.removeClass('selected');
        }

        updateAllButtons();
        updateSelectedSlots();
    }

    function updatePresentButton () {
        var presentButton = $("#presentButton");

        if (existsAnUnverifiedSelectedSlot() || slotsPicked.length == 0)
            disableButton(presentButton);
        else
            enableButton(presentButton);
    }

    function updateVerifyButton () {
        var verifyButton = $("#verifyButton");

        if (slotsPicked.length == 0) {
            disableButton(verifyButton);
        } else {
            enableButton(verifyButton);
        }

    }

    function updateRemoveButton () {
        var removeButton = $("#removeButton");

        if (slotsPicked.length == 0) {
            disableButton(removeButton);
        } else {
            enableButton(removeButton);
        }

    }

    function updateAllButtons () {
        updatePresentButton();
        updateVerifyButton();
        updateRemoveButton();
    }

    function existsAnUnverifiedSelectedSlot () {
        var exists = false;

        for (var i = 0; i < slotsPicked.length; i++) {
            exists = !($("[id = '" + slotsPicked[i] + "']").hasClass('verified'));

            if (exists)
                return exists;
        }

        return exists;
    }

    function updateSelectedSlots () { // for the upper part
        var slotContainerID = "#slots_selected";

        $.ajax({
            url: '<?php echo base_url('moderator/' . MODERATOR_DECODE_SLOTS) ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                slots: slotsPicked
            }
        })
            .done(function(result) {

                var verifStatus = null;
                var colorStatus = null;
                var attendance = "";

                var out = [];

                for (var i = 0; i < result.length; i++) {
                    if (result[i].verified == 1) {
                        verifStatus = "Verified";
                        colorStatus = "green";

                        if (result[i].attendance == 1)
                            attendance = "[Claimed]";
                        else
                            attendance = " [Unclaimed]";

                    } else {
                        verifStatus = "Unverified";
                        colorStatus = "red";
                    }

                    out[i] = ("<div class = 'slotRow'>" +
                        "<span id = " + slotsPicked[i] + " class = 'col-md-1 delete-button text-center'>X</span>" +
                        "<span class = 'col-md-1'>" + result[i].roomName + "</span>" +
                        "<span class = 'col-md-2'>Pc No. " + result[i].compNo + "</span>" +
                        "<span class = 'col-md-2'>" + result[i].userid + " </span>" +
                        "<span class = 'col-md-1'> <span class = '" + colorStatus + "'>" + verifStatus + "</span> </span>" +
                        "<span class = 'col-md-2'> <span class = '" + colorStatus + "'>" + attendance + "</span> </span>" +
                        "<span class = 'col-md-3 text-center pull-right'>" + result[i].start + " - " + result[i].end + "</span>" +
                        "</div>");

                }

                if (out.length != slotsPicked.length)
                    updateSelectedSlots();
                else
                    $(slotContainerID).html(out);

                if (slotsPicked.length == 0)
                    $(slotContainerID).empty();

            })
            .fail(function() {
                $(slotContainerID).empty();
                console.log("fail");

            })
            .always(function() {

                console.log("complete");
            })
            .then(function(result) {

            });

    }

    function updateTimesHeader() {

        var slotTable = $('#slotTable');

        slotTable.floatThead('destroy');

        var currentTimeArray = times_DISPLAY;

        var timesRow = document.createElement("tr");
        var PCNumbersTH = document.createElement("th");

        PCNumbersTH.appendChild(document.createTextNode("PC Numbers"));

        timesRow.appendChild(PCNumbersTH);

        var n = 0; // use to traverse for creating IDs
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
                url: '<?php echo base_url('moderator/' . MODERATOR_BUSINESS_RULES) ?>',
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

                    currentDeptID = result['departmentid'];
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
                        url: '<?php echo base_url('moderator/' . MODERATOR_GET_TIMES) ?>',
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

                    return $.ajax({
                        url: '<?php echo base_url('moderator/' . MODERATOR_GET_COMPUTERS) ?>',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            roomid:roomid,
                            currdate: dateToday,
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

                    var queriedComputers = result['computers'];
                    var queriedReservations = result['reservations'];
                    var queriedDisabledSlots = result['disabledslots'];

                    for(i=0;i<queriedComputers.length;i++){ // retrieve all computers from result
                        computers[i]=queriedComputers[i];
                    }

                    for(i=0;i<queriedReservations.length;i++){ // retrieve all reservations from result
                        reservations[i]=queriedReservations[i];
                    }

                    for (i = 0; i < queriedDisabledSlots.length; i++) {
                        disabledSlots[i] = queriedDisabledSlots[i];
                    }

                    console.log(reservations);

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

            var currentTimeArray = times;

            for (var i = 0; i < roomIDs.length; i++) {
                var roomTitleRow = document.createElement("tr");
                var roomTitleCell = document.createElement("th");

                roomTitleCell.appendChild(document.createTextNode("Room: " + (roomName = roomNames[i])));
                roomTitleCell.setAttribute("colspan", currentTimeArray.length + 1);

                roomTitleRow.appendChild(roomTitleCell);

                $('#tableBody').append(roomTitleRow);

                for (var k = 0; k < computers.length; k++) {

                    if (computers[k].roomid == roomIDs[i]) {

                        var newTableRow = document.createElement("tr");
                        var newPCNoCell = document.createElement("th");

                        newPCNoCell.appendChild(document.createTextNode("PC No. " + computers[k].computerno));

                        newPCNoCell.setAttribute("id", "PC_" + computers[k].computerid);

                        newTableRow.appendChild(newPCNoCell);

                        var n = 0; // counter for traversing through currentTimeArray

                        for (var m = 0; m < currentTimeArray.length - 1; m++) { // generate time slot cells
                            var slotCell = document.createElement("td");
                            var clickableSlot1 = document.createElement("div");

                            slotCell.className = "nopadding";

                            var taken = false;
                            var corresReservation = null;
                            var isDisabled = false;

                            for (var p = 0; p < reservations.length; p++) {
                                if ((reservations[p].start_restime == currentTimeArray[n]) && (reservations[p].date == dateSelected) && (reservations[p].computerid == computers[k].computerid)) {
                                    taken = true;
                                    corresReservation = reservations[p];
                                    break;
                                }
                            }

                            for (var q = 0; q < disabledSlots.length; q++) {
                                if ((disabledSlots[q].start_time == currentTimeArray[n]) && (disabledSlots[q].computerid == computers[k].computerid)) {
                                    isDisabled = true;
                                    break;
                                }
                            }

                            var chosenTime1 = currentTimeArray[n++];
                            var chosenTime2 = currentTimeArray[n];

                            var computerID = computers[k].computerid;
                            var computerNo = computers[k].computerno;

                            var strID = computerID + "_" + dateSelected + "_" + chosenTime1 + "_" + chosenTime2;

                            clickableSlot1.setAttribute("id", strID);

                            clickableSlot1.className = "slotCell pull-left";

                            if (isDisabled) {
                                clickableSlot1.className = clickableSlot1.className + " disabled";
                            } else {
                                clickableSlot1.className = clickableSlot1.className + " enabled";
                            }

                            if (($.inArray(clickableSlot1.getAttribute("id"), slotsPicked)) > -1) {
                                clickableSlot1.className = clickableSlot1.className + " selected";
                            }

                            if (taken){
                                clickableSlot1.className = clickableSlot1.className + " reserved";
                                clickableSlot1.setAttribute("id", strID + "_" + corresReservation.reservationid);

                                if (corresReservation.<?php echo COLUMN_ATTENDANCE; ?> == 1)
                                    clickableSlot1.className = clickableSlot1.className + " present";

                                if (corresReservation.<?php echo COLUMN_VERIFIED; ?> == 1)
                                    clickableSlot1.className = clickableSlot1.className + " verified";

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



<?php
include 'm_navbar.php';
?>

<div id="removeMessage" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Prompt Message</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove the selected slots?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                <button id = "removeReservation" type="button" class="btn btn-success" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<div id="verifyMessage" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Prompt Message</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to verify the selected slots?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                <button id = "verifySlot" type="button" class="btn btn-success" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<div id="presentMessage" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Prompt Message</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark the selected slots present or checked in?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                <button id = "markPresent" type="button" class="btn btn-success" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<div class = "row col-md-10 col-md-offset-1">

    <div class = "panel panel-default">

        <div class = "panel-body">

            <div class = "row">

                <div class = "col-md-8 col-md-offset-1">
                    <div class = "panel panel-default">
                        <div class = "panel-heading">
                            <b><?php echo $roomname ?></b> Reservations Currently Selected: <b class = "pull-right"><?php echo date("M.d, Y"); ?></b>

                        </div>
                        <div class = "panel-body">
                            <div id = "slots_selected">
                            </div>
                        </div>
                    </div>
                </div>

                <div id = "mod_controls_container" class = "col-md-2">
                    <div class = "pull-right">
                        <button id = "presentButton" class = "btn btn-success col-md-12" data-toggle="modal" data-target="#presentMessage">Mark Present</button>
                        <button id = "verifyButton" class = "btn btn-success col-md-12" data-toggle="modal" data-target="#verifyMessage">Verify Slots</button>
                        <button id = "removeButton" class = "btn btn-danger col-md-12" data-toggle="modal" data-target="#removeMessage">Remove</button>
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
                <div class = "col-md-10 col-md-offset-2">

                    <div id = "legend">
                        <div class = "row">

                            <div class = "col-md-2">
                                <div class="legend enabled pull-left"></div> Enabled
                            </div>

                            <div class = "col-md-2">
                                <div class="legend unverified pull-left"></div> Unverified
                            </div>

                            <div class = "col-md-2">
                                <div class="legend verified pull-left"></div> Verified
                            </div>

                            <div class = "col-md-2">
                                <div class="legend present pull-left"></div> Present
                            </div>

                            <div class = "col-md-2">
                                <div class="legend disabled pull-left"></div> Disabled
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            </div>

        </div>

    </div>

</div>


</body>
