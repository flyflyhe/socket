<?php

include __DIR__.'/client.php';

$strHost = "127.0.0.1";
$port = 25003;
for ($i = 0; $i < 1000; $i++) {

    $pid = pcntl_fork();
    //父进程和子进程都会执行下面代码
    if ($pid == -1) {
        //错误处理：创建子进程失败时返回-1.
        die('could not fork');
    } else if ($pid) {
        //父进程会得到子进程号，所以这里是父进程执行的逻辑
        
        //pcntl_wait($pid, $status); //等待子进程中断，防止子进程成为僵尸进程。
    } else {
        //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
        $pClient = new ClientSocket($strHost, $port);

        //var_dump($pClient->read());
        $strMsg = '你是一只狗哈哈哈啊:'.uniqid();
        //var_dump($strMsg);
        $pClient->write($strMsg);
        //var_dump($pClient->read());

        sleep(10);
        $pClient->close();
        die($i.'执行完毕'.PHP_EOL);
    }
}