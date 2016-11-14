<script xmlns="http://www.w3.org/1999/html">

    function changeViewToEdit(table, footer){
        console.log(table);
        var tableA = table;
        var rows = tableA.rows;
        var tID = table.id;
        var fID = footer.id;

        var cells1 = rows[0].cells;
        var curSettingTI = cells1[1].getAttribute("name");
        cells1[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" class=\"form-control input-sm\" id=\"exampleInputAmount\" value=\""+curSettingTI+ "\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        var cells2 = rows[1].cells;
        var curSettingTL = cells2[1].getAttribute("name");
        cells2[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" class=\"form-control input-sm\" id=\"exampleInputAmount\" value=\""+curSettingTL+"\">" +
            "<div class=\"input-group-addon \">timeslots</div>" +
            "</div>";

        var cells3 = rows[2].cells;
        var curSettingRA = cells3[1].getAttribute("name");
        cells3[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" class=\"form-control input-sm\" id=\"exampleInputAmount\" value=\""+curSettingRA+ "\">" +
            "<div class=\"input-group-addon \">days</div>" +
            "</div>";

        var cells4 = rows[3].cells;
        var curSettingRE = cells4[1].getAttribute("name");
        cells4[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" class=\"form-control input-sm\" id=\"exampleInputAmount\" value=\""+curSettingRE+"\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        var cells5 = rows[4].cells;
        var curSettingCE = cells5[1].getAttribute("name");
        cells5[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" class=\"form-control input-sm\" id=\"exampleInputAmount\" value=\""+curSettingCE+"\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        footer.innerHTML =
            "<button class=\"btn btn-default col-md-offset-8 col-md-2\" type=\"button\" onclick=\"changeViewToView("+tID+","+fID+")\">Cancel</button>"+
            "<input class=\"btn btn-default  col-md-offset-0 col-md-2\" type=\"submit\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer){

        console.log(table);
        var tableA = table;
        var rows = tableA.rows;

        var cells1 = rows[0].cells;
        var curSettingTI = "A timeslot is equal to 15 minutes.";
        cells1[1].innerHTML = curSettingTI;

        var cells2 = rows[1].cells;
        var curSettingTL = "The user can choose up to 4 timeslots.";
        cells2[1].innerHTML = curSettingTL;

        var cells3 = rows[2].cells;
        var curSettingRA = "The user can reserve 1 day before the actual reservation date.";
        cells3[1].innerHTML = curSettingRA;

        var cells4 = rows[3].cells;
        var curSettingRE = "The reservation will expire if the user fails to show up in 15 minutes.";
        cells4[1].innerHTML = curSettingRE;

        var cells5 = rows[4].cells;
        var curSettingCE = " The confirmation email will expire after 60 minutes.";
        cells5[1].innerHTML = curSettingCE;

        footer.innerHTML =
            " <button class=\"btn btn-default col-md-3 col-md-offset-9\" type=\"button\" onclick=\"changeViewToEdit("+table.id+", "+footer.id+")\">Edit Information</button>";

    }

</script>


<head>
</head>
<body>

<?php
include 'a_navbar.php';
?>

<div class = "col-md-2"></div>
<div id="panels" class = "col-md-8">
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
                                <table class="table table-hover" id="rulestable">
                                    <tbody>

                                    <tr>
                                        <th scope="row"> Timeslot Interval </th>
                                        <td id="timeslot_interval_<?=$rule->business_rulesid?>" name="15">A timeslot is equal to <?=$rule->interval?> minutes.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Timeslot Limit </th>
                                        <td id="timeslot_limit_<?=$rule->business_rulesid?>" name="4">The user can choose up to <?=$rule->limit?> timeslots.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Reservation Access </th>
                                        <td id="reservation_access_<?=$rule->business_rulesid?>" name="1">The user can reserve <?=$rule->accessibility?> day/s before the actual reservation date.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Reservation Expiry </th>
                                        <td id="reservation_expiry_<?=$rule->business_rulesid?>" name="15">The reservation will expire if the user fails to show up after <?=$rule->reservation_expiry?> minutes.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Confirmation Expiry </th>
                                        <td id="confirmation_expiry_<?=$rule->business_rulesid?>" name="60">The confirmation email will expire after <?=$rule->confirmation_expiry?> minutes if not confirmed.</td>
                                    </tr>


                                    </tbody>
                                </table>

                            </li>
                            <div class = "panel-footer clearfix" id = "rulestable_footer">
                                <button class="btn btn-default col-md-3 col-md-offset-9" type="button" onclick="changeViewToEdit(rulestable, rulestable_footer)">Edit Information</button>
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