<?php
include 'config/Database.php';
session_start();

// Guard: Only logged in technicians can access this report generator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'technician') {
    die("Access denied. Only technicians can generate reports.");
}

// 1. Require the Composer Autoloader (Handles Dompdf automatically via Terminal Install)
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 2. Setup options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); 

// 3. Instantiate dompdf class with options
$dompdf = new Dompdf($options);

// 4. Fetch all 'solved' tickets for the formal report
$query = "SELECT tickets.issue_description, tickets.status, tickets.created_at,
                 facilities.facilites_name, facilities.location, 
                 reporter.fullname AS reporter_name, technician.fullname AS technician_name
          FROM tickets 
          JOIN facilities ON tickets.facilities_id = facilities.id
          JOIN users AS reporter ON tickets.reporter_id = reporter.id
          JOIN users AS technician ON tickets.technician_id = technician.id
          WHERE tickets.status = 'solved'
          ORDER BY tickets.created_at DESC";

$result = mysqli_query($conn, $query);

// 5. Build the HTML Structure
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Office Facilities Repair Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .status-badge { font-weight: bold; color: green; }
    </style>
</head>
<body>
    <div class="text-center">
        <h2>OFFICE FACILITIES MAINTENANCE REPORT</h2>
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    <hr>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Facility Name</th>
                <th>Location</th>
                <th>Issue Description</th>
                <th>Reported By</th>
                <th>Resolved By</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

if (mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '
            <tr>
                <td class="text-center">' . $no++ . '</td>
                <td>' . htmlspecialchars($row['facilites_name']) . '</td>
                <td>' . htmlspecialchars($row['location']) . '</td>
                <td>' . htmlspecialchars($row['issue_description']) . '</td>
                <td>' . htmlspecialchars($row['reporter_name']) . '</td>
                <td>' . htmlspecialchars($row['technician_name']) . '</td>
                <td>' . $row['created_at'] . '</td>
                <td class="status-badge">' . strtoupper($row['status']) . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" class="text-center">No solved tickets found in the record.</td></tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// 6. Load HTML content into Dompdf
$dompdf->loadHtml($html);

// 7. Setup paper size and orientation (Landscape is preferred due to many columns)
$dompdf->setPaper('A4', 'landscape');

// 8. Render the HTML as PDF
$dompdf->render();

// 9. Output the generated PDF to Browser (Inline preview)
$dompdf->stream("office_reporting_report.pdf", array("Attachment" => false));
?>