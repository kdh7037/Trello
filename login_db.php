<?php
$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );
mysqli_select_db( $con, "workspace");

$query = "select mem_id,  mem_email, mem_password from member where (mem_email = '$_POST[mem_email]') and (mem_password = '$_POST[mem_password]')";
				$result = mysqli_query($con, $query);
				$row = mysqli_fetch_row($result);

if ($row[0] == "") {
	echo("
				<script>
				window.alert('이메일과 비밀번호를 확인해 주세요');
				history.go(-1);
				</script>
			  ");
	exit;
}
else {
	if (!session_is_registered('trello_logon')) {
			session_register('trello_logon');
			$trello_logon = array($row[0], $row[1], $row[2]);
	}
}

header("Location:http://127.0.0.1/");

?>