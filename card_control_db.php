<?php
$command=preg_split("/[\\\\]/", $_POST[message]);		//message 분리

$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );	//데이터베이스 연결
mysqli_select_db( $con, "workspace");

switch ($command[0]) {
case "add": 							//add\card\list_index\name
	for($i=4; $i<count($command); $i++)			//이름에 \가 있을시 분리된 name 복구
		$command[3].="\\\\$command[$i]";
								//리스트의 마지막 카드  id 추출(=row[0])
	$query = "select card_id from card
		where (link_right=0) and (list_id=$command[2])";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
								//해당 리스트에 카드 추가
	$query = "insert into card (list_id, card)
		values ($command[2], '$command[3]')";
	mysqli_query( $con, $query );
	
	if ($row[0] != "") {					//해당 리스트에 카드가 1개이상 있었을때
																		//추가한 카드 id 추출(=id[0])
		$query = "select max(card_id) from card";
		$result = mysqli_query( $con, $query );
		$id = mysqli_fetch_row($result);
								//추가한 카드를 리스트의 마지막 카드 뒤로 이동
		$query = "update card
			set link_right = $id[0] 
			where card_id = $row[0]";
		mysqli_query( $con, $query );
		$query = "update card
			set link_left = $row[0] 
			where card_id = $id[0]";
		mysqli_query( $con, $query );
	}
	break;
case "delete":							//delete\card_index
								//삭제할 카드 양옆 카드 id 추출(=link[])
	$query = "select link_left, link_right
		from card where card_id = $command[1]";
	$result = mysqli_query($con, $query);
	$link = mysqli_fetch_row($result);
								//카드 삭제
	$query = "delete from card
		where card_id ='$command[1]'";
	mysqli_query( $con, $query );
								//삭제된 카드 양옆 카드 연결
	$query = "update card
		set link_right = $link[1] 
		where link_right = $command[1]";
	mysqli_query( $con, $query );
	$query = "update card
		set link_left = $link[0] 
		where link_left = $command[1]";
	mysqli_query( $con, $query );
	break;
case "modify":
	switch ($command[1]) {
		case "card_name":				//modify\card_name\card_index\new_name
			for($i=4; $i<count($command); $i++)	//이름에 \가 있을시 분리된 new_name 복구 
				$command[3].="\\\\$command[$i]";
								//이름 변경
			$query = "update card
				set card = '$command[3]'
				where card_id ='$command[2]'";
			mysqli_query( $con, $query );
			break;
		case "card_place":				//modify\card_place\card_index\card_up\list_index
			if($command[2]==$command[3]) break;	//card_index = card_up 일 경우 break;
		
			$left_id = $command[3];			//left_id=card_up
																						//이동시킬 카드 양옆 카드 id 추출(=link[])
			$query = "select link_left, link_right
				from card where card_id = $command[2]";
			$result = mysqli_query($con, $query);
			$link = mysqli_fetch_row($result);
								//양옆 카드 연결
			$query = "update card
				set link_right = '$link[1]'
				where card_id ='$link[0]'";
			mysqli_query( $con, $query );
			$query = "update card
				set link_left = '$link[0]'
				where card_id ='$link[1]'";
			mysqli_query( $con, $query );
		
		
			if($left_id == '0') {			//card_up = 0
								//옮길 카드의 list_id 변경
				$query = "update card
					set list_id = '$command[4]'
					where card_id ='$command[2]'";
				mysqli_query( $con, $query );
								//카드를 옮길 리스트의 첫번째 카드 id 추출(=first_id[0])
				$query = "select card_id from card 
					where (link_left = '0') and (list_id = '$command[4]')";
				$result = mysqli_query($con, $query);
				$first_id = mysqli_fetch_row($result);
								//이동시킬 카드를 첫번째 카드 왼쪽으로 이동
				$query = "update card
					set link_right = '$first_id[0]'
					where card_id ='$command[2]'";
				mysqli_query( $con, $query );
				$query = "update card
					set link_left = '$command[2]'
					where card_id ='$first_id[0]'";
				mysqli_query( $con, $query );
				$query = "update card
					set link_left = '0'
					where card_id ='$command[2]'";
				mysqli_query( $con, $query );
			}
			else {					//card_up > 0
								//card_up의 list_id 추출(=id_list[0])
			$query = "select list_id
				from card where card_id =$left_id";
			$result = mysqli_query($con, $query);
			$id_list = mysqli_fetch_row($result);
								//옮길 카드의 list_id 변경
			$query = "update card
				set list_id = '$id_list[0]'
				where card_id ='$command[2]'";
			mysqli_query( $con, $query );
								//card_up의 오른쪽 카드 id 추출(=right_id[0])
			$query = "select link_right
				from card where card_id = $command[3]";
			$result = mysqli_query($con, $query);
			$right_id = mysqli_fetch_row($result);
								//이동시킬 카드를 카드 right_id[0], left_id사이로 이동
			$query = "update card
				set link_right = '$command[2]'
				where card_id ='$left_id'";
			mysqli_query( $con, $query );
			$query = "update card
				set link_left = '$command[2]'
				where card_id = '$right_id[0]'";
			mysqli_query( $con, $query );
			$query = "update card
				set link_right = '$right_id[0]'
				where card_id = '$command[2]'";
			mysqli_query( $con, $query );
			$query = "update card
				set link_left = '$left_id'
				where card_id = '$command[2]'";
			mysqli_query( $con, $query );
			}
			break;
			case "description":				//modify\description\card_index\string
				for($i=4; $i<count($command); $i++)	//내용에 \가 있을시 분리된 string 복구 
					$command[3].="\\\\$command[$i]";
									//description 추가 or 변경
				$query = "update card
					set card_description = '$command[3]'
					where card_id ='$command[2]'";
				mysqli_query( $con, $query );
		}
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
