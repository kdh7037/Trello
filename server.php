<?php
session_start();

echo "Server is working!\n";

function handshake($client, $headers, $socket) { //handshake for new user

	if(preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $headers, $match))
		$version = $match[1];
	else {
		print("The client doesn't support WebSocket");
		return false;
	}

	if($version == 13) {
		// Extract header variables
        if(preg_match("/GET (.*) HTTP/", $headers, $match))
			$root = $match[1];
		if(preg_match("/Host: (.*)\r\n/", $headers, $match))
			$host = $match[1];
		if(preg_match("/Origin: (.*)\r\n/", $headers, $match))
			$origin = $match[1];
		if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $match))
			$key = $match[1];
        
		$acceptKey = $key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
		$acceptKey = base64_encode(sha1($acceptKey, true));

		$upgrade = "HTTP/1.1 101 Switching Protocols\r\n".
				   "Upgrade: websocket\r\n".
				   "Connection: Upgrade\r\n".
				   "Sec-WebSocket-Accept: $acceptKey".
				   "\r\n\r\n";

		socket_write($client, $upgrade);
		return true;
	}
	else {
		print("WebSocket version 13 required (the client supports version {$version})");
		return false;
    }
}

function unmask($payload) { //unmask the data sent from clients
	$length = ord($payload[1]) & 127;

	if($length == 126) {
		$masks = substr($payload, 4, 4);
		$data = substr($payload, 8);
	}
	elseif($length == 127) {
		$masks = substr($payload, 10, 4);
		$data = substr($payload, 14);
	}
	else {
		$masks = substr($payload, 2, 4);
		$data = substr($payload, 6);
	}

	$text = '';
    for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

function encode($text) { //encode the data before sending to clients
	// 0x1 text frame (FIN + opcode)
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);

	if($length <= 125) 		$header = pack('CC', $b1, $length); 	elseif($length > 125 && $length < 65536) 		$header = pack('CCS', $b1, 126, $length); 	elseif($length >= 65536)
		$header = pack('CCN', $b1, 127, $length);

	return $header.$text;
}

error_reporting(E_ALL);
/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting as it comes in. */
ob_implicit_flush();
// create a streaming socket, of type TCP/IP
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($sock, "0.0.0.0", 7867);
socket_listen($sock);

