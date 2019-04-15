<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';
include 'includes/image.inc.php';
include 'includes/post.img.php';

$showTimeline = false;

if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
} else {
    echo due('Not logged in!');
}

if (isset($_POST['uploadprofileimg'])) {
    Image::uploadImage(DB::query('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid' => $userid)));
}
?>

<title>My Account</title>

<h1>My Account</h1>

<form action="my-account.php" method="post" enctype="multipart/form-data">
    Upload a profile image:
    <input type="file" name="profileimg">
    <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>
