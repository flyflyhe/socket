<?php

require dirname(__FILE__).'/Request.php';
const STR_END = 'EXIT!';
$res_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (socket_connect($res_socket, '127.0.0.1', '8992')) {
    $in = 'weihe weih!'.STR_END;
    socket_write($res_socket, $in, 1024); #客户端发送字节 与后台设置要相同 同样都已1024为一次发送

    $out = '';
    while ($buf = socket_read($res_socket, 1024)) {
        $out .= $buf;
    }
    var_dump($out);
}
socket_close($res_socket);