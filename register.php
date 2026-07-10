<?php
include 'config/Database.php';
session_start();

// AUTHENTICATION GUARD: If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO users (fullname, email, password, role) 
              VALUES ('$fullname', '$email', '$password', '$role')";

    if (mysqli_query($conn, $query)) {
        echo "<p>Registration successful! Please <a href='login.php'>Login</a></p>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Office Reporting System</title>
</head>
<body>
    <h2>User Registration Form</h2>
    <form action="" method="POST">
        <table border="0">
            <tr>
                <td>Full Name:</td>
                <td><input type="text" name="fullname" required></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td>Role:</td>
                <td>
                    <select name="role" required>
                        <option value="employee">Employee</option>
                        <option value="technician">Technician</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="register">Register</button></td>
            </tr>
        </table>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>