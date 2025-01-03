<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'health_connect');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    
    // Prepare and execute SQL statement
    $stmt = $conn->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $first_name, $hashed_password);
        $stmt->fetch();
        
        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['first_name'] = $first_name;
            
            // If "Remember Me" is checked, set cookies
            if (isset($_POST['remember_me'])) {
                setcookie("user_id", $id, time() + (86400 * 30), "/"); // 30 days
                setcookie("user_name", $first_name, time() + (86400 * 30), "/");
            }
            
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No account found with that email!";
    }
    $stmt->close();
}

// Check if user is already logged in via cookies
if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_name'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['first_name'] = $_COOKIE['user_name'];
    header("Location: dashboard.php");
    exit();
}

$conn->close();
?>
