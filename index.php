<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';
include 'includes/post.inc.php';
$showTimeline = false;

if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
    $showTimeline = true;
} else {
    echo 'Not Logged in!';
}

if (isset($_GET['postid'])) {
    Post::likePost($_GET['postid'], $userid);
}

$followingPosts = DB::query('SELECT posts.id, posts.body, posts.likes, users.username FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = 13
ORDER BY posts.likes DESC;');

foreach ($followingPosts as $post) {
    echo $post['body'] . " ~ " . $post['username'] . "";
    echo "<form action='index.php?&postid=" . $post['id'] . "' method='post'>";
    if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $post['id'], ':userid' => $userid))) {
        echo "<input type='submit' name='like' value='Like'>";
    } else {
        echo "<input type='submit' name='unlike' value='Unlike'>";
    }
    echo "<span>" . $post['likes'] . " Likes</span>
            </form>
             <hr /> <br />";
}
