<?php
class ClientSocket
{
    protected $strHost = "127.0.0.1";
    protected $nPort = 2015;
    protected $nProtocol = SOL_TCP;
    private $pSocket = null;
    public $strErrorCode = "";
    public $strErrorMsg = "";

    public function __construct($strHost = "127.0.0.1", $port = 20, $protocol = SOL_TCP)
    {
        $this->strHost = $strHost;
        $this->nPort = $port;
        $this->nProtocol = $protocol;
        if ($this->create()) {
            $this->connect();
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

    protected function connect()
    {
        $pSocket = $this->create();
        $bRes = socket_connect($pSocket, $this->strHost, $this->nPort);
        if (!$bRes) {
            $this->log();
        }
        return $bRes;
    }

    public function read()
    {
        $strMessage = "";
        $strBuffer = "";
        while ($strBuffer = socket_read($this->pSocket, 1024, PHP_BINARY_READ)) {
            $strMessage .= $strBuffer;
            #如果本次读取的长度小于1024则跳出
            if (1024 != mb_strlen($mxMessage)) {
                break;
            }
        }
        return $strMessage;
    }

    public function write($msg)
    {
        $bRes = socket_write($this->pSocket, $msg, mb_strlen($msg));
        if (!$bRes) {
            $this->log();
        }
        return true;
    }

    public function send($msg)
    {
        $bRes = socket_send($this->pSocket, $msg, mb_strlen($msg));
        if (!$bRes) {
            $this->log();
        }
        return true;
    }

    public function recv()
    {
        $strMeg = "";
        $strBuffer = "";
        $bRes = socket_recv($this->pSocket, $strBuffer, 1024, MSG_WAITALL);
        if (!$bRes) {
            $this->log();
        }
        $strMeg .= $strBuffer;
        return $strMeg;
    }

    private function log()
    {
        $this->strErrorCode = socket_last_error();
        $this->strErrorMsg = socket_strerror($this->strErrorCode);
    }

    public function close()
    {
        socket_close($this->pSocket);
    }
    public function __destory()
    {
        if ($this->pSocket) {
            $this->close();
        }
    }
}

$strHost = "127.0.0.1";
$port = 25003;
$pClient = new ClientSocket($strHost, $port);

var_dump($pClient->read());
$strMsg = base64_encode('你是一只狗哈哈哈啊:'.uniqid());
var_dump($strMsg);
$pClient->write($strMsg);
var_dump(base64_decode($pClient->read()));

$pClient->close();