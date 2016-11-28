<?php
for ($i = 1; $i <= 5; ++$i) { 
    $pid = pcntl_fork(); 

    if ($pid == -1) {
        
    } else if ($pid) {
        echo $pid.'进程开启'.PHP_EOL;
    } else {
        while(1) {
            sleep(5);
            file_put_contents('/tmp/test'.$pid.'log', date('Y-m-d H:i:s'), FILE_APPEND);
        }
    }
}