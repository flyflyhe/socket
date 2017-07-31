<?php
$redis = new Redis;
$redis->connect('127.0.0.1', 9502);
$taskId = $redis->lpush("myqueue", json_encode(array("hello", "swoole")));
var_dump($taskId);