</head>
<body>
<form class="col-md-4 col-md-offset-4" method="post" action="<?=base_url("superuser/su_index")?>">

    <img src="http://tinypic.com/evb0ut.jpg">

    <span id="error-message"><?=$errorMessage?></span>

    <div class="form-group">
        <label for="suEmail">Email:</label>
        <input type="email" class="form-control" name="suEmail" id="suEmail" placeholder="Ex. 123@dlsu.edu.ph" required>
    </div>

    <BR><BR>

    <div class="form-group">
        <label for="suPass">Password:</label>
        <input type="password" class="form-control" name="suPass" id="suPass" placeholder="Enter Your Password Here" required>
    </div>

    <BR><BR>


    <button type="submit" class="btn btn-default" id="submit-sign-in">Sign In</button>
</form>
</body>
</html>