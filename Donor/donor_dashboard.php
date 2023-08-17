<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] != 'Donor') {
    header("Location: ../login.php");
    exit();
}

// Fetch all categories from the database
$categories_sql = "SELECT id, name FROM categories";
$categories_result = $conn->query($categories_sql);

// Handle search by category
if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $stories_sql = "SELECT * FROM stories WHERE category_id = ?";
    $stmt = mysqli_prepare($conn, $stories_sql);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $stories_result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    // Fetch all stories if no category is selected
    $all_stories_sql = "SELECT * FROM stories";
    $all_stories_result = $conn->query($all_stories_sql);
}
function getCategoryName($conn, $category_id) {
    $category_sql = "SELECT name FROM categories WHERE id = ?";
    $stmt = mysqli_prepare($conn, $category_sql);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $category_name);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $category_name;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
    <h3>Search Stories by Category</h3>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
        <label for="category">Select Category:</label>
        <select id="category" name="category_id" class="form-control">
            <option value="">All Categories</option>
            <?php
            while ($row = $categories_result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <h3 class="mt-4">All Stories</h3>
    <?php
    if (isset($stories_result)) {
        if (mysqli_num_rows($stories_result) > 0) {
            echo '<div class="row">';
            while ($story = mysqli_fetch_assoc($stories_result)) {
                echo '<div class="col-lg-4 col-md-6 mb-4">';
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $story['title'] . '</h5>';
                echo '<p class="card-text">' . $story['description'] . '</p>';
                echo '<a href="story_details.php?story_id=' . $story['story_id'] . '" class="btn btn-primary">See Details</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No stories found in the selected category.</p>';
        }
    } elseif (isset($all_stories_result)) {
        if (mysqli_num_rows($all_stories_result) > 0) {
            echo '<div class="row">';
            while ($story = mysqli_fetch_assoc($all_stories_result)) {
                echo '<div class="col-lg-4 col-md-6 mb-4">';
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $story['title'] . '</h5>';
                echo '<p class="card-text"><strong>Category:</strong> ' . getCategoryName($conn, $story['category_id']) . '</p>';
                echo '<p class="card-text">' . $story['description'] . '</p>';
                echo '<a href="story_details.php?story_id=' . $story['story_id'] . '" class="btn btn-primary">See Details</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No stories found.</p>';
        }
    }
    ?>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
