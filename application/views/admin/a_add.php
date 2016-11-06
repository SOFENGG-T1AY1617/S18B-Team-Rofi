<script xmlns="http://www.w3.org/1999/html">
    $(document).ready()
    {

    }

    function addRoom(table){
        console.log(table);
        var tableA = table;

        var row = tableA.insertRow(-1);

        var cellName = row.insertCell(0);
        var cellNumber = row.insertCell(1);

        cellName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter name of the room\">";
        cellNumber.innerHTML = "<input type=\"number\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder='' =\"Enter number of PCs in room\">";

    }

    function cancelAddBldg(building){
        console.log("aa");
        console.log(building);
        var buildingA = building;
        buildingA.innerHTML = "<button class=\"btn btn-default col-md-2 col-md-offset-10\" type=\"button\" onclick=\"addBldg("+building.id+")\">Add Building</button>";
    }

    function addBldg(building){
        console.log(building);
        var buildingA = building;
        buildingA.innerHTML = "<form class=\"form-inline\"><div class=\" col-md-6\">"+
            "<input type=\"text\" class=\"form-control\" placeholder=\"Enter name of the building\"></div>" +
                "<div class=\"col-md-2\"></div>"+
            "<button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"cancelAddBldg("+building.id+")\">Cancel</button>" +
            "<button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"\">Save Changes</button></form>";

    }

    function changeViewToEdit(table, footer){
        console.log(table);
        var tableA = table;
        var rows = tableA.rows;

        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curName = cells[0].innerHTML;
            var curNum = cells[1].innerHTML;


            cells[0].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" value=\""+curName+"\">"
            cells[1].innerHTML = "<input type=\"number\" class=\"form-control\" id=\"exampleInputEmail1\" value=\""+curNum+"\">"

        }
        var tID = table.id;
        var fID = footer.id;

        var addID = ""+tID+"_addbtn";
        console.log(addID);
        var add = document.getElementById(addID.toString());

        add.innerHTML =
                " <button type =\"button\" onclick=\"addRoom("+tID+")\" class=\"btn btn-default addroom-btn\"><i class=\"material-icons\">add</i></button>";

        footer.innerHTML =
            "<button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"changeViewToView("+tID+","+fID+")\">Cancel</button>"+
            "<input class=\"btn btn-default  col-md-offset-8 col-md-2\" type=\"submit\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer){

        console.log(table);
        var tableA = table;
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
            table.deleteRow(deleteRows[i]);
        }


        var addID = ""+table.id+"_addbtn";
        var add = document.getElementById(addID.toString());



        add.innerHTML =
            "";


        footer.innerHTML =
            " <button class=\"btn btn-default col-md-2 col-md-offset-10\" type=\"button\" onclick=\"changeViewToEdit("+table.id+", "+footer.id+")\">Edit Rooms</button>";

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



<div class = "col-md-2"></div>
<div id="panels" class = "col-md-8">


    <!-- SINGLE PANEL -->
    <?php foreach($buildings as $row):?>
        <div class="panel-group" role="tablist">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="collapseListGroupHeading1">
                    <h4 class="panel-title">
                        <a href="#collapseListGroup1" class="" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapseListGroup1">
                            <?=$row->name?></a> </h4>
                </div>
                <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup1" aria-labelledby="collapseListGroupHeading1" aria-expanded="false">
                    <ul class="list-group">
                        <form>
                            <li class="list-group-item">
                                <table class="table table-hover" id="gokongweitable">
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

                                <div id = "<?=$row->buildingid?>_addbtn">
                                </div>
                            </li>
                            <div class = "panel-footer clearfix" id = "gokongweitable_footer">
                                <button class="btn btn-default col-md-2 col-md-offset-10" type="button" onclick="changeViewToEdit(gokongweitable, gokongweitable_footer)">Edit Rooms</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    <?php endforeach;?>

    <!-- end of panel -->
</div>
<div class = "col-md-2"></div>

<div class="col-md-12"></div>
<div class="col-md-2"></div>
<div class="col-md-8">

<div class="panel-group clearfix" role="tablist">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="collapseListGroupHeading2 ">
            <h4 class="panel-title clearfix" id="add_building">
            <button class="btn btn-default col-md-2 col-md-offset-10" type="button" onclick="addBldg(add_building)">Add Building</button>
            </h4>
        </div>
    </div>
</div>

</div>
</body>
</html>