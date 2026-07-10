<?php
include 'config/Database.php';
session_start();

// AUTHENTICATION GUARD: Must be logged in AND must be a technician to print report
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'technician') {
    header("Location: login.php");
    exit();
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

// 4. Fetch ALL tickets (pending, processing, solved) for the comprehensive report
$query = "SELECT tickets.issue_description, tickets.status, tickets.created_at,
                 facilities.facilites_name, facilities.location, 
                 reporter.fullname AS reporter_name, assigned_tech.fullname AS technician_name
          FROM tickets 
          JOIN facilities ON tickets.facilities_id = facilities.id
          JOIN users AS reporter ON tickets.reporter_id = reporter.id
          LEFT JOIN users AS assigned_tech ON tickets.technician_id = assigned_tech.id
          ORDER BY tickets.created_at DESC";

$result = mysqli_query($conn, $query);

// 5. Build the HTML Structure
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Office Facilities Maintenance Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .status-pending { font-weight: bold; color: #dc3545; }    /* Red */
        .status-processing { font-weight: bold; color: #ffc107; } /* Yellow/Orange */
        .status-solved { font-weight: bold; color: #28a745; }     /* Green */
    </style>
</head>
<body>
    <div class="text-center">
        <h2>COMPREHENSIVE OFFICE FACILITIES MAINTENANCE REPORT</h2>
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    <hr>
    
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Facility Name</th>
                <th width="15%">Location</th>
                <th width="20%">Issue Description</th>
                <th width="12%">Reported By</th>
                <th width="12%">Assigned Tech</th>
                <th width="11%">Date</th>
                <th width="5%">Status</th>
            </tr>
        </thead>
        <tbody>';

if (mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $status_class = '';
        if ($row['status'] == 'pending') {
            $status_class = 'status-pending';
        } elseif ($row['status'] == 'processing') {
            $status_class = 'status-processing';
        } elseif ($row['status'] == 'solved') {
            $status_class = 'status-solved';
        }

        $tech_name = $row['technician_name'] ? htmlspecialchars($row['technician_name']) : '-';

        $html .= '
            <tr>
                <td class="text-center">' . $no++ . '</td>
                <td>' . htmlspecialchars($row['facilites_name']) . '</td>
                <td>' . htmlspecialchars($row['location']) . '</td>
                <td>' . htmlspecialchars($row['issue_description']) . '</td>
                <td>' . htmlspecialchars($row['reporter_name']) . '</td>
                <td>' . $tech_name . '</td>
                <td>' . $row['created_at'] . '</td>
                <td class="' . $status_class . '">' . strtoupper($row['status']) . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" class="text-center">No reported tickets found in the database.</td></tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// 6. Load HTML content into Dompdf
$dompdf->loadHtml($html);

// 7. Setup paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// 8. Render the HTML as PDF
$dompdf->render();

// 9. Output the generated PDF to Browser (Inline preview)
$dompdf->stream("comprehensive_office_report.pdf", array("Attachment" => false));
?>