<?php
session_start();
require 'db.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: index1111.html');
    exit;
}

// Fetch user information from the session
$user = $_SESSION['user'];

// Example of dynamic data retrieval: Replace these queries with actual database logic
// Fetch library statistics
$stmt = $pdo->prepare("SELECT COUNT(*) AS student_count FROM users WHERE role = 'student'");
$stmt->execute();
$studentCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) AS staff_count FROM users WHERE role = 'staff'");
$stmt->execute();
$staffCount = $stmt->fetchColumn();

// Dummy data for visitors (Replace with actual logic)
$visitorCount = 21; // Example static value
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Library</title>
    <link rel="stylesheet" href="home_styles1.css">
    <style>
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        header {
            background: #003366;
            color: white;
            padding: 10px 20px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-container .logo {
            display: flex;
            align-items: center;
        }

        .header-container .logo img {
            margin-right: 10px;
        }

        .header-container .search-bar {
            display: flex;
        }

        .header-container .search-bar input {
            padding: 5px;
            font-size: 1rem;
            border: none;
            border-radius: 4px 0 0 4px;
        }

        .header-container .search-bar button {
            background: #0055a5;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        nav {
            background: #0055a5;
            color: white;
        }

        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: space-around;
            margin: 0;
        }

        nav ul li {
            padding: 10px 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        .hero {
            position: relative;
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .hero video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0; /* Ensure the video is behind other content */
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
            z-index: 1;
        }

        .hero h2,
        .hero p,
        .hero .search-library,
        .hero .links {
            position: relative;
            z-index: 2; /* Ensures content is above the video and overlay */
        }



        .hero input {
            padding: 10px;
            width: 300px;
            border-radius: 5px;
            border: none;
            margin-right: 10px;
        }

        .hero button {
            padding: 10px 20px;
            background: #0055a5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .announcement {
            background: #e8f4ff;
            padding: 20px;
            margin: 20px;
            border-left: 4px solid #0055a5;
        }

        .library-info {
            padding: 20px;
            background: white;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        canvas {
            display: block;
            margin: 0 auto;
            max-width: 300px;
        }

        .header-container .logo img {
            width: 60px; /* Adjust the width */
            height: auto; /* Maintain aspect ratio */
            margin-right: 10px; /* Space between the logo and text */
        }


        .logout-btn {
            background-color: #dc3545;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .hero {
            width: 60%;
            max-width: 1500px;
            height: auto;
            padding: 301px;

        }

        .logo{
            font-size: 20px;
            font-weight: bold;

        }

    </style>
</head>
<body>
    <section id="home page">
    <header>
        <div class="header-container">
        <div class="logo">
            <img src="photos/home_toplogo.png" alt="University Logo">
            <h1>ELsewedy University Of Technology</h1>
            <p> Polytechnic <br> Of Egypt</p>
        </div>

            <form method="POST" action="logout.php">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="books.php">Books</a></li>
            <li><a href="announcements.php">Announcements</a></li>
            <li><a href="purchases.php">Purchases</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <main>
    <section class="hero">
    <video autoplay muted loop>
        <source src="photos/home_background.mp4" type="video/mp4">
    </video>
    </section>


        <section class="announcement">
            <h3>New Library Search Launched</h3>
            <p>For the first time, you can search everything in one place. If you have any questions or need some help, please Contact Us</a>.</p>
        </section>

        <section class="library-info">
            <h3>Main Library</h3>
            <p>vacation hours:<br>• friday (all day) <br> • satrday (all day)<br><br>
            Welcome to the University Library
            At Elsewedy University of Technology, our library serves as the heart of academic exploration and innovation. Discover a world of knowledge through our extensive collection of books, e-resources, and research materials, designed to inspire and support your academic journey.

            Whether you're here to find your next great read, conduct research, or explore our digital resources, we’re committed to providing you with a welcoming and productive environment. Our user-friendly platform makes it easy to search, access, and manage your library activities online.

            Explore, learn, and grow with us—your gateway to a brighter academic future starts here!
            </p>
            <!-- <div class="stats">
                <p>People in the main library now:</p>
                <canvas id="libraryChart" width="400" height="400"></canvas>
                <p>Busiest times today: Based on previous visitor data</p>
            </div> -->
        </section>
        
        <!-- Include Chart.js library -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('libraryChart').getContext('2d');
            const libraryChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Students', 'Staff', 'Visitors'],
                    datasets: [{
                        label: 'Library Visitors',
                        data: [<?php echo $studentCount; ?>, <?php echo $staffCount; ?>, <?php echo $visitorCount; ?>],
                        backgroundColor: [
                            '#007bff', // Blue for Students
                            '#28a745', // Green for Staff
                            '#ffc107'  // Yellow for Visitors
                        ],
                        borderColor: [
                            '#ffffff',
                            '#ffffff',
                            '#ffffff'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script> -->
    </main>
    </section>

    <footer style="background-color: #003366; color: white; text-align: center; padding: 10px 0; position: relative; bottom: 0; width: 100%;">
    <p>&copy; <?php echo date('Y'); ?> Developed by Team: Mohamed, Rawan, Monira, Alaa. All rights reserved.</p>
    </footer>

</body>
</html>
