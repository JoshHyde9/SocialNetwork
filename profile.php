<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';
include 'includes/post.inc.php';
include 'includes/image.inc.php';
include 'includes/notify.inc.php';

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

        if (isset($_POST['deletepost'])) {
            if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid' => $_GET['postid'], ':userid' => $followerid))) {
                DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid' => $_GET['postid'], ':userid' => $followerid));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid' => $_GET['postid']));
                echo 'Post deleted!';
            }
        }

        if (isset($_POST['post'])) {
            if ($_FILES['postimg']['size'] == 0) {
                Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
            } else {
                $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                Image::uploadImage('postimg', 'UPDATE posts SET postimg=:postimg WHERE id=:postid', array(':postid' => $postid));
            }
        }

        if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
            Post::likePost($_GET['postid'], $followerid);
        }

        $posts = Post::displayPosts($userid, $username, $followerid);

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

<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
    <textarea name="postbody" cols="80" rows="8"></textarea>
    <br />Upload an image:
    <input type="file" name="postimg">
    <input type="submit" name="post" value="Post">
</form>


<div class="posts">
    <?php echo $posts; ?>
</div>
