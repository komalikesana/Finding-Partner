<?php
session_start();

$connection = mysqli_connect("localhost", "root", "", "team");

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_username = $_SESSION['username']; // Use the username instead of ID

if (isset($_GET['action']) && isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
    $action = $_GET['action'];

    if ($action == 'accept') {
        // Update the status to 'accepted' for the connection
        $query_accept = "UPDATE connections_table SET status = 'accepted' WHERE id = $request_id";
        mysqli_query($connection, $query_accept);
    } elseif ($action == 'decline') {
        // Delete the connection request if declined
        $query_decline = "DELETE FROM connections_table WHERE id = $request_id";
        mysqli_query($connection, $query_decline);
    }
}

// Fetch accepted friends based on the username
$query_friends = "
    SELECT u.username, u.name 
    FROM team_members u
    JOIN connections_table c ON (u.username = c.sender_username OR u.username = c.receiver_username)
    WHERE ((c.sender_username = '$user_username' OR c.receiver_username = '$user_username') AND c.status = 'accepted')
    AND u.username != '$user_username'"; 

$result_friends = mysqli_query($connection, $query_friends);

// Fetch messages from accepted connections
$query_messages = "
    SELECT c.sender_username, c.receiver_username, c.message, c.created_at, u.name AS sender_name
    FROM connections_table c
    JOIN team_members u ON u.username = c.sender_username
    WHERE (c.receiver_username = '$user_username' OR c.sender_username = '$user_username') AND c.status = 'accepted'
    ORDER BY c.created_at DESC";

$result_messages = mysqli_query($connection, $query_messages);

// Fetch pending friend requests sent to the user
$query_pending_requests = "
    SELECT u.username, u.name, c.id AS request_id
    FROM team_members u
    JOIN connections_table c ON c.sender_username = u.username
    WHERE c.receiver_username = '$user_username' AND c.status = 'pending'";

$result_pending_requests = mysqli_query($connection, $query_pending_requests);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Friends</title>
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
            background-color:rgb(255, 255, 255);
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
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin:15px 140px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007BFF;
        }
        .section {
            margin-bottom: 30px;
        }
        .card {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-weight: bold;
        }
        .message {
            background: #f8f8f8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            position: relative; /* Ensure the timestamp is positioned relative to the message */
        }

        .message-timestamp {
            position: absolute;
            top: 5px;
            right: 10px;
            font-size: 0.8rem;
            color: #888;
        }
        .pending-request {
            background-color: #f0ad4e;
            padding: 10px;
            border-radius: 5px;
        }
                /* Container for friends */
        .friend-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        .friend-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        /* Individual friend box */
        .friend-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Increased width for better appearance */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .friend-box:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        /* Friend info (name, username) */
        .friend-info {
            flex: 1; /* Make the name and username take the available space */
        }

        .friend-name {
            font-size: 1.1rem;
            color: #007BFF; /* Blue color for name */
        }

        .friend-username p {
            font-size: 0.9rem;
            color: #666;
        }

        /* Chat button style */
        .chat-btn {
            padding: 8px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .chat-btn:hover {
            background-color: #0056b3;
        }


        .message-list, .pending-list {
            list-style-type: none;
            padding: 0;
        }
        .message-item, .pending-item {
            margin: 10px 0;
        }
        .btn {
            padding: 8px 15px;
            margin: 5px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-decline {
            background-color: #dc3545;
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
        <h2>My Friends</h2>

       <!-- Accepted Friends -->
        <div class="section">
            <h3>Accepted Friend Requests</h3>
            <?php if (mysqli_num_rows($result_friends) > 0): ?>
                <div class="friend-container">
                    <?php while ($friend = mysqli_fetch_assoc($result_friends)): ?>
                        <div class="friend-box">
                            <div class="friend-info">
                                <div class="friend-name">
                                    <strong><?php echo $friend['name']; ?></strong>
                                </div>
                                <div class="friend-username">
                                    <p><?php echo $friend['username']; ?></p>
                                </div>
                            </div>
                            <a href="chat.php?friend_username=<?php echo $friend['username']; ?>" class="btn chat-btn">Chat</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No accepted friends yet.</p>
            <?php endif; ?>
        </div>
       <!-- Messages -->
        <div class="section">
            <h3>Messages</h3>
            <?php if (mysqli_num_rows($result_messages) > 0): ?>
                <div class="message-container">
                    <?php 
                        $previous_sender = ''; // To track the sender of the previous message
                        while ($message = mysqli_fetch_assoc($result_messages)): 
                            // Check if the sender has changed to group messages
                            if ($message['sender_username'] !== $previous_sender):
                                // Display sender's name if it's a new sender
                    ?>
                        <div class="message-sender">
                            <strong><?php echo $message['sender_name']; ?>:</strong>
                        </div>
                    <?php 
                            endif; 
                            // Display the message content
                    ?>
                    <div class="message">
                        <p><?php echo $message['message']; ?></p>
                        <div class="message-timestamp">
                            <small><?php echo $message['created_at']; ?></small>
                        </div>
                    </div>
                    <?php 
                            $previous_sender = $message['sender_username']; // Update the sender to track
                        endwhile; 
                    ?>
                </div>
            <?php else: ?>
                <p>No messages yet.</p>
            <?php endif; ?>
        </div>



        <!-- Pending Friend Requests -->
        <div class="section">
            <h3>Pending Friend Requests</h3>
            <?php if (mysqli_num_rows($result_pending_requests) > 0): ?>
                <ul class="pending-list">
                    <?php while ($pending = mysqli_fetch_assoc($result_pending_requests)): ?>
                        <li class="pending-item">
                            <div class="pending-request">
                                <strong><?php echo $pending['name']; ?></strong> has sent you a friend request.
                                <a href="?action=accept&request_id=<?php echo $pending['request_id']; ?>" class="btn">Accept</a>
                                <a href="?action=decline&request_id=<?php echo $pending['request_id']; ?>" class="btn btn-decline">Decline</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No pending friend requests.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        &copy; <?= date('Y') ?> Team Finder. All rights reserved.
    </footer>
</body>
</html>

<?php
// Close the database connection
mysqli_close($connection);
?>
