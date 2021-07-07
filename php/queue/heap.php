<?php


$heap = new SplMinHeap();
$heap->insert([22, 333]);
$heap->insert([22, 33]);
$heap->insert([222, 3]);

//var_export($heap->extract());
//var_export($heap->extract());
//var_export($heap->extract());

$heap->rewind();
foreach ($heap as $v) {
    var_dump($v);
}