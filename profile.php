<?php
include 'classes/db.php';
include 'classes/Login.php';

if (Login::isLoggedIn()) {
    echo 'Logged in!';
    echo Login::isLoggedIn();
} else {
    echo 'Not Logged in!';
}
?>

<title>Profile</title>

<h1>Profile</h1>