<?php
$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );		//데이터베이스 연결
mysqli_select_db( $con, "workspace");
									//입력한 이메일과 비밀번호에 맞는 회원정보 추출(=mem_info[])
$query = "select mem_id, mem_name,  mem_email, mem_password from member 
				where (mem_email = '$_POST[mem_email]') and (mem_password = '$_POST[mem_password]')";
$result = mysqli_query($con, $query);
$mem_info = mysqli_fetch_row($result);
									//입력한 이메일과 비밀번호에 맞는 회원정보가 없을때
if ($mem_info[0] == "") {
									//알림문 출력 후 뒤로가기
	echo("
				<script>
				window.alert('이메일과 비밀번호를 확인해 주세요');
				history.go(-1);
				</script>
			  ");
	exit;
}
/*else {								//입력한 이메일과 비밀번호에 맞는 회원정보가 있을때
	if (!session_is_registered('trello_logon')) {			//세션 하나 만들어서 로그인한 회원정보들 
			session_register('trello_logon');		//입력해야 되는데 이 세션함수는 이미 없어진 함수라길래
			$trello_logon = array($row[0], $row[1], $row[2]);//
	}
}*/
									//main.php로 이동
header("Location:http://127.0.0.1/main.php");

?>
