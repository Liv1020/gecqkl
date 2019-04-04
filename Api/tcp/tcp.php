<?php
/*$host = '39.106.161.182';
    $port = 3399;
    $timeout = 5;

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (socket_connect($socket, $host, $port) === false) { // 创建连接
        socket_close($socket);
        $message = 'create socket error';
        throw new Exception($message, socket_last_error());
    }   

    if (socket_write($socket, $buffer) === false) { // 发包
        socket_close($socket);
        $message = sprintf("write socket error:%s", socket_strerror(socket_last_error()));
        throw new Exception($message, socket_last_error());
    }   

    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);

    $rspBuffer = socket_read($socket, 65536); // 接收回包

    socket_close($socket);*/
	// Server
// 设置错误处理
error_reporting(E_ALL);
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
?>