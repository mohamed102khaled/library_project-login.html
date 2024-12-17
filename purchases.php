<?php
session_start();
require 'db.php'; // Include database connection

// Check if a book ID is passed to add to the cart
if (isset($_GET['book_id'])) {
    $book_id = (int)$_GET['book_id'];

    // Initialize the purchases cart if it doesn't exist
    if (!isset($_SESSION['purchase_cart'])) {
        $_SESSION['purchase_cart'] = [];
    }

    // Fetch book details from the database
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        // Add the book to the cart if it doesn't already exist
        if (!array_key_exists($book_id, $_SESSION['purchase_cart'])) {
            $_SESSION['purchase_cart'][$book_id] = [
                'id' => $book['id'],
                'name' => $book['name'],
                'author' => $book['author'],
                'price' => $book['price']
            ];
        }
    }

    // Redirect to the purchases page
    header('Location: purchases.php');
    exit;
}

// Handle book removal from the cart
if (isset($_GET['remove_id'])) {
    $remove_id = (int)$_GET['remove_id'];
    unset($_SESSION['purchase_cart'][$remove_id]);

    // Redirect to the purchases page
    header('Location: purchases.php');
    exit;
}

// Handle checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['purchase_cart']) && isset($_POST['checkout'])) {
    $user_id = $_SESSION['user']['id']; // Ensure the user is logged in and their ID is stored in the session

    foreach ($_SESSION['purchase_cart'] as $book) {
        $stmt = $pdo->prepare("INSERT INTO purchases (user_id, book_id, purchase_date, purchase_amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $book['id'],
            date('Y-m-d'),
            $book['price']
        ]);
    }

    // Clear the cart after successful checkout
    unset($_SESSION['purchase_cart']);

    // Redirect to a success page or reload
    header('Location: purchases.php?success=1');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5fff3;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
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
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .actions {
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 14px;
            color: white;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn-remove {
            background-color: #dc3545;
        }

        .btn-remove:hover {
            background-color: #c82333;
        }

        .success-message {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Purchases</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">Purchase completed successfully!</div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['purchase_cart'])): ?>
            <form method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total_amount = 0; ?>
                        <?php foreach ($_SESSION['purchase_cart'] as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['name']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($book['price'], 2)); ?></td>
                                <td class="actions">
                                    <a href="purchases.php?remove_id=<?php echo $book['id']; ?>" class="btn btn-remove">Remove</a>
                                </td>
                            </tr>
                            <?php $total_amount += $book['price']; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total Amount</th>
                            <th><?php echo htmlspecialchars(number_format($total_amount, 2)); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <button type="submit" name="checkout" class="btn">Checkout</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty. <a href="books.php">Go back to the books page</a>.</p>
        <?php endif; ?>
    </div>
</body>
</html>
