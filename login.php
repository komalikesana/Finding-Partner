<?php
session_start(); 
$connection = mysqli_connect("localhost", "root", "", "team");

if (!$connection) {
    die("Could not connect: " . mysqli_connect_error());
}
if (isset($_POST['login'])) {
    $name = $_POST['name'];
    $pass = $_POST['password'];

    $check_query = "SELECT * FROM client WHERE Username='$name'";
    $result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $db_password = $row['Password'];
        if ($pass === $db_password) {
            $_SESSION['username'] = $name; // Store logged-in username
            header('Location: home.php'); // Redirect to home page
            exit();
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('Username not found');</script>";
    }
}


mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .container1{
            border:1px solid gray;
            width:40%;
            background-color: white;
            height:auto;
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
    <div class="container1" style="margin-top:70px;">
        <div class="container">
            <div class="div1"><a href="login.php"><button><h1>SIGN IN</h1></button></a></div>
            <div class="div1"><a href="signup.php"><button>
            <h1>SIGN UP</h1></button></a></div>
        </div><br>
        <form action="login.php" method="post">
            <div class="form">
                <label for="name" style><b>Username</b></label>
                <input type="text" class="input" name="name" placeholder="Enter Username or Email" required>
            </div>
            <div class="form">
                <label for="password" style><b>Password</b></label>
                <input type="password" class="input" name="password" id="password" placeholder="Enter Password" title="Password should contain atleast 8 chracters" required>
            </div>
            <input type="checkbox" value="showpassword" onclick="show()" class="show">Show password
            <div class="form">
                <input type="submit" name="login" value="Login" class="input" style="margin-top: 20px; color: white; background-color: blue;border-radius:25px;width:30%">
            </div>
        </form>
        <script>
            function show(){
                var passwordInput=document.getElementById('password');
                if(passwordInput.type==='password'){
                    passwordInput.type='text';
                }
                else{
                    passwordInput.type='password';
                }
            }
        </script>
        
    </div>
</body>
</html>