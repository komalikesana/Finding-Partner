<?php
$connection = mysqli_connect("localhost", "root", "", "team");

if (!$connection) {
    die("Could not connect: " . mysqli_connect_error());
}

// Create the 'teams' table
$query_create_teams_table = "
CREATE TABLE IF NOT EXISTS teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(255) NOT NULL,
    created_by VARCHAR(255) NOT NULL,
    FOREIGN KEY (created_by) REFERENCES client(username)
);
";

if (mysqli_query($connection, $query_create_teams_table)) {
    echo "Teams table created successfully.<br>";
} else {
    echo "Error creating teams table: " . mysqli_error($connection) . "<br>";
}

// Create the 'team_details' table
$query_create_team_details_table = "
CREATE TABLE IF NOT EXISTS team_details (
    team_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT,
    member_username VARCHAR(255),
    FOREIGN KEY (team_id) REFERENCES teams(team_id),
    FOREIGN KEY (member_username) REFERENCES client(username)
);
";

if (mysqli_query($connection, $query_create_team_details_table)) {
    echo "Team Details table created successfully.<br>";
} else {
    echo "Error creating team details table: " . mysqli_error($connection) . "<br>";
}

// Create the 'team_requests' table
$query_create_team_requests_table = "
CREATE TABLE IF NOT EXISTS team_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_username VARCHAR(255),
    receiver_username VARCHAR(255),
    team_id INT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (sender_username) REFERENCES client(username),
    FOREIGN KEY (receiver_username) REFERENCES client(username),
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
);
";

if (mysqli_query($connection, $query_create_team_requests_table)) {
    echo "Team Requests table created successfully.<br>";
} else {
    echo "Error creating team requests table: " . mysqli_error($connection) . "<br>";
}

// Create the 'connections_table' (for managing connections between users)
$query_create_connections_table = "
CREATE TABLE IF NOT EXISTS connections_table (
    connection_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_username VARCHAR(255),
    receiver_username VARCHAR(255),
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (sender_username) REFERENCES users(username),
    FOREIGN KEY (receiver_username) REFERENCES users(username)
);
";

if (mysqli_query($connection, $query_create_connections_table)) {
    echo "Connections table created successfully.<br>";
} else {
    echo "Error creating connections table: " . mysqli_error($connection) . "<br>";
}

mysqli_close($connection);
?>
