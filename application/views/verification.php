<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/25/2016
 * Time: 9:35 PM
 */

?>

<script type="text/javascript">
    $(document).ready(function() {
        var message = "<?=$message?>";
        console.log(message)
        $("#result-text").text(message);
    });
</script>

<div class="container">
    <div class = "row">
        <div class = "col-md-10 col-md-offset-1">
            <div class="panel-body">
                <div class = "row">
                    <div class = "col-md-12 text-center">
                       <span id="result-text"></span>
                    </div>
                </div>
                <div class = "row">
                    <div class = "col-md-3 col-md-offset-5">
                        <a class="btn btn-success" href="<?=site_url("")?>">OK!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
