<?php
$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );		//데이터베이스 연결
mysqli_select_db( $con, "workspace");
																	//list_info를 넣을 배열들 선언
$list_id = array();
$list_link_left = array();
$list_link_right = array();
$list = array();
$card_num = array();
																	//card_info를 넣을 배열들 선언
$card_list_id = array();
$card_id = array();
$card_link_left = array();
$card_link_right = array();
$card = array();
$card_description = array();
$comment_num = array();
																	//순서대로 저장하기 위해 link 선언 l = list, c = card
$l_link_left=0;
$c_link_left = 0;
																	//list_count = 총 리스트 개수
$query = "select * from list ";
$result = mysqli_query($con, $query);
$list_count = mysqli_num_rows($result);

for($i = 0; $i < $list_count; $i++) {
																	//link_left가  $l_link_left인 리스트 정보 추출(=$list_info[])
	$query = "select * from list where link_left = $l_link_left";
	$result = mysqli_query($con, $query);
	$list_info = mysqli_fetch_row($result);
/*
	[]안의 숫자에 따라 리스트 정보들이 저장됨
	ex) $list_id[$i] = i번째 리스트의 list_id, $list_link_left[$i] = i번째 리스트의 link_left 등등
*/
	list($list_id[$i], $list_link_left[$i], $list_link_right[$i], $list[$i], $card_num[$i]) = $list_info;
																	//$l_link_left 변경
	$l_link_left = $list_id[$i];
																	//card_count = 해당 리스트의 (list_id = $list_id[$i])총 카드 개수
	$query = "select * from card where list_id = $list_id[$i]";
	$result = mysqli_query($con, $query);
	$card_count = mysqli_num_rows($result);

	for($j = 0; $j < $card_count; $j++) {
																	//해당 리스트의 (list_id = $list_id[$i]) link_left가  $c_link_left인 카드 정보 추출(=$card_info[])
		$query = "select * from card where (link_left = $c_link_left) and (list_id =$list_id[$i])";
		$result = mysqli_query($con, $query);
		$card_info = mysqli_fetch_row($result);
	/*
		[]안의 숫자에 따라 리스트 정보들이 저장됨
		$card_list_id[$i][$j] = i번째 리스트의 j번째 카드의 list_id, 
		$card_link_left[$i][$j] = i번째 리스트의 j번째 카드의 link_left 등등
	*/
		list($card_list_id[$i][$j], $card_id[$i][$j], $card_link_left[$i][$j], $card_link_right[$i][$j], $card[$i][$j], $card_description[$i][$j], $comment_num[$i][$j]) = $card_info;
																	//$c_link_left 변경
		$c_link_left = $card_id[$i][$j];
	}
																	//$c_link_left 초기화
	$c_link_left = 0;
}
																	//$l_link_left 초기화
$l_link_left=0;
																	//데이터베이스 종료
mysqli_close( $con );
																	//이러면 각 배열에 순서대로 저장 완료

?>