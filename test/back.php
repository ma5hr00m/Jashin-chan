<?php
$host = '0.0.0.0'; // 监听IP
$port = '9502'; // 监听端口
$null = NULL; // null值

// 创建WebSocket服务器
$server = new \WebSocket\Server($host, $port);

// 建立连接时触发
$server->on('open', function($socket) use(&$server){
    // 保存连接信息
    $server->connections[$socket->id] = $socket;
    echo "Client [{$socket->id}] connected.\n";
});

// 接收客户端消息时触发
$server->on('message', function($socket, $message) use(&$server){
    $message = trim($message);

    // 广播消息给所有客户端
    foreach($server->connections as $conn){
        if($conn->id != $socket->id){
            $conn->send("Client [{$socket->id}]: {$message}");
        }
    }
});

// 关闭连接时触发
$server->on('close', function($socket) use(&$server){
    // 删除连接信息
    unset($server->connections[$socket->id]);
    echo "Client [{$socket->id}] disconnected.\n";
});

// 启动服务器
echo "Server is starting...\n";
$server->run();
