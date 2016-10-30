<script xmlns="http://www.w3.org/1999/html">
    $(document).ready()
    {
        $('li#add_button').addClass("active");
    }
</script>
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
                        Gokongwei Hall </a> </h4>
            </div>
            <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup1" aria-labelledby="collapseListGroupHeading1" aria-expanded="false">
                <ul class="list-group">
                    <li class="list-group-item">
                        <form>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Room Name</th>
                                    <th>Number of PCs</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td><input type="text" class="form-control" id="exampleInputEmail1" placeholder="302A"></td>
                                    <td> <input type="number" class="form-control" id="exampleInputEmail1" placeholder="40"></td>
                                </tr>

                                <tr>
                                    <td><input type="text" class="form-control" id="exampleInputEmail1" placeholder="302B"></td>
                                    <td> <input type="number" class="form-control" id="exampleInputEmail1" placeholder="40"></td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </li>
                <div class="panel-footer">
                    <input class="btn btn-default" type="submit" value="Save Changes"></div>
            </div>
        </div>
    </div>
    <!-- end of panel -->


    <div class="panel-group" role="tablist">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="collapseListGroupHeading2 ">
                <h4 class="panel-title">
                    <a href="#collapseListGroup2" class="" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapseListGroup2">
                        Velasco Building </a> </h4>
            </div>
            <div class="panel-collapse collapse in" role="tabpanel" id="collapseListGroup2" aria-labelledby="collapseListGroupHeading2" aria-expanded="false">
                <ul class="list-group">
                    <li class="list-group-item">
                        <form>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Room Name</th>
                                    <th>Number of PCs</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td><input type="text" class="form-control" id="exampleInputEmail1" placeholder="211"></td>
                                    <td> <input type="number" class="form-control" id="exampleInputEmail1" placeholder="20"></td>
                                </tr>

                                <tr>
                                    <td><input type="text" class="form-control" id="exampleInputEmail1" placeholder="213"></td>
                                    <td> <input type="number" class="form-control" id="exampleInputEmail1" placeholder="20"></td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </li>
                    <div class="panel-footer">
                        <input class="btn btn-default" type="submit" value="Save Changes"></div>
            </div>
        </div>
    </div>

</div>
<div class = "col-md-2"></div>



</body>
</html>