
<link href="<?=base_url()?>/assets/css/clockpicker.css" rel="stylesheet">
<script src="<?=base_url()?>/assets/js/clockpicker.js"></script>

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
    function changeViewToEdit(table, footer){
        console.log(footer);
        var tableA = document.getElementById(table);
        initialTable = tableA.innerHTML;
        var rows = tableA.rows;
        //var tID = table.id;
        //var fID = footer.id;

        var cells0 = rows[0].cells;
        var curIDRT = $(cells0[1]).attr("id");
        var curSettingRT = $(cells0[1]).data("value");
        console.log(cells0);

        cells0[1].innerHTML = "<div class=\"clearfix\">"+
            "<div class=\"col-md-2\"><label>START : </label></div>"+
            "<div class=\"input-group clockpicker\">"+
            "<input type=\"text\" class=\"form-control\" value=\"08:00\">"+
            "<span class=\"input-group-addon\">"+
            "<span class=\"glyphicon glyphicon-time\"></span>"+
            "</span>"+
            "</div>"+
            "<div class=\"col-md-2\"><label>END : </label></div>"+
            "<div class=\"input-group clockpicker\">"+
            "<input type=\"text\" class=\"form-control\" value=\"20:00\">"+
            "<span class=\"input-group-addon\">"+
            "<span class=\"glyphicon glyphicon-time\"></span>"+
            "</span>"+
            "</div>";

        var cells1 = rows[1].cells;
        var curIDTI = $(cells1[1]).attr("id");
        var curSettingTI = $(cells1[1]).data("value");
        cells1[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\""+curIDTI+ "\" value=\""+curSettingTI+ "\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        var cells2 = rows[2].cells;
        var curIDTL = $(cells2[1]).attr("id");
        var curSettingTL = $(cells2[1]).data("value");
        cells2[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\""+curIDTL+ "\" value=\""+curSettingTL+"\">" +
            "<div class=\"input-group-addon \">timeslots</div>" +
            "</div>";

        var cells3 = rows[3].cells;
        var curIDRA = $(cells3[1]).attr("id");
        var curSettingRA = $(cells3[1]).data("value");
        cells3[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\""+curIDRA+ "\" value=\""+curSettingRA+ "\">" +
            "<div class=\"input-group-addon \">days</div>" +
            "</div>";

        var cells4 = rows[4].cells;
        var curIDRE = $(cells4[1]).attr("id");
        var curSettingRE = $(cells4[1]).data("value");
        cells4[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\""+curIDRE+ "\" value=\""+curSettingRE+"\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        var cells5 = rows[5].cells;
        var curIDCE = $(cells5[1]).attr("id");
        var curSettingCE = $(cells5[1]).data("value");
        cells5[1].innerHTML = "<div class= \"input-group\">" +
            "<input type=\"number\" min=\"0\" class=\"form-control input-sm number-input\" id=\""+curIDCE+ "\" value=\""+curSettingCE+"\">" +
            "<div class=\"input-group-addon \">minutes</div>" +
            "</div>";

        setInputRules();


        document.getElementById(footer).innerHTML =
            "<button class=\"btn btn-default col-md-offset-8 col-md-2\" type=\"button\" onclick=\"changeViewToView('"+table+"','"+footer+"')\">Cancel</button>"+
            "<input class=\"btn btn-default  col-md-offset-0 col-md-2\" type=\"button\" onclick=\"submitChanges('"+table+"')\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer){

        document.getElementById(table).innerHTML = initialTable;

        document.getElementById(footer).innerHTML =
            " <button class=\"btn btn-default col-md-3 col-md-offset-9\" type=\"button\" onclick=\"changeViewToEdit("+table+", "+footer+")\">Edit Information</button>";

    }

    function submitChanges(tableID) {
        var newTableData = getTableData(tableID);

        if (newTableData == null) {
            toastr.error("Input is lacking, please try again.", "Error");
            return;
        }

        var business_rulesid = tableID.split("_")[0];
        console.log(business_rulesid);

        $.ajax({
            url: '<?=base_url('admin/' . ADMIN_UPDATE_BUSINESS_RULES)?>',
            type: 'GET',
            dataType: 'json',
            data: {
                business_rulesid: business_rulesid,
                interval: newTableData[0],
                limit: newTableData[1],
                accessibility: newTableData[2],
                reservation_expiry: newTableData[3],
                confirmation_expiry: newTableData[4],
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
        var table = document.getElementById(tableID);
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
        return jObject;
    }

</script>

<head>
</head>
<body>

<?php
include 'a_navbar.php';
?>

<ol class="breadcrumb  col-md-offset-2 col-md-10">
    <li><a href="#">Admin</a></li>
    <li><a href="#">Application Settings</a></li>
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
                                        <td id="reservation_time_<?=$rule->business_rulesid?>" data-value="">The reservation will start from  <strong><u> 8:00 am</u></strong> to  <strong><u> 8:00 pm</u></strong>.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Timeslot Interval </th>
                                        <td id="timeslot_interval_<?=$rule->business_rulesid?>" data-value="<?=$rule->interval?>">A timeslot is equal to <strong><u><?=$rule->interval?></u></strong> minutes.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Timeslot Limit </th>
                                        <td id="timeslot_limit_<?=$rule->business_rulesid?>" data-value="<?=$rule->limit?>">The user can choose up to <strong><u><?=$rule->limit?></u></strong> timeslots.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Reservation Access </th>
                                        <td id="reservation_access_<?=$rule->business_rulesid?>" data-value="<?=$rule->accessibility?>">The user can reserve <strong><u><?=$rule->accessibility?></u></strong> day/s before the actual reservation date.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Reservation Expiry </th>
                                        <td id="reservation_expiry_<?=$rule->business_rulesid?>" data-value="<?=$rule->reservation_expiry?>">The reservation will expire if the user fails to show up after <strong><u><?=$rule->reservation_expiry?></u></strong> minutes.</td>
                                    </tr>

                                    <tr>
                                        <th scope="row"> Confirmation Expiry </th>
                                        <td id="confirmation_expiry_<?=$rule->business_rulesid?>" data-value="<?=$rule->confirmation_expiry?>">The confirmation email will expire after <strong><u><?=$rule->confirmation_expiry?></u></strong> minutes if not confirmed.</td>
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