//connect to db
$con = mysqli_connect( "localhost", "root", "1841aa" );
mysqli_select_db( $con, "workspace");
// create a list of all the clients that will be connected to us..
// add the listening socket to this list
$clients = array($sock);
while (true)
{
    // create a copy, so $clients doesn't get modified by socket_select()
    $read = $clients;
    $write = null;
    $except = null;
    // get a list of all the clients that have data to be read from
    // if there are no clients with data, go to next iteration

    if (socket_select($read, $write, $except, 0) < 1)
        continue;
    // check if there is a client trying to connect
    if (in_array($sock, $read))
    {
        $clients[] = $newsock = socket_accept($sock);
        if(@socket_recv($newsock,$data,2048,0)<=0)
            continue;

        if(!handshake($newsock,$data,$sock)) continue;

        //socket_write($newsock, "There are ".(count($clients) - 1)." client(s) connected to the server\n");
        socket_getpeername($newsock, $ip, $port);
        echo "New client connected: {$ip}\n";

        $key = array_search($sock, $read);
        unset($read[$key]);
    }
    $number_of_error=0;    
    // loop through all the clients that have data to read from
    foreach ($read as $read_sock)
    {
        if($read_sock==$sock) continue;
        // read until newline or 1024 bytes
        // socket_read while show errors when the client is disconnected, so silence the error messages
        $data = @socket_read($read_sock, 4096, PHP_BINARY_READ);

        // check if the client is disconnected
        if ($data === false)
        {
            // remove client for $clients array
            $key = array_search($read_sock, $clients);
            unset($clients[$key]);
            echo "client disconnected.\n";
            continue;
        }

        $data = trim($data);
        if (!empty($data))
        {
            $decoded_data=unmask($data);
            echo " send {$decoded_data}\n";
            $send_data=$decoded_data;
            $command=preg_split("/[\\\\]/",$decoded_data);
            switch ($command[0]) {
                case "add": 
                    switch($command[1])
                    {
                        case "card": //add\card\list_index
                            for($i=4; $i<count($command); $i++)			//이름에 \가 있을시 분리된 name 복구
                                $command[3].="\\\\$command[$i]";
                                                        //리스트의 마지막 카드  id 추출(=row[0])
                            $query = "select card_id from card
                                where (link_right=0) and (list_id=$command[2])";
                            $result = mysqli_query($con, $query);
                            $row = mysqli_fetch_row($result);
                                                        //해당 리스트에 카드 추가
                            $query = "insert into card (list_id)
                                values ($command[2])";
                            mysqli_query( $con, $query );
							//해당 리스트 card_num+1
			    $query = "update list 
				set card_num = card_num + 1 
				where list_id=$command[2]";
			    mysqli_query( $con, $query );
                           				//추가한 카드 id 추출(=id[0])
                            $query = "select max(card_id) from card";
                            $result = mysqli_query( $con, $query );
                            $id = mysqli_fetch_row($result);
                            if ($row[0] != "") {	//해당 리스트에 카드가 1개이상 있었을때
                                                        
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
                            $send_data.="\\".$id[0];
                            echo "card added to list\n";
                            break;
                        case "list":    //add\list\name
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
                            //추가한 리스트 id 추출(=id[0])
                            $query = "select max(list_id) from list";
                            $result = mysqli_query( $con, $query );
                            $id = mysqli_fetch_row($result);
                            if ($row[0] != "") {	//리스트가 1개이상 있었을경우
                                                        
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
                            $send_data.="\\".$id[0];
                            echo "list added to workspace\n";
                            break;
                        case "comment":
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
                        default:
                            "Error: $decoded_data\n";
                            $number_of_error++;

                    }							
                    break;
                case "delete":							
                    switch($command[1]){
                        case "card":
                            //delete\card_index
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
                            echo "card deleted from list\n";
                            break;
                        case "list":
                            //delete\list_index
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
                            echo "list deleted from workspace\n";
                            break;
                        case "comment":
                                                        //delete\comment\comment_index
                                                            //댓글 삭제
                            $query = "delete from comment
                                where comment_id ='$command[1]'";
                            mysqli_query( $con, $query );
                            break;
                        default:
                            echo "Error: $decoded_data\n";
                            $number_of_error++;
                    }
                    break;
                case "modify":
                    switch($command[1])
                     {   
                        case "list_name":			//modify\list_name\list_index\new_name
                            for($i=4; $i<count($command); $i++)		//이름에 \가 있을시 분리된 new_name 복구 
                                $command[3].="\\\\$command[$i]";
                                                    //이름 변경
                            $query = "update list
                                set list = '$command[3]'
                                where list_id ='$command[2]'";
                            mysqli_query( $con, $query );
                            echo "list name modified\n";
                            break;
                        case "list_place": //modify\list_place\list_index\list_left
                            if($command[2]==$command[3]) break; //list_index = list_index 일 경우 break;						
                        
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
                            echo "list's position changed\n";
                            break;
                    case "card_name": 			//modify\card_name\card_index\new_name
                        for($i=4; $i<count($command); $i++)		//이름에 \가 있을시 분리된 new_name 복구 
                            $command[3].="\\\\$command[$i]";
                                                //이름 변경
                        $query = "update card
                            set card = '$command[3]'
                            where card_id ='$command[2]'";
                        mysqli_query( $con, $query );

                        echo "card's name modified\n";
                        break;
                    case "card_place":							//modify\card_place\card_index\card_up\list_index
                        if($command[2]==$command[3]) break;		//card_index = card_up 일 경우 break;
                        
                        $left_id = $command[3];				//left_id=card_up
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
                        
                        
                        if($left_id == '0') {				//card_up = 0
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
                        else {						//card_up > 0
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
                        echo "card's position changed\n";
                        break;
                    case "comment":
                        //modify\comment_string\comment_index\new_ string
                        for($i=4; $i<count($command); $i++)		//내용에 \가 있을시 분리된 new_ string 복구 
                            $command[3].="\\\\$command[$i]";
                                                //내용 변경
                        $query = "update comment
                            set mess = '$command[3]'
                            where comment_id ='$command[2]'";
                        mysqli_query( $con, $query );
                        break;
                    default:
                        "Error: $decoded_data\n";
                        $number_of_error++;
                    }
                    break;
                case "load":
                    switch($command[1])
                    {
                        case "workspace":
                            $split_string="GdwiSEoRfXJsyiw";

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
                            $card_count=array();
                                                                //순서대로 저장하기 위해 link 선언 l = list, c = card
                            $l_link_left=0;
                            $c_link_left = 0;
                                                                //list_count = 총 리스트 개수
                            $query = "select * from list ";
                            $result = mysqli_query($con, $query);
                            $list_count = mysqli_num_rows($result);
                            
                            for($i = 0; $i < $list_count; $i++) {
                                                                //link_left가  $l_link_left인 리스트 정보 추출(=$list_info[])
                                $query = "select * from list 
                                    where link_left = $l_link_left";
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
                                $query = "select * from card 
                                    where list_id = $list_id[$i]";
                                $result = mysqli_query($con, $query);
                                $card_count[$i] = mysqli_num_rows($result);
                            
                                for($j = 0; $j < $card_count[$i]; $j++) {
                                                                //해당 리스트의 (list_id = $list_id[$i]) link_left가  $c_link_left인 카드 정보 추출(=$card_info[])
                                    $query = "select * from card 
                                        where (link_left = $c_link_left) and (list_id =$list_id[$i])";
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
                            for($i=0; $i<$list_count; $i++)
                            {
                                $send_data.="\\list_info\\".$list_id[$i].$split_string.$list[$i].$split_string.$card_num[$i];
                                for($j=0; $j<$card_count[$i]; $j++)
                                {
                                    $send_data.="\\card_info\\".$card_id[$i][$j].$split_string
                                                .$split_string.$card[$i][$j];
                                }
                            }
                            break;
                        case "card_detail":
                            								//보여줄 카드의 id를 받아와 $card_id에 저장
                            $card_id=$command[2];
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
                            //$comment_info[0] = messsage, $comment_info[1] = date, $comment_info[2] = user_id, 
                            $send_data.="\\".$comment_info[2].$split_string.$comment_info[1].$split_string.$comment_info[0];
                            }
                    }
                default:
                    "Error: $decoded_data\n";
                    $number_of_error++;
                }
            
            // send some message to listening socket
            //socket_write($read_sock, $send_data);
            // send this to all the clients in the $clients array (except the first one, which is a listening socket)
            if($number_of_error==0)
                foreach ($clients as $send_sock)
                {
                    if ($send_sock == $sock)
                        continue;
                    $encoded_data=encode($send_data);
                    socket_write($send_sock, $encoded_data);
                } // end of broadcast foreach
        }
    } // end of reading foreach

}
// close the listening socket
socket_close($sock);

?>
