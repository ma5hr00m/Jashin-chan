<?php
include 'mysql.php';

$notice = "Please input your username and password";

if(isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 使用预处理语句减少 SQL 注入攻击的风险
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $notice = "The username is already taken!";
    } else {
        // 使用预处理语句减少 SQL 注入攻击的风险
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);

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
        <title>Register Page</title>
        <link rel="stylesheet" href="./src/css/login.css">
        <meta charset="utf-8">
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
                <input type="file" name="fileToUpload" id="fileToUpload">
                <input id="login" type=submit name="register" value="Register">
            </form>
            <span id="register-tip">
                Already have an account? <a id="link" href="login.php">Sign in</a>
            </span>
        </main>
        <script defer src='https://static.cloudflareinsights.com/beacon.min.js'data-cf-beacon='{"token":"9d5fad3465e44f3a9fbe6990767d6ae4"}'></script>
        <script async src='https://www.googletagmanager.com/gtag/js?id=G-BLTN92ZVE0'></script>
        <script>
            window.dataLayer=window.dataLayer||[];
            function gtag() {
                dataLayer.push(arguments)}gtag('js',new Date());
                gtag('config','G-BLTN92ZVE0');
        </script>
    </body>
</html>
