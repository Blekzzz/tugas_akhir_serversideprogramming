<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h2>Form Login</h2>
    <form action="../actions/auth.php?action=login" method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Belum punya akun? Daftar</a>
</body>
</html>