<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?=base_url()?>assets/js/jquery-3.1.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->

<script xmlns="http://www.w3.org/1999/html">

    function selectBuilding(buildingid) {

        console.log("HI");
        if (buildingid != "") {
            console.log(buildingid);

            $.ajax({
                url: '<?php echo base_url('getRooms') ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    buildingid: buildingid
                }
            })
                .done(function(result) {
                    //console.log(result);
                    console.log("done");

                    $("#form_room").empty();

                    var out=[];

                    //out[0]= '<option value="0" selected >All Rooms</option>';

                    var firstRoomID;

                    if (result[0] != null)
                        firstRoomID = result[0].roomid;
                    else
                        firstRoomID = "";

                    for(var i=0;i<result.length;i++){
                        out[i] = '<option value="'+result[i].roomid+'" >'+result[i].name+'</option>';
                    }

                    if (out.length > 0)
                        $("#form_room").append(out);

                    selectRoom(firstRoomID);

                    numOfRooms = result.length;

                })
                .fail(function() {
                    console.log("fail");
                })
                .always(function() {
                    console.log("complete");
                });

            /*$.post('application/controllers/ajax/foo', function(data) {
             console.log(data)
             }, 'json');*/

        }
    }

    function selectRoom(roomid) {

        //updateTimesHeader(dateSelected == dateToday);

        var buildingid = $("#form_building").val();


        $("#form_room").attr('disabled', false);

        if (buildingid!=""&&roomid != "") {
            var interval;

            console.log(buildingid + "-" + roomid);

            $.ajax({
                url: '<?php echo base_url('analytics/test') ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    roomid: roomid
                }
            })
                .done(function (result) {
                    var out = [];
                    console.log(result);

                    for(var i=0;i<result.length;i++){
                        out[0]="<li>ComputerNo: "+result[i]['computerno']+" Used "+result[i]['uses']+" times</li>"
                    }

                    if (out.length > 0)
                        $("#output").empty().append(out);
                })
                .fail(function () {
                    console.log("fail");
                })
                .always(function () {
                    console.log("complete");
                })
                .then(function () {

                })
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


<ol class="breadcrumb  col-md-offset-2 col-md-10">
    <li><a href="#">Admin</a></li>
    <li class="active">Reports</li>
</ol>

        <div class = "row">
            <div class = "col-md-5">
                <div class = "panel panel-default">
                    <div class = "panel-body">
                        <div class = "form-group col-md-7">
                            Building:
                            <select class="form-control" id="form_building" name="form-building" onchange="selectBuilding(this.value)">
                                <option value="" selected disabled>Choose a building...</option>
                                <?php foreach($buildings as $row):?>
                                    <option value="<?=$row->buildingid?>"><?=$row->name?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class = "form-group col-md-5">
                            Room:
                            <select class="form-control" id="form_room" name="form-room" onchange="selectRoom(this.value)" disabled=true>
                                <option value="" selected></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="output"></div>

</body>
</html>