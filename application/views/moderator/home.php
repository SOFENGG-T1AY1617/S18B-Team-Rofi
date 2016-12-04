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
    var slotsDisabled = [];
    var computers = [];
    var reservations = [];
    var request;
    var dateToday = "<?=date("Y-m-d")?>";
    var dateSelected = dateToday;
    var currentDeptID = 0;
    var roomName;

    var times_today;
    var times_tomorrow;
    var times_today_DISPLAY;
    var times_tomorrow_DISPLAY;

    $(document).ready(function() {

        $(document).on( "click", ".slotCell:not(.selected)",function() {
            selectSlot($(this));
        });

        $(document).on( "click", ".slotCell.selected",function() {
            deselectSlot($(this));
        });

        selectRoom (<?php echo $roomid;?>);
    });

    function slotCheckedIn (slot) {
        if (slot.hasClass('disabled'))
            slot.removeClass('disabled');

        slot.addClass('enabled');
    }

    function slotCheckedOut (slot) {
        if (slot.hasClass('enabled'))
            slot.removeClass('enabled');

        slot.addClass('disabled');
        slotsDisabled.push(slot.attr('id'));
    }

    function selectSlot (slot) {
        if (($.inArray(slot.attr('id'), slotsPicked)) == -1)
            slotsPicked.push(slot.attr("id"));

        slot.addClass ('selected');

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

        updateSelectedSlots();
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
                $(slotContainerID).empty();

                for (var i = 0; i < result.length; i++) {
                    $(slotContainerID).append(
                        "<div>" +
                        "<span class = 'col-md-1'>X</span>" +
                        "<span class = 'col-md-1'>" + result[i].roomName + "</span>" +
                        "<span class = 'col-md-2'>Pc No. " + result[i].compNo + "</span>" +
                        "<span class = 'col-md-4'>someone@gmail.com</span>" +
                        "<span class = 'col-md-1 text-center'>" + result[i].start + "</span>" +
                        "<span class = 'col-md-1 text-center'> - </span>" +
                        "<span class = 'col-md-1 text-center'>" + result[i].end + "</span>" +
                        "<span class = 'col-md-offset-1'></span>" +
                        "</div>"
                    );
                }

            })
            .fail(function() {
                $(slotContainerID).empty();
                console.log("fail");

            })
            .always(function() {

                console.log("complete");
            });

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

            th.setAttribute("id", "TIME_" + currentTimeArrayForIDs[n++] + "_" + currentTimeArrayForIDs[n]);
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
                        url: '<?php echo base_url('moderator/' . MODERATOR_GET_COMPUTERS) ?>',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            roomid:roomid,
                            currdate: dateToday
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
                            for (var p = 0; p < reservations.length; p++) {
                                if ((reservations[p].start_restime == currentTimeArray[n]) && (reservations[p].date == dateSelected) && (reservations[p].computerid == computers[k].computerid))
                                    taken = true;
                            }

                            var chosenTime1 = currentTimeArray[n++];
                            var chosenTime2 = currentTimeArray[n];

                            if (!taken) {
                                var computerID = computers[k].computerid;
                                var computerNo = computers[k].computerno;

                                var strID = computerID + "_" + dateSelected + "_" + chosenTime1 + "_" + chosenTime2;

                                clickableSlot1.setAttribute("id", strID);

                                clickableSlot1.className = "slotCell pull-left";

                                var currentSlotID = "#" + strID;

                                if (computers[k].statusid == <?php echo PC_DISABLED ?>) {
                                    clickableSlot1.className = clickableSlot1.className + " disabled";
                                } else {
                                    clickableSlot1.className = clickableSlot1.className + " enabled";
                                }

                                if (($.inArray(clickableSlot1.getAttribute("id"), slotsPicked)) > -1) {
                                    clickableSlot1.className = clickableSlot1.className + " selected";
                                }

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

<div class = "row col-md-10 col-md-offset-1">

    <div class = "panel panel-default">

        <div class = "panel-body">

            <div class = "row">

                <div class = "col-md-10 col-md-offset-1">
                    <div class = "panel panel-default">
                        <div class = "panel-heading">
                            Slots Currently Selected:

                        </div>
                        <div class = "panel-body">
                            <div id = "slots_selected">
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

            </div>

        </div>

    </div>

</div>


</body>
