<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "team");

if (!$connection) {
    die("Could not connect: " . mysqli_connect_error());
}

if (!isset($_SESSION['username'])) {
    die("You must be logged in to create a team.");
}

$user_username = $_SESSION['username'];

// Fetch friends who have accepted the request from connections_table
$query_friends = "
    SELECT receiver_username AS friend_username FROM connections_table 
    WHERE sender_username = ? AND status = 'accepted'
    UNION
    SELECT sender_username AS friend_username FROM connections_table 
    WHERE receiver_username = ? AND status = 'accepted'
";

$stmt_friends = mysqli_prepare($connection, $query_friends);
mysqli_stmt_bind_param($stmt_friends, "ss", $user_username, $user_username);
mysqli_stmt_execute($stmt_friends);
$result_friends = mysqli_stmt_get_result($stmt_friends);

// Initialize the success flag
$team_created = false;

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $team_name = mysqli_real_escape_string($connection, $_POST['team_name']);
    $members = isset($_POST['members']) ? $_POST['members'] : []; // Get selected members

    // Create the team
    $query_create_team = "INSERT INTO teams (team_name, created_by) VALUES (?, ?)";
    $stmt_create_team = mysqli_prepare($connection, $query_create_team);
    mysqli_stmt_bind_param($stmt_create_team, "ss", $team_name, $user_username);
    mysqli_stmt_execute($stmt_create_team);

    $team_id = mysqli_insert_id($connection);  // Get the ID of the created team

    // Insert members into team_details table
    foreach ($members as $member) {
        if ($member != $user_username) {  // Don't insert yourself
            $query_add_member = "INSERT INTO team_details (team_id, member_username) VALUES (?, ?)";
            $stmt_add_member = mysqli_prepare($connection, $query_add_member);
            mysqli_stmt_bind_param($stmt_add_member, "is", $team_id, $member);
            mysqli_stmt_execute($stmt_add_member);
        }
    }

    // Send team request to each member
    foreach ($members as $member) {
        if ($member != $user_username) {  // Don't send request to yourself
            $query_send_request = "INSERT INTO team_requests (sender_username, receiver_username, team_id) VALUES (?, ?, ?)";
            $stmt_send_request = mysqli_prepare($connection, $query_send_request);
            mysqli_stmt_bind_param($stmt_send_request, "ssi", $user_username, $member, $team_id);
            mysqli_stmt_execute($stmt_send_request);
        }
    }

    $team_created = true;  // Set flag to true when team is successfully created
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team</title>
    <style>
       .navbar {
            background-color: rgba(45, 137, 229, 0.9);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            height: 60px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .nav-link {
            color: black;
            font-family: Arial, sans-serif;
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

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-link:not(:first-child) {
            margin-left: 15px;
        }

        .logo {
            width: 100px;
            height: 70px;
        }

        footer {
            margin-top: 40px;
            text-align: center;
            color: #000;
            padding: 30px;
            font-size: 14px;
            background-color: rgba(45, 137, 229, 0.9);
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin-top: 80px;
            margin-bottom: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            text-align: center;
            color: #007b5e;
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-bottom: 15px;
        }

        .checkbox-group label {
            margin-bottom: 5px;
        }

        .checkbox-group input {
            margin-right: 10px;
        }

        button {
            background-color: #007b5e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #005c42;
        }

        .view-teams-btn {
            background-color: #007b5e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-top: 15px;
            text-align: center;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .view-teams-btn:hover {
            background-color: #005c42;
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
        </div>
    </nav>
<div class="container">
    <h2>Create a Team</h2>

    <!-- Form to create team -->
    <form method="POST" action="create_team.php">
        <input type="text" name="team_name" class="input-field" placeholder="Team Name" required><br>

        <div class="checkbox-group">
            <label for="members">Add members:</label>
            <?php while ($row = mysqli_fetch_assoc($result_friends)): ?>
                <label>
                    <input type="checkbox" name="members[]" value="<?= $row['friend_username'] ?>"> 
                    <?= $row['friend_username'] ?>
                </label>
            <?php endwhile; ?>
        </div><br>

        <button type="submit">Create Team</button>
    </form>

    <!-- Button to view teams -->
    <form action="view_teams.php" method="get">
        <button class="view-teams-btn" type="submit">View My Teams</button>
    </form>
</div>

<?php if ($team_created): ?>
    <script>
        // Display success message and reload the page
        alert("Team created successfully and requests sent!");
        window.location.href = "create_team.php";  // Redirect to the same page
    </script>
<?php endif; ?>

</body>
</html>
