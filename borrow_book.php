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

// Check if book ID is provided
if (!isset($_GET['book_id'])) {
    die('Book ID is required.');
}

// Fetch book details
$book_id = $_GET['book_id'];
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    die('Book not found.');
}

// Handle book borrowing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrow_date = date('Y-m-d');
    $return_date = $_POST['return_date'];

    // Insert borrowing record into the database
    $stmt = $pdo->prepare("INSERT INTO borrowings (user_id, book_id, borrow_date, return_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['id'], $book_id, $borrow_date, $return_date]);

    // Redirect to home page after successful borrowing
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Borrow Book</title>
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

    .borrow-container {
      background-color: #fff;
      padding: 20px;
      padding-left: 20px;
      padding-right: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .book-img {
      text-align: center;
      margin-bottom: 20px;
    }

    .book-img img {
      width: 100px;
      height: 150px;
      object-fit: cover;
      border-radius: 10px;
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
  <div class="borrow-container">
    <div class="book-img">
      <img src="<?php echo htmlspecialchars($book['photo_url'] ?? 'default_book.jpg'); ?>" alt="Book Cover">
    </div>
    <form method="POST">
      <h2><?php echo htmlspecialchars($book['name']); ?></h2>
      <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
      <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>

      <label for="borrow_date">Start Date:</label>
      <input type="text" id="borrow_date" name="borrow_date" value="<?php echo date('Y-m-d'); ?>" readonly>

      <label for="return_date">End Date:</label>
      <input type="date" id="return_date" name="return_date" required>

      <button type="submit">Confirm Borrowing</button>
    </form>
  </div>
</body>
</html>
