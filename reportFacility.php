<?php
// Ambil library dompdf dan koneksi database dengan path yang benar
require_once 'vendor/autoload.php';
require_once 'config/Database.php';

use Dompdf\Dompdf;

// 1. Ambil data fasilitas dari database menggunakan mysqli ($conn)
$query = "SELECT * FROM facilities";
$result = $conn->query($query);

// 2. Susun struktur HTML yang akan diubah menjadi PDF
$html = '
<h2 style="text-align: center; font-family: sans-serif;">LAPORAN DATA FASILITAS - FIXIT</h2>
<table width="100%" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-family: sans-serif;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th width="10%">ID</th>
            <th width="45%">Nama Fasilitas</th>
            <th width="45%">Lokasi</th>
        </tr>
    </thead>
    <tbody>';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td style="text-align: center;">' . $row['id'] . '</td>
            <td>' . $row['facilites_name'] . '</td>
            <td>' . $row['location'] . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="3" style="text-align: center;">Tidak ada data fasilitas.</td></tr>';
}

$html .= '</tbody></table>';

// 3. Inisialisasi Dompdf dan render HTML-nya
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // Ukuran kertas A4 Potrait
$dompdf->render();

// 4. Output PDF langsung ke browser (Attachment false = preview langsung di tab baru browser)
$dompdf->stream("laporan_fasilitas_fixit.pdf", ["Attachment" => false]);