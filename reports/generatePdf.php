<?php

require_once '../config/database.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$sql = "SELECT t.id, f.facilites_name, f.location, t.issue_description, t.status, t.created_at, ud.name as reporter_name 
        FROM tickets t 
        JOIN facilities f ON t.facilities_id = f.id
        JOIN users u ON t.reporter_id = u.id
        JOIN user_details ud ON u.employee_id = ud.id";
$stmt = $pdo->query($sql);
$tickets = $stmt->fetchAll();

$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; padding: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .status { font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Keluhan & Kerusakan Fasilitas</h2>
        <p>Dicetak pada: ' . date('d-m-Y H:i:s') . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 20%;">Fasilitas</th>
                <th style="width: 20%;">Lokasi</th>
                <th style="width: 15%;">Pelapor</th>
                <th style="width: 25%;">Deskripsi Kerusakan</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>';

foreach ($tickets as $ticket) {
    $html .= '<tr>
                <td>' . $ticket['id'] . '</td>
                <td>' . htmlspecialchars($ticket['facilites_name']) . '</td>
                <td>' . htmlspecialchars($ticket['location']) . '</td>
                <td>' . htmlspecialchars($ticket['reporter_name']) . '</td>
                <td>' . htmlspecialchars($ticket['issue_description']) . '</td>
                <td class="status">' . $ticket['status'] . '</td>
              </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Kerusakan_Fasilitas.pdf", array("Attachment" => false));