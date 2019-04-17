<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';

$showTimeline = false;
if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
} else {
    die('Not logged in');
}

if (isset($_POST['send'])) {

    if (DB::query("SELECT id FROM users WHERE id=:receiver", array(':receiver' => $_GET['receiver']))) {

        DB::query("INSERT INTO messages VALUES ('', :body, :sender, :receiver, 0)", array(':body' => $_POST['body'], ':sender' => $userid, ':receiver' => htmlspecialchars($_GET['receiver'])));
        echo "Message Sent!";
    } else {
        die('Invalid user!');
    }
}
?>

<h1>Send A Private Message</h1>

<form action="send-message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']); ?>" method="post">
<textarea name="body" id="" cols="80" rows="8"></textarea>
<input type="submit" name="send" value="Send Message">
</form>