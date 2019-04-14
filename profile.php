<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';
include 'includes/post.inc.php';

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
            Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
        }

        if (isset($_GET['postid'])) {
            if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $_GET['postid'], ':userid' => $followerid))) {
                DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid' => $_GET['postid']));
                DB::query('INSERT INTO post_likes VALUES (null, :postid, :userid)', array(':postid' => $_GET['postid'], ':userid' => $followerid));
            } else {
                DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid' => $_GET['postid']));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array('postid' => $_GET['postid'], ':userid' => $followerid));
            }
        }

        $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid' => $userid));
        $posts = "";

        foreach ($dbposts as $p) {
            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $p['id'], ':userid' => $followerid))) {
                $posts .= htmlspecialchars($p['body']) . "
                <form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
                <input type='submit' name='like' value='Like'>
                <span>" . $p['likes'] . " Likes</span>
                </form>
                <hr /> <br />";
            } else {
                $posts .= htmlspecialchars($p['body']) . "
                <form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
                <input type='submit' name='unlike' value='Unlike'>
                <span>" . $p['likes'] . " Likes</span>
                </form>
                <hr /> <br />";
            }
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
