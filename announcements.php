<?php
require 'db.php'; // Include database connection

// Fetch announcements from the database
$stmt = $pdo->prepare("SELECT title, content, created_at FROM announcements ORDER BY created_at DESC");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
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

        .announcement {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
        }

        .announcement h2 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #007bff;
        }

        .announcement p {
            margin: 0 0 5px;
            color: #555;
        }

        .announcement .date {
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Announcements</h1>

        <?php if (empty($announcements)): ?>
            <p>No announcements available at the moment.</p>
        <?php else: ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement">
                    <h2><?php echo htmlspecialchars($announcement['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                    <div class="date">
                        Sent on: <?php echo date('F j, Y, g:i A', strtotime($announcement['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
