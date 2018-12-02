<?php
 echo "hello world!";
function handshake($client, $headers, $socket) {

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

error_reporting(E_ALL);
/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting as it comes in. */
ob_implicit_flush();
// create a streaming socket, of type TCP/IP
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($sock, "0.0.0.0", 7867);
socket_listen($sock);

//connect to db
$con = mysqli_connect( "localhost", "root", "pw" );
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
        if((int)@socket_recv($newsock,$data,2048,MSG_DONTWAIT)<0)
            continue;

        if(!handshake($newsock,$data,$sock))
            continue;

        socket_write($newsock, "There are ".(count($clients) - 1)." client(s) connected to the server\n");
        socket_getpeername($newsock, $ip, $port);
        echo "New client connected: {$ip}\n";

        $key = array_search($sock, $read);
        unset($read[$key]);
    }

    // loop through all the clients that have data to read from
    foreach ($read as $read_sock)
    {
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
            echo " send {$data}\n";
            // do sth..
            $command=preg_split("/[\]/",$data);
            switch($command[0])
            {
                case "add": 
                    switch($command[1])
                    {
                        case "list":
                            for($i=3; $i<count($command); $i++)
                                $command[2].=$command[$i];
                            //add list, command[2]: name of the list
                            break;
                        case "card":
                            for($i=4; $i<count($command); $i++)
                                $command[3].=$command[$i];
                            //add card, command[2]: name of the card, command[3]: index of parent list
                            break;
                        case "comment":
                            for($i=4; $i<count($command); $i++)
                                $command[3].=$command[$i];
                            //add comment, command[2]: index of parent card, command[3]: comment string
                            break;
                        default:
                    }
                    break;
                case "delete":
                    switch($command[1])
                    {
                        case "list":
                            for($i=3; $i<count($command); $i++)
                                $command[2].=$command[$i];
                            //delete list, command[2]: index of the list
                            break;
                        case "card":
                            for($i=3; $i<count($command); $i++)
                                $command[2].=$command[$i];
                            //delete card, command[2]: index of the card
                            break;
                        default:
                    }
                case "modify":
                     switch($command[1])
                    {
                        case "list_name":
                            for($i=4; $i<count($command); $i++)
                                $command[3].=$command[$i];    
                            //change list name, command[2]: index of list, command[3]: new name
                            break;
                        case "list_place":
                            for($i=4; $i<count($command); $i++)
                                $command[3].=$command[$i];
                            //change list's position, command[2]: index of list, command[3]: index of list which will come to the left, if less than 0, the moving list will be the first
                            break;
                        case "card_name":
                            for($i=4; $i<count($command); $i++)
                                $command[3].=$command[$i];
                            //change card's name, command[2]: index of card, command[3]: new name
                            break;
                        case "card_place":
                            for($i=4; $i<count($command); $i++)
                                $command[3].=$command[$i];
                            //change card's position, command[2]: index of card, command[3]: index of card which will come to above, if less than 0, the moving card will be the first in list
                            break;
                        case "description":
                            for($i=4; $i<count($command); $i++)
                                $command[3].=$command[$i];
                            //add or change card's description, command[2]: index of card, command[3]: description string;
                            break;
                        default:
                    }
                    case "load":
                        switch($command[1])
                        {
                            case "workspace":
                                for($i=3; $i<count($command); $i++)
                                    $command[2].=$command[$i];
                                //load workspace
                                break;
                            case "description":
                                for($i=4; $i<count($command); $i++)
                                    $command[3].=$command[$i];
                            default:
                        }
                    default:
            }
 
            // send some message to listening socket
            socket_write($read_sock, $send_data);
            // send this to all the clients in the $clients array (except the first one, which is a listening socket)
            foreach ($clients as $send_sock)
            {
                if ($send_sock == $sock)
                    continue;
                socket_write($send_sock, $data);
            } // end of broadcast foreach
        }
    } // end of reading foreach

}
// close the listening socket
socket_close($sock);

?>