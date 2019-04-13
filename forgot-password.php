<?php
include 'classes/db.php';

if (isset($_POST['forgotpassword'])) {
    $cstrong = true;
    $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));

    $email = $_POST['email'];
    $user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email' => $email))[0]['id'];
    DB::query('INSERT INTO password_tokens VALUES (null, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $user_id));
    echo 'Email sent!';
    echo '<br />';
    echo $token;
}
?>

<h1>Forgot Password?</h1>
<form action="forgot-password.php" method="post">
    <input type="text" name="email" placeholder="Email...">
    <input type="submit" name="forgotpassword" value="Reset Password">
</form>