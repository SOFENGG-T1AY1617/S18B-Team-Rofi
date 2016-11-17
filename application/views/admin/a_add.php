<script xmlns="http://www.w3.org/1999/html">
    $(document).ready(function() {
       $(".add-room-btn").click(function() {
           var buildingid = $(this).attr("id");
           buildingid = buildingid.split("-")[1];
           //console.log("Building id: " + buildingid);
           $("#add_table").attr("name", buildingid);
           $("#modal-building-name").text($(this).data("buildingname"));

       });

        addRoom('add_table');
        setInputRules();

    });

    function setInputRules() {
        $('input[type=number]').numeric();
        $(".number-input").keypress(function(event) {
            if ( event.which == 45 || event.which == 189) {
                event.preventDefault();
            }
        });

        $(".number-input").bind('paste', function(e) {
            var pasteData = e.clipboardData.getData('text/plain');
            if (pasteData.match(/[^0-9]/))
                e.preventDefault();
        }, false);
    }

    function addRoom(table){
        console.log(table);
        var tableA = document.getElementById(table);
        var row = tableA.insertRow(-1);

        var cellName = row.insertCell(0);
        var cellNumber = row.insertCell(1);
        var deleteCol =  row.insertCell(2);
        var i = tableA.rows.length-1;

        cellName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter name of the room\">";
        cellNumber.innerHTML = "<input type=\"number\" min=\"0\" class=\"form-control number-input\" id=\"exampleInputEmail1\" placeholder=\"Enter number of PCs in the room\">";
        deleteCol.innerHTML = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+i+")\" class=\"btn btn-default\"><i class=\"material-icons\">clear</i></button>"

        setInputRules();
    }

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

    var initialTableData;

    var initialTable;
    var initialFooter;

    function changeViewToEdit(table, footer){
        console.log(table);
        var tableA = document.getElementById(table);
        var footerA  = document.getElementById(footer);

        initialTable = tableA.innerHTML;
        initialFooter = footerA.innerHTML;

        var rows = tableA.rows;

        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;


            var curRoomID = $(cells[0]).attr("id");
            var curCapID = $(cells[1]).attr("id");
            var curName = cells[0].innerHTML;
            var curNum = cells[1].innerHTML;

            cells[0].innerHTML = "<input type=\"text\" class=\"form-control\" id=\""+curRoomID+"\" value=\""+curName+"\">";
            cells[1].innerHTML = "<input type=\"number\" min=\"0\" class=\"form-control number-input\" id=\""+curCapID+"\" value=\""+curNum+"\">";
            cells[2].innerHTML = "<button type =\"button\" onclick=\"hideRow("+curCapID+")\" class=\"btn btn-default\"><i class=\"material-icons\">clear</i></button>";


        }

        setInputRules();

        var tID = table;
        var fID = footer;

        initialTableData = getTableDataWithID(tID);

        footerA.innerHTML =

            "<button class=\"btn btn-default col-md-offset-8 col-md-2\" type=\"submit\"><!--onclick=\"changeViewToView('"+tID+"','"+fID+"')\"-->Cancel</button>"+
            "<input class=\"btn btn-default col-md-2\" onclick=\"submitChanges('"+tID+"')\" type=\"button\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer){


        console.log("fuck this shit im out ~");
        var tableA = document.getElementById(table);
        var footerA = document.getElementById(footer);

//        var rows = tableA.rows;

