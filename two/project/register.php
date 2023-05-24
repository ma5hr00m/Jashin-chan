<?php
// 引用mysql.php配置
include 'mysql.php';

$notice = "Please input your username and password";

if(isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $status = 0;

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $notice = "The username is already taken!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, status) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $password, $status);
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $notice =  "Register failed";
        }
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="./src/css/login.css">
        <link rel="shortcut icon" href="./src/image/favicon.svg" type="image/x-icon" />
        <title>Wirror</title>
    </head>
    <body>
        <main>
            <h1>Wirror</h1>
            <h2>Create an account</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <label id="username-label" class="label">Username</label>
                <input id="username" class="input" type="text" name="username" required>
                <label id="password-label" class="label">Password</label>
                <input id="password" class="input" type="password" name="password" required>
                <span id="notice" class="tip"><?php echo $notice;?></span>
                <input id="login" type="submit" name="register" value="Register">
            </form>
            <span id="register-tip">
                Already have an account? <a id="link" href="login.php">Sign in</a>
            </span>
        </main>
    </body>
</html>
