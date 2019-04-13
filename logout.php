<?php
include 'classes/db.php';
?>

<h1>Logout of your account</h1>
<p>Are you sure you want to logout of your account?</p>
<form action="logout.php" method="post">
<input type="checkbox" name="alldevices"> Logout all devices?
<input type="submit" name="comfirm" value="Confirm">
</form>
