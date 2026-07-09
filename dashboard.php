<?php
include 'config/Database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$fullname = $_SESSION['fullname'];

// Handle Employee Form Submission (Creating a Ticket)
if ($role == 'employee' && isset($_POST['submit_ticket'])) {
    $facilities_id = $_POST['facilities_id'];
    $issue_description = $_POST['issue_description'];

    $insert_query = "INSERT INTO tickets (reporter_id, facilities_id, issue_description, status) 
                     VALUES ('$user_id', '$facilities_id', '$issue_description', 'pending')";
    
    if (mysqli_query($conn, $insert_query)) {
        echo "<p>Report submitted successfully!</p>";
    } else {
        echo "<p>Error submitting report: " . mysqli_error($conn) . "</p>";
    }
}

// Handle Technician Action (Updating Ticket Status)
if ($role == 'technician' && isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['new_status'];

    $update_query = "UPDATE tickets SET status = '$new_status', technician_id = '$user_id' WHERE id = '$ticket_id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<p>Ticket status updated successfully!</p>";
    } else {
        echo "<p>Error updating status: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Office Reporting System</title>
</head>
<body>
    <h1>This is Dashboard</h1>
    <hr>
    <p>Welcome, <strong><?php echo $fullname; ?></strong>!</p>
    <p>You are logged in as: <strong><?php echo ucfirst($role); ?></strong></p>
    <p><a href="logout.php">Log Out</a></p>
    <hr>

    <?php if ($role == 'employee'): ?>
        <h3>Report a Facility Issue</h3>
        <form action="" method="POST">
            <table border="0">
                <tr>
                    <td>Select Facility:</td>
                    <td>
                        <select name="facilities_id" required>
                            <option value="">-- Select Facility --</option>
                            <?php
                            $facility_query = "SELECT * FROM facilities";
                            $facility_result = mysqli_query($conn, $facility_query);
                            while ($facility = mysqli_fetch_assoc($facility_result)) {
                                echo "<option value='" . $facility['id'] . "'>" . $facility['facilites_name'] . " (" . $facility['location'] . ")</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Issue Description:</td>
                    <td>
                        <textarea name="issue_description" rows="5" cols="40" required></textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit" name="submit_ticket">Submit Report</button></td>
                </tr>
            </table>
        </form>

        <br><hr><br>

        <h3>Your Submitted Reports & Status</h3>
        <?php
        // Fetch only tickets submitted by this specific logged-in employee
        $my_ticket_query = "SELECT tickets.issue_description, tickets.status, tickets.created_at,
                                   facilities.facilites_name, facilities.location
                            FROM tickets 
                            JOIN facilities ON tickets.facilities_id = facilities.id
                            WHERE tickets.reporter_id = '$user_id'
                            ORDER BY tickets.created_at DESC";
        
        $my_ticket_result = mysqli_query($conn, $my_ticket_query);

        if (mysqli_num_rows($my_ticket_result) > 0): 
        ?>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Facility Name</th>
                        <th>Location</th>
                        <th>Issue Description</th>
                        <th>Reported At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($my_ticket = mysqli_fetch_assoc($my_ticket_result)): 
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $my_ticket['facilites_name']; ?></td>
                            <td><?php echo $my_ticket['location']; ?></td>
                            <td><?php echo $my_ticket['issue_description']; ?></td>
                            <td><?php echo $my_ticket['created_at']; ?></td>
                            <td>
                                <strong><?php echo strtoupper($my_ticket['status']); ?></strong>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><em>You have not submitted any reports yet.</em></p>
        <?php endif; ?>

    <?php elseif ($role == 'technician'): ?>
        <h3>Technician Panel</h3>
        <p><a href="generateReport.php" target="_blank"><strong>[ Create & Download PDF Report (Solved Tickets) ]</strong></a></p>
        <h3>Active Broken Facilities List</h3>
        <?php
        // Fetch tickets that are NOT solved yet (pending or processing)
        $ticket_query = "SELECT tickets.id AS ticket_id, tickets.issue_description, tickets.status, tickets.created_at,
                                facilities.facilites_name, facilities.location, users.fullname AS reporter_name 
                         FROM tickets 
                         JOIN facilities ON tickets.facilities_id = facilities.id
                         JOIN users ON tickets.reporter_id = users.id
                         WHERE tickets.status != 'solved'
                         ORDER BY tickets.created_at DESC";
        
        $ticket_result = mysqli_query($conn, $ticket_query);

        if (mysqli_num_rows($ticket_result) > 0): 
        ?>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Facility Name</th>
                        <th>Location</th>
                        <th>Issue Description</th>
                        <th>Reported By</th>
                        <th>Reported At</th>
                        <th>Current Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($ticket = mysqli_fetch_assoc($ticket_result)): 
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $ticket['facilites_name']; ?></td>
                            <td><?php echo $ticket['location']; ?></td>
                            <td><?php echo $ticket['issue_description']; ?></td>
                            <td><?php echo $ticket['reporter_name']; ?></td>
                            <td><?php echo $ticket['created_at']; ?></td>
                            <td><strong><?php echo strtoupper($ticket['status']); ?></strong></td>
                            <td>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
                                    <select name="new_status" required>
                                        <option value="pending" <?php if($ticket['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="processing" <?php if($ticket['status'] == 'processing') echo 'selected'; ?>>Processing</option>
                                        <option value="solved">Solved</option>
                                    </select>
                                    <button type="submit" name="update_status">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><em>No broken facilities reported. All systems are operational!</em></p>
        <?php endif; ?>

    <?php endif; ?>

</body>
</html>