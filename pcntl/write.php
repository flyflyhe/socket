<?php
/**
 *使用此多进程 编写爬虫程序时 不用担心子进程成为僵尸进程 只要子进程有退出的逻辑
  因为子进程的父进程最先执行完毕 所有子进程的父进程都会被设置为init进程 不可能出现僵尸进程
 */
for ($i = 1; $i <= 5; ++$i) {
    echo '主进程pid为:'.posix_getpid().PHP_EOL;
    $pid = pcntl_fork(); 

    if ($pid == -1) {
        
    } else if ($pid) {
        echo $pid.'进程开启'.PHP_EOL;
    } else {
        while(1) {
            sleep(5);
            echo '进程:'.posix_getpid()."进程".$i."输出";
        }
    }
}