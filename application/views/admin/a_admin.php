<script xmlns="http://www.w3.org/1999/html">

    function addAdministrator(table){
        console.log(table);
        var tableA = table;
        var tID = table.id;
        var row = tableA.insertRow(-1);

        var cellName = row.insertCell(0);
        var cellEmail = row.insertCell(1);
        var cellUsername = row.insertCell(2);
        var clearbtn = row.insertCell(3);

        cellName.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter name\">";
        cellEmail.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter email of Administrator\">";
        cellUsername.innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder =\"Enter assigned username\">";
        clearbtn.innerHTML = "<button type =\"button\" onclick=\"clearAdministrator("+tID+", "+(table.rows.length-1)+")\" class=\"btn btn-default clearmod-btn\"><i class=\"material-icons\">clear</i></button>";

    }

    function clearAdministrator(table, rowNum){
        console.log(table);
        var tableA = table;
        tableA.deleteRow(rowNum);

    }


    function changeViewToEdit(table, footer){
        console.log(table);
        var tableA = table;
        var rows = tableA.rows;
        var tID = table.id;
        var fID = footer.id;

        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curName = cells[0].innerHTML;
            var curEmail = cells[1].innerHTML;
            var curUsername = cells[2].innerHTML;

            cells[0].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputName\" value=\""+curName+"\">"
            cells[1].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputEmail\" value=\""+curEmail+"\">"
            cells[2].innerHTML = "<input type=\"text\" class=\"form-control\" id=\"exampleInputUsername\" value=\""+curUsername+"\">"
            cells[3].innerHTML = "<button type =\"button\" onclick=\"clearAdministrator("+tID+", "+i+")\" class=\"btn btn-default clearmod-btn\"><i class=\"material-icons\">clear</i></button>";

        }
        var addID = ""+tID+"_addbtn";
        console.log(addID);
        var add = document.getElementById(addID.toString());

        add.innerHTML =
        " <button type =\"button\" onclick=\"addAdministrator("+tID+")\" class=\"btn btn-default addmod-btn\"><i class=\"material-icons\">add</i></button>";

        footer.innerHTML =
        "<button class=\"btn btn-default col-md-2\" type=\"button\" onclick=\"changeViewToView("+tID+","+fID+")\">Cancel</button>"+
        "<input class=\"btn btn-default  col-md-offset-8 col-md-2\" type=\"submit\" value=\"Save Changes\"></div>";

    }

    function changeViewToView(table, footer){

        /* Change that the html reapplies the original data in the database */
        /*
        console.log(table);
        var tableA = table;
        var rows = tableA.rows;
        var deleteRows=[];
        var lengthofdel=0;
        for(var i = 1; i < rows.length; i++){
            var cells = rows[i].cells;

            var curName = cells[0].getElementsByTagName("input")[0].value;
            var curEmail = cells[1].getElementsByTagName("input")[0].value;
            var curUsername = cells[2].getElementsByTagName("input")[0].value;
;

            if(curName != "" && curEmail != "" && curUsername != ""){
                cells[0].innerHTML = curName;
                cells[1].innerHTML = curEmail;
                cells[2].innerHTML = curUsername;
                cells[3].innerHTML = "";
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
        */

        var addID = ""+table.id+"_addbtn";
        var add = document.getElementById(addID.toString());



        add.innerHTML =
            "";

        footer.innerHTML =
            " <button class=\"btn btn-default col-md-2 col-md-offset-10\" type=\"button\" onclick=\"changeViewToEdit("+table.id+", "+footer.id+")\">Edit Accounts</button>";

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
                    List of Administrators</h4>
            </div>
            <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup1" aria-labelledby="collapseListGroupHeading1" aria-expanded="false">
                <ul class="list-group">
                    <form>
                        <li class="list-group-item">
                            <table class="table table-hover" id="modtable">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach($administrators as $admin):?>
                                    <tr>
                                        <td><?=$admin->first_name . " " . $admin->last_name?></td>
                                        <td>Email</td>
                                        <td>Username?</td>
                                        <td></td>

                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>

                            <div id = "modtable_addbtn">
                            </div>
                        </li>
                        <div class = "panel-footer clearfix" id = "modtable_footer">
                            <button class="btn btn-default col-md-2 col-md-offset-10" type="button" onclick="changeViewToEdit(modtable, modtable_footer)">Edit Accounts</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
<div class = "col-md-2"></div>




</body>
</html>