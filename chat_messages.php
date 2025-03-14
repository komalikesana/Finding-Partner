<?php
$connection = mysqli_connect("localhost", "root", "", "team");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the table
$sql_create_table = "
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_username VARCHAR(50) NOT NULL,
    receiver_username VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_username) REFERENCES client(username) ON DELETE CASCADE,
    FOREIGN KEY (receiver_username) REFERENCES client(username) ON DELETE CASCADE
) ENGINE=InnoDB";

if (mysqli_query($connection, $sql_create_table)) {
    echo "Table 'chat_messages' created successfully.";
} else {
    echo "Error creating table: " . mysqli_error($connection);
}

mysqli_close($connection);
?>
