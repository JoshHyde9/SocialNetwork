<?php
include 'includes/db.inc.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {
        if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username' => $username))[0]['password'])) {
            echo 'Logged in!';

            $cstrong = true;
            $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));

            $user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $username))[0]['id'];
            DB::query('INSERT INTO login_tokens VALUES (null, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $user_id));

            // Set login token to a cookie
            setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', null, null, true);
            setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', null, null, true);
        } else {
            echo 'Incorrect Password';
        }
    } else {
        echo 'User does not exist!';
    }
}
?>
<title>Login</title>

<h1>Login to your account</h1>

<form action="login.php" method="post">
<input type="text" name="username" placeholder="Username...">
<input type="password" name="password" placeholder="Password...">
<input type="submit" name="login" value="Login">
</form>
