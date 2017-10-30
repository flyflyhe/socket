<?php

$processArr = [];
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$bool = socket_bind($socket, "127.0.0.1", 9999);
if (!$bool) {
    throw new Exception("绑定端口失败");
}

$i = 1;
//父进程和子进程都会执行下面代码
while ($i--) {
    $pid = pcntl_fork();
    if ($pid == -1) {
        //错误处理：创建子进程失败时返回-1.
         die('could not fork');
    } else if ($pid) {
         //父进程会得到子进程号，所以这里是父进程执行的逻辑
         #pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程.
         pcntl_signal(SIGCHLD, function ($signal) use ($processArr) {
            echo "get signal $signal".PHP_EOL;
            $childId = pcntl_wait($status);
            unset($processArr[$childId]);
            var_export($processArr);            
         });
         $processArr[$pid] = 1;
         if (count($processArr))
            var_export($processArr);
    } else {
         //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
        $bool = socket_listen($socket);
        while (1) {
            $client = socket_accept($socket);
            if (socket_getpeername($client, $address, $port)) {
                echo "当前进程id- ".posix_getpid()." -Client $address : $port is now connected to us. \n";
            }
            #socket_write($client, "hello world from server\n");
            $line = socket_read($client, 1024);
            echo $line.PHP_EOL;
        }
        exit();
    }
}
while(count($processArr)){
    sleep(10);
};
