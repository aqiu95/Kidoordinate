<?php

include'global.php';
include'header.php';

//logged in and verified
?>



<!DOCTYPE html>
<html lang="en">
    <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <link rel="stylesheet" href="css/connect.css">
    <link rel="icon" type="image/png" href="images/icon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">

    <title>Kidoordinate - Requests</title>
    </head>

    <body>

        <div>
            <a id="top"></a>
            <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
                <a class="navbar-brand" href="./dashboard.php">
                    <img src="images/logo.png" style="width:25%">
                </a>      
                <div class="navbar-nav" id="navLinks">
                     <a class="nav-item nav-link navbar-text navlink" href="./dashboard.php"><img src="http://icons.iconarchive.com/icons/paomedia/small-n-flat/1024/calendar-icon.png" width="35px" height="35px"></a>
                    <a class="nav-item nav-link navbar-text navlink" href="./connect.php"><img src="https://image.flaticon.com/icons/png/128/109/109859.png" width="35px" height="35px"></a>
                    <a class="nav-item nav-link navbar-text navlink" href="./messages.php"><img src="http://images.apusapps.com/src/icon-clear-msg-notification.png" width="35px" height="35px"></a>
                    <a class="nav-item nav-link navbar-text navlink" href="./requests.php"><img src="https://d30y9cdsu7xlg0.cloudfront.net/png/157558-200.png" width="35px" height="35px"></a>
                    <a class="nav-item nav-link navbar-text navlink" href="./account.php"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/OOjs_UI_icon_advanced.svg/2000px-OOjs_UI_icon_advanced.svg.png" width="35px" height="35px"></a>
                     <a class="nav-item nav-link navbar-text navlink" href="./logout.php"><img src="https://cdn2.iconfinder.com/data/icons/large-home-icons/256/Exit_delete_close_remove_door_logout_out.png" width="35px" height="35px"></a>
                </div>
            </nav>
        </div>


    <h1 align="center">Messages</h1>
        <div class="container text-center">
<?php

if(isset($_GET['action']) && $_GET['action'] == 'send' && isset($_GET['id'])){
    if(count(array_filter($_POST))!=count($_POST)){
        echo "Something is empty";
    }
    $stmt = $conn->prepare("SELECT firstname, lastname FROM parents WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows === 1) {
        $stmt->bind_result($firstname, $lastname);
        $stmt->fetch();
    }
    else {
        exit('Invalid ID!');
    }
    $stmt->close();
    
    if($_GET['id'] == $_SESSION['userid']){
        exit('You can\'t send a message to yourself!');
    }
    $zero = 0;
    $stmt = $conn->prepare('INSERT INTO messages (fromid, toid, content, isread) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('iisi', $_SESSION['userid'], $_GET['id'], $_POST['message'], $zero);
    $stmt->execute();
    $stmt->close();
    
    echo 'Your message has been successfully sent!';
}

elseif(isset($_GET['action']) && $_GET['action'] == 'compose' && isset($_GET['id'])){
    //check if parent id is a real id
    $stmt = $conn->prepare("SELECT firstname, lastname FROM parents WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows === 1) {
        $stmt->bind_result($firstname, $lastname);
        $stmt->fetch();
    }
    else {
        exit('Invalid ID!');
    }
    $stmt->close();
    
    if($_GET['id'] == $_SESSION['userid']){
        exit('You can\'t send a message to yourself!');
    }
    
    echo 'Composing a message to: ' . $firstname . ' ' . $lastname . '<br/>
    <form id="compose" method="post" action="messages.php?action=send&id=' . $_GET['id'] . '">
    <label for="message">Message</label>
          <textarea rows="4" cols="50" id="message" name="message" required>
Message
</textarea><br/><input type="submit">Submit</button>
      </form>';
    
}
else {
    $read = 0;
    echo 'Unread messages:<br/><hr>';
    //then find unread messages
    $stmt = $conn->prepare("SELECT fromid, content FROM messages WHERE toid = ? AND isread = ?");
    $stmt->bind_param("ii", $_SESSION['userid'], $read);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0) {
        $stmt->bind_result($fromparentid, $content); 
        while ($stmt->fetch()) {
            //find from parent name
            $stmt2 = $conn->prepare("SELECT firstname, lastname FROM parents WHERE id = ?");
            $stmt2->bind_param("i", $fromparentid);
            $stmt2->execute();
            $stmt2->store_result();
            if($stmt2->num_rows === 1) {
                $stmt2->bind_result($fromparentfirstname, $fromparentlastname);
                $stmt2->fetch();
            }
            $stmt2->close();

            echo 'From: ' . $fromparentfirstname . ' ' . $fromparentlastname . '<br/>Message:<br/>' . $content . '<br/><br/><a href="messages.php?action=compose&id=' . $fromparentid. '">Reply</a><hr>';
        }
    }
    else {
        echo 'You have no unread messages!';
    }
    $stmt->close();
    echo '<br/><br/>';
    //find read messages
    echo 'Messages:<br/><hr>';
    $read = 1;
    $stmt = $conn->prepare("SELECT fromid, content FROM messages WHERE toid = ? AND isread = ?");
    $stmt->bind_param("ii", $_SESSION['userid'], $read);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0) {
        $stmt->bind_result($fromparentid, $content); 
        while ($stmt->fetch()) {
            //find from parent name
            $stmt2 = $conn->prepare("SELECT firstname, lastname FROM parents WHERE id = ?");
            $stmt2->bind_param("i", $fromparentid);
            $stmt2->execute();
            $stmt2->store_result();
            if($stmt2->num_rows === 1) {
                $stmt2->bind_result($fromparentfirstname, $fromparentlastname);
                $stmt2->fetch();
            }
            $stmt2->close();

            echo 'From: ' . $fromparentfirstname . ' ' . $fromparentlastname . '<br/>Message:<br/>' . $content . '<br/><br/><a href="messages.php?action=compose&id=' . $fromparentid. '">Reply</a><hr>';
        }
    }
    else {
        echo 'You have no messages!';
    }
    $stmt->close();
    echo '<br/><br/>';
    //find sent messages
    echo 'Sent Messages:<br/><hr>';
    $stmt = $conn->prepare("SELECT toid, content FROM messages WHERE fromid = ?");
    $stmt->bind_param("i", $_SESSION['userid']);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0) {
        $stmt->bind_result($toparentid, $content); 
        while ($stmt->fetch()) {
            //find to parent name
            $stmt2 = $conn->prepare("SELECT firstname, lastname FROM parents WHERE id = ?");
            $stmt2->bind_param("i", $toparentid);
            $stmt2->execute();
            $stmt2->store_result();
            if($stmt2->num_rows === 1) {
                $stmt2->bind_result($toparentfirstname, $toparentlastname);
                $stmt2->fetch();
            }
            $stmt2->close();

            echo 'To: ' . $toparentfirstname . ' ' . $toparentlastname . '<br/>Message:<br/>' . $content . '<br/><hr>';
        }
    }
    else {
        echo 'You have no sent messages!';
    }
    $stmt->close();
}
?>
            </div>
<div class="container text-center">
        <small class="text-muted"><a href="#top">Back to Top</a></small>
        </div>

</body>
</html>