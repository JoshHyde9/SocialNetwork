<?php

require_once 'db.inc.php';
require_once 'mail.inc.php';

$db = new DB("127.0.0.1", "SocialNetwork", "root", "");
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if ($_GET['url'] == "auth") {
    } else if ($_GET['url'] == "users") {
    }
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if ($_GET['url'] == "users") {
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);
        $username = $postBody->username;
        $email = $postBody->email;
        $password = $postBody->password;

        if (!$db->query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {

            if (strlen($username) >= 3 && strlen($username) <= 32) {

                if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

                    if (strlen($password) >= 6 && strlen($password) <= 60) {

                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                            if (!$db->query('SELECT email FROM users WHERE email=:email', array(':email' => $email))) {

                                $db->query('INSERT INTO users VALUES (null, :username, :password, :email, \'0\', \'\')', array(':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT), ':email' => $email));
                                Mail::sendMail('Welcome to our social network!', 'Your account has been created.', $email);
                                echo '{ "Success": "Account successfully created" }';
                                http_response_code(200);
                            } else {
                                echo '{ "Error": "Email is already in use" }';
                                http_response_code(409);
                            }
                        } else {
                            echo '{ "Error": "Please enter a valid email" }';
                            http_response_code(409);
                        }
                    } else {
                        echo '{ "Error": "Password must be at least 6 characers and less than 61" }';
                        http_response_code(411);
                    }
                } else {
                    echo '{ "Error": "Username can only include a-z, A-Z and _" }';
                    http_response_code(409);
                }
            } else {
                echo '{ "Error": "Username has to be greater than 3 characters and less than 33 long" }';
                http_response_code(411);
            }
        } else {
            echo '{ "Error": "User already exists" }';
            http_response_code(409);
        }
    }

    if ($_GET['url'] == "auth") {
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);
        $username = $postBody->username;
        $password = $postBody->password;
        if ($db->query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {
            if (password_verify($password, $db->query('SELECT password FROM users WHERE username=:username', array(':username' => $username))[0]['password'])) {
                $cstrong = true;
                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                $user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username' => $username))[0]['id'];
                $db->query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token' => sha1($token), ':user_id' => $user_id));
                echo '{ "Token": "' . $token . '" }';
            } else {
                echo '{ "Error": "Invalied username or password" }';
                http_response_code(401);
            }
        } else {
            echo '{ "Error": "Invalied username or password" }';
            http_response_code(401);
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    if ($_GET['url'] == "auth") {
        if (isset($_GET['token'])) {
            if ($db->query("SELECT token FROM login_tokens WHERE token=:token", array(':token' => sha1($_GET['token'])))) {
                $db->query('DELETE FROM login_tokens WHERE token=:token', array(':token' => sha1($_GET['token'])));
                echo '{ "Status": "Success" }';
                http_response_code(200);
            } else {
                echo '{ "Error": "Invalid token" }';
                http_response_code(400);
            }
        } else {
            echo '{ "Error": "Malformed request" }';
            http_response_code(400);
        }
    }
} else {
    http_response_code(405);
}
