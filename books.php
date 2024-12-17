<?php
require 'db.php'; // Include database connection
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id']; // Logged-in user ID

// Fetch categories and related books
$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt = $pdo->prepare("
    SELECT books.id, books.name AS book_name, books.categoury, books.photo_url, books.description, books.author, books.pdf_url
    FROM books 
    WHERE books.name LIKE ? OR books.categoury LIKE ?
    ORDER BY books.categoury, books.name
");
$stmt->execute(['%' . $search . '%', '%' . $search . '%']);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group books by category
$groupedBooks = [];
foreach ($books as $book) {
    $groupedBooks[$book['categoury']][] = $book;
}

// Check user's purchased and borrowed books
$purchasedBooksStmt = $pdo->prepare("SELECT book_id FROM purchases WHERE user_id = ?");
$purchasedBooksStmt->execute([$userId]);
$purchasedBooks = $purchasedBooksStmt->fetchAll(PDO::FETCH_COLUMN);

$borrowedBooksStmt = $pdo->prepare("
    SELECT book_id 
    FROM borrowings 
    WHERE user_id = ? AND (return_date IS NULL OR return_date >= CURRENT_DATE)
");
$borrowedBooksStmt->execute([$userId]);
$borrowedBooks = $borrowedBooksStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
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
            color: #333;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 50%;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #0056b3;
        }

        .category {
            margin-top: 20px;
        }

        .category h2 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            color: #007bff;
        }

        .books {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .book {
            width: calc(25% - 20px);
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            padding: 10px;
            transition: transform 0.3s ease;
        }

        .book:hover {
            transform: translateY(-5px);
        }

        .book img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .book-name {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .botton {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #28a745; /* Green background */
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .botton:hover {
            background-color: #218838; /* Darker green on hover */
            transform: translateY(-3px); /* Slightly lifts the button */
        }

        .pdf-button {
            background-color: #007bff; /* Blue button */
        }

        .pdf-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .ownership-message {
            color: #ff6f61; /* Light red for visibility */
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Search Books</h1>
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Search by book name or category" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if (empty($groupedBooks)): ?>
            <div class="no-results">No books found.</div>
        <?php else: ?>
            <?php foreach ($groupedBooks as $category => $books): ?>
                <div class="category">
                    <h2><?php echo htmlspecialchars($category); ?></h2>
                    <div class="books">
                        <?php foreach ($books as $book): ?>
                            <div class="book">
                                <img src="<?php echo htmlspecialchars($book['photo_url'] ?? 'default_book.jpg'); ?>" alt="Book Cover">
                                <div class="book-name"><?php echo htmlspecialchars($book['book_name']); ?></div>

                                <?php if (in_array($book['id'], $purchasedBooks)): ?>
                                    <p class="ownership-message">You already own this book.</p>
                                    <a href="<?php echo htmlspecialchars($book['pdf_url']); ?>" class="botton pdf-button" target="_blank">Read PDF</a>
                                <?php elseif (in_array($book['id'], $borrowedBooks)): ?>
                                    <p class="ownership-message">You are currently borrowing this book.</p>
                                    <a href="<?php echo htmlspecialchars($book['pdf_url']); ?>" class="botton pdf-button" target="_blank">Read PDF</a>
                                <?php else: ?>
                                    <a href="borrow_book.php?book_id=<?php echo $book['id']; ?>" class="botton">Borrow Book</a>
                                    <a href="purchases.php?book_id=<?php echo $book['id']; ?>" class="botton">Order Book</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
