<?php
include 'classes/db.php';
include 'classes/Login.php';

if (!Login::isLoggedIn()) {
    die('Not logged in!');
}

if (isset($_POST['confirm'])) {
    if (isset($_POST['alldevices'])) {
        DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid' => Login::isLoggedIn()));
    } else {
        if (isset($_COOKIE['SNID'])) {
            DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token' => sha1($_COOKIE['SNID'])));
        }
        setcookie('SNID', '1', time() - 3600);
        setcookie('SNID_', '1', time() - 3600);
    }
}
?>

<title>Logout of your account</title>

<h1>Logout of your account</h1>
<p>Are you sure you want to logout of your account?</p>
<form action="logout.php" method="post">
<input type="checkbox" value="alldevices" name="alldevices"> Logout all devices?
<input type="submit" name="confirm" value="Confirm">
</form>
