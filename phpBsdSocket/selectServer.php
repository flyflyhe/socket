<?php
include __DIR__."/server.php";
#循环监听客户端
class SelectServerSocket extends ServerSocket
{
    public function run()
    {
        $this->loop();
    }

    protected function reply()
    {
        echo 'reply调用'.PHP_EOL;
        $mxData = $this->read();
        if (!$mxData) {
            return false;
        }
        var_dump($mxData);
        $strMessage = "Client: ".trim($mxData)."\n";
        $this->write($strMessage);
    }
    
    protected function read($cSocket)
    {
        if (!is_resource($cSocket)) {
            return false;
        }
        $strMessage = "";
        $strBuffer = "";
        while ($strBuffer = socket_read($cSocket, 1024, PHP_BINARY_READ)) {
            $strMessage .= $strBuffer;
            #如果本次读取的长度小于1024则跳出
            if (1024 != mb_strlen($mxMessage)) {
                break;
            }
        }
        return base64_decode($strMessage);
    }

    public function loop()
    {
        $arrRead = [];
        $arrWrite = $arrExp = null;
        $key = uniqid();
        $arrClient[$key] = $this->pSocket;

        while(true) {
            $arrRead = $arrClient;
            var_dump($arrRead);
            if (socket_select($arrRead, $arrWrite, $arrExp, null) < 1) {
                continue;
            }
            foreach ($arrRead as $pSocket) {
                if ($pSocket === $this->pSocket) {
                    $this->connect();
                    $key=uniqid();
                    $arrClient[$key] = $this->pClient;
                } else {
                    $bRes = $this->reply();
                    echo 1111;
                    if ($bRes === false) {
                        $nKey = array_search($pSocket, $arrClient, true);
                        echo $nKey;
                        $this->close($arrClient[$nKey]);
                        unset($arrClient[$nKey]);
                        continue;
                    }
                }
            }
        }
    }
}
$strHost     = "127.0.0.1";
$nPort       = 25003;
$pServer = new SelectServerSocket($strHost, $nPort);
$pServer->run();