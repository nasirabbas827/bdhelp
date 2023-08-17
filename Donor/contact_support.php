<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] != 'Donor') {
    header("Location: ../login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION["user_id"];

// Fetch user details from the database
$sql = "SELECT id, username, email, phone FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fetched_id, $username, $email, $phone);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION["user_id"];
    $message_text = $_POST["message"];

    $sql_insert = "INSERT INTO messages (sender_id, message_text) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("is", $sender_id, $message_text);
    $stmt_insert->execute();
    $stmt_insert->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Support</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php
include('navbar.php');
?>

<div class="container mt-5">
    <h2 class="text-center">Contact Support</h2>
    <form method="post">
        <div class="form-group">
            <label for="message">Write your message:</label>
            <textarea name="message" class="form-control" rows="5" placeholder="Write your message..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Send Message</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

