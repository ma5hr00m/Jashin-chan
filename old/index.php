<?php
session_start();
$username = $_GET['username'];


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
                <div id="user-box"></div>
            </aside>
            <main>
                <div id="message-box"></div>
                <div id="command-box">
                    <form id="uploadForm" method="post" enctype="multipart/form-data">
                        <input type="file" id="file" name="file" onchange="uploadFile()">
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
    let ws = new WebSocket('ws://localhost:8880');

    const params = new URLSearchParams(window.location.search);
    const username = params.get('username');
    const now = new Date();


    ws.onopen = function (event) {
        ws.send(JSON.stringify({
            type: 'login',
            name: username,
        }))
    }

    ws.onmessage = function (event) {
        let data = JSON.parse(event.data);

        switch (data.type) {
            case 'close':
            case 'login':
                $('#user-box').html('');
                data.users.forEach(function (item) {
                    $('#user-box').append(`<p class="user-dock"><img class="user-image" src="./src/image/users.svg" /><span class="user-text">${item}</span></p>`);});
                if (data.msg) {
                    $('#message-box').append(`<p style="color: grey;">${data.msg}</p>`);
                }
                break;
            case 'message':
                $('#message-box').append(`<p class="message-dock">
                        <span class="message-user">${data.name}</span>
                        <span class="message-content">${data.msg}</span>
                        <span class="message-time">${data.time}</span>
                    </p>`);
                break;
            case 'image':
                $('#message-box').append(`<p><span style="color: grey;">${data.name}</span><span style="color: red;">${data.time}</span><a href="${data.path}" download><img src="${data.path}"></a></p>`);
                break;
            case 'file':
                $('#message-box').append(`<p><span style="color: grey;">${data.name}</span><span style="color: red;">${data.time}</span><a href="${data.path}" download>${data.file}</a></p>`);
                break;
        }
    };

    ws.onclose = function (event) {
        $('#message-box').append(`<p><span style="color: grey;">${now.getHours()}:${now.getMinutes()}</span><span style="color: red;">Server closed.</span></p>`);
    };

    document.onkeydown = function (event) {
        if (event.keyCode === 13) {
            send();
        }
    }

    function send() {
        let content = $('#content').val();
        $('#content').val('');
        if (!content) {
            return;
        }
        ws.send(JSON.stringify({
            type:'message',
            msg: content
        }));
    }


    function uploadFile() {
        var formData = new FormData($("#uploadForm")[0]);
        var fileName = document.getElementById('file').files[0].name;

        $.ajax({
            url: 'http://localhost:8880/upload.php',
            type: 'POST',
            data: formData,
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                alert(data);
            },
            error: function (data) {
                alert(data);
            }
        })

        if (isImage(fileName)){
            ws.send(JSON.stringify({
                type: 'image',
                filename: fileName,
            }))
        } else {
            ws.send(JSON.stringify({
                type: 'file',
                filename: fileName,
            }))
        }
        
    }

    function isImage(filename) {
        const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp'];
        const extension = filename.slice(filename.lastIndexOf('.')).toLowerCase();
        return imageExtensions.includes(extension);
    }
</script>