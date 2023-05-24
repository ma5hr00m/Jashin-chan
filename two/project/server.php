<?php 
new WebSocket();

class WebSocket {
    // 定义成员属性
    protected $socket;
    protected $user = [];
    protected $socket_list = [];

    // 对象初始化
    public function __construct()
    {
        // 创建socket对象 | 设置socket选项 | 绑定监听端口 | 开始监听客户端连接
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, true);
        socket_bind($this->socket, 0, 4010);
        socket_listen($this->socket);

        // 保存已连接用户
        $this->socket_list[] = $this->socket;

        // 循环接收消息
        while (true) {
            // 获取可读的socket连接
            $tmp_sockets = $this->socket_list;
            socket_select($tmp_sockets, $write, $except, null);

            // 遍历连接并根据不同情况作出处理
            foreach ($tmp_sockets as $sock) {
                // 若有新连接则加入socket_listp[]并将用户信息保存至user[]数组
                if ($sock == $this->socket) {
                    $conn_sock = socket_accept($sock);
                    $this->socket_list[] = $conn_sock;
                    $this->user[] = ['socket' => $conn_sock, 'handshake' => false, 'name' => 'noname'];
                } else {
                    // 获取请求 | 获取连接索引
                    $request = socket_read($sock, 1024000);
                    $k = $this->getUserIndex($sock);

                    // 空数据，跳过
                    if (!$request) {
                        continue;
                    }

                    // 关闭连接 | 释放资源
                    if((\ord($request[0]) & 0xf) == 0x8) {
                        $this->close($k);
                        continue;
                    }

                    // 判断连接是否完成握手
                    if(!$this->user[$k]['handshake']) {
                        $this->handshake($k, $request);
                    } else {
                        $this->send($k, $request);
                    }
                }
            }
        }
    }

    // 关闭指定连接
    protected function close($k)
    {
        $u_name = $this->user[$k]['name'] ?? 'noname';
        socket_close($this->user[$k]['socket']);
        $socket_key = array_search($this->user[$k]['socket'], $this->socket_list);
        unset($this->socket_list[$socket_key]);
        unset($this->user[$k]);

        $user = [];
        foreach ($this->user as $v) {
            $user[] = $v['name'];
        }

        // 向所有用户发送close消息
        $res = [
            'type' => 'close',
            'users' => $user,
            'msg' => $u_name . ' disconnected',
            'time' => date('H:i:s')
        ];
        $this->sendAllUser($res);
    }

    // 获取指定用户在user[]中的索引
    protected function getUserIndex($socket)
    {
        foreach ($this->user as $k => $v) {
            if ($v['socket'] == $socket) {
                return $k;
            }
        }
    }

    // 握手协议，建立websocket连接
    protected function handshake($k, $request)
    {
        preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $request, $match);
        $key = base64_encode(sha1($match[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

        $response = "HTTP/1.1 101 Switching Protocols\r\n";
        $response .= "Upgrade: websocket\r\n";
        $response .= "Connection: Upgrade\r\n";
        $response .= "Sec-WebSocket-Accept: {$key}\r\n\r\n";
        socket_write($this->user[$k]['socket'], $response);
        $this->user[$k]['handshake'] = true;
    }

    // 处理客户端消息
    public function send($k, $msg)
    {
        // 解码 | 转化为json格式
        $msg = json_decode( $this->decode($msg), true);

        // 若不是指定类型消息，直接跳出函数
        if (!isset($msg['type'])) {
            return;
        }

        // 处理不同类型的消息
        switch ($msg['type']) {
            // 用户登陆
            case 'login':
                $this->user[$k]['name'] = $msg['name'] ?? 'anonymous';
                $users = [];
                foreach ($this->user as $v) {
                    $users[] = $v['name'];
                }
                $res = [
                    'type' => 'login',
                    'name' => $this->user[$k]['name'],
                    'msg' => $this->user[$k]['name'] . ' connected',
                    'users' => $users,
                ];
                $this->sendAllUser($res);
                break;

            // 文本消息
            case 'message':
                $res = [
                    'type' => 'message',
                    'name' => $this->user[$k]['name'] ?? 'anonymous',
                    'msg' => $msg['msg'],
                    'time' => date('H:i:s'),
                ];
                $this->sendAllUser($res);
                break;

            // 图像消息
            case 'image':
                $res = [
                    'type' => 'image',
                    'name' => $this->user[$k]['name'] ?? 'anonymous',
                    'file' => $msg['filename'],
                    'path' => 'http://localhost:4000/upload/' . $msg['filename'],
                    'time' => date('H:i:s'),
                ];
                $this->sendAllUser($res);
                break;

            // 文件消息
            case 'file':
                $res = [
                    'type' => 'file',
                    'name' => $this->user[$k]['name'] ?? '无名氏',
                    'file' => $msg['filename'],
                    'path' => 'http://localhost:4000/upload/' . $msg['filename'],
                    'time' => date('H:i:s'),
                ];
                $this->sendAllUser($res);
                break;
        }
    }

    // 将指定消息发送给所有用户
    protected function sendAllUser($msg)
    {
        // 格式化为json | 编码
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }
        $msg = $this->encode($msg);

        foreach ($this->user as $k => $v) {
            socket_write($v['socket'], $msg, strlen($msg));
        }
    }

    // 解码 | 编码
    protected function decode($buffer)
    {
        $len = \ord($buffer[1]) & 127;
        if ($len === 126) {
            $masks = \substr($buffer, 4, 4);
            $data = \substr($buffer, 8);
        } else {
            if ($len === 127) {
                $masks = \substr($buffer, 10, 4);
                $data = \substr($buffer, 14);
            } else {
                $masks = \substr($buffer, 2, 4);
                $data = \substr($buffer, 6);
            }
        }
        $dataLength = \strlen($data);
        $masks = \str_repeat($masks, \floor($dataLength / 4)) . \substr($masks, 0, $dataLength % 4);
        return $data ^ $masks;
    }

    protected function encode($buffer)
    {
        if (!is_scalar($buffer)) {
            throw new \Exception("You can't send(" . \gettype($buffer) . ") to client, you need to convert it to a string. ");
        }
        $len = \strlen($buffer);
        $first_byte = "\x81";
        if ($len <= 125) {
            $encode_buffer = $first_byte . \chr($len) . $buffer;
        } else {
            if ($len <= 65535) {
                $encode_buffer = $first_byte . \chr(126) . \pack("n", $len) . $buffer;
            } else {
                $encode_buffer = $first_byte . \chr(127) . \pack("xxxxN", $len) . $buffer;
            }
        }

        return $encode_buffer;
    }
}