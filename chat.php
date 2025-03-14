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

// Fetch friend's details from the team_members table
$query_friend = "SELECT name FROM team_members WHERE username = ?";
$stmt_friend = mysqli_prepare($connection, $query_friend);
mysqli_stmt_bind_param($stmt_friend, "s", $friend_username);
mysqli_stmt_execute($stmt_friend);
$result_friend = mysqli_stmt_get_result($stmt_friend);

if (mysqli_num_rows($result_friend) == 0) {
    die("The specified friend does not exist in the team_members table.");
}
$friend = mysqli_fetch_assoc($result_friend);

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = mysqli_real_escape_string($connection, $_POST['message']);

    // Insert the message into the chat_messages table
    $query_send_message = "
        INSERT INTO chat_messages (sender_username, receiver_username, message, created_at)
        VALUES (?, ?, ?, NOW())";
    $stmt_send_message = mysqli_prepare($connection, $query_send_message);
    mysqli_stmt_bind_param($stmt_send_message, "sss", $user_username, $friend_username, $message);
    mysqli_stmt_execute($stmt_send_message);

    // Return a response with the sent message
    echo json_encode([
        'sender_username' => $user_username,
        'message' => $message,
        'created_at' => date("Y-m-d H:i:s")
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($friend['name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .header .back-button {
            background-color: #007b5e;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .header .back-button:hover {
            background-color: #005c42;
        }

        h2 {
            text-align: center;
            color: #007b5e;
            font-size: 20px;
            margin-bottom: 10px;
            flex-grow: 1;
        }

        .messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px;
            background-color: #e5e5e5;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
        }

        .messages .sent {
            align-self: flex-end;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border-radius: 18px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .messages .received {
            align-self: flex-start;
            background-color: #f1f1f1;
            color: #333;
            padding: 10px 15px;
            border-radius: 18px;
            max-width: 70%;
            word-wrap: break-word;
        }

        small {
            font-size: 12px;
            color: #888;
            display: block;
            margin-top: 5px;
            text-align: right;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            resize: none;
            font-size: 14px;
            margin-bottom: 15px;
            outline: none;
            transition: all 0.3s;
        }

        textarea:focus {
            border-color: #007b5e;
            box-shadow: 0 0 5px rgba(0, 123, 94, 0.5);
        }

        button {
            background-color: #007b5e;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #005c42;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button and Title -->
        <div class="header">
            <a href="myfriend.php" class="back-button">Back</a>
            <h2>Chat with <?php echo htmlspecialchars($friend['name']); ?></h2>
        </div>

        <!-- Display Messages -->
        <div class="messages" id="messages">
            <?php
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

            while ($msg = mysqli_fetch_assoc($result_messages)) {
                $class = $msg['sender_username'] === $user_username ? 'sent' : 'received';
                echo "<div class='$class'>";
                echo htmlspecialchars($msg['message']);
                echo "<small>" . htmlspecialchars($msg['created_at']) . "</small>";
                echo "</div>";
            }
            ?>
        </div>

        <!-- Send a Message -->
        <form id="messageForm">
            <textarea name="message" id="messageInput" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageForm = document.getElementById('messageForm');
            const messageInput = document.getElementById('messageInput');
            const messagesContainer = document.getElementById('messages');

            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();  // Prevent the form from reloading the page

                const message = messageInput.value.trim();
                if (message === '') return;

                // Send the message via AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        // Create a new message div for the sent message
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add(response.sender_username === '<?php echo $user_username; ?>' ? 'sent' : 'received');
                        messageDiv.innerHTML = response.message + '<small>' + response.created_at + '</small>';
                        // Append the new message at the top
                        messagesContainer.insertBefore(messageDiv, messagesContainer.firstChild);
                        messageInput.value = '';  // Clear the input field
                    }
                };
                xhr.send('message=' + encodeURIComponent(message));  // Send the message to the server
            });

            // Poll for new messages every 3 seconds
            function fetchMessages() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_messages.php?friend_username=<?php echo $friend_username; ?>', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const messages = JSON.parse(xhr.responseText);
                        // Clear current messages
                        messagesContainer.innerHTML = '';
                        // Display new messages
                        messages.forEach(function(message) {
                            const messageDiv = document.createElement('div');
                            messageDiv.classList.add(message.sender_username === '<?php echo $user_username; ?>' ? 'sent' : 'received');
                            messageDiv.innerHTML = message.message + '<small>' + message.created_at + '</small>';
                            messagesContainer.appendChild(messageDiv);
                        });
                    }
                };
                xhr.send();
            }

            // Start polling when the page is loaded
            setInterval(fetchMessages, 3000);  // Fetch messages every 3 seconds
        });
    </script>
</body>
</html>

<?php
mysqli_close($connection);
?>
