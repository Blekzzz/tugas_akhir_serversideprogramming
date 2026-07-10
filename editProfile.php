<?php
include 'config/Database.php';
session_start();

// AUTHENTICATION GUARD: If NOT logged in, kick back to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $birth_date = $_POST['birth_date'];
    $birth_place = $_POST['birth_place'];
    $department = $_POST['department'];

    $update_query = "UPDATE users SET 
                        fullname = '$fullname', 
                        address = '$address', 
                        phone_number = '$phone_number', 
                        birth_date = '$birth_date', 
                        birth_place = '$birth_place', 
                        department = '$department' 
                     WHERE id = '$user_id'";

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['fullname'] = $fullname;
        echo "<p style='color: green;'>Profile updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error updating profile: " . mysqli_error($conn) . "</p>";
    }
}

$select_query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $select_query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - Office Reporting System</title>
</head>
<body>
    <h2>Edit Profile Information</h2>
    <p><a href="dashboard.php"><- Back to Dashboard</a></p>
    <hr>

    <form action="" method="POST">
        <table border="0" cellpadding="5">
            <tr>
                <td>Email:</td>
                <td><input type="email" value="<?php echo $user['email']; ?>" disabled> <em>(Cannot be changed)</em></td>
            </tr>
            <tr>
                <td>Role:</td>
                <td><input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled> <em>(Cannot be changed)</em></td>
            </tr>
            <tr>
                <td>Full Name:</td>
                <td><input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><textarea name="address" rows="3" cols="30"><?php echo htmlspecialchars($user['address']); ?></textarea></td>
            </tr>
            <tr>
                <td>Phone Number:</td>
                <td><input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>"></td>
            </tr>
            <tr>
                <td>Birth Date:</td>
                <td><input type="date" name="birth_date" value="<?php echo $user['birth_date']; ?>"></td>
            </tr>
            <tr>
                <td>Birth Place:</td>
                <td><input type="text" name="birth_place" value="<?php echo htmlspecialchars($user['birth_place']); ?>"></td>
            </tr>
            <tr>
                <td>Department:</td>
                <td><input type="text" name="department" value="<?php echo htmlspecialchars($user['department']); ?>"></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="update_profile">Save Changes</button></td>
            </tr>
        </table>
    </form>
</body>
</html>