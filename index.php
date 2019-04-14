<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';
$showTimeline = false;

if (Login::isLoggedIn()) {
    echo Login::isLoggedIn();
    $showTimeline = true;
} else {
    echo 'Not Logged in!';
}

$followingPosts = DB::query('SELECT posts.body, posts.likes, users.username FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = 13
ORDER BY posts.likes DESC;');

foreach ($followingPosts as $post) {
    echo $post['body'] . " ~ " . $post['username'] . "<hr />";
}
