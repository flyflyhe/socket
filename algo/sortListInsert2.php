<?php

$list = [1, 3, 4, 8, 9, 19, 20, 21, 22, 28,];

$insertStart = (int)$argv[1];

var_dump(searchInsert($list, $insertStart));

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