<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="signin.css">
    <script src="css/bootstrap/js/bootstrap.js">
    </script>
</head>

<body>
    <div class="container">
        <form method="post" class="form-signin" action="login_db.php">
            <h2 class="form-signin-heading">Log in to Trello</h2>
            
            <label>Email</label>
            <label for="inputEmail" class="sr-only">Email</label>
            <input type="email" name="mem_email" id="inputEmail" class="form-control">
            <label>Password</label>
            <label for="inputPassword" class="sr-only">Email</label>
            <input type="password" name="mem_password" id="inputPassword" class="form-control">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember" value="remember-me" > Remember me
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">로 그 인</button>
        </form>

    </div>


</body>

</html>