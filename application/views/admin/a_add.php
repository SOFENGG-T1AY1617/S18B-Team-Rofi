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
    <div class="panel-group" role="tablist">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="collapseListGroupHeading1">
                <h4 class="panel-title">
                    <a href="#collapseListGroup1" class="" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapseListGroup1">
                        Gokongwei Hall (G)</a> </h4>
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

                                <tr>
                                    <td>302A</td>
                                    <td>40</td>
                                </tr>

                                <tr>
                                    <td>302B</td>
                                    <td>40</td>
                                </tr>
                                </tbody>
                            </table>

                        <div id = "gokongweitable_addbtn">
                        </div>
                    </li>
                        <div class = "panel-footer clearfix" id = "gokongweitable_footer">
                                <button class="btn btn-default col-md-2 col-md-offset-10" type="button" onclick="changeViewToEdit(gokongweitable, gokongweitable_footer)">Edit Rooms</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <!-- end of panel -->

    <div class="panel-group" role="tablist">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="collapseListGroupHeading2 ">
                <h4 class="panel-title">
                    <a href="#collapseListGroup2" class="" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapseListGroup2">
                        Velasco Building (V)</a> </h4>
            </div>
            <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup2" aria-labelledby="collapseListGroupHeading2" aria-expanded="false">
                <ul class="list-group">
                    <form>
                        <li class="list-group-item">
                            <table class="table table-hover" id="velascotable">
                                <thead>
                                <tr>
                                    <th>Room Name</th>
                                    <th>Number of PCs</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td>211</td>
                                    <td>20</td>
                                </tr>

                                <tr>
                                    <td>213</td>
                                    <td>20</td>
                                </tr>
                                </tbody>
                            </table>
                            <div id="velascotable_addbtn"></div>
                        </li>
                        <div class="panel-footer clearfix" id="velascotable_footer"> <button class="btn btn-default col-md-2 col-md-offset-10" type="button" onclick="changeViewToEdit(velascotable, velascotable_footer)">Edit Rooms</button></div>
                    </form>
                </ul></div>
        </div>
    </div>


</div>
<div class = "col-md-2"></div>



</body>
</html>