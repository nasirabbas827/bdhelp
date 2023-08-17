<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}


// Handle story status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $story_id = $_POST['story_id'];
    $new_status = $_POST['new_status'];

    $update_sql = "UPDATE stories SET status = ? WHERE story_id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $story_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Fetch stories with category names from the database
$stories_sql = "SELECT s.*, c.name AS category_name FROM stories s
                INNER JOIN categories c ON s.category_id = c.id";
$stories_result = $conn->query($stories_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    <?php include('admin_navbar.php'); ?>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>

        <h3>Update Story Status</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Story ID</th>
                    <th>Profile Picture</th>
                    <th>Category</th>
                    <th>Beneficiary Name</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Amount Needed</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $stories_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['story_id'] . "</td>";
                    echo "<td><img src='../Beneficiary/" . $row['profile_picture'] . "' width='100'></td>";
                    echo "<td>" . $row['category_name'] . "</td>";
                    echo "<td>" . $row['beneficiary_name'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>$" . $row['amount_needed'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>";
                    echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='POST'>";
                    echo "<input type='hidden' name='story_id' value='" . $row['story_id'] . "'>";
                    echo "<select name='new_status' class='form-control'>";
                    echo "<option value='approved'>Approved</option>";
                    echo "<option value='pending'>Pending</option>";
                    echo "<option value='rejected'>Rejected</option>";
                    echo "</select>";
                    echo "<input type='submit' name='update_status' value='Update' class='btn btn-primary mt-2'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
