<?php
include 'mysql.php';

if(isset($_POST['register'])) {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // 检查用户名是否存在
    $sql = "SELECT * FROM usrs WHERE username='$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "The username is already taken!";
    } else {
        // 注册信息插入数据库
        $sql = "INSERT INTO usrs (username, password) VALUES ('$username', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit;
        } else {
            echo "Register failed";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<meta charset="utf-8">
</head>
<body>
	<h1>Register Page</h1>
    <button onclick="location.href = 'login.php'" >Back to Login</button>
	<form method="post" action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>'>
		<label>Username</label>
		<input type="text" name="username" required>
        <br>
		<label>Password</label>
		<input type="password" name="password" required>
        <br>
		<input type="submit" name="register" value="Register">
	</form>
</body>
</html>
