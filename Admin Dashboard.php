<?php
session_start();
require 'db.php';

// Ensure the user is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';
$message = '';

// Handle book management actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_book'])) {
        // Add new book
        $stmt = $pdo->prepare("INSERT INTO books (name, author, categoury, price, photo_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['author'],
            $_POST['category'],
            $_POST['price'],
            $_POST['photo_url']
        ]);
        $message = 'Book added successfully!';
    } elseif (isset($_POST['edit_book'])) {
        // Update book details
        $stmt = $pdo->prepare("UPDATE books SET name = ?, author = ?, categoury = ?, price = ?, photo_url = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['author'],
            $_POST['category'],
            $_POST['price'],
            $_POST['photo_url'],
            $_POST['id']
        ]);
        header('Location: Admin Dashboard.php'); // Redirect back to the Admin Dashboard after update
        exit;
    } elseif (isset($_POST['delete_book'])) {
        // Delete the book
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'Book deleted successfully!';
    }
}
// Handle user deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate_user'])) {
    // Deactivate the user
    $stmt = $pdo->prepare("UPDATE users SET status = 'Inactive' WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $message = 'User deactivated successfully!';
}

// Handle user activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_user'])) {
    // Activate the user
    $stmt = $pdo->prepare("UPDATE users SET status = 'Active' WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $message = 'User activated successfully!';
}




// Fetch all books
$books = $pdo->query("SELECT * FROM books ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch the book details for editing
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all books
$books = $pdo->query("SELECT * FROM books ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all borrowings
$borrowings = $pdo->query(
    "SELECT borrowings.*, users.username, books.name AS book_name FROM borrowings 
    JOIN users ON borrowings.user_id = users.id 
    JOIN books ON borrowings.book_id = books.id"
)->fetchAll(PDO::FETCH_ASSOC);

// Fetch all purchases
$purchases = $pdo->query(
    "SELECT purchases.*, users.username, books.name AS book_name FROM purchases 
    JOIN users ON purchases.user_id = users.id 
    JOIN books ON purchases.book_id = books.id"
)->fetchAll(PDO::FETCH_ASSOC);
if (isset($_POST['delete_purchase'])) {
    // Delete the purchase record
    $stmt = $pdo->prepare("DELETE FROM purchases WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $message = 'Purchase deleted successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <h1>Admin Dashboard</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <h2>Manage Books</h2>
        <?php if ($action === 'edit' && isset($book)): ?>
            <!-- Edit Book Form -->
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($book['id']); ?>">
                <div class="form-group">
                    <label for="name">Book Name</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($book['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" name="category" id="category" value="<?php echo htmlspecialchars($book['categoury']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($book['price']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="photo_url">Photo URL</label>
                    <input type="text" name="photo_url" id="photo_url" value="<?php echo htmlspecialchars($book['photo_url']); ?>">
                </div>
                <button type="submit" name="edit_book" class="btn">Update Book</button>
            </form>
        <?php else: ?>
            <!-- Add New Book Form -->
            <form method="POST">
                <div class="form-group">
                    <label for="name">Book Name</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" name="author" id="author" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" name="category" id="category" required>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" required>
                </div>
                <div class="form-group">
                    <label for="photo_url">Photo URL</label>
                    <input type="text" name="photo_url" id="photo_url">
                </div>
                <button type="submit" name="add_book" class="btn">Add Book</button>
            </form>
        <?php endif; ?>

        <h2>Books List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['id']); ?></td>
                        <td><?php echo htmlspecialchars($book['name']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['categoury']); ?></td>
                        <td>$<?php echo number_format($book['price'], 2); ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo htmlspecialchars($book['id']); ?>" class="btn">Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                <button type="submit" name="delete_book" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
                <h2>Manage Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo isset($user['status']) ? htmlspecialchars($user['status']) : 'Active'; ?></td>
                        <td>
                            <!-- Redirecting to Account Information page -->
                            <a href="account_info.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn">Edit</a>
                            
                            <!-- Change the button text and color based on user status -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <?php if (isset($user['status']) && $user['status'] === 'Inactive'): ?>
                                    <button type="submit" name="activate_user" class="btn" style="background-color: green;">Activate</button>
                                <?php else: ?>
                                    <button type="submit" name="deactivate_user" class="btn btn-danger">Deactivate</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>



        <h2>Manage Borrowings</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book ID</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowings as $borrowing): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($borrowing['username']); ?></td>
                        <td><?php echo htmlspecialchars($borrowing['book_name']); ?></td>
                        <td><?php echo htmlspecialchars($borrowing['borrow_date']); ?></td>
                        <td><?php echo htmlspecialchars($borrowing['return_date'] ?? 'Not Returned'); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $borrowing['id']; ?>">
                                <input type="date" name="return_date" value="<?php echo htmlspecialchars($borrowing['return_date']); ?>">
                                <button class="btn" name="update_borrowing">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Manage Purchases</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book ID</th>
                    <th>Purchase Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($purchase['username']); ?></td>
                        <td><?php echo htmlspecialchars($purchase['book_name']); ?></td>
                        <td><?php echo htmlspecialchars($purchase['purchase_date']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $purchase['id']; ?>">
                                <button type="submit" name="delete_purchase" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>
</body>
</html>
