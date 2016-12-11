
<link href="<?=base_url()?>/assets/css/clockpicker.css" rel="stylesheet">
<script src="<?=base_url()?>/assets/js/clockpicker.js"></script>

<style>
    .timeinput{
        display:inline-block!important;
        width:100px!important;
    }
    .selTime{
        padding-right: 5px;
        padding-left: 0px;
    }
</style>

<script xmlns="http://www.w3.org/1999/html">

    $(document).ready(function(){
        $('.clockpicker').clockpicker();
    });




    function setInputRules() {
        $('input[type=number]').numeric();
        $(".number-input").keypress(function(event) {
            if ( event.which == 45 || event.which == 189 ) {
                event.preventDefault();
            }
        });

        $(".number-input").bind('paste', function(e) {
            var pasteData = e.clipboardData.getData('text/plain');
            if (pasteData.match(/[^0-9]/))
                e.preventDefault();
        }, false);
    }
    var initialTable;
    var initialFooter;
    function changeViewToEdit(table, footer){
        console.log(footer);
        var tableA = document.getElementById(table);
        var footerA = document.getElementById(footer);

        initialTable = tableA.innerHTML;
        initialFooter=footerA.innerHTML;
        var rows = tableA.rows;
        //var tID = table.id;
        //var fID = footer.id;
//        console.log("Rows: " + rows);
//        console.log(rows[0].cells);
        var cells0 = rows[0].cells;
        var curIDRT = $(cells0[1]).attr("id");
        var curSettingRT = $(cells0[1]).data("value");
//        console.log(cells0);

//        var timeRow = document.getElementById();
//        console.log($(cells0[1]).data("startTime"));
        //console.log($("#startTime").text());
        //var startTime = $("#startTime").text().replace("AM", "").replace("PM", "").trim();
        var startTime = $("#startTime").data("value");
        console.log(startTime);
        var startTimeHour = startTime.split(":")[0];
        var startTimeMinute = startTime.split(":")[1];

        //var endTime = $("#endTime").text().replace("PM", "").replace("AM", "").trim();
        var endTime = $("#endTime").data("value");
        var endTimeHour = endTime.split(":")[0];
        var endTimeMinute = endTime.split(":")[1];

        var strHour = "";

        for(var i=0; i<24; i++){
            if (i < 10)
                i = "0" + i;
            strHour += "<option value ='"+i+"'>"+i+"</option>";
        }

        var strMin = "";

        for(var m=0; m<60; m++){
            if (m < 10)
                m  = "0" + m;
            strMin += "<option value ='"+m+"'>"+m+"</option>";
        }

        cells0[1].innerHTML = "<div class=\"clearfix\">"+
            "<div class=\"col-md-2\"><label>START : </label></div>"+
            "<div class=\"input-group col-md-4\">"+
            "<div class=\"col-md-6 selTime\"><select id=\"startTimeHourInput\"  class='form-control'>" + "<option value=\""+startTimeHour+ "\" selected hidden>" + startTimeHour + "</option>"+ strHour +
            "</select></div>" + "<div class=\"col-md-6 selTime\"><select id=\"startTimeMinuteInput\" class='form-control'>"+ "<option value= \"" + startTimeMinute + "\"selected hidden>" + startTimeMinute + "</option>" + strMin + "</select></div></div>" +
            "<div class=\"col-md-2\"><label>END : </label></div>"+
            "<div class=\"input-group col-md-4\">"+
            "<div class=\"col-md-6 selTime\"><select id=\"endTimeHourInput\"  class='form-control'>" + "<option value=\"" + endTimeHour + "\" selected hidden>" + endTimeHour +"</option>"+ strHour +
            "</select></div>" + "<div class=\"col-md-6 selTime\"><select id=\"endTimeMinuteInput\" class='form-control'>"+ "<option value=\"" + endTimeMinute + "\" selected hidden>" + endTimeMinute + "</option>" + strMin + "</select></div></div>";

        var cells1 = rows[1].cells;
        var curIDTI = $(cells1[1]).attr("id");
        var curSettingTI = $(cells1[1]).data("value");
        console.log(curSettingTI);
        cells1[1].innerHTML = "<div class= \"input-group col-md-4\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\"timeslotIntervalInput\" value=\""+curSettingTI+ "\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        var cells2 = rows[2].cells;
        var curIDTL = $(cells2[1]).attr("id");
        var curSettingTL = $(cells2[1]).data("value");
        cells2[1].innerHTML = "<div class= \"input-group col-md-4\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\"timeslotLimitInput\" value=\""+curSettingTL+"\">" +
            "<div class=\"input-group-addon \">timeslots</div>" +
            "</div>";

        /*var cells3 = rows[3].cells;
        var curIDRA = $(cells3[1]).attr("id");
        var curSettingRA = $(cells3[1]).data("value");
        cells3[1].innerHTML = "<div class= \"input-group col-md-4\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\"reservationAccessInput\" value=\""+curSettingRA+ "\">" +
            "<div class=\"input-group-addon \">days</div>" +
            "</div>";*/

        var cells4 = rows[3].cells;
        var curIDRE = $(cells4[1]).attr("id");
        var curSettingRE = $(cells4[1]).data("value");
        cells4[1].innerHTML = "<div class= \"input-group col-md-4\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\"reservationExpiryInput\" value=\""+curSettingRE+"\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        var cells5 = rows[4].cells;
        var curIDCE = $(cells5[1]).attr("id");
        var curSettingCE = $(cells5[1]).data("value");
        cells5[1].innerHTML = "<div class= \"input-group col-md-4\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\"confirmationExpiryInput\" value=\""+curSettingCE+"\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        setInputRules();


        document.getElementById(footer).innerHTML =
            "<button class=\"btn btn-default col-md-offset-8 col-md-2\" type=\"button\" onclick=\"changeViewToView('"+table+"','"+footer+"')\">Cancel</button>"+
            "<input class=\"btn btn-default  col-md-offset-0 col-md-2\" type=\"button\" onclick=\"submitChanges('"+table+"')\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer){
        var content = document.getElementById(table);
        var content2 = document.getElementById(footer);
        console.log(content);
                console.log(content2);

        document.getElementById(table).innerHTML = initialTable;
        
        document.getElementById(footer).innerHTML = initialFooter;
       //     " <button class=\"btn btn-default col-md-3 col-md-offset-9\" type=\"button\" onclick=\"changeViewToEdit("+table+", "+footer+")\">Edit Information</button>";

    }

    function submitChanges(tableID) {
        var newTableData = getTableData(tableID);

        if (newTableData == 1) {
            toastr.error("Input is lacking, please try again.", "Error");
            return;
        }
        else if (newTableData == 2) {
            toastr.error("Sorry, start time cannot be greater than or equal to end time.", "Error");
            return;
        }

        var business_rulesid = tableID.split("_")[0];
        console.log(business_rulesid);

        console.log(newTableData);

        $.ajax({
            url: '<?=base_url('admin/' . ADMIN_UPDATE_BUSINESS_RULES)?>',
            type: 'GET',
            dataType: 'json',
            data: {
                business_rulesid: business_rulesid,
                start_time: newTableData[0],
                end_time: newTableData[1],
                interval: newTableData[2],
                limit: newTableData[3],
                //accessibility: newTableData[4],
                reservation_expiry: newTableData[4],
                confirmation_expiry: newTableData[5],
            }
        })
            .done(function(result) {
                console.log("done");
                console.log(result);

                if (result['result'] == "success") {

                    /*$(window).load(function(){
                     toastr.success("Changes were made successfully.", "Success");
                     });*/
                    toastr.success("Changes were made successfully.", "Success");
                    var delay = 1000;
                    setTimeout(function() {
                        reloadPage();
                    }, delay);


                }
                else {
                    reloadPage();
                }
            })
            .fail(function() {
                console.log("fail");
                //console.log(result);
            })
            .always(function() {
                console.log("complete");
            });
    }

    function reloadPage() {
        <?php
        // TODO Might be better if it didn't have to reload page. Clear table data then query through database?
        echo 'window.location = "'. site_url("admin/".ADMIN_BUSINESS_RULES) .'";';
        ?>
    }

    function getTableData(tableID) {
        /*var table = document.getElementById(tableID);
        var jObject = [];
        for (var i = 0; i < table.rows.length; i++)
        {

            // Get data from the cell
            var value = table.rows[i].cells[1].childNodes[0].childNodes[0].value;

            if (value == "") {
                jObject = null;
                break;
            }

            jObject[i] = value;

        }
        return jObject;*/
        var tableData = [];
        
        var startTimeInt = parseInt($("#startTimeHourInput").val()) * 60 + parseInt($("#startTimeMinuteInput").val());
        var endTimeInt = parseInt($("#endTimeHourInput").val()) * 60 + parseInt($("#endTimeMinuteInput").val());

        if (startTimeInt >= endTimeInt) {
            return 2;
        }

        var startTime = $("#startTimeHourInput").val() + ":" + $("#startTimeMinuteInput").val() + ":00";
        tableData[0] = startTime;

        var endTime = ($("#endTimeHourInput").val()) + ":" + $("#endTimeMinuteInput").val() + ":00";
        tableData[1] = endTime;

        tableData[2] = $("#timeslotIntervalInput").val();
        tableData[3] = $("#timeslotLimitInput").val();
        //tableData[4] = $("#reservationAccessInput").val();
        tableData[4] = $("#reservationExpiryInput").val();
        tableData[5] = $("#confirmationExpiryInput").val();


        for (var i = 0; i < tableData.length; i++) {
            if (tableData[i] == null || tableData[i].trim() == "") {
                console.log(i);
                return 1;
            }
        }


        return tableData;
    }

