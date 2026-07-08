<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$username = $email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username)) $errors['username'] = "Username is required.";
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $errors['general'] = "Username or Email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $insert = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                if ($insert->execute([$username, $email, $hashed_password])) {
                    $_SESSION['success'] = "Registration successful! You can now log in.";
                    header("Location: index.php");
                    exit;
                }
            }
        } catch (PDOException $e) {
            $errors['general'] = "System error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 380px; }
        h2 { margin: 0 0 20px 0; color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #666; font-size: 14px; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        input:focus { border-color: #007bff; outline: none; }
        .btn { width: 100%; padding: 12px; background: #007bff; border: none; color: white; font-size: 16px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn:hover { background: #0056b3; }
        .error { color: #dc3545; font-size: 12px; margin-top: 4px; }
        .alert { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
        .switch-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .switch-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>Create Account</h2>
    
    <?php if (isset($errors['general'])): ?>
        <div class="alert"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST" novalidate>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>">
            <?php if (isset($errors['username'])): ?>
                <div class="error"><?= $errors['username'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="error"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <div class="error"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <?php if (isset($errors['confirm_password'])): ?>
                <div class="error"><?= $errors['confirm_password'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Register</button>
    </form>

    <div class="switch-link">
        Already registered? <a href="index.php">Login here</a>
    </div>
</div>

</body>
</html>