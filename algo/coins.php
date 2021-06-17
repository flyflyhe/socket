<?php

$coins = [1, 2, 5, 10];
$amount = 100;

function coinChange(array $coins, int $amount)
{
    $max = $amount + 1;
    $dp = array_fill(0, $max, $max);
    $dp[0] = 0;
    for ($i = 1; $i <= $amount; $i++) {
        for ($j = 0; $j < count($coins); $j++) {
            if ($coins[$j] <= $i) {
                $dp[$i] = min($dp[$i], $dp[$i - $coins[$j]] + 1);
            }
        }
    }
    return $dp[$amount] > $amount ? -1 : $dp[$amount];
}

var_dump(coinChange($coins, $amount));

/**
 * @param array $coins
 * @param int $amount
 * @return int|mixed
 * // 完全背包问题思路二伪代码(空间优化版)
 * dp[0,...,W] = 0
 * for i = 1,...,N
 * for j = W,...,w[i] //
 * for k = [0, 1,..., min(n[i], j/w[i])]
 * dp[j] = max(dp[j], dp[j−k*w[i]]+k*v[i])
 */

function maxCombination(int $amount)
{
    $money = [1, 2, 5, 10];
    $dp = array_fill(0, $amount + 1, 0);
    $dp[0] = 1;
    for ($i = 0; $i < 4; ++$i) {
        for ($j = $money[$i]; $j <= $amount; ++$j) {
            $dp[$j] = ($dp[$j] + $dp[$j - $money[$i]]);
        }
    }

    return $dp[$amount];
}

var_dump(maxCombination(100));