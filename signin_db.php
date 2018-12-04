<?php
						//입력받은 값 앞뒤 공백(띄어쓰기) 제거
$mem_name = trim($_POST[mem_name]);
$mem_email = trim($_POST[mem_email]);
$mem_password = trim($_POST[mem_password]);
						//mem_name, $mem_email, mem_password 중 하나라도 없을 시 오류 출력, 뒤로가기
if( !$mem_name  || !$mem_email  || !$mem_password) {
	echo("
		<script>
		window.alert('입력값이 부족합니다.');
		history.go(-1);
		</script>
		");
	exit;
  }
						//데이터메이스 연결
$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );
mysqli_select_db( $con, "workspace");
						//데이터베이스에 mem_email 가 이미 존재하면 그 정보 추출(=row[0])
$query = "select * from member where mem_email='".$mem_email."'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_row($result);
if ($row[0] == "") {				//데이터베이스에 mem_email 없었을때
						//입력받은 값 데이터베이스에 입력
$query = "insert into member
	(mem_name, mem_email, mem_password)
	values ('$mem_name', '$mem_email', '$mem_password')";
	mysqli_query( $con, $query );
  	mysqli_close( $con );
						//알림문 출력 후 login.php로 이동
	echo('
				 <script>
				 alert("회원가입이 완료되었습니다.");
				 location.href="http://127.0.0.1/login.php";
				 </script>
	 		  ');
}
else {						//데이터베이스에 mem_email 가 이미 존재할때
						//알림문 출력 
	echo("
				 <script>
				 window.alert('이미 존재하는 이메일입니다.');
				 history.go(-1);
				 </script>
	 		  ");
	exit;
  }
?>
