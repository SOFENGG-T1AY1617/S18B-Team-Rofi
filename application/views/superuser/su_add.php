<script xmlns="http://www.w3.org/1999/html">
    $(document).ready(function() {
        $(".add-room-btn").click(function() {
            var buildingid = $(this).attr("id");
            buildingid = buildingid.split("-")[1];
            //console.log("Building id: " + buildingid);
            $("#add_table").attr("name", buildingid);
            $("#modal-building-name").text($(this).data("buildingname"));

            var type = $(this).data("type");

            $(".area_type").text(type);
            //addRoom('add_table');

        });
    });

    function cancelAddBldg(building){
        console.log("aa");
        console.log(building);
        var buildingA = document.getElementById(building);
        buildingA.innerHTML = "<button class=\"btn btn-default col-md-2 col-md-offset-10\" type=\"button\" onclick=\"addBldg('"+buildingA.id+"')\">Add Building</button>";
    }

    function addBldg(building){
        console.log(building);
        var buildingA = document.getElementById(building);
        buildingA.innerHTML = "<form class=\"form-inline\"><div class=\" col-md-6\">"+
            "<input type=\"text\" class=\"form-control\" placeholder=\"Enter name of the building\"></div>" +
            "<div class=\"col-md-2\"></div>"+
            "<button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"cancelAddBldg('"+buildingA.id+"')\">Cancel</button>" +
            "<button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"\">Save Changes</button></form>";

    }


    function deleteRow(table, index){
        var tableA = document.getElementById(table);
        for(var x=index+1;x<tableA.rows.length;x++)
        {
            tableA.rows[x].cells[2].innerHTML = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(x-1)+")\" class=\"btn btn-default\">&times;</button>"
            console.log(x-1);
        }

        tableA.deleteRow(index);
    }

    function hideRow(capacityID){
        //var tableA = document.getElementById(table);
        //tableA.deleteRow(index);
        $(capacityID).parents('tr').hide();
        $(capacityID).val("-1");
        console.log($(capacityID).val());
    }

    function getTableData(tableID) {
        var table = document.getElementById(tableID);
        var jObject = [];
        for (var i = 1; i < table.rows.length; i++)
        {
            var row = i - 1;
            // create array within the array - 2nd dimension
            //jObject[row] = [];

            var valid = true;
            var columns = [];
            // columns within the row
            //for (var j = 0; j < table.rows[i].cells.length; j++)
            for (var j = 0; j < table.rows[i].cells.length-1; j++)
            {
                //jObject[row][j] = table.rows[i].cells[j].childNodes[0].value;
                columns[j] = table.rows[i].cells[j].childNodes[0].value;

                if (!columns[j].trim()) {
                    valid = false;
                    return false;
                }
            }

            if (valid) {
                jObject[row] = columns;
            }
        }
        return jObject;
    }

    function getTableDataWithID(tableID) {
        var table = document.getElementById(tableID);
        var jObject = [];
        for (var i = 1; i < table.rows.length; i++)
        {
            var row = i - 1;
            // create array within the array - 2nd dimension
            //jObject[row] = [];

            var valid = true;
            var columns = [];
            // columns within the row
            //for (var j = 0; j < table.rows[i].cells.length; j++)

            columns[0] = table.rows[i].cells[0].childNodes[0].id.split("_")[1];
            for (var j = 0; j < 2; j++)
            {
                //jObject[row][j] = table.rows[i].cells[j].childNodes[0].value;
                columns[j + 1] = table.rows[i].cells[j].childNodes[0].value;

                if (!columns[j + 1].trim()) {
                    valid = false;
                    break;
                }
            }

            /*columns[1] = table.rows[i].cells[0].childNodes[0].value;
             columns[2] = table.rows[i].cells[1].childNodes[0].value;*/

            if (valid) {
                jObject[row] = columns;
            }
        }
        return jObject;
    }

    function submitBuilding() {
        var buildingName = $('#bldgName').val();
        var optionsRadios= document.querySelector('input[name="optionsRadios"]:checked').value;
        console.log("Adding"+optionsRadios);

        if (!buildingName.trim()) {
            console.log("no input");
            toastr.error("An input field is empty. Please fill it and try again.", "Oops!");
            return;
        }

        $.ajax({
            url: '<?=base_url('admin/addBuilding')?>',
            type: 'GET',
            dataType: 'json',
            data: {
                buildingName: buildingName,
                optionsRadios: optionsRadios
            }
        })
            .done(function(result) {
                console.log("done");
                //location.reload(true);
                if(result=="success"){
                    reloadPage();
                }
                else{
                    toastr.error("Building already exists.", "Oops!");
                }


            })
            .fail(function(result) {
                console.log("fail");
                console.log(result);
            })
            .always(function() {
                console.log("complete");
            });

        $('#AddNewBuildingModal').modal('toggle');
    }

    function submitChanges(tableID, initialTableData) {
        console.log(initialTableData);
        var initial = parseTableData(initialTableData);
        console.log(initial);
        //return;

        var changedData = getChangedData(initial, getTableDataWithID(tableID));

        $.ajax({
            url: '<?=base_url('admin/updateRooms')?>',
            type: 'GET',
            dataType: 'json',
            data: {
                changedData: changedData,
            }
        })
            .done(function(result) {
                console.log("done");
                console.log(result);

                if (result['result'] == "success") {

                    /*$(window).load(function(){
                     toastr.success("Changes were made successfully.", "Success");
                     });*/

                    var notChanged = result['notChanged'];
                    var notDeleted = result['notDeleted'];

                    if (notChanged.length > 0) {
                        var toast = "";

                        for (var i = 0; i < notChanged.length - 1; i++) {
                            toast += notChanged[i] + ", ";
                        }

                        toast += notChanged[notChanged.length - 1] + " could not be changed due to duplicate room names.";
                        toastr.error(toast, "Error!");
                    }

                    if (notDeleted.length > 0) {
                        var toast = "";

                        for (var i = 0; i < notDeleted.length - 1; i++) {
                            toast += notDeleted[i] + ", ";
                        }

                        toast += notDeleted[notDeleted.length - 1] + " could not be deleted due to existing reservations.";
                        toastr.error(toast, "Error!");
                    }

                    if (notDeleted.length == 0 && notChanged.length == 0)
                        toastr.success("Changes were made successfully.", "Success");
                    else {
                        toastr.warning("Not all changes were successful.", "Warning");
                    }


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

                //reloadPage();
            })
            .always(function() {
                console.log("complete");
            });
    }

    function parseTableData(tableData) {
        var data = tableData.split(",");
        var newTableData = [];
        for(var i = 0; i < data.length; i +=3) {
            var columns = [];
            for (var j = i; j < i + 3; j++) {
                columns[j % 3] = data[j];
            }
            newTableData[i / 3] = columns;
        }

        return newTableData;
    }

    function getChangedData(initialTableData, newTableData) {
        var changedData = [];
        var changedDataIndex = 0;

        for (var i = 0; i < initialTableData.length; i++) {
            if (initialTableData[i][1] != newTableData[i][1] ||
                initialTableData[i][2] != newTableData[i][2]) {
                changedData[changedDataIndex] = newTableData[i];
                changedDataIndex++;
            }
        }

        return changedData;
    }

    function reloadPage() {
        <?php
        // TODO Might be better if it didn't have to reload page. Clear table data then query through database?
        echo 'window.location = "'. site_url("admin/".ADMIN_AREA_MANAGEMENT) .'";';
        ?>
    }

</script>

</head>
<body>


<?php
include 'su_navbar.php';
?>

    <div class="panel-group clearfix col-md-offset-2 col-md-8" role="tablist">
        <button type ="button"data-toggle="modal" data-target="#AddNewBuildingModal" class="btn btn-success btn-block">+ Add Building</button>
    </div>
    <div id="panels" class = "col-md-offset-2 col-md-8">


        <?php foreach($buildings as $row):?>
            <div class="panel-group" role="tablist">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="collapseListGroupHeading<?=$row->buildingid?>">
                        <h4 class="panel-title clearfix ">
                            <a href="#collapseListGroup<?=$row->buildingid?>" class="col-md-6" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseListGroup<?=$row->buildingid?>">
                                <?=$row->name?></a>
                        </h4>
                    </div>
                    <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup<?=$row->buildingid?>" aria-labelledby="collapseListGroupHeading<?=$row->buildingid?>" aria-expanded="false">
                        <ul class="list-group">
                            <form>
                                <li class="list-group-item">
                                    <table class="table table-hover" id="<?=$row->buildingid?>table">
                                        <thead>
                                        <tr>
                                            <th><?=$row->type?> Name</th>
                                            <th>Number of PCs</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i=0; ?>
                                        <?php foreach($rooms as $room):?>
                                            <?php if($room->buildingid == $row->buildingid): ?>
                                                <?php $i += 1; ?>
                                                <tr>
                                                    <td id="room_<?=$room->roomid?>"><?=$room->name?></td>
                                                    <td id="capacity_<?=$room->roomid?>"><?=$room->capacity?></td>
                                                    <td></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach;?>
                                        </tbody>


                                    </table>


                                    <?php if($i == 0):?>
                                        <div id="norooms_message">
                                            <h4 style="text-align: center"> NO REGISTERED <?=strtoupper($row->type)?>S </h4>
                                        </div>
                                    <?php endif;?>

                                </li>

                            </form>
                    </div>
                </div>
            </div>
        <?php endforeach;?>

        <!-- end of panel -->
    </div>

<!-- Modal -->
<div id="AddNewBuildingModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Building</h4>
            </div>
            <form>
                <div class="modal-body clearfix">

                    <div class="form-group">
                        <label for="bldgName">Building Name:</label>
                        <input type="text" class="form-control" id="bldgName" placeholder="Enter the name of the building...">
                    </div>
                    <!--<div class="form-group">
                        <label for="bldgPrefix">Prefix:</label>
                        <input type="text" class="form-control" id="bldgPrefix" placeholder="Enter the prefix to be used. (ie. G for Gokongwei, SJ for St. Joseph Hall etc...)">
                    </div>-->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitBuilding()">Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>



</body>
</html>