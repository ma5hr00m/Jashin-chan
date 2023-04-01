<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 设置上传文件目录和文件名
    $uploadDir = './upload/';
    $fileName = basename($_FILES["file"]["name"]);
    $uploadFile = $uploadDir . $fileName;

    // 移动上传文件到指定目录
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadFile)) {
        echo "Upload success!";
    } else {
        echo "Upload failed!";
    }
} else {
    echo "Invalid request!";
}
?>
