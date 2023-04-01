<?php
include 'mysql.php';

session_start();

if($_POST && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM `usrs` WHERE username='$username'";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if($row['password'] == $password) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['loggedin'] = true;
            header("Location: index.php?username=$username");
            exit();
        } else {
            echo "Failed to login as $username, please try again.";
        }
    } else {
        echo "Username $username don't exist.";
    }
}
?>



<!DOCTYPE html>
<html>
    <head>
        <title>Login Page</title>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>Login Page</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <lable>Username</lable>
            <input type="text" name="username" required>
            <br>
            <lable>Password</lable>
            <input type="password" name="password" required>
            <br>
            <input type=submit name="login" value="Login">
        </form>
        <button onclick="location.href='register.php'">Register</button>
    </body>
</html>