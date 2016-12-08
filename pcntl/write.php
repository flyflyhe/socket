<?php
for ($i = 1; $i <= 5; ++$i) { 
    $pid = pcntl_fork(); 

    if ($pid == -1) {
        
    } else if ($pid) {
        echo $pid.'进程开启'.PHP_EOL;
    } else {
        while(1) {
            sleep(5);
            echo "进程".$i."输出";
        }
    }
}