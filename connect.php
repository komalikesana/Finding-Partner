<?php
// Start session to get the logged-in user's username
session_start();

// Establish database connection
$connection = mysqli_connect("localhost", "root", "", "team");
if (!$connection) {
    die("Could not connect: " . mysqli_connect_error());
}

// Check if 'id' is passed in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input to prevent SQL injection

    // Fetch user details based on the ID
    $query = "SELECT * FROM team_members WHERE id = $id";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $receiver_username = $user['username']; // Get receiver's username
    } else {
        echo "<p>User not found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid request. No user ID provided.</p>";
    exit;
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $sender_username = $_SESSION['username']; // Use the logged-in user's username
    $message = mysqli_real_escape_string($connection, $_POST['message']);

    // Insert message into the connections table
    $insertQuery = "INSERT INTO connections_table (sender_username, receiver_username, message, status) 
                    VALUES ('$sender_username', '$receiver_username', '$message', 'pending')";
    if (mysqli_query($connection, $insertQuery)) {
        // Redirect to 'myfriends.php' after successful message submission
        header("Location: myfriend.php");
        exit; // Always call exit after a header redirect
    } else {
        echo "<p>Error: " . mysqli_error($connection) . "</p>";
    }
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connect with <?php echo htmlspecialchars($user['name']); ?></title>
<style>
    body {
        margin: 0;
        font-family: 'Arial', sans-serif;
        background: url('background.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #333;
    }

    .navbar {
        background-color: rgba(45, 137, 229, 0.9);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        height: 60px;
    }

    .nav-link {
        color: #fff;
        text-decoration: none;
        padding: 8px 12px;
        font-weight: 500;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .nav-link:hover {
        background-color: rgb(255, 255, 255);
        transform: scale(1.05);
    }

    .logo {
        width: 100px;
        height: 70px;
    }

    .nav-links {
        display: flex;
        align-items: center;
    }

    .nav-link:not(:first-child) {
        margin-left: 15px;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #f9f9f9;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    h1 {
        text-align: center;
        color: #2d89e5;
    }

    p {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #555;
    }

    textarea {
        width: 100%;
        padding: 10px;
        font-size: 1rem;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .btn-container {
        text-align: center;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 1rem;
        color: #fff;
        background-color: #2d89e5;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn:hover {
        background-color: #2168b5;
        transform: scale(1.05);
    }

    .submit-btn {
        margin-top: 10px;
        display: block;
    }
    footer {
            margin-top: 40px;
            text-align: center;
            color: #000;
            padding:30px;
            font-size: 14px;
            background-color: rgba(45, 137, 229, 0.9);
            bottom:0;
        }
</style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <img src="logo.jpg" class="logo" alt="Logo">
        <div class="nav-links">
            <a href="home.php" class="nav-link"><b>Home</b></a>
            <a href="group.php" class="nav-link"><b>Find group</b></a>
            <a href="myfriend.php" class="nav-link"><b>Connections</b></a>
            <a href="create_team.php" class="nav-link"><b>Create Teams</b></a>
            <a href="logout.php" class="nav-link"><b>Logout</b></a>
        </div>
    </nav>

<div class="container">
    <h1>Connect with <?php echo htmlspecialchars($user['name']); ?></h1>
    <p><strong>Profile:</strong> <?php echo htmlspecialchars($user['profile']); ?></p>
    <p><strong>Skills:</strong> <?php echo htmlspecialchars($user['skills']); ?></p>
    <p><strong>College:</strong> <?php echo htmlspecialchars($user['college']); ?></p>
    <p><strong>Year of Study:</strong> <?php echo htmlspecialchars($user['year_of_study']); ?></p>
    <p><strong>Availability:</strong> <?php echo htmlspecialchars($user['availability']); ?></p> <!-- Added Availability -->
    <form method="POST">
        <textarea name="message" placeholder="Enter your message here..." required></textarea>
        <button type="submit" class="btn submit-btn">Send Message</button>
    </form>
    <div class="btn-container">
        <a href="home.php" class="btn">Back to Home</a>
    </div>
</div>
<footer>
        &copy; <?= date('Y') ?> Team Finder. All rights reserved.
    </footer>
</body>
</html>
