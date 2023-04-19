<?php
if (isset($_FILES['file'])) {
	$fileName = $_FILES['file']['name'];
	$fileTempName = $_FILES['file']['tmp_name'];
	$fileSize = $_FILES['file']['size'];
	$fileError = $_FILES['file']['error'];

	if ($fileError === UPLOAD_ERR_OK) {
		$uploadPath = './upload/' . $fileName;
		move_uploaded_file($fileTempName, $uploadPath);
		echo '文件上传成功';
	} else {
		echo '文件上传失败';
	}
}
