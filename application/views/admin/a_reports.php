<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?=base_url()?>assets/js/jquery-3.1.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->

<script xmlns="http://www.w3.org/1999/html">


    function check(){
        $.ajax({
            url: '<?=base_url('analytics/test')?>',
            type: 'GET',
            dataType: 'json',
            data: {

            }
        })
            .done(function(result) {
                console.log("done");
                console.log(result);
            })
            .fail(function(result) {
                console.log("fail");
                console.log(result);
            })
            .always(function() {
                console.log("complete");
            });
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

<button onclick="check()">HIIII</button>

</body>
</html>