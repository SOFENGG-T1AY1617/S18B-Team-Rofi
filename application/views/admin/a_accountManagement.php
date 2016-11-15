<script xmlns="http://www.w3.org/1999/html">

    function addAccountModerator(table){
        console.log(table);
        var tableA =document.getElementById(table);
        var row = tableA.insertRow(-1);


        var cellFName = row.insertCell(0);
        var cellLName = row.insertCell(1);
        var cellEmail = row.insertCell(2);
       // var cellDept = row.insertCell(3);
        var del         = row.insertCell(3);

        cellFName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter first name\">";
        cellLName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter last name\">";
        cellEmail.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter email\">";
        //cellDept.innerHTML = "<select type='text' class='form-control' placeholder='Enter department'> <option value='0' disabled selected>Choose a Department</option><?php foreach($departments as $dep):?><option value=<?=$dep->departmentid?>><?=$dep->name?></option><?php endforeach;?></select>";
        del.innerHTML       = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(tableA.rows.length-1)+")\" class=\"btn btn-default clearmod-btn\" id=\"DELETECOLUMN\"><i class=\"material-icons\">clear</i></button>";

        //cellDept.innerHTML  = "<input type=\"text\" class=\"form-control\" id=\"exampleInputDept\" placeholder =\"Enter department\">";

    }
    function addAccountAdmin(table){
        console.log(table);
        var tableA =document.getElementById(table);
        var row = tableA.insertRow(-1);


        var cellFName = row.insertCell(0);
        var cellLName = row.insertCell(1);
        var cellEmail = row.insertCell(2);
        var cellDept = row.insertCell(3);
        var del         = row.insertCell(4);

        cellFName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter first name\">";
        cellLName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter last name\">";
        cellEmail.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter email\">";
        cellDept.innerHTML = "<select type='text' class='form-control' placeholder='Enter department'> <option value='0' disabled selected>Choose a Department</option><?php foreach($departments as $dep):?><option value=<?=$dep->departmentid?>><?=$dep->name?></option><?php endforeach;?></select>";
        del.innerHTML       = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(tableA.rows.length-1)+")\" class=\"btn btn-default clearmod-btn\" id=\"DELETECOLUMN\"><i class=\"material-icons\">clear</i></button>";

        //cellDept.innerHTML  = "<input type=\"text\" class=\"form-control\" id=\"exampleInputDept\" placeholder =\"Enter department\">";

    }

    function clearAccount(table, rowNum){

        //updateIndexOfDeleteButtons2(table,rowNum);
        table.deleteRow(rowNum);

    }
    function updateIndexOfDeleteButtons2(table,index)
    {
        var tableA = document.getElementById(table);
        for(var x=index+1;x<tableA.rows.length;x++)
        {
            for(y=1;y<tableA.rows.length;y++)
            {
                console.log(document.getElementById(tableA.rows[x].cells[y].innerHTML.id));
                if( document.getElementById(tableA.rows[x].cells[y])=="DELETECOLUMN")
                {
                    tableA.rows[x].cells[y].innerHTML = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(x-1)+")\" class=\"btn btn-default clearmod-btn\"><i class=\"material-icons\">clear</i></button>";
                    console.log(x-1);
                }
            }


        }
    }

    function updateIndexOfDeleteButtons(table,index,row)
    {
        var tableA = document.getElementById(table);
        for(var x=index+1;x<tableA.rows.length;x++)
        {
            tableA.rows[x].cells[row].innerHTML = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(x-1)+")\" class=\"btn btn-default clearmod-btn\"><i class=\"material-icons\">clear</i></button>";
            console.log(x-1);

        }
    }

    function cancelAddAccount(tableID){
        var table = document.getElementById(tableID);
        var rows = table.rows;
        var i;
        console.log(rows.length);
        for(i=rows.length-1; i>1; i--){
            table.deleteRow(i);
        }

    }


    function changeViewToEdit(table, footer, modal){
        console.log(table);
        var tableA = document.getElementById(table);
        var rows = tableA.rows;
        var tID = table;
        var fID = footer;

        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curFName = cells[0].innerHTML;
            var curLName = cells[1].innerHTML;
            var curEmail = cells[2].innerHTML;
            var curDept = cells[3].innerHTML;

            cells[0].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputName\" value=\""+curFName+"\">"
            cells[1].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputName\" value=\""+curLName+"\">"
            cells[2].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail\" value=\""+curEmail+"\">"
            cells[3].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputDept\" value=\""+curDept+"\">"
            cells[4].innerHTML = "<button type =\"button\" onclick=\"clearAccount("+tID+", "+i+")\" class=\"btn btn-default clearmod-btn\"><i class=\"material-icons\">clear</i></button>";

        }

        document.getElementById(footer).innerHTML =
            "<button class=\"btn btn-default col-md-2 col-md-offset-8\" type=\"button\" onclick=\"changeViewToView('"+tID+"','"+fID+"', '"+modal+"')\">Cancel</button>"+
            "<input class=\"btn btn-default col-md-2 col-md-offset-0\" type=\"submit\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer, modal){
        console.log(table);

        var tableA = document.getElementById(table);
        var footerA = document.getElementById(footer);
        var rows = tableA.rows;
        var deleteRows=[];
        var lengthofdel=0;
        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curFName = cells[0].getElementsByTagName("input")[0].value;
            var curLName = cells[1].getElementsByTagName("input")[0].value;
            var curEmail = cells[2].getElementsByTagName("input")[0].value;
            var curDept = cells[3].getElementsByTagName("input")[0].value;


            cells[4].innerHTML = "";

            if(curLName != "" && curFName != "" && curEmail != "" && curDept !=""){
                cells[0].innerHTML = curFName;
                cells[1].innerHTML = curLName;
                cells[2].innerHTML = curEmail;
                cells[3].innerHTML = curDept;
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
        console.log(modal);
        var s;
        if(modal == 'AddNewModeratorModal'){
            s ='Add Moderators';
        } else
            s = 'Add Admins';

        footerA.innerHTML =

                "<button type =\"button\"data-toggle=\"modal\" data-target=\"#"+modal+"\" class=\"btn btn-default col-md-2 col-md-offset-8\"> "+s+" </button>" +
                " <button class=\"btn btn-default col-md-2 col-md-offset-0\" type=\"button\" onclick=\"changeViewToEdit('"+tableA.id+"', '"+footerA.id+"','" +modal+"' )\">Edit Accounts</button>";

    }


    function getTableData(tableID) {
        var table = document.getElementById(tableID);
        //var tr = table.getElementsByTagName('tr');

        var jObject = [];
        for (var i = 1; i < table.rows.length; i++)
        {
            var row = i - 1;
            // create array within the array - 2nd dimension
            //jObject[row] = [];

            var valid = true;
            var columns = [];
            // columns within the row
            for (var j = 0; j < table.rows[i].cells.length-1; j++)
            {
                //jObject[row][j] = table.rows[i].cells[j].childNodes[0].value;
                columns[j] = table.rows[i].cells[j].childNodes[0].value;

                if (columns[j] == "") {
                    valid = false;
                    break;
                }
            }

            if (valid) {
                jObject[row] = columns;
            }
        }
        return jObject;
    }

    function submitModerator() {
        var tableID = $("#add_table").attr("id");
        var tableData = getTableData(tableID);
        console.log(tableData);


        $.ajax({
            url: '<?=base_url('admin/addModerators')?>',
            type: 'GET',
            dataType: 'json',
            data: {
                moderators: tableData
            }
        })
            .done(function(result) {
                console.log("done");
                //location.reload(true);
                // TODO Might be better if it didn't have to reload page. Clear table data then query through database?

            })
            .fail(function(result) {
                console.log("fail");
                console.log(result);
            })
            .always(function() {
                console.log("complete");
            });
    }

    function submitAdmins() {
        var tableID = $("#add_tableA").attr("id");
        var tableData = getTableData(tableID);
        console.log(tableData);

        $.ajax({
            url: '<?=base_url('admin/addAdmins')?>',
            type: 'GET',
            dataType: 'json',
            data: {
                admins: tableData
            }
        })
            .done(function(result) {
                console.log("done");
                //location.reload(true);
                // TODO Might be better if it didn't have to reload page. Clear table data then query through database?

            })
            .fail(function(result) {
                console.log("fail");
                console.log(result);
            })
            .always(function() {
                console.log("complete");
            });
    }

    function deleteRow(table, index){
        var tableA = document.getElementById(table);
        //updateIndexOfDeleteButtons2(table,index);
        updateIndexOfDeleteButtons(table,index,3);
        tableA.deleteRow(index);
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

    <div class="panel-group" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="collapseListGroupHeadingMod">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#collapseListGroupMod" aria-expanded="true" aria-controls="collapseListGroupMod">
                        List of Moderators
                    </a></h4>
            </div>
            <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroupMod" aria-labelledby="collapseListGroupHeadingMod" aria-expanded="false">
                <ul class="list-group">
                    <form>
                        <li class="list-group-item">
                            <table class="table table-hover" id="modtable">
                                <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach($moderators as $mod):?>
                                    <tr>
                                        <td><?=$mod->first_name?></td>
                                        <td><?=$mod->last_name?></td>
                                        <td><?=$mod->email?></td>
                                        <td><?=$mod->name?></td>
                                        <td></td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </li>
                        <div class = "panel-footer clearfix" id = "modtable_footer">
                            <button type ="button"data-toggle="modal" data-target="#AddNewModeratorModal" class="btn btn-default col-md-2 col-md-offset-8">Add Moderators</button>
                            <button class="btn btn-default col-md-2 col-md-offset-0" type="button" onclick="changeViewToEdit('modtable','modtable_footer', 'AddNewModeratorModal')">Edit Accounts</button>
                        </div>
                    </form>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php if($_SESSION['admin_typeid'] == 1): ?>
    <!-- Only show admin panel if user is a superuser -->
    <div id="panels" class = "col-md-offset-2 col-md-8">

        <div class="panel-group" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="collapseListGroupHeadingAdmin">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" href="#collapseListGroupAdmin" aria-expanded="true" aria-controls="collapseListGroupAdmin">
                            List of Admins
                        </a>
                    </h4>
                </div>
                <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroupAdmin" aria-labelledby="collapseListGroupHeadingAdmin" aria-expanded="false">
                    <ul class="list-group">
                        <form>
                            <li class="list-group-item">
                                <table class="table table-hover" id="admintable">
                                    <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($administrators as $admin):?>
                                        <tr>
                                            <td><?=$admin->first_name?></td>
                                            <td><?=$admin->last_name?></td>
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
                                <button class="btn btn-default col-md-2 col-md-offset-0" type="button" onclick="changeViewToEdit('admintable','admintable_footer', 'AddNewAdminModal')">Edit Accounts</button>
                            </div>
                        </form>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class = "col-md-2"></div>
<?php endif;?>

<!-- Moderator Modal -->
<div id="AddNewModeratorModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Moderator Account/s</h4>
            </div>
            <form>
                <div class="modal-body clearfix">

                    <button type = "button" class = "btn btn-default btn-block  " onclick = "addAccountModerator('add_table')">Add Another Moderator</button>
                    <table class="table table-hover" id="add_table" name="">  <!-- TODO: somehow insert table id in name for add ? -->
                        <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Delete Row</th>

                        </tr>
                        </thead>
                        <tbody>
                        <tbody>

                        <tr>
                            <td><input type="text" class="form-control" placeholder="Enter first name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter last name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter email"></td>

                            <td><button type ="button" onclick="deleteRow('add_table', 1)" class="btn btn-default clearmod-btn"><i class="material-icons">clear</i></button></td>

                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cancelAddAccount('add_table')">Cancel</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="submitModerator('add_table')">Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Admin Modal -->
<div id="AddNewAdminModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Admin Account/s</h4>
            </div>
            <form>
                <div class="modal-body clearfix">

                    <button type = "button" class = "btn btn-default btn-block  " onclick = "addAccountAdmin('add_tableA')">Add Another Admin</button>
                    <table class="table table-hover" id="add_tableA" name="">  <!-- TODO: somehow insert table id in name for add ? -->
                        <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Delete Row</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tbody>

                        <tr>
                            <td><input type="text" class="form-control" placeholder="Enter first name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter last name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter email"></td>

                            <td><select type='text' class='form-control' placeholder='Enter Department'>
                                    <option value='0' disabled selected>Choose a Department</option>
                                    <?php foreach($departments as $dep):?>
                                        <option value=<?=$dep->departmentid?>><?=$dep->name?></option>
                                    <?php endforeach;?>
                                </select></td>

                            <td><button type ="button" onclick="deleteRow('add_tableA', 1)" class="btn btn-default clearmod-btn"><i class="material-icons">clear</i></button></td>

                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cancelAddAccount('add_tableA')">Cancel</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="submitAdmins('add_tableA')" >Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>


</body>
</html>