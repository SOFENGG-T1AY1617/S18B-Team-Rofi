<script xmlns="http://www.w3.org/1999/html">

    function addAdmin(table){
        console.log(table);
        var tableA =document.getElementById(table);
        var row = tableA.insertRow(-1);

        var cellName = row.insertCell(0);
        var cellEmail = row.insertCell(1);
        var cellUsername = row.insertCell(2);

        cellName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter name\">";
        cellEmail.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter email\">";
        cellUsername.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter department\">";

    }

    function clearAdmin(table, rowNum){
        console.log(table);
        var tableA = table;
        tableA.deleteRow(rowNum);

    }

    function cancelAddAdmin(tableID){
        var table = document.getElementById(tableID);
        var rows = table.rows;
        var i;
        console.log(rows.length);
        for(i=rows.length-1; i>1; i--){
            table.deleteRow(i);
        }

    }


    function changeViewToEdit(table, footer){
        console.log(table);
        var tableA = document.getElementById(table);
        var rows = tableA.rows;
        var tID = table;
        var fID = footer;

        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curName = cells[0].innerHTML;
            var curEmail = cells[1].innerHTML;
            var curDept = cells[2].innerHTML;

            cells[0].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputName\" value=\""+curName+"\">"
            cells[1].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail\" value=\""+curEmail+"\">"
            cells[2].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputDept\" value=\""+curDept+"\">"
            cells[3].innerHTML = "<button type =\"button\" onclick=\"clearAdmin("+tID+", "+i+")\" class=\"btn btn-default clearmod-btn\"><i class=\"material-icons\">clear</i></button>";

        }

        document.getElementById(footer).innerHTML =
            "<button class=\"btn btn-default col-md-2 col-md-offset-8\" type=\"button\" onclick=\"changeViewToView('"+tID+"','"+fID+"')\">Cancel</button>"+
            "<input class=\"btn btn-default col-md-2 col-md-offset-0\" type=\"submit\" value=\"Save Changes\"></div>";

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
            var curEmail = cells[1].getElementsByTagName("input")[0].value;
            var curDept = cells[2].getElementsByTagName("input")[0].value;


            cells[3].innerHTML = "";

            if(curName != "" && curEmail != "" && curDept !=""){
                cells[0].innerHTML = curName;
                cells[1].innerHTML = curEmail;
                cells[2].innerHTML = curDept;
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

        footerA.innerHTML =
            "<button type =\"button\"data-toggle=\"modal\" data-target=\"#AddNewAdminModal\" class=\"btn btn-default col-md-2 col-md-offset-8\">Add Admins</button>" +
            " <button class=\"btn btn-default col-md-2 col-md-offset-0\" type=\"button\" onclick=\"changeViewToEdit('"+tableA.id+"', '"+footerA.id+"')\">Edit Accounts</button>";

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

    <div class="panel-group" role="tablist">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="collapseListGroupHeading1">
                <h4 class="panel-title">
                    List of Admins</h4>
            </div>
            <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup1" aria-labelledby="collapseListGroupHeading1" aria-expanded="false">
                <ul class="list-group">
                    <form>
                        <li class="list-group-item">
                            <table class="table table-hover" id="admintable">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($administrators as $admin):?>
                                    <tr>
                                        <td><?=$admin->first_name . " " . $admin->last_name?></td>
                                        <td><?=$admin->email?></td>
                                        <td><?=$admin->name?></td>
                                        <td></td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </li>
                        <div class = "panel-footer clearfix" id = "admintable_footer">
                            <button type ="button"data-toggle="modal" data-target="#AddNewAdminModal" class="btn btn-default col-md-2 col-md-offset-8">Add Admins</button>
                            <button class="btn btn-default col-md-2 col-md-offset-0" type="button" onclick="changeViewToEdit('admintable','admintable_footer')">Edit Accounts</button>
                        </div>
                    </form>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class = "col-md-2"></div>


<!-- Modal -->
<div id="AddNewAdminModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Admin Account/s</h4>
            </div>
            <form>
                <div class="modal-body clearfix">

                    <button type = "button" class = "btn btn-default btn-block  " onclick = "addAdmin('add_table')">Add Another Admin</button>
                    <table class="table table-hover" id="add_table" name="">  <!-- TODO: somehow insert table id in name for add ? -->
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tbody>

                        <tr>
                            <td><input type="text" class="form-control" placeholder="Enter name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter email"></td>
                            <td><input type="text" class="form-control" placeholder="Enter department"></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cancelAddAdmin('add_table')">Cancel</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>



</body>
</html>