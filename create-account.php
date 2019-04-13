<?php
include 'classes/db.php';

if (isset($_POST['createaccount'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {

        if (strlen($username) >= 3 && strlen($username) <= 32) {
            if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    if (strlen($password) >= 6 && strlen($password) <= 60) {

                        DB::query('INSERT INTO users VALUES (null, :username, :password, :email)', array(':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT), ':email' => $email));
                        echo 'Success!';
                    } else {
                        echo 'Password must be at least 6 characters or less than 61';
                    }
                } else {
                    echo 'Please enter a valid email!';
                }
            } else {
                echo 'Username can only include characters a-z, A-Z and _';
            }
        } else {
            echo 'Username has to be greater than 3 characters and less than 33 long!';
        }
    } else {
        echo 'Username already exists!';
    }
}
?>

<h1>Register</h1>
<form action="create-account.php" method="POST">
<input type="text" name="username" value="" placeholder="Username...">
<input type="password" name="password" value="" placeholder="Password...">
<input type="email" name="email" value="" placeholder="someone@someone.com">
<input type="submit" name="createaccount" value="Create Account">
</form>
