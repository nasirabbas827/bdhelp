<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all donation records with additional details
$donations_sql = "SELECT d.*, s.title AS story_title, s.amount_needed, s.description, c.name AS category_name, u.username AS donor_name, ub.username AS beneficiary_name
                  FROM donations d
                  INNER JOIN stories s ON d.story_id = s.story_id
                  INNER JOIN categories c ON d.category_id = c.id
                  INNER JOIN users u ON d.donor_id = u.id
                  INNER JOIN users ub ON s.beneficiary_id = ub.id";
$donations_result = mysqli_query($conn, $donations_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Donations</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2>All Donations</h2>
        <?php if (mysqli_num_rows($donations_result) > 0) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Story Title</th>
                        <th>Beneficiary Name</th>
                        <th>Donor Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount Needed</th>
                        <th>Amount Donated</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($donations_result)) { ?>
                        <tr>
                            <td><?php echo $row['donation_id']; ?></td>
                            <td><?php echo $row['story_title']; ?></td>
                            <td><?php echo $row['beneficiary_name']; ?></td>
                            <td><?php echo $row['donor_name']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td>$<?php echo $row['amount_needed']; ?></td>
                            <td>$<?php echo $row['amount']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No donation records found.</p>
        <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
