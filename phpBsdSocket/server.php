<?php
class ServerSocket
{
    protected $strHost = "127.0.0.1";
    protected $nPort = 2015;
    protected $nProtocol = SOL_TCP;
    protected $pSocket = null;
    protected $pClient = null;

    public $strErrorCode = "";
    public $strErrorMsg = "";
    public function __construct($strHost = "127.0.0.1", $port = 20, $protocol = SOL_TCP)
    {
        $this->pProtocol = $strHost;
        $this->nPort = $port;
        $this->nProtocol = $protocol;
        if ($this->create() && $this->bind()) {
            $this->listen();
        }
    }

    protected function create()
    {
        $this->pSocket = socket_create(AF_INET, SOCK_STREAM, $this->nProtocol);
        if (!$this->pSocket) {
            $this->log();
        }
        return $this->pSocket;
    }

    protected function bind()
    {
        $bRes = socket_bind($this->pSocket, $this->strHost, $this->nPort);
        if (!$bRes) {
            $this->log();
        }
        return $bRes;
    }

    protected function listen()
    {
        $bRes = socket_listen($this->pSocket, 10);
        if (!$bRes) {
            $this->log();
        }
        return $bRes;
    }

    protected function accept()
    {
        $this->pClient = socket_accept($this->pSocket);
        if (!$this->pClient) {
            $this->log();
        }
        return $this->pClient;
    }

    protected function connect()
    {
        $this->accept();
        if (socket_getpeername($this->pClient, $address, $port)) {
            echo "Client $address : $port is now connected to us. \n";
        }
        $this->write("hello world from server\n");
    }

    protected function reply()
    {
        echo 'reply调用'.PHP_EOL;
        $mxData = $this->read();
        var_dump($mxData);
        if ($mxData == false) {
            socket_close($this->pClient);
            echo "client disconnected.\n";
            return false;
        } else {
            $strMessage = "Client: ".trim($mxData)."\n";
            $this->write($strMessage);
            return true;
        }
    }

    public function run()
    {
        $this->connect();
        $this->reply();
    }

    public function read()
    {
        echo 'read调用'.PHP_EOL;
        $out = '';
        while ($mxMessage = socket_read($this->pClient, 1024, PHP_BINARY_READ)) {  #注意 主要是这里 这里用的是默认的 type 这里要读取1024字节 如果客户端没有发送这么多数据就会造成子连接的阻塞 导致程序无法继续向下执行 所以要执行一些判断

            $out .= $mxMessage;
            
            #如果本次读取的长度小于1024则跳出
            if (1024 != mb_strlen($mxMessage)) {
                break;
            }
        }    
        if (!$out && $mxMessage === false) {
            $this->log();
        }
        return base64_decode($out);
    }

    public function write($msg)
    {
        #使用base64_encode编码
        $msg = base64_encode($msg);
        $bRes = socket_write($this->pClient, $msg, mb_strlen($msg));
        if (!$bRes) {
            $this->log();
        }
        return $bRes;
    }

    public function close()
    {
        $bRes = socket_close($this->pSocket);
        $this->pSocket = null;
    }

    protected function log()
    {
        $this->strErrorCode = socket_last_error();
        $this->strErrorMsg = socket_strerror($this->strErrorCode);
    }

    public function __destruct()
    {
        if ($this->pSocket) {
            $this->close();
        }
    }
}
// $strHost     = "127.0.0.1";
// $nPort       = 25003;
// $pServer = new ServerSocket($strHost, $nPort);
// $pServer->run();