</script>

<head>
</head>
<body>

<?php
include 'a_navbar.php';
?>

<ol class="breadcrumb  col-md-offset-2 col-md-10">
    <li>Admin</li>
    <li>Application Settings</li>
    <li class="active">Adjust Business Rules</li>
</ol>

<div class = "col-md-2"></div>
<div id="panels" class = "col-md-8" id="rulePanel">
    <?php foreach($rules as $rule): ?>
        <div class="panel-group" role="tablist">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="collapseListGroupHeading1">
                    <h4 class="panel-title">
                        <?=$rule->name?> Business Rules</h4>
                </div>
                <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup1" aria-labelledby="collapseListGroupHeading1" aria-expanded="false">
                    <ul class="list-group">
                        <form>
                            <li class="list-group-item">
                                <table class="table table-hover" id="<?=$rule->business_rulesid?>_rulestable">
                                    <tbody>

                                    <tr>
                                        <th scope="row"> Reservation Time </th>
                                        <td id="reservation_time">The reservation will start from  <strong><u><span id="startTime" data-value="<?=$rule->start_time?>"><?=date('h:i A', strtotime($rule->start_time))?></span></u></strong>
                                            to  <strong><u><span id="endTime" data-value="<?=$rule->end_time?>"><?=date('h:i A', strtotime($rule->end_time))?></span></u></strong>.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Timeslot Interval </th>
                                        <td id="timeslot_interval" data-value="<?=$rule->interval?>">A timeslot is equal to <strong><u><?=$rule->interval?></u></strong> minutes.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Timeslot Limit </th>
                                        <td id="timeslot_limit" data-value="<?=$rule->limit?>">The user can choose up to <strong><u><?=$rule->limit?></u></strong> timeslots.</td>
                                    </tr>

                                    <!--<tr>
                                        <th scope="row"> Reservation Access </th>
                                        <td id="reservation_access" data-value="<?/*=$rule->accessibility*/?>">The user can reserve <strong><u><?/*=$rule->accessibility*/?></u></strong> day/s before the actual reservation date.</td>
                                    </tr>-->

                                    <tr>
                                        <th scope="row"> Reservation Expiry </th>
                                        <td id="reservation_expiry" data-value="<?=$rule->reservation_expiry?>">The reservation will expire if the user fails to show up after <strong><u><?=$rule->reservation_expiry?></u></strong> minutes.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Confirmation Expiry </th>
                                        <td id="confirmation_expiry" data-value="<?=$rule->confirmation_expiry?>">The confirmation email will expire after <strong><u><?=$rule->confirmation_expiry?></u></strong> minutes if not confirmed.</td>
                                    </tr>


                                    </tbody>
                                </table>

                            </li>
                            <div class = "panel-footer clearfix" id = "<?=$rule->business_rulesid?>_rulestable_footer">
                                <button class="btn btn-default col-md-3 col-md-offset-9" type="button" onclick="changeViewToEdit('<?=$rule->business_rulesid?>_rulestable', '<?=$rule->business_rulesid?>_rulestable_footer')">Edit Information</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>
<div class = "col-md-2"></div>

</body>
</html>