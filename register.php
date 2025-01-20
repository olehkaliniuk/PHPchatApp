<?php

session_start();
include("db.php");

// if(isset($_SESSION['username'])){
//     header("Location: chat.php");
//     exit();
// }


if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST["username"];
    $password = $_POST["password"];
}


//check if username already exists

$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s",$username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $error= "Username already exists";
}else{
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $haschedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("ss",$username, $haschedPassword);
    if($stmt->execute()){
        $_SESSION["username"] = $username;
        header("Location: login.php");
        exit;
    }else{
        $error = "Registration failed";
    }
    $stmt->close();

}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="container">
        <div class="lr">
        <h1>Register</h1>


        <form method="POST">
            <input type="text" name="username" id="username" placeholder="username" required> <br>
            <input type="text" name="password" id="password" placeholder="password" required> <br>
            <button type="submit">Register</button>
        </form>
        <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
        <p>Already have an account? <a href="login.php">Login here</a>  </p>
        </div>

    </div>
    
</body>
</html>