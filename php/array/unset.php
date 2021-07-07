<?php

$arr = [1,2,3];
print_r($arr);
echo "count:", count($arr), PHP_EOL;
unset($arr[1]);
print_r($arr);
echo "count:", count($arr), PHP_EOL;