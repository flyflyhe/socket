<?php

$list = [1, 3, 4, 8, 9, 19, 21, 21, 22, 22, 23, 28];

if (count($list) % 2 !== 0) {
    die('不是偶数数组');
}

$insertStart = (int)$argv[1];
$insertEnd = (int)$argv[2];

//找到大概插入位置
$needInsertStart = searchInsert($list, $insertStart);
$needInsertEnd = searchInsert($list, $insertEnd);

//所在新位置需 大于所有左侧数据
if ($needInsertStart > 0) {
    for ($i = $needInsertStart - 1; $i > 0; $i--){
        if ($list[$i] < $insertStart) {
            break;
        }
    }
    $needInsertStart = $i + 1;
}

//所在新位置需 小于所有右侧数据
if ($needInsertEnd < count($list) - 1) {
    for ($i = $needInsertEnd + 1; $i < count($list) - 1; $i++){
        if ($list[$i] > $insertEnd) {
            break;
        }
    }
    $needInsertEnd = $i - 1;
}


echo $needInsertStart, ',', $needInsertEnd,  PHP_EOL;

$sList = (array)array_slice($list, 0, $needInsertStart);

//如果插入位置的值 相等 则分割时不带相等值 防止重复
//$eList = array_slice($list, $list[$needInsertEnd] > $insertEnd ? $needInsertEnd  : $needInsertEnd + 1);
//$eList = array_slice($list, $list[$needInsertEnd] == $insertEnd ? $needInsertEnd + 1  : $needInsertEnd);
if ($list[$needInsertEnd] == $insertEnd) {
    $eList = array_slice($list, $needInsertEnd + 1);
} else {
    $eList = array_slice($list, $needInsertEnd);
}

if (count($sList) % 2 == 1) {
    array_push($sList, $insertStart - 1);
}

if (count($eList) % 2 == 1) {
    $eList = array_merge([$insertEnd + 1], $eList);
}

$list = array_values(array_merge($sList, [$insertStart, $insertEnd], $eList));

echo implode(',', $list);

function searchInsert(array $nums, int $target):int
{
    $n = count($nums);
    echo 'len', $n, PHP_EOL;
    if ($n === 0) return 0;
    if ($target < $nums[0]) return 0;
    if ($target > end($nums)) return $n;

    $l = 0;
    $r = $n - 1;
    while ($l < $r) {
        $mid = intval($l + floor(($r - $l) / 2));
        echo 'mid', $mid, PHP_EOL;
        if ($nums[$mid] === $target) return $mid;
        // 当中间元素严格小于目标元素时，肯定不是解
        if ($nums[$mid] < $target) {
            // 下一轮搜索区间是 [mid+1, right]
            $l = $mid + 1;
        } else {
            $r = $mid;
        }
    }

    return $l;
}