<?php
session_start();
$username = $_GET['username'];

/*
if (isset($_SESSION['username']) && isset($_SESSION['username']) && $_SESSION['username'] === $_GET['username'] && $_SESSION['loggedin'] == true) {
} else {
    // 清除session
    session_unset();
    session_destroy();
    // 跳转登录页面
    header('Location: /login.php');
    exit;
}
*/
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link type="text/css" rel="stylesheet" href="./src/css/index.css">
        <title>ChatRoom</title>
    </head>
    <body>
        <div id="wrapper">
            <aside>
                <div class=user-title>
                    <h2>Online Users</h2>
                </div>
                <div id="user-box">
                    <p class="user-dock"></p>
                </div>
            </aside>
            <main>
                <div id="message-box"></div>
                <div id="command-box">
                    <form id="uploadForm" method="post" enctype="multipart/form-data" onclick="document.getElementById('file').click();">
                        <input type="file" id="file" name="file" onchange="uploadFile()">
                        <img id="upload-image" src="./src/image/upload.svg">
                    </form>
                    <textarea id="content"></textarea>
                    <button id="send" class="func-button" onclick="send()">
                        <img id="send-image" src="./src/image/send.svg">
                    </button>
                </div>
            </main>
        </div>
    </body>
</html>

<script src="https://cdn.bootcss.com/jquery/2.2.1/jquery.min.js"></script>
<script type="text/javascript">

    // 连接 websocket
    let ws = new WebSocket('ws://localhost:8880');

    const params = new URLSearchParams(window.location.search);
    const username = params.get('username');
    const now = new Date();

    // 发送login信息
    ws.onopen = function (event) {
        ws.send(JSON.stringify({
            type: 'login',
            name: username,
        }))
    }

    //接受信息
    ws.onmessage = function (event) {
        let data = JSON.parse(event.data);

        // 根据信息分类添加元素
        switch (data.type) {
            case 'close':
            case 'login':
                $('#user-box').html('');
                data.users.forEach(function (item) {
                    $('#user-box').append(`<p class="user-dock"><span class="user-text">${item}</span></p>`);});
                if (data.msg) {
                    $('#message-box').append(`<span class="tip-dock" style="color: grey;width: 100%; display: flex;">${data.msg}</span>`);
                }
                break;
            case 'message':
                $('#message-box').append(`<p class="message-dock">
                        <span class="message-user" style="color: grey;">${data.name}</span>
                        <span class="message-time" style="color: red;">${data.time}</span>
                        <span class="message-content">${data.msg}</span>
                    </p>`);
                break;
            case 'image':
                $('#message-box').append(`<p><span style="color: grey;">${data.name}</span><span style="color: red;">${data.time}</span><a href="${data.path}" download="${data.file}"><img src="${data.path}"></a></p>`);
                break;
            case 'file':
                $('#message-box').append(`<p><span style="color: grey;">${data.name}</span><span style="color: red;">${data.time}</span><a href="${data.path}" download="${data.file}">${data.file}</a></p>`);
                break;
        }
    };


    // 关闭ws通信添加对应元素
    ws.onclose = function (event) {
        $('#message-box').append(`<p><span style="color: grey;">${now.getHours()}:${now.getMinutes()}</span><span style="color: red;">Server closed.</span></p>`);
    };

    // 绑定快捷键发送信息
    document.onkeydown = function (event) {
        if (event.keyCode === 13) {
            send();
        }
    }

    // 发送wsd的message信息
    function send() {
        let content = $('#content').val();
        // 对输入内容进行转义
        content = escapeHtml(content);
        $('#content').val('');
        if (!content) {
            return;
        }
        ws.send(JSON.stringify({
            type:'message',
            msg: content
        }));
    }

    // 文件上传到upload.php
    function uploadFile() {
    // 获取表单和文件元素
        const form = document.getElementById('uploadForm');
        const file = document.getElementById('file').files[0];

        // 创建XMLHttpRequest对象和FormData对象
        const xhr = new XMLHttpRequest();
        const formData = new FormData(form);
        formData.append('file', file);

        // 发送POST请求
        xhr.open('POST', 'upload.php', true);
        xhr.send(formData);

        if ( isImage(file.name) ){
            ws.send(JSON.stringify({
                type: 'image',
                filename: file.name,
            }))
        } else {
            ws.send(JSON.stringify({
                type: 'file',
                filename: file.name,
            }))
        }
    }

    function isImage(filename) {
        const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'];
        const extension = filename.slice(filename.lastIndexOf('.')).toLowerCase();
        return imageExtensions.includes(extension);
    }

    function escapeHtml(html) {
        const escapeChar = {
            '<': '&lt;',
            '>': '&gt;',
            '&': '&amp;',
            '"': '&quot;',
            "'": '&#39;',
        };
        return html.replace(/[<>&"']/g, m => escapeChar[m]);
    }
</script>