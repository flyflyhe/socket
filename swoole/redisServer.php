<?php
use Swoole\Redis\Server;

$server = new Server("127.0.0.1", 9502, SWOOLE_BASE);

$server->set(array(
    'task_worker_num' => 32,
    'worker_num' => 1,
));

$server->setHandler('LPUSH', function ($fd, $data) use ($server) {
    $taskId = $server->task($data);
    if ($taskId === false)
    {
        return Server::format(Server::ERROR);
    }
    else
    {
        return Server::format(Server::INT, $taskId);
    }
});

$server->on('Finish', function() {

});

$server->on('Task', function ($serv, $taskId, $workerId, $data) {
    var_dump($data);
});

$server->start();