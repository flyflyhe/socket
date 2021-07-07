<?php

$content = file_get_contents('/tmp/a.txt');

$uidDayMap = explode(PHP_EOL, $content);

shuffle($uidDayMap);

$a = $b = $uidDayMap;

$start = microtime();
asort($a);
$end = microtime();

echo '执行时间 sort ', $end - $start, PHP_EOL;

$start = microtime();
$minHeap = new SplMinHeap();
foreach ($uidDayMap as $uid => $day) {
    $minHeap->insert([$day, $uid]);
}
$end = microtime();

echo '执行时间 heap sort', $end - $start, PHP_EOL;