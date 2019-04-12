<?php
/**
 * @description HttpServer类
 * @author luoluolzb <luoluolzb@163.com>
 * @date   2018/3/2
 */
class HttpServer
{
    protected $port;
    protected $address;
    protected $socket;

    /**
     * 构造函数
     * @param string  $address 监听地址
     * @param integer $port    监听端口
     */
    public function __construct($address = 'localhost', $port = 80)
    {
        $this->port = $port;
        $this->address = $address;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (! $this->socket) {
            throw new Exception("Http Server create failed!");
        }
        //绑定地址和端口
        socket_bind($this->socket, $address, $this->port);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        socket_close($this->socket);
    }

    /**
     * 开始运行http服务器
     */
    public function run()
    {
        //开始进行监听
        socket_listen($this->socket);
        while (true) {
            //获取请求socket
            $msg_socket = socket_accept($this->socket);
            //获取请求内容
            $buf = socket_read($msg_socket, 99999);
            echo $buf;  //输出请求内容
            //写入相应内容（输出"Hello World!"）
            socket_write($msg_socket, $this->text("Hello World!"));
            //关闭请求socket
            socket_close($msg_socket);
        }
    }

    /**
     * 获取http协议的文本内容
     * @param  string $content string
     * @return string
     */
    private function text($content)
    {
        //协议头
        $text = 'HTTP/1.0 200 OK' . "\r\n";
        $text .= 'Content-Type: text/plain' . "\r\n";
        $text .= 'Content-Length: ' . strlen($content) . "\r\n";

        //以空行分隔
        $text .= "\r\n";

        //协议正文
        $text .= $content;
        return $text;
    }
}

//测试
$server = new HttpServer();
echo "Server running at http://localhost\r\n";
$server->run();