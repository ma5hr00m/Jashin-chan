<?php
include 'mysql.php';

$notice = "Please input your username and password";

if(isset($_POST['register'])) {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM usrs WHERE username='$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $notice = "The username is already taken!";
    } else {
        $sql = "INSERT INTO usrs (username, password) VALUES ('$username', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit;
        } else {
            $notice =  "Register failed";
        }
    }
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
                <input id="login" type=submit name="register" value="Register">
            </form>
            <span id="register-tip">
                Already have an account? <a id="link" href="login.php">Sign in</a>
            </span>
        </main>
    </body>
</html>
