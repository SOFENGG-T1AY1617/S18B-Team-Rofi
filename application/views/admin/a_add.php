<script xmlns="http://www.w3.org/1999/html">
    $(document).ready()
    {

    }

    function addRoom(table){
        console.log(table);
        var tableA = document.getElementById(table);
        var row = tableA.insertRow(-1);

        var cellName = row.insertCell(0);
        var cellNumber = row.insertCell(1);

        cellName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter name of the room\">";
        cellNumber.innerHTML = "<input type=\"number\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter number of PCs in the room\">";

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

    function changeViewToEdit(table, footer){
        console.log(table);
        var tableA = document.getElementById(table);
        var rows = tableA.rows;

        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curName = cells[0].innerHTML;
            var curNum = cells[1].innerHTML;


            cells[0].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" value=\""+curName+"\">"
            cells[1].innerHTML = "<input type=\"number\" class=\"form-control\" id=\"exampleInputEmail1\" value=\""+curNum+"\">"

        }
        var tID = table;
        var fID = footer;

        var footerA  = document.getElementById(footer);

        var addID = ""+tID+"_addbtn";
        console.log(addID);
        var add = document.getElementById(addID.toString());

        footerA.innerHTML =

            "<button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"changeViewToView('"+tID+"','"+fID+"')\">Cancel</button>"+
            "<input class=\"btn btn-default  col-md-offset-8 col-md-2\" type=\"submit\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer){

        console.log(table);
        var tableA = document.getElementById(table);
        var footerA = document.getElementById(footer);
        var rows = tableA.rows;
        var deleteRows=[];
            var lengthofdel=0;
        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curName = cells[0].getElementsByTagName("input")[0].value;
            var curNum = cells[1].getElementsByTagName("input")[0].value;


            if(curName != "" && curNum != ""){
                cells[0].innerHTML = curName;
                cells[1].innerHTML = curNum;
            }
            else{
                console.log(i);
                deleteRows[lengthofdel] = i;
                lengthofdel ++;
            }

        }

        for(var i=lengthofdel-1; i >= 0 ; i--){
            tableA.deleteRow(deleteRows[i]);
        }


        var addID = ""+tableA.id+"_addbtn";
        var add = document.getElementById(addID.toString());



        add.innerHTML =
            "";


        footerA.innerHTML =
    "<button type =\"button\" data-toggle=\"modal\" data-target=\"#AddNewRoomsModal\" class=\" col-md-offset-8 btn btn-default col-md-2\">Add Room</button>"+
            " <button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"changeViewToEdit('"+tableA.id+"', '"+footer+"')\">Edit Rooms</button>";

    }

    function cancelAddRoom(tableID){
        var table = document.getElementById(tableID);
        var rows = table.rows;
        var i;
        console.log(rows.length)
        for(i=rows.length-1; i>1; i--){
            table.deleteRow(i);
        }

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


<div class="panel-group clearfix col-md-offset-2 col-md-8" role="tablist">
                <button type ="button"data-toggle="modal" data-target="#AddNewBuildingModal" class="btn btn-default col-md-12">Add Building</button>
</div>

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
                                                <td><?=$room->name?></td>
                                                <td><?=$room->capacity?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>

                                <div id = "<?=$row->buildingid?>table_addbtn">
                                </div>
                            </li>
                            <div class = "panel-footer clearfix" id = "<?=$row->buildingid?>footer">
                                <button type ="button"data-toggle="modal" data-target="#AddNewRoomsModal" class="btn btn-default col-md-2 col-md-offset-8">Add Rooms</button>
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
                <h4 class="modal-title">[BLDG NAME HERE] - Add New Rooms</h4>
            </div>
            <form>
            <div class="modal-body clearfix">

                        <button type = "button" class = "btn btn-default btn-block  " onclick = "addRoom('add_table')">Add Another Room</button>
                        <table class="table table-hover" id="add_table" name="">  <!-- TODO: somehow insert table id in name for add ? -->
                            <thead>
                            <tr>
                                <th>Room Name</th>
                                <th>Number of PCs</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td><input type="text" class="form-control" placeholder="Enter name of room"></td>
                                <td><input type="number" class="form-control" placeholder="Enter number of PCs in the room"></td>
                            </tr>
                            </tbody>
                        </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cancelAddRoom('add_table')">Cancel</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Confirm</button>
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
                    <div class="form-group">
                        <label for="bldgPrefix">Prefix:</label>
                        <input type="text" class="form-control" id="bldgPrefix" placeholder="Enter the prefix to be used. (ie. G for Gokongwei, SJ for St. Joseph Hall etc...)">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>

</body>
</html>