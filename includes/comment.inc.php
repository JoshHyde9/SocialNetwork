<?php
class Comment
{
    public static function createComment($commentBody, $postId, $userId)
    {
        if (strlen($commentBody) > 200 || strlen($commentBody) < 1) {
            die('Comment must be 200 characters long or less!');
        }

        if (DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid' => $userId))) {
            echo 'Invalid post ID';
        } else {
            DB::query('INSERT INTO comments VALUES (null, :comment, :userid, NOW(), :postid', array(':comment' => $commentBody, ':userid' => $userId, ':postid' => $postId));
        }
    }
}
