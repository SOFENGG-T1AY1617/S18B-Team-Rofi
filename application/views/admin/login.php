</head>
<body>
    <form class="col-md-4 col-md-offset-4">

        <img src="http://tinypic.com/evb0ut.jpg" method="post" action="<?=base_url("admin/signIn")?>">
        <div class="form-group">
            <label for="adminEmail">Email:</label>
            <input type="email" class="form-control" id="adminEmail" placeholder="Ex. 123@dlsu.edu.ph" required>
        </div>

        <BR><BR>

        <div class="form-group">
            <label for="adminPassword">Password:</label>
            <input type="password" class="form-control" id="adminPassword" placeholder="Enter Your Password Here" required>
        </div>

        <BR><BR>


        <button type="submit" class="btn btn-default" id="submit-sign-in">Sign In</button>
    </form>
</body>
</html>