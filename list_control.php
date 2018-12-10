<?php 
$length = strlen($_POST[message]);
$type = substr($_POST[message], 0, 1);

$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );
mysqli_select_db( $con, "workspace");

switch ($type) {
case "a":                                    //add_list
	$query = "insert into list ()
     		  values ()";
	mysqli_query( $con, $query );
	break;
case "d":                                   //delete_list num
	$num = substr($_POST[message], 12, 5);
	$query = "delete from list 
			  where list_id ='$num'";
	mysqli_query( $con, $query );
	break;
case "m":                                   //modify_list (name or id) num
    $type_m = substr($_POST[message], 12, 1);
    if($type_m == 'n') {                     //이름 바꾸기 data=바꿀이름
        $num = substr($_POST[message], 17, 5);
        $query = "update list
	 		      set list = '$_POST[data]'
			      where list_id ='$num'";
    }
    else {                                  //순서 바꾸기 data=이 리스트와 바꿀 리스트의 id
        $num = substr($_POST[message], 15, 5);
        $query = "update list
	 		      set list_id = '0'
             	  where list_id ='$num'";
        mysqli_query( $con, $query );
		$query = "update list
                  set list_id = '$num'
              	  where list_id = '$_POST[data]'";
		mysqli_query( $con, $query );
		$query = "update list
                  set list_id = '$_POST[data]'
              	  where list_id = '0'";
    }
	mysqli_query( $con, $query );
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