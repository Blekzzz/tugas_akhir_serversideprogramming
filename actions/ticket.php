<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $facilities_id = $_POST['facilities_id'];
    $issue_description = trim($_POST['issue_description']);

    if (empty($facilities_id) || empty($issue_description)) {
        die("Error: Semua field wajib diisi!");
    }

    $sql = "INSERT INTO tickets (reporter_id, facilities_id, issue_description, status) VALUES (:reporter_id, :facilities_id, :issue_description, 'pending')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':reporter_id' => $_SESSION['user_id'],
        ':facilities_id' => $facilities_id,
        ':issue_description' => $issue_description
    ]);

    header("Location: ../views/dashboard.php");
    exit;
}

if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['role'] !== 'technician') {
        die("Akses ditolak: Hanya teknisi yang dapat mengubah status.");
    }

    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];

    $sql = "UPDATE tickets SET status = :status, technician_id = :technician_id WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':status' => $status,
        ':technician_id' => $_SESSION['user_id'],
        ':id' => $ticket_id
    ]);

    header("Location: ../views/dashboard.php");
    exit;
}