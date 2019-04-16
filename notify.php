<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';
include 'includes/post.inc.php';
include 'includes/image.inc.php';

if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
} else {
    echo 'Not logged in!';
}
echo "<h1>Notifications</h1>";
if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid' => $userid))) {
    $notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid' => $userid));

    foreach ($notifications as $n) {
        if ($n['type'] = 1) {
            $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid' => $n['sender']))[0]['username'];

            if ($n['extra'] == "") {
                echo "You have recieved a notifcation! <hr />";
            } else {
                $extra = json_decode($n['extra']);

                echo $senderName . " Mentioned you in a post! - " . $extra->postbody . "<hr />";
            }
        } else if ($n['type'] == 2) {
            $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid' => $n['sender']))[0]['username'];
            echo $senderName . "Liked your post! <hr />";
        }
    }
}
