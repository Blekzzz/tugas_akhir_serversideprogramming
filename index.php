<?php
session_start();
require_once 'config/database.php';

// If a valid session already exists, bypass login entirely
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$identifier = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier)) $errors['identifier'] = "Username or Email is required.";
    if (empty($password)) $errors['password'] = "Password is required.";

    if (empty($errors)) {
        try {
            // Find user by username or email matching your table setup
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$identifier, $identifier]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Initialize authentication keys
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                header("Location: dashboard.php");
                exit;
            } else {
                $errors['general'] = "Invalid username/email or password.";
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
    <title>Login</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 380px; }
        h2 { margin: 0 0 20px 0; color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #666; font-size: 14px; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        input:focus { border-color: #007bff; outline: none; }
        .btn { width: 100%; padding: 12px; background: #28a745; border: none; color: white; font-size: 16px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn:hover { background: #218838; }
        .error { color: #dc3545; font-size: 12px; margin-top: 4px; }
        .alert { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; }
        .switch-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .switch-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>Login Account</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?>
        <div class="alert"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form action="index.php" method="POST" novalidate>
        <div class="form-group">
            <label for="identifier">Username or Email</label>
            <input type="text" id="identifier" name="identifier" value="<?= htmlspecialchars($identifier) ?>">
            <?php if (isset($errors['identifier'])): ?>
                <div class="error"><?= $errors['identifier'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <div class="error"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Sign In</button>
    </form>

    <div class="switch-link">
        New here? <a href="register.php">Create an account</a>
    </div>
</div>

</body>
</html>