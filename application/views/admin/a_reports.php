<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?=base_url()?>assets/js/jquery-3.1.1.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->

<style>
    .graphlabel{
        text-align: center;
        padding-top: 10px;
    }

    .row{
        position:fixed;
    }

</style>

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

                    getData(firstRoomID);

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

    function getData(roomid) {

        //updateTimesHeader(dateSelected == dateToday);

        var buildingid = $("#form_building").val();


        $("#form_room").attr('disabled', false);
        $("#radio-today").attr('disabled', false);
        $("#radio-weekly").attr('disabled', false);

        if (buildingid!=""&&roomid != "") {
            var interval;

            console.log(buildingid + "-" + roomid);

            var dateType = $('input[name=optradio]:checked').val();

            $.ajax({
                url: '<?php echo base_url('analytics/getData') ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    roomid: roomid,
                    dateType: dateType
                }
            })
                .done(function (result) {
                    $("#output").empty();
                    var out = [];
                    console.log(result);

                    updateGraphs(result);
                })
                .fail(function () {
                    console.log("fail");
                })
                .always(function () {
                    console.log("complete");
                })
                .then(function () {

                })

            $("#lgraph1").html("Computer Use over Time");
            $("#lgraph2").html("Computer Use per Computer");

        }


    }

    function updateGraphs(result) {

        var computers = result['computers'];
        var times = result['times']['times_today'];
        var timesDisplay = result['times']['times_today_DISPLAY'];
        var reservations = result['reservations'];
        var reservationsTime = result['reservationsTime'];

        var data1=[];
        for(var i = 0; i<times.length; i++){
            var time = times[i];
            var uses = 0;
            for(var j = 0; j<reservationsTime.length;j++){
                if(reservationsTime[j]['time']==time)
                    uses = reservationsTime[j]['uses'];
            }
            data1[i]={'uses':uses,"time":timesDisplay[i]};
        }



        $('#graph1').empty();
        new Morris.Bar({
            // ID of the element in which to draw the chart.
            element: 'graph1',
            // Chart data records -- each entry in this array corresponds to a point on
            // the chart.
            data: data1,
            // The name of the data record attribute that contains x-values.
            xkey: 'time',
            // A list of names of data record attributes that contain y-values.
            ykeys: ['uses'],
            // Labels for the ykeys -- will be displayed when you hover over the
            // chart.
            labels: ['Value'],
            resize: true,
            postUnits:" Uses",
            xLabelAngle: 45
        });


        var data2 =[];

        for(var i = 0; i<computers.length; i++){
            var comp = computers[i]['computerno'];
            var uses = 0;
            for(var j = 0; j<reservations.length;j++){
                if(reservations[j]['computerno']==comp)
                    uses = reservations[j]['uses'];
            }
            data2[i]={'uses':uses,"computerno":"Comp No. "+comp};
        }





        $('#graph2').empty();
        new Morris.Bar({
            // ID of the element in which to draw the chart.
            element: 'graph2',
            // Chart data records -- each entry in this array corresponds to a point on
            // the chart.
            data: data2,
            // The name of the data record attribute that contains x-values.
            xkey: 'computerno',
            // A list of names of data record attributes that contain y-values.
            ykeys: ['uses'],
            // Labels for the ykeys -- will be displayed when you hover over the
            // chart.
            labels: ['Value'],
            resize: true,
            postUnits:" Uses",
            xLabelAngle: 45
        });
    }

</script>

<link href="<?=base_url()?>/assets/css/admin_style.css" rel="stylesheet">

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

<div class="clearfix col-md-7 col-md-offset-2">
    <label id="lgraph1" class="graphlabel col-md-12"></label>
    <div id="graph1" class="graph"></div>
    <label id="lgraph2" class="graphlabel col-md-12"></label>
    <div id="graph2" class="graph"></div>
</div>

        <div class = "row col-md-offset-9 col-md-3">
            <div class = "">
                <div class = "panel panel-default">
                    <div class = "panel-body">
                        <div class = "form-group col-md-12">
                            Building:
                            <select class="form-control" id="form_building" name="form-building" onchange="selectBuilding(this.value)">
                                <option value="" selected disabled>Choose a building...</option>
                                <?php foreach($buildings as $row):?>
                                    <option value="<?=$row->buildingid?>"><?=$row->name?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class = "form-group col-md-12">
                            Room:
                            <select class="form-control" id="form_room" name="form-room" onchange="getData(this.value)" disabled=true>
                                <option value="" selected></option>
                            </select>
                        </div>
                        <div class="radio form-group col-md-5">
                            <div class="radio" id="radio-date" name="form-date">
                                <label><input type="radio" id="radio-today" onchange="getData($('#form_room').val())" name="optradio" value="today" checked disabled="true">
                                    Today
                                </label>
                                <label><input type="radio" id="radio-weekly" onchange="getData($('#form_room').val())" name="optradio" value="weekly" disabled="true">
                                    Weekly
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</body>
</html>