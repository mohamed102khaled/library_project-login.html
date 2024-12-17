<?php
session_start();
require 'db.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: profile.html');
    exit;
}

// Fetch user information from the session
$user = $_SESSION['user'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    // Update user information in the database
    $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ?, email = ?, phone = ?, password = ? WHERE id = ?");
    $stmt->execute([$name, $username, $email, $phone, $password, $user['id']]);

    // Update the session data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Redirect to the home page after successful update
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5fff3;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .profile-container {
      background-color: #fff;
      padding: 20px;
      padding-left: 20px;
      padding-right: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .profile-img {
      text-align: center;
      margin-bottom: 20px;
    }

    .profile-img img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      display: block;
      width: 100%;
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px;
      font-size: 16px;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="profile-container">
    <div class="profile-img">
      <img src="photos/login_logo.jpeg" alt="Profile Picture">
    </div>
    <form method="POST">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

      <label for="phone">Phone:</label>
      <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" placeholder="Enter new password (leave empty to keep current)">

      <button type="submit">Save</button>
    </form>
  </div>
</body>
</html>
