<?php
session_start();
require 'db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? '';
$user = null;

// Fetch user details for display
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_user'])) {
        // Update user details
        $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ?, email = ?, phone = ?, role = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['username'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['role'],
            $_POST['id']
        ]);
        header('Location: Admin Dashboard.php'); // Redirect back to the Admin Dashboard after update
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5fff3;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 5px 10px;
            font-size: 12px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: red;
            color: white;
        }

        .btn-danger:hover {
            background-color: darkred;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .message {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User Information</h1>

        <?php if ($user): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" name="edit_user" class="btn">Update User</button>
            </form>
        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
