<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if ($role === 'employee') {
    $sql = "SELECT t.*, f.facilites_name, f.location FROM tickets t JOIN facilities f ON t.facilities_id = f.id WHERE t.reporter_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
} else {
    $sql = "SELECT t.*, f.facilites_name, f.location, ud.name as reporter_name FROM tickets t JOIN facilities f ON t.facilities_id = f.id JOIN users u ON t.reporter_id = u.id JOIN user_details ud ON u.employee_id = ud.id";
    $stmt = $pdo->query($sql);
}
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['name']); ?> (<?php echo ucfirst($role); ?>)</h2>
    <a href="../actions/auth.php?action=logout">Logout</a> | 
    <a href="../reports/generate_pdf.php" target="_blank">Cetak PDF Laporan</a>
    
    <?php if ($role === 'employee'): ?>
        <h3>Buat Laporan Kerusakan Baru</h3>
        <form action="../actions/ticket.php?action=create" method="POST">
            <label>Pilih Fasilitas:</label>
            <select name="facilities_id" required>
                <?php
                $fac_stmt = $pdo->query("SELECT * FROM facilities");
                while ($fac = $fac_stmt->fetch()) {
                    echo "<option value='{$fac['id']}'>{$fac['facilites_name']} - {$fac['location']}</option>";
                }
                ?>
            </select><br><br>
            <textarea name="issue_description" placeholder="Deskripsi kerusakan..." required></textarea><br><br>
            <button type="submit">Kirim Laporan</button>
        </form>
    <?php endif; ?>

    <h3>Daftar Tiket Laporan</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fasilitas</th>
                <th>Lokasi</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <?php if ($role === 'technician'): ?><th>Pelapor</th><th>Aksi</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?php echo $ticket['id']; ?></td>
                <td><?php echo htmlspecialchars($ticket['facilites_name']); ?></td>
                <td><?php echo htmlspecialchars($ticket['location']); ?></td>
                <td><?php echo htmlspecialchars($ticket['issue_description']); ?></td>
                <td><strong><?php echo strtoupper($ticket['status']); ?></strong></td>
                <?php if ($role === 'technician'): ?>
                    <td><?php echo htmlspecialchars($ticket['reporter_name']); ?></td>
                    <td>
                        <form action="../actions/ticket.php?action=update_status" method="POST" style="display:inline;">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                            <select name="status">
                                <option value="pending" <?php if($ticket['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="processing" <?php if($ticket['status'] == 'processing') echo 'selected'; ?>>Processing</option>
                                <option value="solved" <?php if($ticket['status'] == 'solved') echo 'selected'; ?>>Solved</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>