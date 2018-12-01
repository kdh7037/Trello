<?php

    error_reporting(E_ALL);
    set_time_limit(0);
    ob_implicit_flush();

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket.\n");
    $result = socket_bind($socket, "0.0.0.0", 80) or die("Could not bind to socket.\n");
    $result = socket_listen($socket, 20) or die("Could not set up socket listener.\n");

    
