<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, username, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $username, $email, $phone, $password]);
        echo "<script>alert('Account created successfully!'); window.location.href = 'login.html';</script>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            echo "<script>alert('Username or email already exists!'); window.location.href = 'login.html#';</script>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
