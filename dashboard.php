<?php
require_once 'config/Database.php';

// Eksekusi query menggunakan driver mysqli ($conn)
$query = "SELECT * FROM facilities";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard FixIt - Backend Project</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Sistem Pelaporan Fasilitas (FixIt)</h2>
    <p>Selamat Datang di Halaman Preview Tugas Akhir Backend.</p>

    <a href="reportFacility.php" class="btn" target="_blank">Cetak Laporan PDF (Dompdf)</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Fasilitas</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['facilites_name']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                    </tr>
                <?php endwhile; ?> <?php else: ?>
                <tr>
                    <td colspan="3">Tidak ada data fasilitas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>