<?php
class Post
{
    public static function createPost($postbody, $loggedInUserId, $profileUserId)
    {
        if (strlen($postbody) > 200 || strlen($postbody) < 1) {
            die('Post must be 200 characters or less!');
        }

        if ($loggedInUserId == $profileUserId) {
            DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody' => $postbody, ':userid' => $profileUserId));
        } else {
            die('Incorrect user!');
        }
    }

    public static function likePost($postid, $likerId)
    {
        if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $postid, ':userid' => $likerId))) {
            DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid' => $postid));
            DB::query('INSERT INTO post_likes VALUES (null, :postid, :userid)', array(':postid' => $postid, ':userid' => $likerId));
        } else {
            DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid' => $postid));
            DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array('postid' => $postid, ':userid' => $likerId));
        }
    }
}
