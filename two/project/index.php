<?php
session_start();

$iniFile = "config.ini";
$config = parse_ini_file($iniFile, true);
$username = $_GET['username'];

if (!isset($_SESSION['username']) || ($_SESSION['username'] !== $_GET['username'])) {
    session_unset();
    session_destroy();
    header('Location: /login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link type="text/css" rel="stylesheet" href="./src/css/index.css">
        <link rel="shortcut icon" href="./src/image/favicon.svg" type="image/x-icon" />
        <title>Wirror</title>
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

    let websocketIP = "<?php echo $config['websocket']['ip']; ?>";
    let websocketPort = "<?php echo $config['websocket']['port']; ?>";
    let websocketAddress = 'ws://' + websocketIP + ':' + websocketPort;

    let webIP = "<?php echo $config['web']['ip']; ?>";
    let webPort = "<?php echo $config['web']['port']; ?>";
    let uploadAddress = 'http://' + webIP + ':' + webPort + '/upload.php';

    const params = new URLSearchParams(window.location.search);
    const username = params.get('username');
    const now = new Date();

    let ws = new WebSocket(websocketAddress);

    ws.onopen = function (event) {
        ws.send(JSON.stringify({
            type: 'login',
            name: username,
        }))
    }

    ws.onmessage = function (event) {
        
        setTimeout(function() {}, 1000);

        let data = JSON.parse(event.data);

        switch (data.type) {
            case 'login':
            case 'close':
                $('#user-box').html('');
                data.users.forEach(function (item) {
                    $('#user-box').append(`<p class="user-dock"><span class="user-text">${item}</span></p>`);});
                if (data.msg) {
                    $('#message-box').append(`
                        <span class="tip-dock" style="color: grey;width: 100%; display: flex;">${data.msg}</span>
                    `);
                }
                break;
            case 'message':
                $('#message-box').append(`
                    <p class="message-dock">
                        <span class="message-user" style="color: grey;">${data.name}</span>
                        <span class="message-time" style="color: red;">${data.time}</span>
                        <span class="message-content">${data.msg}</span>
                    </p>`);
                break;
            case 'image':
                $('#message-box').append(`
                    <p>
                        <span style="color: grey;">${data.name}</span>
                        <span style="color: red;">${data.time}</span>
                        <a href="${data.path}" download="${data.file}">
                            <img src="${data.path}">
                        </a>
                    </p>
                `);
                break;
            case 'file':
                $('#message-box').append(`
                    <p>
                        <span style="color: grey;">${data.name}</span>
                        <span style="color: red;">${data.time}</span>
                        <a href="${data.path}" download="${data.file}">${data.file}</a>
                    </p>
                `);
                break;
        }
    };

    ws.onclose = function (event) {
        $('#message-box').append(`
            <p>
                <span style="color: grey;">${now.getHours()}:${now.getMinutes()}</span>
                <span style="color: red;">Server closed.</span>
            </p>
        `);
    };

    document.onkeydown = function (event) {
        if (event.keyCode === 13) {
            send();
        }
    }

    function send() {
        let content = $('#content').val();
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

    function uploadFile() {
        const form = document.getElementById('uploadForm');
        const file = document.getElementById('file').files[0];

        const formData = new FormData(form);
        formData.append('file', file);

        fetch(uploadAddress, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => alert(data))
        .catch(error => alert(error));

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
        const imageExtensions = ['.jpg', '.jpeg', '.png', '.svg'];
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