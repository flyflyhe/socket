<?php

//判断第三个字符串 是否由前两个字符串交错而成且不改变 s1 和 s2中各个字符原有的相对顺序

function isInterleave(string $s1, string $s2, string $s3):bool
{
    $n  = strlen($s1);
    $m = strlen($s2);
    $s = strlen($s3);

    if ($n + $m != $s) {
        return false;
    }

    for ($i = 0; $i < $n + 1; $i++) {
        for ($j = 0; $j < $m + 1; $j++) {
            $dp[$i][$j] = false;
        }
    }

    $dp[0][0] = true;

    for ($i = 0; $i < $n + 1; $i++) {
        for ($j = 0; $j < $m + 1; $j++) {
            if ($dp[$i][$j]
                || ($i - 1 >= 0 && $dp[$i-1][$j] && $s1[$i-1]==$s3[$i+$j-1])
                || ($j-1>=0 && $dp[$i][$j-1] && $s2[$j-1]==$s3[$i+$j-1])
            ) {
                $dp[$i][$j] = true;
            } else {
                $dp[$i][$j] = false;
            }
        }
    }

    var_export($dp);
    return $dp[$n][$m];

}

isInterleave('aabcc', 'dbbca', 'aadbbcbcac');