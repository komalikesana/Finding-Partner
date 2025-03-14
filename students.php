<?php
// Connect to the MySQL database
$connection = mysqli_connect("localhost", "root", "", "team");

if (!$connection) {
    die("Could not connect: " . mysqli_connect_error());
}

// Create the team_members table if it doesn't exist, including the 'username' and 'availability' columns
$sql = "CREATE TABLE IF NOT EXISTS team_members (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    profile VARCHAR(100) NOT NULL,
    skills VARCHAR(200) NOT NULL,
    college VARCHAR(100) NOT NULL,
    year_of_study INT NOT NULL,
    availability ENUM('weekend', 'weekday', 'anytime') NOT NULL
)";

if (mysqli_query($connection, $sql)) {
    echo "Table created successfully<br>";
    
    // Insert team member details into the table, including the 'username' and 'availability' columns
    $insertQuery = "INSERT INTO team_members (name, username, profile, skills, college, year_of_study, availability) VALUES
    ('Alice Johnson', 'alice.j', 'Frontend Developer', 'HTML, CSS, JavaScript, React', 'SRM University', 3, 'anytime'),
    ('Bob Smith', 'bob.smith', 'Data Analyst', 'Python, SQL, Excel', 'IIT Delhi', 2, 'weekend'),
    ('Catherine Lee', 'catherine.l', 'Java Framework Specialist', 'Java, Spring, Hibernate', 'NIT Trichy', 4, 'weekday'),
    ('David Wilson', 'david.w', 'AI Researcher', 'Python, TensorFlow, NLP', 'BITS Pilani', 3, 'anytime'),
    ('Eva Brown', 'eva.brown', 'Cybersecurity Specialist', 'Network Security, Cryptography', 'VIT Chennai', 2, 'weekend'),
    ('Frank Green', 'frank.green', 'Cloud Engineer', 'AWS, Azure, Docker', 'Anna University', 4, 'anytime'),
    ('Grace Miller', 'grace.miller', 'Machine Learning Engineer', 'Python, Scikit-learn, Pandas', 'SRM University', 3, 'weekday'),
    ('Henry Davis', 'henry.davis', 'Backend Developer', 'Node.js, MongoDB, Express', 'IIT Kharagpur', 2, 'anytime'),
    ('Isla White', 'isla.white', 'UI/UX Designer', 'Figma, Adobe XD, CSS', 'NIT Surathkal', 1, 'weekend'),
    ('Jack Taylor', 'jack.taylor', 'Game Developer', 'Unity, C#, Unreal Engine', 'BITS Goa', 4, 'weekday'),
    ('Karen Anderson', 'karen.anderson', 'Frontend Developer', 'React, JavaScript, CSS', 'Amity University', 3, 'anytime'),
    ('Liam Moore', 'liam.moore', 'DevOps Engineer', 'Jenkins, Kubernetes, Git', 'Jadavpur University', 2, 'anytime'),
    ('Mia Thomas', 'mia.thomas', 'Full Stack Developer', 'MERN, LAMP Stack', 'SRM University', 4, 'weekday'),
    ('Noah Harris', 'noah.harris', 'Blockchain Developer', 'Solidity, Ethereum, Hyperledger', 'IIT Bombay', 3, 'anytime'),
    ('Olivia Martin', 'olivia.martin', 'AI Engineer', 'Deep Learning, TensorFlow', 'NIT Warangal', 2, 'weekend'),
    ('Patrick Young', 'patrick.young', 'Java Framework Specialist', 'Java, Spring Boot, Hibernate', 'IIT Madras', 4, 'anytime'),
    ('Quinn Martinez', 'quinn.martinez', 'Embedded Systems Engineer', 'C, C++, Microcontrollers', 'NIT Calicut', 3, 'weekday'),
    ('Sophia Robinson', 'sophia.robinson', 'Software Tester', 'Selenium, JUnit, QA', 'VIT Vellore', 1, 'weekend'),
    ('Thomas Walker', 'thomas.walker', 'Network Engineer', 'CCNA, CCNP, Networking', 'IIT Roorkee', 2, 'anytime'),
    ('Uma Scott', 'uma.scott', 'Robotics Engineer', 'ROS, MATLAB, Python', 'SRM University', 4, 'weekday')";

    if (mysqli_query($connection, $insertQuery)) {
        echo "Team member details inserted successfully";
    } else {
        echo "Error inserting team member details: " . mysqli_error($connection);
    }
} else {
    echo "Error creating table: " . mysqli_error($connection);
}

// Close the database connection
mysqli_close($connection);
?>
