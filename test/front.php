<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WebSocket Chatroom</title>
    <script type="text/javascript">
        var wsUri = "ws://localhost:9502"; // 服务器地址
        var websocket = new WebSocket(wsUri); // 创建WebSocket连接

        // 向服务器发送消息
        function send(){
            var message = document.getElementById('message').value;
            websocket.send(message);
        }

        // 接收服务器端的消息
        websocket.onmessage = function(event){
            var message = event.data;
            var chat = document.getElementById('chat');
            var html = chat.innerHTML;
            chat.innerHTML = html + '\n' + message;
        }
    </script>
</head>
<body>
<textarea id="chat" cols="60" rows="20"></textarea><br />
<input type="text" id="message" />
<input type="button" value="Send" onclick="send()" />
</body>
</html>
