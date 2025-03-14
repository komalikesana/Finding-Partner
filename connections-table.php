<?php
// Establish a connection to the database
$connection = mysqli_connect("localhost", "root", "", "team");

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to create the 'connections_table' if it doesn't already exist
$sql = "
CREATE TABLE IF NOT EXISTS connections_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_username VARCHAR(255) NOT NULL,
    receiver_username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_username) REFERENCES team_members(username) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (receiver_username) REFERENCES team_members(username) ON DELETE CASCADE ON UPDATE CASCADE
);
";

// Execute the query for 'connections_table'
if (mysqli_query($connection, $sql)) {
    echo "Table 'connections_table' created successfully or already exists.";
} else {
    echo "Error creating table: " . mysqli_error($connection);
}

// Close the database connection
mysqli_close($connection);
?>
