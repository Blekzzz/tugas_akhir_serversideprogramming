<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Office Reporting System</title>
</head>
<body>
    <h1>This is Dashboard</h1>
    <hr>
    <p>Welcome, <strong><?php echo $_SESSION['fullname']; ?></strong>!</p>
    <p>You are logged in as: <strong><?php echo $_SESSION['role']; ?></strong></p>
    
    <br>
    <a href="logout.php">Log Out</a>
</body>
</html>