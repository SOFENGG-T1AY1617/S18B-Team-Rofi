
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?=base_url()?>assets/js/jquery-3.1.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->

<script xmlns="http://www.w3.org/1999/html">



    function addAccountModerator(table){
        console.log(table);
        var tableA =document.getElementById(table);
        var row = tableA.insertRow(-1);


        var cellFName = row.insertCell(0);
        var cellLName = row.insertCell(1);
        var cellEmail = row.insertCell(2);
        var del         = row.insertCell(3);

        cellFName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter first name\">";
        cellLName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter last name\">";
        cellEmail.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter email\">";
        del.innerHTML       = "<button type =\"button\" onclick=\"deleteRow('"+table+"', "+(tableA.rows.length-1)+")\" class=\"btn btn-default clearmod-btn\" id=\"DELETECOLUMN\"><i class=\"material-icons\">clear</i></button>";


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

        var tableA = document.getElementById(table);
        updateIndexOfDeleteButtons2(table,rowNum);
        /*
        var table_ID= $(table).attr("id");
        //$('table.row['+rowNum+']').hide();

        console.log(table_ID);
        var tableID = $(table.rows[rowNum].cells[0]).attr("id");
        console.log(table.rows[rowNum].cells[0]);
        console.log($(tableID).parents('tr'));
        $(tableID).parents('tr').hide();
        $(tableID).val("-1");*/

        tableA.deleteRow(rowNum);
    }
    
    function updateIndexOfDeleteButtons2(table,index)
    {
        console.log(table);
        var tableID= $(table).attr("id");
        var tableA = document.getElementById(table);
        for(var x=index+1;x<tableA.rows.length;x++)
        {
            for(y=0;y<tableA.rows[x].cells.length;y++)
            {
                console.log(tableA.rows[x].cells[y].id);
  
                console.log(tableA.rows.length);
                if( tableA.rows[x].cells[y].id=="DELETECOLUMN")
                {
                    tableA.rows[x].cells[y].innerHTML = "<button type =\"button\" onclick=\"clearAccount('"+table+"', "+(x-1)+")\" class=\"btn btn-default clearmod-btn\" id=\"DELETECOLUMN\"><i class=\"material-icons\">clear</i></button>";
                    console.log(tableA.rows[x].cells[y].id);

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
    /*
    function updateIndexOfDeleteButtonsAdmin(table,index)
    {
        var tableA = document.getElementById(table);
        for(var x=index+1;x<tableA.rows.length;x++)
        {
            tableA.rows[x].cells[4].innerHTML = "<button type =\"button\" onclick=\"deleteRowAdmin('"+table+"', "+(x-1)+")\" class=\"btn btn-default clearmod-btn\"><i class=\"material-icons\">clear</i></button>";
            console.log(x-1);

        }
    }
    */
    function cancelAddAccount(tableID){
        var table = document.getElementById(tableID);
        var rows = table.rows;
        var i;
        console.log(rows.length);
        for(i=rows.length-1; i>1; i--){
            table.deleteRow(i);
        }

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
            cells[4].innerHTML = "<button type =\"button\" onclick=\"clearAccount('"+tID+"', "+i+")\" class=\"btn btn-default clearmod-btn\" id=\"DELETECOLUMN\"><i class=\"material-icons\">clear</i></button>";

            console.log(tID);

           cells[4].id="DELETECOLUMN";



//            document.getElementById("#" + curDeptID).value = 1;

            if (<?php print_r($_SESSION["admin_typeid"]);?> !=1)
            $("#"+curDeptID).prop("disabled", true);

        }


    }

    function emptyTable(table){
        var tableA=document.getElementById(table);

        for(var x=tableA.rows.length-1;x>0;x--)
        {
            tableA.deleteRow(x);
        }                           

    }
    function repopulateTable(table){
        /*Insert DB extraction Codes Here*/
        var tableA=document.getElementById(table);

        var out = "<thead> <tr> <th>First Name</th><th>Last Name</th><th>Email</th><th>Department</th><th></th></tr></thead><tbody>";


        if(table=="admintable")
         out += " <?php foreach($administrators as $admin):?><tr><td><?=$admin->first_name?></td><td><?=$admin->last_name?></td><td><?=$admin->email?></td><td><?=$admin->name?></td><td></td></tr><?php endforeach;?>";
        else
           out += "<?php foreach($moderators as $mod):?><tr><td><?=$mod->first_name?></td><td><?=$mod->last_name?></td><td><?=$mod->email?></td><td><?=$mod->name?></td><td></td></tr><?php endforeach;?>"



            //TODO FIX THIS^^^^ VERY COWBOY

        tableA.innerHTML = out;
        /*
        var x=tableA.rows.length;
        var row = tableA.insertRow(x);
        var fName = row.insertCell(0);
        var lName = row.insertCell(1);
        var email = row.insertCell(2);
        var name = row.insertCell(3);
        var deleteButton = row.insertCell(4);
        fName.innerHTML= "Test";                                       
        lName.innerHTML= "Test";  
        email.innerHTML= "Test";  
        name.innerHTML="Test";  
        deleteButton.innerHTML="Test";  
        */


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
            var curDept = cells[3].getElementsByTagName("select")[0].value;


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


        emptyTable(table);
        repopulateTable(table);
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

                if (columns[j]) {
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

        console.log(table);
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

            columns[0] = table.rows[i].cells[0].childNodes[0].data;
            console.log(columns[0]);
            for (var j = 0; j < 3; j++)
            {
                //jObject[row][j] = table.rows[i].cells[j].childNodes[0].value;
                columns[j + 1] = table.rows[i].cells[j+1].childNodes[0].data;

                if (columns[j + 1] == "") {
                    valid = false;
                    break;
                }
            }
            console.log(columns);
            /*columns[1] = table.rows[i].cells[0].childNodes[0].value;
             columns[2] = table.rows[i].cells[1].childNodes[0].value;*/

            if (valid) {
                jObject[row] = columns;
            }
        }
        return jObject;
    }

    function getModChangedData(newTableData) {
        var changedData = [];
        var changedDataIndex = 0;




        var dept = <?php echo json_encode($departments); ?>;
        var mods = <?php echo json_encode($moderators); ?>;

        console.log(mods);

        for (var i = 0; i < initialModTableData.length; i++) {


            var inDeptID;
            for(var j = 0; j<dept.length; j++)
            {
              //  console.log(dept[j].departmentid+" CHECK "+ initialModTableData[i][3]);
                if(dept[j].name == initialModTableData[i][3]) {
                    inDeptID=dept[j].departmentid;
                  //  console.log(dept[j].departmentid+" CHECK "+ initialModTableData[i][3]);
                }
            }

            if (initialModTableData[i][0] != newTableData[i][0] ||
                initialModTableData[i][1] != newTableData[i][1] ||
                initialModTableData[i][2] != newTableData[i][2] ||
               // initialModTableData[i][4] != newTableData[i][4]
                inDeptID != newTableData[i][3]
            ) {
                for(var k = 0; k<mods.length; k++){
                    if(initialModTableData[i][2]==mods[k]['email']){
                        newTableData[i][4] = mods[k]['moderatorid'];
                            console.log("HERE: " + mods[k]['moderatorid']);
                    }
                }

                changedData[changedDataIndex] = newTableData[i];
                changedDataIndex++;

                //console.log("aa "+ initialModTableData[i]+" "+newTableData[i]);
            }
        }

        return changedData;
    }

    function submitModeratorChanges(tableID) {
        var changedData = getModChangedData(getTableData(tableID));

        $.ajax({
            url: '<?=base_url('admin/updateModerators')?>',
            type: 'GET',
            dataType: 'json',
            data: {
                changedData: changedData
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
            .fail(function(result) {
                console.log("fail");
                console.log(result);
            })
            .always(function() {
                console.log("complete");
            });
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
                //
                reloadPage();
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

    function getAdminChangedData(newTableData) {
        var changedData = [];
        var changedDataIndex = 0;




        var dept = <?php echo json_encode($departments); ?>;
        var admins = <?php echo json_encode($administrators); ?>;

        //console.log(mods);

        for (var i = 0; i < initialAdminTableData.length; i++) {


            var inDeptID;
            for(var j = 0; j<dept.length; j++)
            {
                //  console.log(dept[j].departmentid+" CHECK "+ initialAdminTableData[i][3]);
                if(dept[j].name == initialAdminTableData[i][3]) {
                    inDeptID=dept[j].departmentid;
                    //  console.log(dept[j].departmentid+" CHECK "+ initialAdminTableData[i][3]);
                }
            }

            if (initialAdminTableData[i][0] != newTableData[i][0] ||
                initialAdminTableData[i][1] != newTableData[i][1] ||
                initialAdminTableData[i][2] != newTableData[i][2] ||
                // initialAdminTableData[i][4] != newTableData[i][4]
                inDeptID != newTableData[i][3]
            ) {
                for(var k = 0; k<admins.length; k++){
                    if(initialAdminTableData[i][2]==admins[k]['email']){
                        newTableData[i][4] = admins[k]['administratorid'];
                        console.log("HERE: " + admins[k]['administratorid']);
                    }
                }

                changedData[changedDataIndex] = newTableData[i];
                changedDataIndex++;

                //console.log("aa "+ initialAdminTableData[i]+" "+newTableData[i]);
            }
        }

        return changedData;
    }

    function submitAdminChanges(tableID) {
        var changedData = getAdminChangedData(getTableData(tableID));

        $.ajax({
            url: '<?=base_url('admin/updateAdmins')?>',
            type: 'GET',
            dataType: 'json',
            data: {
                changedData: changedData
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

        if (tableData == false) {
            toastr.error("An input field is empty. Please fill it and try again.", "Oops!");
            return;
        }

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

                if (result['result'] == "success") {
                    console.log(result['numAdded']);
                    var toast = "";
                    if (result['numAdded'] == 0) {
                        toast = "No admins were added.";
                    }
                    else if (result['numAdded'] == 1) {
                        toast = "1 admin was added successfully.";
                    }
                    else if (result['numAdded'] > 1 ) {
                        toast = result['numAdded'] + " admins were added successfully.";
                    }
                    toastr.success(toast, "Success");

                    var notAdded = result['notAdded'];
                    console.log(notAdded);

                    toast = ""
                    if (notAdded.length == 1) {
                        toast = notAdded + " was not added.";
                    }
                    else {
                        for (var i = 0; i < notAdded.length - 1; i++) {
                            toast = toast + notAdded[i] + ", ";
                        }

                        toast = toast + notAdded[notAdded.length - 1] + " were not added.";
                    }

                    toastr.error(toast, "Oops!");

                    var delay = 1000;
                    setTimeout(function() {
                        reloadPage();
                    }, delay);


                }
                else {
                    reloadPage();
                }

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

        $("#AddNewAdminModal").modal("toggle");
    }


    function deleteRow(table, index){
        var tableA = document.getElementById(table);
        //updateIndexOfDeleteButtons2(table,index);
        var row;
        if(table=="add_table")
            row=3;
        else if (table=="add_tableA")
            row=4;


        updateIndexOfDeleteButtons(table,index,row);
        tableA.deleteRow(index);
    }/*
    function deleteRowAdmin(table, index){
        var tableA = document.getElementById(table);
        //updateIndexOfDeleteButtons2(table,index);
        updateIndexOfDeleteButtonsAdmin(table,index);
        tableA.deleteRow(index);
    }*/


    function reloadPage() {
        <?php
        // TODO Might be better if it didn't have to reload page. Clear table data then query through database?
        echo 'window.location = "'. site_url("admin/".ADMIN_ACCOUNT_MANAGEMENT) .'";';
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
                    <button type="button" class="btn btn-success" onclick="submitAdmins('add_tableA')" >Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>


</body>
</html>