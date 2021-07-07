<?php

$arr = [1,2,3];
print_r($arr);
echo "count:", count($arr), PHP_EOL;
unset($arr[1]);
print_r($arr);
echo "count:", count($arr), PHP_EOL;

$list = new SplFixedArray(3);
$list[0] = 1;
$list[1] = 2;
$list[2] = 3;
print_r($list);
$list->offsetUnset(1);
print_r($list);