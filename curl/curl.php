<?php
class CurlMultiUtil 
{
    /**
    * 根据url,postData获取curl请求对象,这个比较简单,可以看官方文档
    */
    private static function getCurlObject($url,$postData=array(),$header=array()){
        $options = array();
        $url = trim($url);
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_TIMEOUT] = 3;
        $options[CURLOPT_RETURNTRANSFER] = true;
        foreach($header as $key=>$value){
            $options[$key] =$value;
        }
        if(!empty($postData) && is_array($postData)){
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($postData);
        }
        if(stripos($url,'https') === 0){
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }
        $ch = curl_init();
        curl_setopt_array($ch,$options);
        return $ch;
    }
    /**
     * [request description]
     * @param  [type] $chList
     * @return [type]
     */
    private static function request($chList){
        $downloader = curl_multi_init();
        // 将三个待请求对象放入下载器中
        foreach ($chList as $ch){
            curl_multi_add_handle($downloader,$ch);
        }
        $res = array();
        // 轮询
        do {
            while (($execrun = curl_multi_exec($downloader, $running)) == CURLM_CALL_MULTI_PERFORM);
            if ($execrun != CURLM_OK) {
                break;
            }
            // 一旦有一个请求完成，找出来，处理,因为curl底层是select，所以最大受限于1024
            while ($done = curl_multi_info_read($downloader)){
                // 从请求中获取信息、内容、错误
                // $info = curl_getinfo($done['handle']);
                $output = curl_multi_getcontent($done['handle']);
                // $error = curl_error($done['handle']);
                $res[] = $output;
                // 把请求已经完成了得 curl handle 删除
                curl_multi_remove_handle($downloader, $done['handle']);
            }
            // 当没有数据的时候进行堵塞，把 CPU 使用权交出来，避免上面 do 死循环空跑数据导致 CPU 100%
            if ($running) {
                $rel = curl_multi_select($downloader, 1);
                if($rel == -1){
                    usleep(1000);
                }
            }
            if($running == false){
                break;
            }
        }while(true);
        curl_multi_close($downloader);
        return $res;
    }
    /**
     * [get description]
     * @param  [type] $urlArr
     * @return [type]
     */
    public static function get($urlArr){
        $data = array();
        if (!empty($urlArr)) {
            $chList = array();
            foreach ($urlArr as $key => $url) {
                $chList[] = self::getCurlObject($url);
            }
            $data = self::request($chList);
        }
        return $data;
    }
}