<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect to the registration form if accessed directly
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registration.html");
    exit();
}

// Debugging: Display POST data (optional, can be removed in production)
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Google reCAPTCHA Secret Key
$secret_key = '6LfB614qAAAAAH2RDJgakZyvDiENk-lLOqPTGcG6'; // Replace with your actual secret key from Google reCAPTCHA

// Verify reCAPTCHA response
if (empty($_POST['g-recaptcha-response'])) {
    echo "Please complete the CAPTCHA";
    exit();
} else {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
    $response_keys = json_decode($response, true);
    
    if (!$response_keys["success"]) {
        echo "CAPTCHA validation failed. Please try again.";
        exit();
    }
}

// Database Connection
$conn = new mysqli('localhost', 'root', '', 'health_connect');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Sanitize Input Function
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Collect and Sanitize Form Data
$first_name = sanitize($_POST['first_name']);
$last_name = sanitize($_POST['last_name']);
$email = sanitize($_POST['email']);
$phone = sanitize($_POST['phone']);
$age = (int)sanitize($_POST['age']);
$gender = sanitize($_POST['gender']);
$blood_group = sanitize($_POST['blood_group']);
$comments = sanitize($_POST['comments']);
$country = sanitize($_POST['country']);
$password = sanitize($_POST['password']);
$confirm_password = sanitize($_POST['confirm_password']);

// Email and Password Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format";
    exit();
}

if ($password !== $confirm_password) {
    echo "Passwords do not match!";
    exit();
}

// Hash the Password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Prepare SQL Query to Insert Data into Database
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, age, gender, blood_group, comments, country, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssisssss", $first_name, $last_name, $email, $phone, $age, $gender, $blood_group, $comments, $country, $hashed_password);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['first_name'] = $first_name;
    header("Location: login.html");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close the Statement and Database Connection
$stmt->close();
$conn->close();
?>
