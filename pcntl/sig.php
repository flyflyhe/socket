<?php
declare(ticks = 1);
 
pcntl_signal(SIGCHLD, "signal_handler");
 
function signal_handler($signal) {
    switch($signal) {
        case SIGCHLD:
            while (pcntl_waitpid(0, $status) != -1) {
                $status = pcntl_wexitstatus($status);
                echo "Child $status completed\n";
            }
            exit;
    }
}
 
for ($i = 1; $i <= 5; ++$i) {
    $pid = pcntl_fork();
 
    if (0 == $pid) {
        sleep(1);
        print "In child $i\n";
        exit($i);
    }
}
 
while(1) {
    // parent does processing here...
}