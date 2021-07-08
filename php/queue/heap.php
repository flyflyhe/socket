<?php


$heap = new SplMinHeap();
$heap->insert(array_reverse(['a', 333]));
$heap->insert(array_reverse(['b', 33]));
$heap->insert(array_reverse(['c', 3]));
$heap->insert(array_reverse(['d', 35]));
$heap->insert(array_reverse(['e', 3911]));
$heap->insert(array_reverse(['f', 234545]));
$heap->insert(array_reverse(['g', 21]));
$heap->insert(array_reverse(['h', 343]));
$heap->insert(array_reverse(['i', 9]));
$heap->insert(array_reverse(['j', 7]));
$heap->insert(array_reverse(['k', 1]));
$heap->insert(array_reverse(['l', 0]));

//var_export($heap->extract());
//var_export($heap->extract());
//var_export($heap->extract());

$heap->rewind();
foreach ($heap as $v) {
    echo $v[0], $v[1], PHP_EOL;
}