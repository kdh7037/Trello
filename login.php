<?php
													//쿠키에 저장된 이메일이 있을때 $str_value에 저장
if($_COOKIE['email']) 
	$str_value = "value='".$_COOKIE['email']."'";
	
if($_POST[pass] == "pass") {					    	//로그인화면을 지나왔을때
													    //입력받은 값 앞뒤 공백(띄어쓰기) 제거
	$mem_email = trim($_POST[mem_email]);
	$mem_password = trim($_POST[mem_password]);
														//mem_email, mem_password 중 하나라도 없을 시 오류 출력, 뒤로가기
	if(!$mem_email  || !$mem_password) {
		echo("
					<script>
					window.alert('입력값이 부족합니다.');
					history.go(-1);
					</script>
				  ");
		exit;
	}
	session_start();											    	//세션 시작
	$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );		//데이터베이스 연결
	mysqli_select_db( $con, "workspace");
																		//입력한 이메일과 비밀번호에 맞는 회원정보 추출(=mem_info[])
	$query = "select mem_id, mem_name,  mem_email, mem_password from member 
					where (mem_email = '$_POST[mem_email]') and (mem_password = '$_POST[mem_password]')";
	$result = mysqli_query($con, $query);
	$mem_info = mysqli_fetch_row($result);

	if ($mem_info[0] == "") {								//입력한 이메일과 비밀번호에 맞는 회원정보가 없을때
															//알림문 출력 후 뒤로가기
		echo("
					<script>
					window.alert('이메일과 비밀번호를 확인해 주세요');
					history.go(-1);
					</script>
				  ");
		exit;
	}
												//로그인 성공시
												//$_SESSION['$mem_info'] 에 $mem_info 저장
												//$_SESSION['$mem_info'][0] = mem_id, [1] = mem_name,
												//			    	    [2] = mem_email, [3] = mem_password																
	$_SESSION['$mem_info'] = array();
	$_SESSION['mem_id'] = $mem_info[0];
	array_push ($_SESSION['$mem_info'], $mem_info[0], $mem_info[1], $mem_info[2], $mem_info[3]);

	$remember = $_POST[remember];
	
	if($remember == "remember-me") {		    //Remember me에 체크되있을때
		$email = $_POST[mem_email];				//입력받은 이메일 쿠키에 저장(10일동안)
		$time = time() + (60*60*24*10);			//현재시간 + 60(초)*60(분)*24(시)*10(일)
		setcookie("email", $email, $time); 
		}
	else 										//체크 안되있을때 쿠키 삭제
		setcookie("email"); 
	
												//main.php로 이동
header("Location:http://127.0.0.1/main.php");
}
												//로그인 화면 출력
echo("
<html lang='ko'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <title>Login</title>
    <link rel='stylesheet' href='css/bootstrap/css/bootstrap.css'>
    <link rel='stylesheet' href='signin.css'>
    <script src='css/bootstrap/js/bootstrap.js'>
    </script>
</head>

<body>
    <div class='container'>
        <form method='post' class='form-signin' action='$_SERVER[PHP_SELF]'>
			<input type='hidden' name='pass' value='pass'>
            <h2 class='form-signin-heading'>Log in to Trello</h2>
            
            <label>Email</label>
            <label for='inputEmail' class='sr-only'>Email</label>
            <input type='email' name='mem_email' id='inputEmail' class='form-control' $str_value>
            <label>Password</label>
            <label for='inputPassword' class='sr-only'>Email</label>
            <input type='password' name='mem_password' id='inputPassword' class='form-control'>
            <div class='checkbox'>
                <label>
                    <input type='checkbox' name='remember' value='remember-me' checked='checked'> Remember me
                </label>
            </div>
            <button class='btn btn-lg btn-primary btn-block' type='submit'>로 그 인</button>
        </form>

    </div>


</body>

</html>
");
?>
