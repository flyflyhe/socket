<?php
declare(ticks = 1);

$childArr = [];
pcntl_signal(SIGCHLD, function ($signal) use (&$childArr) {
    switch($signal) {
        case SIGCHLD:
            // while (pcntl_waitpid(0, $status) != -1) {
            //     $status = pcntl_wexitstatus($status);
            //     echo "Child $status completed\n";
            // }
            $child = pcntl_wait($status);
            unset($childArr[$child]);
            echo "Child $child completed\n";
    }
});
 
for ($i = 1; $i <= 5; ++$i) {
    $pid = pcntl_fork();
 
    if (0 == $pid) {
        sleep(1);
        print "In child $i\n";
        exit($i);
    } else if ($pid > 0) {
        $childArr[$pid] = 1;
    }
}

while(count($childArr)) {
   var_export($childArr);
   sleep(1);
}
exit();