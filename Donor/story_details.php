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
    $story_sql = "SELECT s.*, c.name AS category_name FROM stories s
                  INNER JOIN categories c ON s.category_id = c.id
                  WHERE s.story_id = ?";
    $stmt = mysqli_prepare($conn, $story_sql);
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $story_result = mysqli_stmt_get_result($stmt);
    $story = mysqli_fetch_assoc($story_result);
    mysqli_stmt_close($stmt);

    // Handle sign agreement form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sign_agreement'])) {
        $donor_id = $_SESSION['user_id'];
        $beneficiary_id = $story['beneficiary_id'];
        $status = "pending";

        // Insert agreement request into the database
        $insert_agreement_sql = "INSERT INTO agreement_requests (story_id, donor_id, beneficiary_id, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_agreement_sql);
        mysqli_stmt_bind_param($stmt, "iiis", $story_id, $donor_id, $beneficiary_id, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $request_submitted = true;
    }
} else {
    header("Location: donor_dashboard.php");
    exit();
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

    <div class="container mt-5">
        <h2>Story Details</h2>
        <?php if (isset($story)) { ?>
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title"><?php echo $story['title']; ?></h3>
                    <p class="card-text"><strong>Category:</strong> <?php echo $story['category_name']; ?></p>
                    <p class="card-text"><?php echo $story['description']; ?></p>

                    <h3 class="mt-3">Sign Agreement</h3>
                    <?php if (isset($request_submitted)) { ?>
                        <p class="text-success">Your agreement request has been submitted. Please wait for approval.</p>
                    <?php } else { ?>
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?story_id=' . $story_id; ?>" method="POST">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="agreement" name="agreement" required>
                                    <label class="form-check-label" for="agreement">I agree to support this story and I want to make a donation. Show me details of the story.</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="sign_agreement">Sign Agreement</button>
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

