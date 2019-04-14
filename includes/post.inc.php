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
}
