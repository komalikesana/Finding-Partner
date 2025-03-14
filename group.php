<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Details</title>
    <style>
         footer {
            margin-top: 40px;
            text-align: center;
            color: #000;
            padding:30px;
            font-size: 14px;
            background-color: rgba(45, 137, 229, 0.9);
            bottom:0;
        }
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
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
            color: #000;
            text-decoration: none;
            padding: 8px 12px;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 0.9rem;
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

        .search-bar {
            display: flex;
            align-items: center;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 5px;
        }

        .search-input {
            padding: 5px;
            border: none;
            outline: none;
            font-size: 1rem;
        }

        .search-button {
            background-color: #2d89e5;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-button:hover {
            background-color: #1a5fa5;
        }

        .search-icon {
            margin-right: 10px;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            font-size: 2.5rem;
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .student-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 30px;
        }

        .student-box {
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            width: 300px;
            background-color: #f0f8ff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        }

        .student-box:hover {
            background-color: #C0FFFF;
            transform: translateY(-10px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        }

        .student-box h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: black;
        }

        .student-box p {
            font-size: 1rem;
            line-height: 1.5;
            color: #555;
            margin-bottom: 10px;
        }

        .connect-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            color: #000;
            background-color: #2d89e5;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-align: center;
            text-decoration: none;
            justify-content: center;
        }

        .connect-btn:hover {
            background-color: #fff;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .student-box {
                width: 90%;
            }

            .nav-link {
                padding: 6px 10px;
                font-size: 0.8rem;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <img src="logo.jpg" class="logo">
        <div class="nav-links">
            <a href="home.php" class="nav-link"><b>Home</b></a>
            <a href="group.php" class="nav-link"><b>Find group</b></a>
            <a href="myfriend.php" class="nav-link"><b>Connections</b></a>
            <a href="create_team.php" class="nav-link"><b>Create Teams</b></a>
            <a href="logout.php" class="nav-link"><b>Logout</b></a>
            <div class="search-bar">
                <input type="text" class="search-input" id="search" placeholder="Search for a group..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="search-button" onclick="searchGroup()">
                    <span class="search-icon">&#128269;</span> Search
                </button>
            </div>
        </div>
    </nav>

    <h1>Students in Groups</h1>

    <div class="student-container">
        <?php
        $connection = mysqli_connect("localhost", "root", "", "team");
        if (!$connection) {
            die("Could not connect: " . mysqli_connect_error());
        }

        $searchQuery = '';
        if (isset($_GET['search'])) {
            // Sanitize the search term to prevent SQL injection
            $searchQuery = mysqli_real_escape_string($connection, $_GET['search']);
            // Modify the query to check for partial matches in both 'skills' and 'profile' columns
            $query = "SELECT * FROM team_members WHERE name LIKE '%$searchQuery%' OR skills LIKE '%$searchQuery%' OR profile LIKE '%$searchQuery%'";
        } else {
            // Default query to get all students if no search query is entered
            $query = "SELECT * FROM team_members";
        }

        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='student-box'>";
                echo "<h3>Name: " . htmlspecialchars($row['name']) . "</h3>";
                echo "<p>Profile: " . htmlspecialchars($row['profile']) . "</p>";
                echo "<p>Skills: " . htmlspecialchars($row['skills']) . "</p>";
                echo "<p>College: " . htmlspecialchars($row['college']) . "</p>";
                echo "<p>Year of Study: " . htmlspecialchars($row['year_of_study']) . "</p>";
                echo "<a href='connect.php?id=" . $row['id'] . "' class='connect-btn'><b>Connect</b></a>";
                echo "</div>";
            }
        } else {
            echo "<p>No students found.</p>";
        }

        mysqli_close($connection);
        ?>
    </div>
    <footer>
        &copy; <?= date('Y') ?> Team Finder. All rights reserved.
    </footer>

    <script>
        function searchGroup() {
            const searchQuery = document.getElementById('search').value;
            window.location.href = `group.php?search=${searchQuery}`;
        }
    </script>
</body>
</html>
