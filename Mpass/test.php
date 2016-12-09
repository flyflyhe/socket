<?php
require ('./Server.php');

class TestPserver implements Mpass_IExecutor {
	function execute(Mpass_Request $client) {

		$input = "";
		
		while ($buf = $client->read(1024)) {
			if (!$buf) {
				break;
			}
			var_dump($buf);
			$input .= trim($buf);
			var_dump(substr(trim($buf), -2));
			if (substr(trim($buf), -5) == "EXIT!") { #这里注意 “\n”换行符是一个字节长度
				$input = substr($input, 0, strlen($input) - 5);
                break;
            }
		}
		//$input = trim($client->read(1024)); #这里注意还是阻塞的问题 如果客户端一直没有法请求服务段就一直不能返回结果了
		//sleep(10);

		echo $input;
		$str = "Hello World! " . microtime(true)
			            . "<pre>{$input}</pre>";

		$response = "HTTP/1.1 200 OK\r\n"
			. "Connection: close\r\n"
			. "Content-Type: text/html\r\n"
			. "Content-Length:" . strlen($str) . "\r\n"
			. "\r\n"
			. $str;

		$client->write($response);
		return TRUE;
	}
}

$host = "127.0.0.1";
$port = 8992;

$service = new Mpass_Server($host, $port, new TestPserver);

$service->run();
