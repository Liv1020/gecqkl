<?php
<?php  
//确保在连接客户端时不会超时  
set_time_limit(0);  
//设置IP和端口号  
$address = "39.106.161.182";  
$port = 3399; 
/** 
 * 创建一个SOCKET  
 * AF_INET=是ipv4 如果用ipv6，则参数为 AF_INET6 
 * SOCK_STREAM为socket的tcp类型，如果是UDP则使用SOCK_DGRAM 
*/  
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() fail:" . socket_strerror(socket_last_error()) . "/n");  
//阻塞模式  
socket_set_block($sock) or die("socket_set_block() fail:" . socket_strerror(socket_last_error()) . "/n");  
//绑定到socket端口  
$result = socket_bind($sock, $address, $port) or die("socket_bind() fail:" . socket_strerror(socket_last_error()) . "/n");  
//开始监听  
$result = socket_listen($sock, 4) or die("socket_listen() fail:" . socket_strerror(socket_last_error()) . "/n");  
echo "OK\nBinding the socket on $address:$port ... ";  
echo "OK\nNow ready to accept connections.\nListening on the socket ... \n";  
do { // never stop the daemon  
    //它接收连接请求并调用一个子连接Socket来处理客户端和服务器间的信息  
    $msgsock = socket_accept($sock) or  die("socket_accept() failed: reason: " . socket_strerror(socket_last_error()) . "/n");  
    while(1){
		//读取客户端数据  
		echo "Read client data \n";  
		//socket_read函数会一直读取客户端数据,直到遇见\n,\t或者\0字符.PHP脚本把这写字符看做是输入的结束符.  
		$buf = socket_read($msgsock, 8192);  
		echo "Received msg: $buf   \n";
 
		if($buf == "bye"){
			//接收到结束消息，关闭连接，等待下一个连接
			socket_close($msgsock);
			continue;
		}
		  
		//数据传送 向客户端写入返回结果  
		$msg = "welcome \n";  
		socket_write($msgsock, $msg, strlen($msg)) or die("socket_write() failed: reason: " . socket_strerror(socket_last_error()) ."/n");  		
	}  
      
} while (true);  
socket_close($sock);  

	// Server
// 设置错误处理
/*error_reporting(E_ALL);
// 设置运行时间
set_time_limit(0);
// 起用缓冲
ob_implicit_flush();
$ip = "39.106.161.182"; // IP地址
$port = 3399; // 端口号
 
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); // 创建一个SOCKET
if ($socket)
    echo "socket_create() successed!\n";
else
    echo "socket_create() failed:".socket_strerror($socket)."\n";
 
$bind = socket_bind($socket, $ip, $port); // 绑定一个SOCKET
if ($bind)
    echo "socket_bind() successed!\n";
else
    echo "socket_bind() failed:".socket_strerror($bind)."\n";
 
$listen = socket_listen($socket); // 间听SOCKET
if ($listen)
    echo "socket_listen() successed!\n";
else
    echo "socket_listen() failed:".socket_strerror($listen)."\n";
 
while (true) {
    $msg = socket_accept($socket); // 接受一个SOCKET
    if (!$msg) {
        echo "socket_accept() failed:".socket_strerror($msg)."\n";
        break;
    }
    $welcome = "Welcome to PHP Server!\n";
    socket_write($msg, $welcome, strlen($welcome));
    while (true) {
        $command = strtoupper(trim(socket_read($msg, 1024)));
        if (!$command)
            break;
        switch ($command) {
            case "HELLO":
                $writer = "Hello Everybody!";
                break;
            case "QUIT":
                $writer = "Bye-Bye";
                break;
            case "HELP":
                $writer = "HELLO\tQUIT\tHELP";
                break;
            default:
                $writer = "Error Command!";
        }
        socket_write($msg, $writer, strlen($writer));
        if ($command == "QUIT")
            break;
    }
    socket_close($msg);
}
socket_close($socket); // 关闭SOCKET
*/
?>