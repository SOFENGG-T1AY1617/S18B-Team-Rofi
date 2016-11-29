
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <!-- ADD CONDITION FOR ADMIN VIEW HERE -->
               
                <span class="icon-bar"></span>
               
                <!-- ADD CONDITION FOR ADMIN VIEW HERE -->
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">DLSU B)</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li id="overview_button"><a href="<?=site_url("admin")?>">Reports<span class="sr-only"></span></a></li>
                <?php if($_SESSION['admin_typeid'] == 1): ?>
                    <li><a href="<?=site_url("admin/" . SU_DEPARTMENTS)?>">Departments</a></li>
                <?php else: ?>
                    <li><a href="<?=site_url("admin/" . ADMIN_ACCOUNT_MANAGEMENT)?>">Account Management</a></li>
                <?php endif;?>



                <?php if($_SESSION['admin_typeid'] == 1): ?>
                    <li><a href="<?=site_url("admin/" . ADMIN_AREA_MANAGEMENT)?>">Buildings</a></li>
                <?php else: ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Application Settings<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li id="add_button"><a href="<?=site_url("admin/" . ADMIN_AREA_MANAGEMENT)?>">Manage Buildings</a></li>
                            <li id="modify_button"><a href="<?=site_url("admin/" . ADMIN_SCHEDULING)?>">Modify Schedule</a></li>
                            <li><a href="<?=site_url("admin/" . ADMIN_BUSINESS_RULES)?>">Adjust Business Rules</a></li>
                        </ul>
                    </li>
                <?php endif;?>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Admin <?=$_SESSION['first_name']?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Modify Account</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="<?=site_url('admin/' . ADMIN_SIGN_OUT)?>">Sign Out</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
