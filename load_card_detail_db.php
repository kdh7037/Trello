<?php
$con = mysqli_connect( "localhost", "root", "qlzkqaqg123" );		//데이터베이스 연결
mysqli_select_db( $con, "workspace");
								//보여줄 카드의 id를 받아와 $card_id에 저장
$card_id = $_POST[card_detail];
								//해당 카드의 이름($card_detail[0]), description($card_detail[1])]) 추출
$query = "select card, card_description from card 
					where card_id =$card_id";
$result = mysqli_query($con, $query);
$card_detail = mysqli_fetch_row($result);
								//$card = $card_detail[0], $card_description = $card_detail[1]
list($card, $card_description) = $card_detail;
								//comment_count = 해당 카드의 댓글 수
$query = "select * from card 
	where card_id = $card_id";
$result = mysqli_query($con, $query);
$comment_count = mysqli_num_rows($result);
								//댓글 내용($comment_info[0]), 날짜($comment_info[1]), 글쓴이($comment_info[2]) 추출
$query = "select mess, date, user_id 
	from comment order by comment_id asc 
	where card_id = $card_id";
$result = mysqli_query($con, $query);
while($comment_info = mysqli_fetch_row($result)) {
	//여기에서 프린트 하면 됨 댓글 다 프린트 될때 까지 반복
	//$comment_info[0] = mess, $comment_info[1] = date, $comment_info[2] = user_id, 
}

?>
