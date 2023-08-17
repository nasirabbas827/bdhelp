<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] != 'Beneficiary') {
    header("Location: ../login.php");
    exit();
}

// Handle story submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $amount_needed = $_POST['amount'];
    $beneficiary_id = $_SESSION['user_id'];
    $beneficiary_name = $_SESSION['username'];
    $status = "pending";

    // Upload profile picture
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

    // Insert story into the database
    $insert_sql = "INSERT INTO stories (category_id, title, description, amount_needed, beneficiary_id, beneficiary_name, profile_picture, status) 
                   VALUES ('$category_id', '$title', '$description', '$amount_needed', '$beneficiary_id', '$beneficiary_name', '$target_file', '$status')";

    if ($conn->query($insert_sql) === TRUE) {
        $story_posted = true;
    } else {
        $story_posted = false;
        $error_message = "Error posting story: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Beneficiary Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5 mb-5">
        <h2>Welcome, <?php echo $_SESSION['username']; ?> (Beneficiary)!</h2>

        <h3>Post Your Story for Donation</h3>
        <?php if(isset($story_posted)) {
            if ($story_posted) {
                echo "<p class='text-success'>Your story has been submitted for donation. Wait for approval.</p>";
            } else {
                echo "<p class='text-danger'>$error_message</p>";
            }
        } ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" class="form-control">
                    <!-- Fetch categories from the categories table -->
                    <?php
                    $category_sql = "SELECT id, name FROM categories";
                    $category_result = $conn->query($category_sql);
                    while ($row = $category_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="amount">Amount Needed:</label>
                <input type="number" id="amount" name="amount" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Post Story</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
