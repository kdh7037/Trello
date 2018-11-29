<?php
$mem_id = trim($_POST[mem_id]);
$mem_email = trim($_POST[mem_email]);
$mem_password = trim($_POST[mem_password]);

if( !$mem_id  || !$mem_email  || !$mem_password) {
    echo("
				 <script>
				 window.alert('입력값이 부족합니다.');
				 history.go(-1);
				 </script>
	 		  ");
	 exit;
  }

$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );
mysqli_select_db( $con, "workspace");

$query = "select * from member where mem_email='".$mem_email."'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_row($result);
if ($row[0] == "") {
$query = "insert into member
    				   (mem_id, mem_email, mem_password)
     			   values ('$mem_id', '$mem_email', '$mem_password')";
	mysqli_query( $con, $query );
  	mysqli_close( $con );
	echo("
				 <script>
				 alert('회원가입이 완료되었습니다.');
				 </script>
	 		  ");
}
else {
	echo("
				 <script>
				 window.alert('이미 존재하는 이메일입니다.');
				 history.go(-1);
				 </script>
	 		  ");
	exit;
  }

header("Location: http://127.0.0.1/login.php");
?>