</head>
<body>


<?php
include 'su_navbar.php';
?>

    <div class="panel-group clearfix col-md-offset-2 col-md-8" role="tablist">
        <button type ="button"data-toggle="modal" data-target="#AddNewBuildingModal" class="btn btn-success btn-block">+ Add Building</button>
    </div>
    <div id="panels" class = "col-md-offset-2 col-md-8">


        <?php foreach($buildings as $row):?>
            <div class="panel-group" role="tablist">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="collapseListGroupHeading<?=$row->buildingid?>">
                        <h4 class="panel-title clearfix ">
                            <a href="#collapseListGroup<?=$row->buildingid?>" class="col-md-8" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseListGroup<?=$row->buildingid?>">
                                <?=$row->name?></a>

                        </h4>
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
                                        <?php $i=0; ?>
                                        <?php foreach($rooms as $room):?>
                                            <?php if($room->buildingid == $row->buildingid): ?>
                                                <?php $i += 1; ?>
                                                <tr>
                                                    <td id="room_<?=$room->roomid?>"><?=$room->name?></td>
                                                    <td id="capacity_<?=$room->roomid?>"><?=$room->capacity?></td>
                                                    <td></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach;?>
                                        </tbody>


                                    </table>


                                    <?php if($i == 0):?>
                                        <div id="norooms_message">
                                            <h4 style="text-align: center"> NO REGISTERED ROOMS </h4>
                                        </div>
                                    <?php endif;?>

                                </li>

                            </form>
                    </div>
                </div>
            </div>
        <?php endforeach;?>

        <!-- end of panel -->
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
                    <!--<div class="form-group">
                        <label for="bldgPrefix">Prefix:</label>
                        <input type="text" class="form-control" id="bldgPrefix" placeholder="Enter the prefix to be used. (ie. G for Gokongwei, SJ for St. Joseph Hall etc...)">
                    </div>-->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitBuilding()">Confirm</button>
                </div>
            </form>
        </div>

    </div>
</div>



</body>
</html>