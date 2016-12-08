<?php

#资源引用可以传递给函数么？
function res($fb)
{
    $msg = is_resource($fb) ? 'is res' : 'not res';
    var_dump(fgets($fb));
    return $msg;
}
$fb = fopen("d:\\test.txt", "r");

echo res($fb);

fclose($fb);