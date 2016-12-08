<?php
$fp = stream_socket_client("tcp://127.0.0.1:25003", $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)".PHP_EOL;
} else {
    fwrite($fp, "hi hello");
    while(!feof($fp)) {
        var_dump(fgets($fp, 1024));
    }
    fclose($fp);
}