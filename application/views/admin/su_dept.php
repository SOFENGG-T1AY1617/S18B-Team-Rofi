
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--<script src="--><?//=base_url()?><!--assets/js/jquery-3.1.1.min.js"></script>-->
<!-- Include all compiled plugins (below), or include individual files as needed -->

<script xmlns="http://www.w3.org/1999/html">

    $(document).ready(function() {
        $(document).ajaxStart(function () {
            $(document.body).css({ 'cursor': 'wait' })
        });
        $(document).ajaxComplete(function () {
            $(document.body).css({ 'cursor': 'default' })
        });

        $('#AddNewDeptModal').on('hidden.bs.modal', function () {
            cancelAddDept('add_table');
        })
    });


    function addDepartment(table){
        console.log(table);
        var tableA =document.getElementById(table);
        var row = tableA.insertRow(-1);

        var cellDName = row.insertCell(0);
        var cellFName = row.insertCell(1);
        var cellLName = row.insertCell(2);
        var cellEmail = row.insertCell(3);
        var del       = row.insertCell(4);


        <!--
        console.log(tableA.rows.length+"Doge")

        cellFName.id= "C0R"
        cellLName.id= "C1R"
        cellEmail.id= "C2R"
        del.id="DELETECOLUMN";
        -->

        console.log(cellFName.id);

        cellDName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter department name\">";
        cellFName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter first name\">";
        cellLName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter last name\">";
        cellEmail.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter email\">";
       // del.innerHTML       = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(tableA.rows.length-1)+")\" class=\"btn btn-default clearmod-btn\" id=\"DELETECOLUMN\"><i class=\"material-icons\">clear</i></button>";


    }


    function cancelAddDept(tableID){
        var table = document.getElementById(tableID);
        var rows = table.rows;
        var i;
        console.log(rows.length);
        for(i=rows.length-1; i>0; i--){
            //table.deleteRow(i);

            for (var j = 0; j < table.rows[i].cells.length; j++)
            {
                table.rows[i].cells[j].childNodes[0].value = "";

            }
        }
        console.log(tableID);
        //addDepartment(tableID);


    }

    var initialModTableData;

    function changeViewToEdit(table, footer, modal){
        //console.log(table);
        var tableA = document.getElementById(table);
        var rows = tableA.rows;
        var tID = table;
        var fID = footer;

        console.log("TABLE ID = "+table);

        if(table=="modtable")
            initialModTableData = getTableDataWithID(tID);
        else
            initialAdminTableData = getTableDataWithID(tID);
        console.log(initialModTableData);

        if(table=="modtable")
            document.getElementById(footer).innerHTML =
                "<button class=\"btn btn-default col-md-2 col-md-offset-8\" type=\"button\" onclick=\"changeViewToView('"+tID+"','"+fID+"', '"+modal+"')\">Cancel</button>"+
                "<button class=\"btn btn-default col-md-2 col-md-offset-0\" type=\"button\" onclick=\"submitModeratorChanges('"+tID+"')\" >Save Changes</div>";
        else
            document.getElementById(footer).innerHTML =
                "<button class=\"btn btn-default col-md-2 col-md-offset-8\" type=\"button\" onclick=\"changeViewToView('"+tID+"','"+fID+"', '"+modal+"')\">Cancel</button>"+
                "<button class=\"btn btn-default col-md-2 col-md-offset-0\" type=\"button\" onclick=\"submitAdminChanges('"+tID+"')\" >Save Changes</div>";

        for(var i = 1; i < rows.length; i++) {
            var cells = rows[i].cells;


            cells[0].id= "C0R"+i;
            cells[1].id= "C1R"+i;
            cells[2].id= "C2R"+i;
            cells[3].id= "C3R"+i;

            var curFNameID = $(cells[0]).attr("id");
            var curLNameID = $(cells[1]).attr("id");
            var curEmailID = $(cells[2]).attr("id");
            var curDeptID = $(cells[3]).attr("id");


            var curFName = cells[0].innerHTML;
            var curLName = cells[1].innerHTML;
            var curEmail = cells[2].innerHTML;
            var curDept = cells[3].innerHTML;


            console.log(curDeptID);
            cells[0].innerHTML = "<input type=\"text\" class=\"form-control\" id=\""+curFNameID+"\"value=\"" + curFName + "\">";
            cells[1].innerHTML = "<input type=\"text\" class=\"form-control\" id=\""+curLNameID+"\" value=\"" + curLName + "\">";
            cells[2].innerHTML = "<input type=\"text\" class=\"form-control\" id=\""+curEmailID+"\" value=\"" + curEmail + "\">";
            var drop = "<select type='text' id=\""+curDeptID+"\" class='form-control' placeholder='Enter department'>";

            var deps = <?php echo json_encode($departments); ?>;


            for(var j = 0; j<deps.length; j++)
            {
                drop+="<option value='" +deps[j].departmentid +"' ";
                if(deps[j].name==curDept) {

                    drop+=" selected ";
                }
                drop+=">"+deps[j].name+"</option>"
            }
            drop+="</select>";
            cells[3].innerHTML = drop;
            cells[4].innerHTML = "<button type =\"button\" onclick=\"clearAccount('"+tID+"', "+(i)+")\" class=\"btn btn-default clearmod-btn\" id=\"DELETECOLUMN\"><i class=\"material-icons\">clear</i></button>";

            console.log(tID);

            cells[4].id="DELETECOLUMN";



//            document.getElementById("#" + curDeptID).value = 1;

            if (<?php print_r($_SESSION["admin_typeid"]);?> !=1)
            $("#"+curDeptID).prop("disabled", true);

        }


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
            for (var j = 0; j < table.rows[i].cells.length; j++)
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

    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    function submitDepartment(tableID) {
        /*$('#confirm-add').attr('disabled', true);
        $("body").css("cursor", "progress");*/
        var tableData = getTableData(tableID);

        if (tableData == false) {
            toastr.error("An input field is empty. Please fill it and try again.", "Oops!");
            return;
        }

        // Validate email
        var email = tableData[0][3];
        if (!isEmail(email)) {
            toastr.error("The email you input is not valid. Please change it and try again.", "Oops!");
            //$("#confirm-add").attr('disabled', false);
            return;
        }

        $('#confirm-add').attr('disabled', true);
        $("body").css("cursor", "progress");

        $.ajax({
            url: '<?=base_url('admin/' . SU_ADD_DEPARTMENT)?>',
            type: 'GET',
            dataType: 'json',
            data: {
                departmentName: tableData[0][0],
                admin_firstName: tableData[0][1],
                admin_lastName: tableData[0][2],
                admin_email: tableData[0][3],
            }
        })
            .done(function(result) {
                console.log("done");
                console.log(result);
                if (result['result'] == "success") {
                    toastr.success("Successfully created " + tableData[0][0] + ".", "Success");

                    var delay = 1000;
                    setTimeout(function() {
                        reloadPage();
                    }, delay);


                }
                else {
                    var errors = result['errors'];

                    var toast = "Sorry, the ";
                    for (var i = 0; i < errors.length - 1; i++) {
                        toast = toast + errors[i] + " and ";
                    }

                    toast = toast + errors[errors.length - 1] + " you entered already exists!";
                    toastr.error(toast, "Oops!");

                    //reloadPage();
                }

                //$('#AddNewDeptModal').modal('toggle');
            })
            .fail(function(result) {
                console.log("fail");
                toastr.error("Sorry, the email could not be sent.", "Oops!");
                //console.log(result);
            })
            .always(function() {
                console.log("complete");
                $("#confirm-add").attr('disabled', false);
                $("body").css("cursor", "default");
            });

    }

    function reloadPage() {
        <?php
        // TODO Might be better if it didn't have to reload page. Clear table data then query through database?
        echo 'window.location = "'. site_url("admin/". SU_DEPARTMENTS) .'";';
        ?>
    }

</script>
</head>
<body>

<?php
include 'a_navbar.php';
?>

<ol class="breadcrumb  col-md-offset-2 col-md-5">
    <li>Admin</a></li>
    <li class="active">Department Management</li>
</ol>

<div class="panel-group clearfix col-md-3" role="tablist">
    <button type ="button"data-toggle="modal" data-target="#AddNewDeptModal" class="btn btn-success btn-block">+ Add Department</button>
</div>
<div id="panels" class = "col-md-offset-2 col-md-8">
    <?php foreach($departments as $dept): ?>
        <div class="panel-group" role="tablist">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="collapseListGroupHeading<?=$dept->departmentid?>">
                    <h4 class="panel-title clearfix ">
                        <a href="#collapseListGroup<?=$dept->departmentid?>" class="col-md-8" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseListGroup<?=$dept->departmentid?>">
                            <?=$dept->name?></a>

                    </h4>
                </div>
                <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup<?=$dept->departmentid?>" aria-labelledby="collapseListGroupHeading<?=$dept->departmentid?>" aria-expanded="false">
                    <ul class="list-group">
                        <form>
                            <li class="list-group-item">
                                <table class="table table-hover" id="<?=$dept->departmentid?>table">
                                    <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td id="fn_1"><?=$dept->first_name?></td>
                                        <td id="ln_1"><?=$dept->last_name?></td>
                                        <td id="email_1"><?=$dept->email?></td>
                                        <td></td>
                                    </tr>
                                    </tbody>


                                </table>


                            </li>

                        </form>
                </div>
            </div>
        </div>
    <?php endforeach;?>
    <!-- end of panel -->
</div>

<!-- Modal -->
<div id="AddNewDeptModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Department</h4>
            </div>
            <form>
                <div class="modal-body clearfix">

                   <!--<button type = "button" class = "btn btn-default btn-block  " onclick = "addDepartment('add_table')">Add Another Department</button>-->
                    <table class="table table-hover" id="add_table" name="">  <!-- TODO: somehow insert table id in name for add ? -->
                        <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Corresponding Admin</th>
                            <th></th>
                            <th></th>

                        </tr>
                        </thead>
                        <tbody>
                        <tbody>

                        <tr>
                            <td><input type="text" class="form-control" placeholder="Enter department name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter first name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter last name"></td>
                            <td><input type="email" class="form-control" placeholder="Enter email"></td>

                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cancelAddDept('add_table')">Cancel</button>
                    <button type="button" id="confirm-add" class="btn btn-success" onclick="submitDepartment('add_table')">Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>

</body>
</html>
</body>
</html>