<?php
session_start(); // Start the session
require 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data based on the username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if the user's status is 'Inactive'
        if ($user['status'] === 'Inactive') {
            echo "<script>alert('Your account is inactive. Please contact the admin.'); window.location.href = 'index.html';</script>";
            exit;
        }

        // If the password is correct, authenticate the user
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user; // Store user data in the session

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header('Location: Admin Dashboard.php'); // Redirect to the admin dashboard
            } else {
                header('Location: home.php'); // Redirect to the Home page
            }
            exit;
        } else {
            echo "<script>alert('Invalid username or password.'); window.location.href = 'index.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password.'); window.location.href = 'index.html';</script>";
    }
}
?>
