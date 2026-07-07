<?php

require_once '../config/Database.php';
session_start();

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = trim($_POST['employee_id']);
    $name        = trim($_POST['name']);
    $department  = trim($_POST['department']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];
    $role        = $_POST['role'];

    if (empty($employee_id) || empty($name) || empty($email) || empty($password) || empty($role)) {
        die("Error: Semua field wajib diisi!");
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql_details = "INSERT INTO user_details (employee_id, name, department) VALUES ('$employee_id', '$name', '$department')";
    
    if ($conn->query($sql_details) === TRUE) {
        $user_detail_id = $conn->insert_id;

        $sql_user = "INSERT INTO users (email, password, role, employee_id) VALUES ('$email', '$hashed_password', '$role', '$user_detail_id')";
        
        if ($conn->query($sql_user) === TRUE) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='../views/login.php';</script>";
            exit;
        } else {
            echo "Error pada tabel users: " . $conn->error;
        }
    } else {
        echo "Error pada tabel user_details: " . $conn->error;
    }
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        die("Error: Semua field wajib diisi!");
    }

    $sql = "SELECT u.*, ud.name FROM users u JOIN user_details ud ON u.employee_id = ud.id WHERE u.email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email']  = $user['email'];
            $_SESSION['role']   = $user['role'];
            $_SESSION['name']   = $user['name'];

            header("Location: ../views/dashboard.php");
            exit;
        } else {
            echo "<script>alert('Password salah!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email tidak terdaftar!'); window.history.back();</script>";
    }
}

if ($action === 'logout') {
    session_destroy();
    header("Location: ../views/login.php");
    exit;
}