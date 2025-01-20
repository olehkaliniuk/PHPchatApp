<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$selectedUser = "";


if(isset($_GET['user'])){
    $selectedUser = $_GET['user'];
    $selectedUser = mysqli_real_escape_string($conn, $selectedUser);
    $showChatBox = true;

}else{
    $showChatBox = false;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="lr">
        <div class="header">
            <h1>My Account</h1>
            <a href="logout.php">Logout</a>
        </div>

        <div class="account-info">
            <div class="welcome">
                <h2>User <?php echo ucfirst($username); ?></h2>
            </div>
            <div class="userlist">
                <h2>Select a user to chat with: </h2>
                <ul>
                    <?php
                    $sql = "SELECT username FROM users WHERE username != '$username'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $user = $row["username"];
                            $user = ucfirst($user);
                            echo "<li> <a href='chat.php?user=$user'>$user</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>


       
    </div>
    <div class="rl">

    <?php if($showChatBox) : ?>
        <div class="chat-box" id="chat-box">
            <div class="chat-box-header">
                <h2><?php echo ucfirst($selectedUser); ?></h2>
                <button class="close-btn" onclick="closeChat()">✖</button>
            </div>
            <div class="chat-box-body" id="chat-box-body">
            </div>
            <form class="chat-form" id="chat-form">
                <input type="hidden" id="sender" value="<?php echo $username; ?>">
                <input type="hidden" id="receiver" value="<?php echo $selectedUser; ?>">
                <input class="inputmessage" type="text" id="message" placeholder="Type your message..." required>
                <button class="btn" type="submit">Send</button>
            </form>
        </div>

        <?php endif; ?>

        </div>

    </div>

    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    function closeChat(){
        document.getElementById("chat-box").style.display = "none"
    }


    function fetchMessages() {
            var sender = $('#sender').val();
            var receiver = $('#receiver').val();
            
            $.ajax({
                url: 'fetch_messages.php',
                type: 'POST',
                data: {sender: sender, receiver: receiver},
                success: function(data) {
                    $('#chat-box-body').html(data);
                    scrollChatToBottom();
                }
            });
        }

    function scrollChatToBottom(){
        var chatBox = $('#chat-box-body');
        chatBox.scrollTop(chatBox.prop("scrollHeight"));
    }    


    $(document).ready(function(){
        //fetch  message every 3 second
        fetchMessages();
        setInterval(fetchMessages, 3000);

    })




    $('#chat-form').submit(function(e) {
            e.preventDefault();
            var sender = $('#sender').val();
            var receiver = $('#receiver').val();
            var message = $('#message').val();

            $.ajax({
                url: 'submit_message.php',
                type: 'POST',
                data: {sender: sender, receiver: receiver, message: message},
                success: function() {
                    $('#message').val('');
                    fetchMessages(); 
                }
            });

            });
        
</script>    

</body>

</html>