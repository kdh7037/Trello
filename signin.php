
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Signin</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="signin.css">
    <script src="css/bootstrap/js/bootstrap.js">
    </script>
</head>

<body>
    <div class="container">
        <form class="form-signin" method="post" action="signin_db.php">
            <center><h2 class="form-signin-heading">Create a Trello Account</h2></center>
            <label>Name</label>
            <label for="inputName" class="sr-only">Name</label>
            <input type="id" name="mem_id" id="inputName" class="form-control" value="">
            <label>Email</label>
            <label for="inputEmail" class="sr-only">Email</label>
            <input type="email" name="mem_email" id="inputEmail" class="form-control" value="">
            <label>Password</label>
            <label for="inputPassword" class="sr-only">Email</label>
            <input type="password" name="mem_password" id="inputPassword" class="form-control" value="">
            <div class="checkbox">
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Create New Account</button>
        </form>

    </div>


</body>

</html>
