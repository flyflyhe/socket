<?php

$o = $list = [1, 3, 4, 8, 9, 19, 21, 21, 22, 22, 23, 28];

if (count($list) % 2 !== 0) {
    die('不是偶数数组');
}

$insertStart = (int)$argv[1];
$insertEnd = (int)$argv[2];

if ($insertStart > $list[count($list)-1]) {
    echo implode(',', array_merge($list, [$insertStart, $insertEnd]));
    exit(0);
}

if ($insertEnd < $list[0]) {
    echo implode(',', array_merge([$insertStart, $insertEnd], $list));
    exit(0);
}

//找到大概插入位置
$needInsertStart = searchInsert($list, $insertStart);

//所在新位置需 大于所有左侧
if ($needInsertStart > 0) {
    for ($i = $needInsertStart; $i > 0; $i--){
        if ($list[$i] < $insertStart) {
            break;
        }
    }
    $needInsertStart = $i + 1;
}

//插入左侧数据
$list = array_merge(array_slice($list, 0, $needInsertStart), [$insertStart], array_slice($list, $needInsertStart));

$needInsertEnd = searchInsert($list, $insertEnd);

//所在新位置需 小于所有右侧数据
if ($needInsertEnd < count($list) - 1) {
    for ($i = $needInsertEnd; $i < count($list) - 1; $i++){
        if ($list[$i] > $insertEnd) {
            echo $i, PHP_EOL;
            break;
        }
    }
    $needInsertEnd = $i;
}

//插入右侧数据
$list = array_merge(array_slice($list, 0, $needInsertEnd), [$needInsertEnd], array_slice($list, $needInsertEnd));

echo $needInsertStart, ',', $needInsertEnd,  PHP_EOL;

//删除左侧右侧(包括左右侧)之间的数据
$sList = (array)array_slice($list, 0, $needInsertStart);
$eList = array_slice($list, $needInsertEnd + 1);

//构建新数组
if (count($sList) % 2 == 1) {
    array_push($sList, $insertStart - 1);
}

if (count($eList) % 2 == 1) {
    $eList = array_merge([$insertEnd + 1], $eList);
}

$list = array_values(array_merge($sList, [$insertStart, $insertEnd],$eList));

echo implode(',', $o), PHP_EOL;
echo implode(',', $list);

function searchInsert(array $nums, int $target):int
{
    $n = count($nums);
    if ($n === 0) return 0;
    if ($target < $nums[0]) return 0;
    if ($target > end($nums)) return $n;

    $l = 0;
    $r = $n - 1;
    while ($l < $r) {
        $mid = intval($l + floor(($r - $l) / 2));
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