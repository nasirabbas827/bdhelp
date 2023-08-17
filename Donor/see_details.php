<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] != 'Donor') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['story_id'])) {
    $story_id = $_GET['story_id'];

    // Fetch story details from the database
    $story_sql = "SELECT s.*, c.name AS category_name, u.username AS beneficiary_name, u.phone AS beneficiary_phone
                  FROM stories s
                  INNER JOIN categories c ON s.category_id = c.id
                  INNER JOIN users u ON s.beneficiary_id = u.id
                  WHERE s.story_id = ?";
    $stmt = mysqli_prepare($conn, $story_sql);
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $story_result = mysqli_stmt_get_result($stmt);
    $story = mysqli_fetch_assoc($story_result);
    mysqli_stmt_close($stmt);
} else {
    header("Location: donor_dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donate'])) {
    $donor_id = $_SESSION['user_id'];
    $donor_name = $_SESSION['username'];
    $donation_amount = $_POST['donation_amount'];

    // Ensure donation amount is not greater than amount needed
    if ($donation_amount <= $story['amount_needed']) {
        // Insert donation record into the database
        $insert_donation_sql = "INSERT INTO donations (donor_id, donor_name, story_id, category_id, beneficiary_id, beneficiary_name, amount) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_donation_sql);
        mysqli_stmt_bind_param($stmt, "issiisi", $donor_id, $donor_name, $story_id, $story['category_id'], $story['beneficiary_id'], $story['beneficiary_name'], $donation_amount);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update the amount needed in the stories table
        $new_amount_needed = $story['amount_needed'] - $donation_amount;
        $update_amount_sql = "UPDATE stories SET amount_needed = ? WHERE story_id = ?";
        $stmt = mysqli_prepare($conn, $update_amount_sql);
        mysqli_stmt_bind_param($stmt, "ii", $new_amount_needed, $story_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $donation_submitted = true;
    } else {
        $donation_error = "Donation amount cannot exceed amount needed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Story Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

    
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5 mb-5">
        <h2>Story Details</h2>
        <?php if (isset($story)) { ?>
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title"><?php echo $story['title']; ?></h3>
                    <p class="card-text"><strong>Category:</strong> <?php echo $story['category_name']; ?></p>
                    <p class="card-text"><strong>Beneficiary Name:</strong> <?php echo $story['beneficiary_name']; ?></p>
                    <p class="card-text"><strong>Beneficiary Phone:</strong> <?php echo $story['beneficiary_phone']; ?></p>
                    <p class="card-text"><strong>Amount Needed:</strong> <?php echo $story['amount_needed']; ?></p>
                    <p class="card-text"><?php echo $story['description']; ?></p>

                    <h3 class="mt-3">Make a Donation</h3>
                    <?php if (isset($donation_submitted)) { ?>
                        <p class="text-success">Your donation has been submitted. Thank you for your support!</p>
                    <?php } else { ?>
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?story_id=' . $story_id; ?>" method="POST">
                            <?php if (isset($donation_error)) { ?>
                                <p style="color: red;"><?php echo $donation_error; ?></p>
                            <?php } ?>
                            <div class="form-group">
                                <label for="donation_amount">Donation Amount:</label>
                                <input type="number" id="donation_amount" name="donation_amount" class="form-control" required min="1" max="<?php echo $story['amount_needed']; ?>">
                            </div>
                            <input type="submit" name="donate" value="Donate" class="btn btn-primary">
                        </form>
                    <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <p>Story not found.</p>
        <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
