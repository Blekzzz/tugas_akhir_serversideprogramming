<?php
include 'config/Database.php';
session_start();

// AUTHENTICATION GUARD: If NOT logged in, kick back to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<p style='color: red;'>Error: New password and confirm password do not match!</p>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_password_query = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";

        if (mysqli_query($conn, $update_password_query)) {
            echo "<p style='color: green;'>Password changed successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error updating password: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password - Office Reporting System</title>
</head>
<body>
    <h2>Change Password</h2>
    <p><a href="dashboard.php"><- Back to Dashboard</a></p>
    <hr>

    <form action="" method="POST">
        <table border="0" cellpadding="5">
            <tr>
                <td>New Password:</td>
                <td><input type="password" name="new_password" required minlength="4"></td>
            </tr>
            <tr>
                <td>Confirm New Password:</td>
                <td><input type="password" name="confirm_password" required minlength="4"></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="change_password">Update Password</button></td>
            </tr>
        </table>
    </form>
</body>
</html>