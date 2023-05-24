<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if (isset($_FILES['file'])) {
	$fileName = $_FILES['file']['name'];
	$fileTempName = $_FILES['file']['tmp_name'];
	$fileError = $_FILES['file']['error'];
	$fileSize = $_FILES['file']['size'];
	$maxFileSize = 10 * 1024 * 1024;

	$uploadDirectory = realpath('./upload') . '/';
    $uploadPath = $uploadDirectory . basename($fileName);

	$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

	if ($fileSize > $maxFileSize) {
		echo '请发送小于 10MB 的文件';
		return;
	}

	if (preg_match('/^(?i:php)$/i', $fileExtension)) {
		echo '非法的文件类型';
		return;
	}

	if (strpos($uploadPath, $uploadDirectory) === false) {
		echo '非法的文件路径';
		return;
	}
	
	if ($fileError !== UPLOAD_ERR_OK) {
		return;
	}
	
	if (file_exists($uploadPath)) {
		echo '该文件已存在';
		return;
	}
	
	if (move_uploaded_file($fileTempName, $uploadPath)) {
		echo '文件上传成功';
	} else {
		echo '文件上传失败';
	}
	
}
