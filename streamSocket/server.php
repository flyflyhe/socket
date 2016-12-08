<?php

$master = [];
$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
if (!$socket) {
    echo "$errstr ($errno)".PHP_EOL;
} else {
    $master[] = $socket;
    $read = $master;
    while(1) {
        $read = $master;
        $mod_fd = stream_select($read, $_w = null, $_E = null, 5);
        if (!$mod_fd) {
            continue;
        }
        foreach ($read as $sinSoc) {
            if ($sinSoc === $socket) {
                $conn = stream_socket_accept($socket);
                fwrite($conn, "hello the time is ".date("Y-m-d H:i:s")."\n");
                $master[] = $conn;
            } else {
                $sock_data = fgets($sinSoc, 1024);
                if (0 === strlen($sock_data)) {
                    $key_to_del = array_search($sinSoc, $master, true);
                    fclose($sinSoc);
                    unset($master[$key_to_del]);
                } elseif ($sock_data === false) {
                    echo "Something bad happened";
                    $key_to_del = array_search($sinSoc, $master, true);
                    unset($master[$key_to_del]);
                } else {
                    echo "The Client has sent:";
                    var_dump($sock_data);
                    fwrite($sinSoc, "You have sent :[".$sock_data."]".PHP_EOL);
                }
            }
        }    
    }
}