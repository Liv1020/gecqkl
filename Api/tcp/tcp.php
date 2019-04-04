<?php
$host = '39.106.161.182';
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

    socket_close($socket);
?>