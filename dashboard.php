<?php
session_start();

// Check for session or cookies
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_name'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['first_name'] = $_COOKIE['user_name'];
    } else {
        header("Location: login.html");
        exit();
    }
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'health_connect');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, email, phone, age, gender, blood_group, comments, country FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $phone, $age, $gender, $blood_group, $comments, $country);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1>
        <div class="user-details">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($age); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
            <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($blood_group); ?></p>
            <p><strong>Comments:</strong> <?php echo htmlspecialchars($comments); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($country); ?></p>
        </div>
        <p><a href="logout.php" class="logout-button">Logout</a></p>
    </div>
</body>
</html>