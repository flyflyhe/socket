<?php

/**
 *打开流替换掉dirty字段
 */
class DirtyWordsFilter extends php_user_filter
{
    public function filter ($in, $out, &$consumed, $closing)
    {
        $words = ['操', '日狗', '日你'];
        $wordData = [];
        foreach ($words as $word) {
            $replacement = array_fill(0, mb_strlen($word), "*");
            $wordData[$word] = implode("", $replacement);
            //$wordData[$word] = str_repeat("*", mb_strlen($word));
        }
        $bad = array_keys($wordData);
        $good = array_values($wordData);
        
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = str_replace($bad, $good, $bucket->data);

            $consumed += $bucket->datalen;

            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}

stream_filter_register('dirty_word_filter', 'DirtyWordsFilter');
$handle = fopen('d:\\test.txt', 'rb');
if (!$handle) {
    die('文件不存在');
}
stream_filter_append($handle, 'dirty_word_filter');
while(feof($handle) !== true) {
    echo fgets($handle);
}
fclose($handle);