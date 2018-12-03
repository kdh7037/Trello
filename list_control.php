<?php
$command=preg_split("/[\\\\]/", $_POST[message]);		//message 분리

$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );	//데이터베이스 연결
mysqli_select_db( $con, "workspace");

switch ($command[0]) {
case "add": 							//add\list\name
	for($i=3; $i<count($command); $i++)			//이름에 \가 있을시 분리된 name 복구
	$command[2].="\\\\$command[$i]";
	
	$query = "select list_id from list 			//마지막 리스트 id 추출(=row[0])
		where link_right=0";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
	
	$query = "insert into list (list)			//리스트 추가
		values ('$command[2]')";
	mysqli_query( $con, $query );
	
	if ($row[0] != "") {					//리스트가 1개이상 있었을때
		$query = "select max(list_id) from list";	//추가한 리스트 id 추출(=id[0])
		$result = mysqli_query( $con, $query );
		$id = mysqli_fetch_row($result);
		
		$query = "update list 				//추가한 리스트를 마지막 리스트 뒤로 이동
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
	$query = "select link_left, link_right 			//삭제할 리스트 양옆 리스트 id 추출(=link[])
		from list where list_id = $command[1]";
	$result = mysqli_query($con, $query);
	$link = mysqli_fetch_row($result);
	
	$query = "delete from list 				//리스트 삭제
		where list_id ='$command[1]'";
	mysqli_query( $con, $query );
	
	$query = "update list 					//삭제된 리스트 양옆 리스트 연결
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
	else {							//modify\list_place\list_index\list_left
		$left_id = $command[3];				//left_id=list_left
		
		$query = "select link_right			//list_left의 오른쪽 리스트 id 추출(=right_id[0])
			from list where list_id = $command[3]";
		$result = mysqli_query($con, $query);
		$right_id = mysqli_fetch_row($result);
		
		$query = "select link_left, link_right 		//이동시킬 리스트 양옆 리스트 id 추출(=link[])
			from list where list_id = $command[2]";
		$result = mysqli_query($con, $query);
		$link = mysqli_fetch_row($result);
		$query = "update list				//양옆 리스트 연결
			set link_right = '$link[1]'
			where list_id ='$link[0]'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_left = '$link[0]'
			where list_id ='$link[1]'";
		mysqli_query( $con, $query );
						
		if($left_id == '0') {				//list_left = 0
			$query = "select list_id		//첫번째 리스트 id 추출(=first_id[0])
				from list where link_left = '0'";
			$result = mysqli_query($con, $query);
			$first_id = mysqli_fetch_row($result);
			
			$query = "update list			//이동시킬 리스트를 첫번째 리스트 왼쪽으로 이동
				set link_right = '$first_id'
				where list_id ='$command[2]'";
			mysqli_query( $con, $query );
			$query = "update list
				set link_left = '$command[2]'
				where list_id ='$first_id'";
			mysqli_query( $con, $query );
			$query = "update list
				set link_left = '0'
				where list_id ='$command[2]'";
			mysqli_query( $con, $query );
		}
		else {						//list_left > 0
		$query = "update list				//이동시킬 리스트를 리스트 right_id[0], left_id사이로 이동
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
echo("								//뒤로가기
	<script>
	history.go(-1);
	</script>
	");
?>
