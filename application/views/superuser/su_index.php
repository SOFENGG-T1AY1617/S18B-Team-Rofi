</head>
<body>


    <div class="panel-group clearfix col-md-offset-2 col-md-8" role="tablist">
        <button type ="button"data-toggle="modal" data-target="#AddNewBuildingModal" class="btn btn-success btn-block">+ Add Building</button>
    </div>

    <div id="panels" class = "col-md-offset-2 col-md-8">


        <!-- SINGLE PANEL -->
        <?php foreach($buildings as $row):?>
            <div class="panel-group" role="tablist">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="collapseListGroupHeading<?=$row->buildingid?>">
                        <h4 class="panel-title clearfix ">
                            <a href="#collapseListGroup<?=$row->buildingid?>" class="col-md-10" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapseListGroup<?=$row->buildingid?>">
                                <?=$row->name?></a></h4>
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
                                        <?php foreach($rooms as $room):?>
                                            <?php if($room->buildingid == $row->buildingid): ?>
                                                <tr>
                                                    <td id="room_<?=$room->roomid?>"><?=$room->name?></td>
                                                    <td id="capacity_<?=$room->roomid?>"><?=$room->capacity?></td>
                                                    <td></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>

                                </li>
                                <div class = "panel-footer clearfix" id = "<?=$row->buildingid?>footer">
                                <span class = " col-md-offset-8 col-md-2">
                                    <button type ="button"data-toggle="modal" data-target="#AddNewRoomsModal" class="btn btn-success btn-block add-room-btn" data-buildingname="<?=$row->name?>" id="add-<?=$row->buildingid?>">+ Add Rooms</button>
                                </span>
                                    <span class = "col-md-2">
                                    <button class="btn btn-info btn-block" type="button" onclick="changeViewToEdit('<?=$row->buildingid?>table','<?=$row->buildingid?>footer')">Edit Rooms</button>
                                </span>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        <?php endforeach;?>

        <!-- end of panel -->
    </div>

</body>
</html>