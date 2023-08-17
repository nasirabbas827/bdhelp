<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission to update status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['new_status'];

    // Update the status in the database
    $update_sql = "UPDATE agreement_requests SET status = ? WHERE request_id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $request_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $status_updated = true;
}

// Fetch all agreement requests
$requests_sql = "SELECT ar.request_id, ar.donor_id, u.username AS donor_name, ar.story_id, s.title, ar.status
                FROM agreement_requests ar
                INNER JOIN users u ON ar.donor_id = u.id
                INNER JOIN stories s ON ar.story_id = s.story_id";
$requests_result = $conn->query($requests_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Requests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2>Manage Agreement Requests</h2>
        <?php if (isset($status_updated)) { ?>
            <p class="alert alert-success">Request status updated successfully.</p>
        <?php } ?>
        
        <?php if (mysqli_num_rows($requests_result) > 0) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Donor Name</th>
                        <th>Story Title</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($requests_result)) { ?>
                        <tr>
                            <td><?php echo $row['request_id']; ?></td>
                            <td><?php echo $row['donor_name']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                    <select name="new_status" class="form-control">
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                    <input type="submit" name="update_status" value="Update" class="btn btn-primary mt-2">
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No agreement requests found.</p>
        <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
