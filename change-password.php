<?php
include 'classes/db.php';
include 'classes/Login.php';

if (Login::isLoggedIn()) {
    if (isset($_POST['changepassword'])) {

        $oldpassword = $_POST['oldpassword'];
        $newpassword = $_POST['newpassword'];
        $newpasswordrepeat = $_POST['newpasswordrepeat'];
        $userid = Login::isLoggedIn();

        if (password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid' => $userid))[0]['password'])) {

            if ($newpassword == $newpasswordrepeat) {

                if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                    DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword' => password_hash($newpassword, PASSWORD_BCRYPT), ':userid' => $userid));
                    echo 'Password succcessfuly changed!';
                } else {
                    echo 'Password must be at least 6 characters and less than 61';
                }

            } else {
                echo 'Passwords do not match!';
            }

        } else {
            echo 'Incorrect old password';
        }
    }
} else {
    die('Not Logged in!');
}
?>

<h1>Change your password</h1>
<form action="change-password.php" method="post">
    <input type="password" name="oldpassword" placeholder="Current Password....">
    <input type="password" name="newpassword" placeholder="New Password...">
    <input type="password" name="newpasswordrepeat" placeholder="Repeat New Password...">
    <input type="submit" name="changepassword" value="Change Password">
</form>
