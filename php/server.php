<?php
error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(0);
ob_implicit_flush();
//地址与接口，即创建socket时需要服务器的IP和端口  
$sk=new Sock('0.0.0.0',4000);//这里表示允许所有ip连接该websocket服务器</span>  
//对创建的socket循环进行监听，处理数据  
$sk->run();
//下面是sock类  
class Sock{
    public $sockets; //socket的连接池，即client连接进来的socket标志
    public $users; //所有client连接进来的信息，包括socket、client名字等
    public $master; //socket的resource，即前期初始化socket时返回的socket资源
    public $real_path;//上传文件时获取的文件路径
    private $sda=array(); //已接收的数据
    private $slen=array(); //数据总长度
    private $sjen=array(); //接收数据的长度
    private $ar=array(); //加密key
    private $n=array();

    public function __construct($address, $port)
    {
        //创建socket并把保存socket资源在$this->master
        $this->master=$this->WebSocket($address, $port);
        //创建socket连接池
        $this->sockets=array($this->master);
    }
    //对创建的socket循环进行监听，处理数据  
    function run()
    {
        $len_content = 0;
        //死循环，直到socket断开
        while(true){
            $changes=$this->sockets;
            $write=NULL;
            $except=NULL;

            socket_select ($changes, $write , $except , NULL);
            foreach($changes as $sock) {
                //如果有新的client连接进来，则
                if($sock==$this->master) {
                    //接受一个socket连接
                    $client=socket_accept($this->master);

                    //给新连接进来的socket一个唯一的ID
                    $key=uniqid();
                    $this->sockets[]=$client; //将新连接进来的socket存进连接池
                    $this->users[$key]=array(
                        'socket'=>$client, //记录新连接进来client的socket信息
                        'shou'=>false //标志该socket资源没有完成握手
                    );
                    //否则1.为client断开socket连接，2.client发送信息
                } else {

                    $len=0;
                    $buffer='';
                    //读取该socket的信息，注意：第二个参数是引用传参即接收数据，第三个参数是接收数据的长度
                    while(($leng = socket_recv($sock,$buf,1024,MSG_DONTWAIT)) > 0){
                        //此处需要注意MSG_DONTWAIT在部分windows版本的php中可能没有定义所以在接收数据时需要重新处理
                        $len += $leng;
                        // $buf1 = $buf;
                        $buffer.=$buf;
                        $buf = null;
                        //var_dump($leng);
                    }
                    //根据socket在user池里面查找相应的$k,即健ID
                    $k=$this->search($sock);

                    //如果接收的信息长度小于7，则该client的socket为断开连接
                    if ($len<7) {
                        //给该client的socket进行断开操作，并在$this->sockets和$this->users里面进行删除
                        $this->send2($k);
                        continue;
                    }
                    //判断该socket是否已经握手
                    if(!$this->users[$k]['shou'] ){
                        //如果没有握手，则进行握手处理
                        $this->woshou($k,$buffer);
                        $buf = null;
                    } else {
                        //走到这里就是该client发送信息了，对接受到的信息进行decode解码处理
                        //
                        $buffer = $this->decode($buffer);
                        if($buffer==false){
                            continue;
                        }
                        if ('send_type' == substr($buffer,0,9)){//表示发送的内容是字符串
                            $this->send($k,$buffer);
                            continue;
                        }
                        if (false !== strpos($buffer, 'filename=')) {
                            $len_content = 0;
                            parse_str($buffer,$msg);
                            //$filename = $this->saveFile(substr($buffer, 9));
                            $filename = $msg['filename'];
                            $fileNewPath = $this->saveFile($msg['filename']);
                            $fileNewName = basename($fileNewPath);
                            $filesize = $msg['filesize'];
                            $this->response($k,array('data' => 'ok', 'filename' =>$filename, 'fileNewName' => "$fileNewName"));
                        } else if (!empty($fileNewName)) {
                            $this->saveFileContent($fileNewPath, $buffer);
                            $len_content += strlen($buffer);
                            if($len_content >= $filesize){
                                $fileNewName = "";
                            }
                            $this->response($k, array('data' => 'ok', 'percent' =>floor($len_content/$filesize*100), 'recv'=> $len_content,'fileName' => $filename));
                        }
                    }
                }
            }
        }
    }
    //指定关闭$k对应的socket  
    function close($k){
        //断开相应socket  
        socket_close($this->users[$k]['socket']);
        //删除相应的user信息  
        unset($this->users[$k]);
        //重新定义sockets连接池  
        $this->sockets=array($this->master);
        foreach($this->users as $v){
            $this->sockets[]=$v['socket'];
        }
        //输出日志  
        $this->e("key:$k close");
    }
    //根据sock在users里面查找相应的$k  
    function search($sock){
        foreach ($this->users as $k=>$v){
            if($sock==$v['socket'])
                return $k;
        }
        return false;
    }
    //传相应的IP与端口进行创建socket操作  
    function WebSocket($address,$port){
        $commonProtocol = getprotobyname("tcp");
        //$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);  
        $server = socket_create(AF_INET, SOCK_STREAM,$commonProtocol);
        socket_set_option($server, SOL_SOCKET, SO_REUSEADDR,1);//1表示接受所有的数据包
        socket_bind($server, $address, $port);
        socket_listen($server);
        $this->e('Server Started : '.date('Y-m-d H:i:s'));
        $this->e('Listening on : '.$address.' port '.$port);
        return $server;
    }

