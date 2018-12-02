<?php

$length = strlen($_POST[message]);
$type = substr($_POST[message], 0, 1);

$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );
mysqli_select_db( $con, "workspace");

switch ($type) {
case "a": 							//add/list/name
	$name = substr($_POST[message], 9, 50);			//name 추출

	$query = "select list_id from list 
		where link_right=0";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
	
	$query = "insert into list (list)
		values ('$name')";
	mysqli_query( $con, $query );
	
	if ($row[0] != "") {
		$query = "select max(list_id) from list";
		$result = mysqli_query( $con, $query );
		$id = mysqli_fetch_row($result);
		
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
case "d":																	//delete/list_index
	$num = substr($_POST[message], 7, 5);
	$query = "select link_left, link_right 
		from list where list_id = $num";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_row($result);
	
	$query = "delete from list 
		where list_id ='$num'";
	mysqli_query( $con, $query );
	
	$query = "update list 
		set link_right = $row[1] 
		where link_right = $num";
	mysqli_query( $con, $query );
	$query = "update list 
		set link_left = $row[0] 
		where link_left = $num";
	mysqli_query( $con, $query );
	break;
case "m":
	$type_modify = substr($_POST[message], 12, 1);
	if($type_modify == 'n') { 														//modify/list_name/list_index/new_name
		$data = substr($_POST[message], 17, 60);			//list_index, new_name 추출
		$name = substr(strchr($data, '/'),1,50);
		$id_length = strlen($data) - strlen($name) - 1;
		$id = substr($data, 0, $id_length);
		
		$query = "update list
			set list = '$name'
			where list_id ='$id'";
		mysqli_query( $con, $query );
    }
	else {																				//modify/list_place/list_index/list_left
		$data = substr($_POST[message], 18, 60);			//list_index, list_left 추출
		$left_id = substr(strchr($data, '/'),1,50);
		$id_length = strlen($data) - strlen($left_id) - 1;
		$id = substr($data, 0, $id_length);
		
		$query = "select link_right
			from list where list_id = $left_id";
		$result = mysqli_query($con, $query);
		$right_id = mysqli_fetch_row($result);
		
		$query = "select link_left, link_right 
			from list where list_id = $id";
		$result = mysqli_query($con, $query);
		$link = mysqli_fetch_row($result);

		$query = "update list
			set link_right = '$link[1]'
			where list_id ='$link[0]'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_left = '$link[0]'
			where list_id ='$link[1]'";
		mysqli_query( $con, $query );
						
		if($left_id == '0') {
			$query = "select list_id
				from list where link_left = '0'";
			$result = mysqli_query($con, $query);
			$first_id = mysqli_fetch_row($result);
			
			$query = "update list
				set link_right = '$first_id'
				where list_id ='$id'";
			mysqli_query( $con, $query );
			$query = "update list
				set link_left = '$id'
				where list_id ='$first_id'";
			mysqli_query( $con, $query );
			$query = "update list
				set link_left = '0'
				where list_id ='$id'";
			mysqli_query( $con, $query );
		}
		else {
		$query = "update list
			set link_right = '$id'
			where list_id ='$left_id'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_left = '$id'
			where list_id = '$right_id[0]'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_right = '$right_id[0]'
			where list_id = '$id'";
		mysqli_query( $con, $query );
		$query = "update list
			set link_left = '$left_id'
			where list_id = '$id'";
		mysqli_query( $con, $query );
		}
    }
	break;
default:
	break;
}

mysqli_close( $con );
echo("
	<script>
	history.go(-1);
	</script>
	");
?>