//        var deleteRows=[];
//            var lengthofdel=0;
//
//        for(var i = 1; i < rows.length; i++){
//            var cells = rows[i].cells;
//
//            var curName = cells[0].getElementsByTagName("input")[0].value;
//            var curNum = cells[1].getElementsByTagName("input")[0].value;
//
//            cells[2].innerHTML = "";
//
//            if(curName != "" && curNum != ""){
//                cells[0].innerHTML = curName;
//                cells[1].innerHTML = curNum;
//            }
//            else{
//                console.log(i);
//                deleteRows[lengthofdel] = i;
//                lengthofdel ++;
//            }
//
//
//        }
//
//        for(var i=lengthofdel-1; i >= 0 ; i--){
//            tableA.deleteRow(deleteRows[i]);
//        }

        tableA.innerHTML = initialTable;
        footerA.innerHTML = initialFooter;

    }

    function cancelAddRoom(tableID){
        var table = document.getElementById(tableID);
        var rows = table.rows;
        var i;

        for(i=rows.length-1; i>0; i--){
            table.deleteRow(i);
        }

        addRoom(tableID);

    }

    function deleteRow(table, index){
        var tableA = document.getElementById(table);
       for(var x=index+1;x<tableA.rows.length;x++)
            {
                tableA.rows[x].cells[2].innerHTML = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(x-1)+")\" class=\"btn btn-default\"><i class=\"material-icons\">clear</i></button>"
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
            for (var j = 0; j < 2; j++)
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

    function submitRoom() {
        var tableID = $("#add_table").attr("id");
        var tableData = getTableData(tableID);

        if (tableData == false) {
            toastr.error("An input field is empty. Please fill it and try again.", "Oops!");
            return;
        }

        $.ajax({
            url: '<?=base_url('admin/addRoom')?>',
            type: 'GET',
            dataType: 'json',
            data: {
                rooms: tableData,
                buildingid: $("#add_table").attr("name"),
            }
        })
            .done(function(result) {
                console.log("done");
                if (result['result'] == "success") {
                    console.log(result['numAdded']);
                    var toast = "";
                    if (result['numAdded'] == 0) {
                        toast = "No rooms were added.";
                    }
                    else if (result['numAdded'] == 1) {
                        toast = "1 room was added successfully.";
                    }
                    else if (result['numAdded'] > 1 ) {
                        toast = result['numAdded'] + " rooms were added successfully.";
                    }

                    if (result['numAdded'] > 0)
                        toastr.success(toast, "Success");
                    else
                        toastr.info(toast, "Info");

                    var notAdded = result['notAdded'];
                    console.log(notAdded);

                    toast = ""
                    if (notAdded.length == 1) {
                        toast = notAdded + " was not added.";
                    }
                    else if (notAdded.length > 1) {
                        for (var i = 0; i < notAdded.length - 1; i++) {
                            toast = toast + notAdded[i] + ", ";
                        }

                        toast = toast + notAdded[notAdded.length - 1] + " were not added.";
                    }

                    if (notAdded.length > 0)
                        toastr.error(toast, "Oops!");

                    var delay = 1000;
                    setTimeout(function() {
                        reloadPage();
                    }, delay);


                }
                else {
                    reloadPage();
                }

            })
            .fail(function(result) {
                console.log("fail");
                console.log(result);
            })
            .always(function() {
                console.log("complete");
            });

        //$("#AddNewRoomsModal").hide();
        $('#AddNewRoomsModal').modal('toggle');
    }

    function submitBuilding() {
        var buildingName = $('#bldgName').val();
       // console.log("Adding"+buildingName);

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
                buildingName: buildingName
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

    function submitChanges(tableID) {
        var changedData = getChangedData(getTableDataWithID(tableID));

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

    function getChangedData(newTableData) {
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


<link href="<?=base_url()?>/assets/css/admin_add_style.css" rel="stylesheet">

</head>
<body>

<?php
/**
 * Created by PhpStorm.
 * User: psion
 * Date: 10/26/2016
 * Time: 9:06 PM
 */
?>

<?php
include 'a_navbar.php';
?>

<?php if($_SESSION['admin_typeid'] == 1): ?>
    <div class="panel-group clearfix col-md-offset-2 col-md-8" role="tablist">
                    <button type ="button"data-toggle="modal" data-target="#AddNewBuildingModal" class="btn btn-default col-md-12">Add Building</button>
    </div>
<?php endif;?>
<div id="panels" class = "col-md-offset-2 col-md-8">


    <!-- SINGLE PANEL -->
    <?php foreach($buildings as $row):?>
        <div class="panel-group" role="tablist">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="collapseListGroupHeading<?=$row->buildingid?>">
                    <h4 class="panel-title clearfix ">
                        <a href="#collapseListGroup<?=$row->buildingid?>" class="col-md-10" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapseListGroup<?=$row->buildingid?>">
                            <?=$row->name?></a></h4>
                </div>
                <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup<?=$row->buildingid?>" aria-labelledby="collapseListGroupHeading<?=$row->buildingid?>" aria-expanded="false">
                    <ul class="list-group">
                        <form>
                            <li class="list-group-item">
                                <table class="table table-hover" id="<?=$row->buildingid?>table">
                                    <thead>
                                    <tr>
                                        <th>Room Name</th>
                                        <th>Number of PCs</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($rooms as $room):?>
                                        <?php if($room->buildingid == $row->buildingid): ?>
                                            <tr>
                                                <td id="room_<?=$room->roomid?>"><?=$room->name?></td>
                                                <td id="capacity_<?=$room->roomid?>"><?=$room->capacity?></td>
                                                <td></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>

                            </li>
                            <div class = "panel-footer clearfix" id = "<?=$row->buildingid?>footer">
                                <button type ="button"data-toggle="modal" data-target="#AddNewRoomsModal" class="btn btn-default col-md-2 col-md-offset-8 add-room-btn" data-buildingname="<?=$row->name?>" id="add-<?=$row->buildingid?>">Add Rooms</button>
                                <button class="btn btn-default col-md-2 col-md-offset-0" type="button" onclick="changeViewToEdit('<?=$row->buildingid?>table','<?=$row->buildingid?>footer')">Edit Rooms</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    <?php endforeach;?>

    <!-- end of panel -->
</div>


<!-- Modal -->
<div id="AddNewRoomsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-building-name"></h4>
            </div>
            <form>
            <div class="modal-body clearfix">

                        <button type = "button" class = "btn btn-default btn-block  " onclick = "addRoom('add_table')">Add Another Room</button>
                        <table class="table table-hover" id="add_table" name="">
                            <thead>
                            <tr>
                                <th>Room Name</th>
                                <th>Number of PCs</th>
                                <th>Delete Row</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                            </tr>
                            </tbody>
                        </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cancelAddRoom('add_table')">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitRoom()">Confirm</button>
            </div>
            </form>
        </div>

    </div>
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