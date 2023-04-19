<?php 
new WebSocket();

class WebSocket
{
    protected $socket;
    protected $user = [];
    protected $socket_list = [];

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, true);
        socket_bind($this->socket, 0, 8880);
        socket_listen($this->socket);

        $this->socket_list[] = $this->socket;

        while (true) {
            $tmp_sockets = $this->socket_list;
            socket_select($tmp_sockets, $write, $except, null);

            foreach ($tmp_sockets as $sock) {
                if ($sock == $this->socket) {
                    $conn_sock = socket_accept($sock);
                    $this->socket_list[] = $conn_sock;
                    $this->user[] = ['socket' => $conn_sock, 'handshake' => false, 'name' => 'noname'];
                } else {
                    $request = socket_read($sock, 1024000);
                    $k = $this->getUserIndex($sock);

                    if (!$request) {
                        continue;
                    }

                    if((\ord($request[0]) & 0xf) == 0x8) {
                        $this->close($k);
                        continue;
                    }

                    if(!$this->user[$k]['handshake']) {
                        $this->handshake($k, $request);
                    } else {
                        $this->send($k, $request);
                    }
                }
            }
        }
    }


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
        $res = [
            'type' => 'close',
            'users' => $user,
            'msg' => $u_name . 'out of line',
            'time' => date('Y-m-d H:i:s')
        ];
        $this->sendAllUser($res);
    }


    protected function getUserIndex($socket)
    {
        foreach ($this->user as $k => $v) {
            if ($v['socket'] == $socket) {
                return $k;
            }
        }
    }


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


    public function send($k, $msg)
    {
        $msg = $this->decode($msg);
        $msg = json_decode($msg, true);

        if (!isset($msg['type'])) {
            return;
        }

        switch ($msg['type']) {
            case 'login':
                $this->user[$k]['name'] = $msg['name'] ?? 'noname';
                $users = [];
                foreach ($this->user as $v) {
                    $users[] = $v['name'];
                }
                $res = [
                    'type' => 'login',
                    'name' => $this->user[$k]['name'],
                    'msg' => $this->user[$k]['name'] . ': login success',
                    'users' => $users,
                ];
                $this->sendAllUser($res);
                break;
            case 'message':
                $res = [
                    'type' => 'message',
                    'name' => $this->user[$k]['name'] ?? '无名氏',
                    'msg' => $msg['msg'],
                    'time' => date('H:i:s'),
                ];
                $this->sendAllUser($res);
                break;
            case 'image':
                $res = [
                    'type' => 'image',
                    'name' => $this->user[$k]['name'] ?? '无名氏',
                    'file' => $msg['filename'],
                    'path' => 'http://localhost:8888/upload/' . $msg['filename'],
                    'time' => date('H:i:s'),
                ];
                $this->sendAllUser($res);
                break;
            case 'file':
                $res = [
                    'type' => 'file',
                    'name' => $this->user[$k]['name'] ?? '无名氏',
                    'file' => $msg['filename'],
                    'path' => 'http://localhost:8888/upload/' . $msg['filename'],
                    'time' => date('H:i:s'),
                ];
                $this->sendAllUser($res);
                break;

        }
    }


    protected function sendAllUser($msg)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }

        $msg = $this->encode($msg);

        foreach ($this->user as $k => $v) {
            socket_write($v['socket'], $msg, strlen($msg));
        }
    }


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