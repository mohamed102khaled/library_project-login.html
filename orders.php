<?php
session_start();
require 'db.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Get the logged-in user's ID
$user = $_SESSION['user'];
$userId = $user['id'];

// Fetch all orders for the logged-in user
$stmt = $pdo->prepare("SELECT 
    purchases.id AS order_id,
    books.name AS book_name,
    books.author,
    books.categoury,
    books.photo_url,
    purchases.purchase_date,
    purchases.purchase_amount
FROM purchases
JOIN books ON purchases.book_id = books.id
WHERE purchases.user_id = ?
ORDER BY purchases.purchase_date DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all borrowings for the logged-in user
$stmt = $pdo->prepare("SELECT 
    borrowings.id AS borrowing_id,
    books.name AS book_name,
    books.author,
    books.categoury,
    books.photo_url,
    borrowings.borrow_date,
    borrowings.return_date
FROM borrowings
JOIN books ON borrowings.book_id = books.id
WHERE borrowings.user_id = ?
ORDER BY borrowings.borrow_date DESC");
$stmt->execute([$userId]);
$borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders and Borrowings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5fff3;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .book-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .no-orders {
            text-align: center;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Orders</h1>
        <?php if (empty($orders)): ?>
            <div class="no-orders">You have not placed any orders yet.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Book Cover</th>
                        <th>Book Name</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Purchase Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($order['photo_url'] ?? 'default_book.jpg'); ?>" alt="Book Cover" class="book-img"></td>
                            <td><?php echo htmlspecialchars($order['book_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['author']); ?></td>
                            <td><?php echo htmlspecialchars($order['categoury']); ?></td>
                            <td><?php echo htmlspecialchars($order['purchase_date']); ?></td>
                            <td>$<?php echo number_format($order['purchase_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="container">
        <h1>My Borrowings</h1>
        <?php if (empty($borrowings)): ?>
            <div class="no-orders">You have not borrowed any books yet.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Book Cover</th>
                        <th>Book Name</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowings as $borrowing): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($borrowing['photo_url'] ?? 'default_book.jpg'); ?>" alt="Book Cover" class="book-img"></td>
                            <td><?php echo htmlspecialchars($borrowing['book_name']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['author']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['categoury']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['borrow_date']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['return_date'] ?? 'Not Returned'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
