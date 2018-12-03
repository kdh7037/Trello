<?php
$command=preg_split("/[\\\\]/", $_POST[message]);		//message 분리

$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );	//데이터베이스 연결
mysqli_select_db( $con, "workspace");

switch ($command[0]) {
case "add": 							//add\list\name
	for($i=3; $i<count($command); $i++)			//이름에 \가 있을시 분리된 name 복구
	$command[2].="\\\\$command[$i]";
								//마지막 리스트 id 추출(=row[0])
	$query = "select list_id from list
		where link_right=0";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
								//리스트 추가
	$query = "insert into list (list)
		values ('$command[2]')";
	mysqli_query( $con, $query );
	
	if ($row[0] != "") {					//리스트가 1개이상 있었을경우
								//추가한 리스트 id 추출(=id[0])
		$query = "select max(list_id) from list";
		$result = mysqli_query( $con, $query );
		$id = mysqli_fetch_row($result);
								//추가한 리스트를 마지막 리스트 뒤로 이동
		$query = "update list 				
			set link_right = $id[0] 
			where list_id = $row[0]";
		mysqli_query( $con, $query );
		$query = "update list 
			set link_left = $row[0] 
			where list_id = $id[0]";
		mysqli_query( $con, $query );
	}
	break;
case "delete":							//delete\list_index
								//삭제할 리스트 양옆 리스트 id 추출(=link[])
	$query = "select link_left, link_right
		from list where list_id = $command[1]";
	$result = mysqli_query($con, $query);
	$link = mysqli_fetch_row($result);
								//리스트 삭제
	$query = "delete from list
		where list_id ='$command[1]'";
	mysqli_query( $con, $query );
								//삭제된 리스트 양옆 리스트 연결
	$query = "update list
		set link_right = $link[1] 
		where link_right = $command[1]";
	mysqli_query( $con, $query );
	$query = "update list 
		set link_left = $link[0] 
		where link_left = $command[1]";
	mysqli_query( $con, $query );
	break;
case "modify":
	if($command[1] == 'list_name') { 			//modify\list_name\list_index\new_name
		for($i=4; $i<count($command); $i++)		//이름에 \가 있을시 분리된 new_name 복구 
			$command[3].="\\\\$command[$i]";
		
		$query = "update list				//이름 변경
			set list = '$command[3]'
			where list_id ='$command[2]'";
		mysqli_query( $con, $query );
	}
	else {							//modify\list_place\list_index\list_index
		if($command[2]==$command[3]) break;		//list_index = list_index 일 경우 break;
		
		$left_id = $command[3];				//left_id=list_left
								//이동시킬 리스트 양옆 리스트 id 추출(=link[])
		$query = "select link_left, link_right
			from list where list_id = $command[2]";
		$result = mysqli_query($con, $query);
		$link = mysqli_fetch_row($result);
								//양옆 리스트 연결
		$query = "update list
			set link_right = '$link[1]'
			where list_id ='$link[0]'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_left = '$link[0]'
			where list_id ='$link[1]'";
		mysqli_query( $con, $query );
						
		if($left_id == '0') {				//list_left = 0
								//첫번째 리스트 id 추출(=first_id[0])
			$query = "select list_id
				from list where link_left = '0'";
			$result = mysqli_query($con, $query);
			$first_id = mysqli_fetch_row($result);
								//이동시킬 리스트를 첫번째 리스트 왼쪽으로 이동
			$query = "update list
				set link_right = '$first_id[0]'
				where list_id ='$command[2]'";
			mysqli_query( $con, $query );
			$query = "update list
				set link_left = '$command[2]'
				where list_id ='$first_id[0]'";
			mysqli_query( $con, $query );
			$query = "update list
				set link_left = '0'
				where list_id ='$command[2]'";
			mysqli_query( $con, $query );
		}
		else {						//list_left > 0
								//list_left의 오른쪽 리스트 id 추출(=right_id[0])
		$query = "select link_right
			from list where list_id = $command[3]";
		$result = mysqli_query($con, $query);
		$right_id = mysqli_fetch_row($result);
								//이동시킬 리스트를 리스트 right_id[0], left_id사이로 이동
		$query = "update list
			set link_right = '$command[2]'
			where list_id ='$left_id'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_left = '$command[2]'
			where list_id = '$right_id[0]'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_right = '$right_id[0]'
			where list_id = '$command[2]'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_left = '$left_id'
			where list_id = '$command[2]'";
		mysqli_query( $con, $query );
		}
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
