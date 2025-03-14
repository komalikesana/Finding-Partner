<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "team");

if (!$connection) {
    die("Could not connect: " . mysqli_connect_error());
}

// Get the logged-in user's username
if (!isset($_SESSION['username'])) {
    die("You must be logged in to access this page.");
}
$user_username = $_SESSION['username'];

// Get the friend's username
if (!isset($_GET['friend_username'])) {
    die("Friend's username is not provided.");
}
$friend_username = $_GET['friend_username'];

// Fetch messages between the logged-in user and the friend
$query_messages = "
    SELECT sender_username, message, created_at
    FROM chat_messages
    WHERE (sender_username = ? AND receiver_username = ?)
       OR (sender_username = ? AND receiver_username = ?)
    ORDER BY created_at DESC";
$stmt_messages = mysqli_prepare($connection, $query_messages);
mysqli_stmt_bind_param($stmt_messages, "ssss", $user_username, $friend_username, $friend_username, $user_username);
mysqli_stmt_execute($stmt_messages);
$result_messages = mysqli_stmt_get_result($stmt_messages);

// Prepare the messages to return as JSON
$messages = [];
while ($msg = mysqli_fetch_assoc($result_messages)) {
    $messages[] = $msg;
}

// Return messages as JSON
echo json_encode($messages);

mysqli_close($connection);
?>
