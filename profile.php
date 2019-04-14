<?php
include 'classes/db.php';
include 'classes/Login.php';

$username = "";
$verified = false;
$isFollowing = false;

if (isset($_GET['username'])) {
    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))) {
        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['username'];
        $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['verified'];
        $followerid = Login::isLoggedIn();

        if (isset($_POST['follow'])) {
            if ($userid != $followerid) {
                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
                    if ($followerid == 13) {
                        DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid' => $userid));
                    }
                    DB::query('INSERT INTO followers VALUES (null, :userid, :followerid)', array(':userid' => $userid, ':followerid' => $followerid));
                } else {
                    echo 'Already following!';
                }
                $isFollowing = true;
            }
        }
        if (isset($_POST['unfollow'])) {

            if ($userid != $followerid) {
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
                    if ($followerid == 13) {
                        DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid' => $userid));
                    }
                    DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid));
                }
                $isFollowing = false;
            }
        }
        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
            $isFollowing = true;
        }

        if (isset($_POST['post'])) {
            $postbody = $_POST['postbody'];
            $loggedInUserId = Login::isLoggedIn();

            if (strlen($postbody) > 200 || strlen($postbody) < 1) {
                die('Post must be 200 characters or less!');
            }

            if ($loggedInUserId == $userid) {
                DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody' => $postbody, ':userid' => $userid));
            } else {
                die('Incorrect user!');
            }
        }

        $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid' => $userid));
        $posts = "";

        foreach ($dbposts as $p) {
            $posts .= htmlspecialchars($p['body']) . "<hr /> <br />";
        }

    } else {
        die('User not found!');
    }
}
?>

<title><?php echo $username; ?>'s Profile</title>

<h1><?php echo $username; ?>'s Profile<?php if ($verified) {echo ' - Verified';}?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
<?php
if ($userid != $followerid) {
    if ($isFollowing) {
        echo '<input type="submit" name="unfollow" value="Unfollow">';
    } else {
        echo '<input type="submit" name="follow" value="Follow">';
    }
}
?>
</form>

<form action="profile.php?username=<?php echo $username; ?>" method="post">
    <textarea name="postbody" cols="80" rows="8"></textarea>
    <input type="submit" name="post" value="Post">
</form>

<div class="posts">
    <?php echo $posts; ?>
</div>
