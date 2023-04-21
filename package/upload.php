<?php
if (isset($_FILES['file'])) {
	$fileName = $_FILES['file']['name'];
	$fileTempName = $_FILES['file']['tmp_name'];
	$fileError = $_FILES['file']['error'];

	$uploadDirectory = realpath('./upload') . '/';
    $uploadPath = $uploadDirectory . basename($fileName);

	if (strpos($uploadPath, $uploadDirectory) !== false) {
		if ($fileError === UPLOAD_ERR_OK) {
			if (!file_exists($uploadPath)) {
				if (move_uploaded_file($fileTempName, $uploadPath)) {
					echo '文件上传成功';
				} else {
					echo '文件上传失败';
				}
			} else {
				echo '该文件已存在';
			}
		}
	} else {
		echo '非法的文件路径';
	}
}