    function woshou($k,$buffer){
        //截取Sec-WebSocket-Key的值并加密，其中$key后面的一部分258EAFA5-E914-47DA-95CA-C5AB0DC85B11字符串应该是固定的  
        $buf = substr($buffer,strpos($buffer,'Sec-WebSocket-Key:')+18);
        $key = trim(substr($buf,0,strpos($buf,"\r\n")));
        $new_key = base64_encode(sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11",true));
        //按照协议组合信息进行返回  
        $new_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $new_message .= "Upgrade: websocket\r\n";
        $new_message .= "Sec-WebSocket-Version: 13\r\n";
        $new_message .= "Connection: Upgrade\r\n";
        $new_message .= "Sec-WebSocket-Accept: " . $new_key ."\r\n\r\n";
        socket_write($this->users[$k]['socket'],$new_message,strlen($new_message));
        //对已经握手的client做标志  
        $this->users[$k]['shou']=true;
        return true;

    }
    //对websocket的封装帧解封装  
    function decode($buffer) {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;
        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        }
        else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        }
        else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }  
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }

    function frame($s){
        $a = str_split($s, 125);
        //添加头文件信息，不然前台无法接受  
        if (count($a) == 1){
            return "\x81" . chr(strlen($a[0])) . $a[0];
        }
        $ns = "";
        foreach ($a as $o){
            $ns .= "\x81" . chr(strlen($o)) . $o;
        }
        return $ns;
    }
    //用户加入或client发送信息
    function send($k,$msg){
        //将查询字符串解析到第二个参数变量中，以数组的形式保存如：parse_str("name=Bill&age=60",$arr)
        parse_str($msg,$g);
        $ar=array();
        if(isset($g['status'])&&isset($g['command'])){
            //第一次进入添加聊天名字，把姓名保存在相应的users里面
            //$this->users[$k]['status']=$g['status'];
            $ar['command']=$g['command'];
            $ar['status']=$g['status'];
            $ar['swdid']=$g['swdid'];
            //$key=$g['key'];
        }else if(isset($g['command'])){
            $ar['command']=$g['command'];
            $ar['swdid']=$g['swdid'];
            //$key=$g['key'];
        }
        //推送信息
        $this->send1($k,$ar);
    }

    //对新加入的client推送已经在线的client
    function getusers(){
        $ar=array();
        foreach($this->users as $k=>$v){
            $ar[]=array('code'=>$k,'name'=>$v['name']);
        }
        return $ar;
    }

    //发送文件消息时
    function response($k,$ar){
        //if($ar['real_path']){
        $str = $this->frame(json_encode($ar));
        $users=$this->users;
        //给自己发消息
        foreach($users as $k1 => $v){
            if($k1 == $k){
                socket_write($v['socket'],$str,strlen($str));
            }
        }
    }

    //$k 发信息人的socketID $key接受人的 socketID ，根据这个socketID可以查找相应的client进行消息推送，即指定client进行发送
    function send1($k,$ar){
        $ar['time']=date('m-d H:i:s');
        //对发送信息进行编码处理
        $str = $this->frame(json_encode($ar));
        $users=$this->users;

        //给除了自己以外的用户发消息
        foreach($users as $k1 => $v){
            if($k1 != $k){
                socket_write($v['socket'],$str,strlen($str));
            }
        }

    }

    //用户退出
    function send2($k){
        $this->close($k);
    }

    //记录日志
    function e($str){
        //$path=dirname(__FILE__).'/log.txt';
        $str=$str."\n";
        //error_log($str,3,$path);
        //编码处理
        echo iconv('utf-8','gbk//IGNORE',$str);
    }

    //生成唯一uuid文件名称
    function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars,0,8) . '-';
        $uuid .= substr($chars,8,4) . '-';
        $uuid .= substr($chars,12,4) . '-';
        $uuid .= substr($chars,16,4) . '-';
        $uuid .= substr($chars,20,12);
        return $prefix . $uuid;
    }
    //保存文件名到指定路径
    function saveFile($filename){
        if(!is_dir("/tmp/uploads/")){
            mkdir("/tmp/uploads/");
        }
        $update_path = '/tmp/uploads/';
        $exe = substr($filename, strrpos($filename, '.'));
        $exe = $exe == '.jpeg' ? '.jpg' : $exe;
        $fileNewName = $this->uuid() . $exe;
        $path = $update_path . $fileNewName;
        // echo "file path : {$path}\n";
        return $path ;
    }

    //保存文件内容
    function saveFileContent($path,$content){
        //$content =
        file_put_contents($path, $content,FILE_APPEND);
    }
}