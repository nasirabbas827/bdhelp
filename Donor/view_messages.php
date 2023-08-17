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

// Delete a message
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $message_id = $_GET['delete'];

    $sql_delete = "DELETE FROM messages WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $message_id);
    $stmt_delete->execute();
    $stmt_delete->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Messages</title>
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
    <h2 class="text-center">Your Messages</h2>
    <?php
    $user_id = $_SESSION["user_id"];
    $sql_user_messages = "SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ?";
    $stmt_user_messages = $conn->prepare($sql_user_messages);
    $stmt_user_messages->bind_param("ii", $user_id, $user_id);
    $stmt_user_messages->execute();
    $result_user_messages = $stmt_user_messages->get_result();

    while ($row = $result_user_messages->fetch_assoc()) {
        echo '<div class="card mb-3">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">Message</h5>';
        echo '<p class="card-text">' . $row['message_text'] . '</p>';
        if ($row['reply_text']) {
            echo '<h6 class="card-subtitle mb-2 text-muted">Admin\'s Reply</h6>';
            echo '<p class="card-text">' . $row['reply_text'] . '</p>';
        }
        echo '<a href="view_messages.php?delete=' . $row['id'] . '" class="btn btn-danger">Delete Message</a>';
        echo '</div>';
        echo '</div>';
    }
    $stmt_user_messages->close();
    ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
