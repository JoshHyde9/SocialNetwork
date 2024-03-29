<?php
include 'includes/db.inc.php';
include 'includes/mail.inc.php';

if (isset($_POST['forgotpassword'])) {
    $cstrong = true;
    $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));

    $email = $_POST['email'];
    $user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email' => $email))[0]['id'];
    DB::query('INSERT INTO password_tokens VALUES (null, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $user_id));
    Mail::sendMail('Forgot Password Verificaation', "<a href='http://localhost/SocialNetwork/change-password?token=$token'</a> Reset your password", $email);
    echo 'Email sent!';
}

?>

<title>Forgot Password?</title>

<h1>Forgot Password?</h1>
<form action="forgot-password.php" method="post">
    <input type="text" name="email" placeholder="Email...">
    <input type="submit" name="forgotpassword" value="Reset Password">
</form>
