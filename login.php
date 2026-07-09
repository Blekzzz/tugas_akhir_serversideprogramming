<?php
include 'config/Database.php';
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<p>Incorrect password!</p>";
        }
    } else {
        echo "<p>Email is not registered!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Office Reporting System</title>
</head>
<body>
    <h2>System Login</h2>
    <form action="" method="POST">
        <table border="0">
            <tr>
                <td>Email:</td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="login">Login</button></td>
            </tr>
        </table>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>