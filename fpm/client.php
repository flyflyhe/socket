<?php

$host = "127.0.0.1";
$port = 10000;
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_connect($sock, '127.0.0.1', $port);

$request = 'GET / HTTP/1.1' . "\r\n" .
           'Host: example.com' . "\r\n\r\n";
socket_write($sock, $request);

socket_close($sock);
