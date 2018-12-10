<?php
$command=preg_split("/[\\\\]/", $_POST[message]);		//message 분리

$con = mysqli_connect( "localhost", "root", "1841aa" );	//데이터베이스 연결
mysqli_select_db( $con, "workspace");

switch ($command[0]) {
case "add": 							//add\comment\card_index\member_index\string
	for($i=5; $i<count($command); $i++)			//내용에 \가 있을시 분리된 string 복구
		$command[4].="\\\\$command[$i]";
								//해당 카드의 list_id 추출(=list_id[0])
	$query = "select list_id from card
		where card_id = $command[2]";
	$result = mysqli_query($con, $query);
	$list_id = mysqli_fetch_row($result);
								//현재 시간 추출(=$today)
	$timestamp = strtotime("+17 hours");
	$today = date("Y/m/d/H/i/s", $timestamp);
								//해당 카드에 댓글 추가
	$query = "insert into comment (list_id, card_id, user_id, mess, date)
		values ($list_id[0] ,$command[2], $command[3], '$command[4]', '$today')";
	mysqli_query( $con, $query );
	break;
case "delete":							//delete\comment_index
								//댓글 삭제
	$query = "delete from comment
		where comment_id ='$command[1]'";
	mysqli_query( $con, $query );
	break;
case "modify":							//modify\comment_string\comment_index\new_ string
		for($i=4; $i<count($command); $i++)		//내용에 \가 있을시 분리된 new_ string 복구 
			$command[3].="\\\\$command[$i]";
								//내용 변경
		$query = "update comment
			set mess = '$command[3]'
			where comment_id ='$command[2]'";
		mysqli_query( $con, $query );
	break;
default:
	break;
}
mysqli_close( $con );
								//뒤로가기
echo("
	<script>
	history.go(-1);
	</script>
	");
?>
