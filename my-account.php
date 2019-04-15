<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';

$showTimeline = false;

if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
} else {
    echo due('Not logged in!');
}

if (isset($_POST['uploadprofileimg'])) {
    $image = base64_encode(file_get_contents($_FILES['profileimg']['tmp_name']));
    $options = array('http' => array(
        'method' => "POST",
        'header' => "Authorization: Bearer 14fb05c9e5c6b219d596370c5e4759af8c738a6d\n" .
        "Content-Type: application/x-www-form-urlencoded",
        "content" => $image,
    ));
    $context = stream_context_create($options);
    $imgurURL = "https://api.imgur.com/3/image";

    if ($_FILES['profileimg']['size'] > 1048576) {
        die('Image is too big!, must be 10MB or less');
    }

    $res = file_get_contents($imgurURL, false, $context);
    $res = json_decode($res);

    DB::query("UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':profileimg' => $res->data->link, ':userid' => $userid));
}
?>

<title>My Account</title>

<h1>My Account</h1>

<form action="my-account.php" method="post" enctype="multipart/form-data">
    Upload a profile image:
    <input type="file" name="profileimg">
    <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>
