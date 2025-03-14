<?php
session_start(); 

$connection = mysqli_connect("localhost", "root", "", "team");
if (!$connection) {
    die("Could not connect: " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS client (
    Username VARCHAR(30) NOT NULL PRIMARY KEY,
    Email VARCHAR(50) NOT NULL,
    Phone_Number VARCHAR(10) NOT NULL,
    Password VARCHAR(255) NOT NULL
)";


if (mysqli_query($connection, $sql)) {
} else {
    echo "Error creating table: " . mysqli_error($connection);
}

if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $number = $_POST['phone_number'];
    $check = "SELECT * from client where Username='$name'";
    $res = mysqli_query($connection, $check);
    if (mysqli_num_rows($res) > 0) {
        echo "<script>alert('User already exists');window.location.href='login.php';</script>";
    } else {
        $insert = "INSERT INTO client(Username,Email,Phone_Number,Password) values('$name','$email','$number','$pass')";
        if (mysqli_query($connection, $insert)) {
            echo "Values inserted successfully";
            $_SESSION['username'] = $name;
            $_SESSION['email'] = $email; 
            header('Location: home.php');
            exit();
        } else {
            echo "Error: " . mysqli_error($connection);
        }
    }
}
mysqli_close($connection);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        .container1{
            border:1px solid gray;
            width:40%;
            background-color: white;
            height: 630px;;
            margin:auto;
        }
        .container{
            width:100%;
            height:auto;
            display: flex;
            margin:auto;
        }
        .div1{
            width:50%;
            text-align: center;
        }
        #div2{
            text-align: center;
        }
        button {
            width: 100%;
            cursor: pointer;
        }
        button:hover {
            background-color: lightgray;
        }
        .form{
            margin-bottom: 20px;
        }
        .input{
            margin:auto;
            display: block;
            width: 90%;
            padding: 3px;
            margin-top:5px;
            font-size: 1rem;
            line-height: 2.5;
            color: #495057;
            background-color: rgb(249, 247, 247);
            background-clip: padding-box;
            border: 1px solid black;
            border-radius: 0.25rem;
        }
        label{
            font-size:20px;
            padding:20px
        }
        .show {
            margin-left: 20px;
        }
        .forgot{
            margin-left: 230px;
            color: blue;
            text-align: right;
        }
        .button{
            margin-top:20px;
            display: inline-block;
            font-weight: 400;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            background-color:blue ;
            border: 1px solid black;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        
        }
    </style>
</head>
<body style="background-color:#495057">
    <div class="container1" style="margin-top: 70px;margin-bottom: 70px">
        <div class="container">
            <div class="div1"><a href="login.php"><button><h1>SIGN IN</h1></button></a></div>
            <div class="div1"><a href="signup.php"><button><h1>SIGN UP</h1></button></a></div>
        </div><br>
        <p style="margin-left: 20px">Please fill in this form to create an account.</p>
        <form action="signup.php" method="post">
            <div class="form">
                <label for="Username"><b>Username</b></label>
                <input type="text" class="input" name="name" placeholder="Create an Username" required>
            </div>
            <div class="form">
                <label for="email"><b>Email</b></label>
                <input type="email" class="input" name="email" id="email" placeholder="Enter Email" required>
            </div>
            <div class="form">
                <label for="phone_number"><b>Phone Number</b></label>
                <input type="tel" class="input" name="phone_number" id="number" placeholder="Enter Phone Number" required>
            </div>
            <div class="form" id="passwordSection">
                <label for="password"><b>Create Password</b></label>
                <input type="password" class="input" name="password" id="password" placeholder="Create Password" title="Password should contain at least 8 characters" required><br>
                <input type="checkbox" value="showpassword" onclick="show()" class="show">Show password
                <br>
            </div>
            <input type="submit" name="signup" value="Register" class="input" style="margin-top: 20px; color: white; background-color: blue;border-radius:25px;width:30%">
        </form>
    </div>
    <script>
        function show() {
            var createPasswordInput = document.getElementById('password');
            if (createPasswordInput.type === 'password') {
                createPasswordInput.type = 'text';
            } else {
                createPasswordInput.type = 'password';
            }
        }
    </script>
</body>
</html>
