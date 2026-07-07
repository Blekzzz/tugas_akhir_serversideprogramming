<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h2>Register Form</h2>
    <form action="../actions/auth.php?action=register" method="POST">
        <input type="text" name="employee_id" placeholder="Employee ID" required><br>
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="text" name="department" placeholder="Department"><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role">
            <option value="employee">Employee</option>
            <option value="technician">Technician</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
    <a href="login.php">Already have an account? Login</a>
</body>
</html>