<?php
class Post
{
    public static function createPost($postbody, $loggedInUserId, $profileUserId)
    {
        if (strlen($postbody) > 200 || strlen($postbody) < 1) {
            die('Post must be 200 characters long or less!');
        }

        $topics = self::getTopics($postbody);

        if (count(Notify::createNotify($postbody)) != 0) {
            foreach (Notify::createNotify($postbody) as $key => $n) {
                $s = $loggedInUserId;
                $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $key))[0]['id'];
                if ($r != 0) {
                    DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type' => $n["type"], ':receiver' => $r, ':sender' => $s, ':extra' => $n["extra"]));
                }
            }
        }

        if ($loggedInUserId == $profileUserId) {
            DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', :topics)', array(':postbody' => $postbody, ':userid' => $profileUserId, ':topics' => $topics));
        } else {
            die('Incorrect user!');
        }
    }

    public static function likePost($postid, $likerId)
    {
        if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $postid, ':userid' => $likerId))) {
            DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid' => $postid));
            DB::query('INSERT INTO post_likes VALUES (null, :postid, :userid)', array(':postid' => $postid, ':userid' => $likerId));
            Notify::createNotify("", $postid);
        } else {
            DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid' => $postid));
            DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array('postid' => $postid, ':userid' => $likerId));
        }
    }

    public static function getTopics($text)
    {
        $text = explode(" ", $text);
        $topics = "";

        foreach ($text as $word) {
            if (substr($word, 0, 1) == '#') {
                $topics .= substr($word, 1) . ",";
            }
        }
        return $topics;
    }

    public static function linkAdd($text)
    {
        $text = explode(" ", $text);
        $newstring = "";

        foreach ($text as $word) {
            if (substr($word, 0, 1) == '@') {
                $newstring .= "<a href='profile.php?username=" . substr($word, 1) . "'>" . htmlspecialchars($word) . "</a> ";
            } else if (substr($word, 0, 1) == '#') {
                $newstring .= "<a href='topics.php?topic=" . substr($word, 1) . "'>" . htmlspecialchars($word) . "</a> ";
            } else {
                $newstring .= htmlspecialchars($word) . " ";
            }
        }
        return $newstring;
    }

    public static function displayPosts($userid, $username, $loggedInUserId)
    {

        $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid' => $userid));
        $posts = "";

        foreach ($dbposts as $p) {
            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $p['id'], ':userid' => $loggedInUserId))) {

                $posts .= "<img src='" . $p['postimg'] . "'>" . self::linkAdd($p['body']) . "
                <form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
                <input type='submit' name='like' value='Like'>
                <span>" . $p['likes'] . " Likes</span>
                ";
                if ($userid == $loggedInUserId) {
                    $posts .= "<input type='submit' name='deletepost' value='Delete Post' />";
                }
                $posts .= "
                </form>
                <hr /> <br />";
            } else {
                $posts .= "<img src='" . $p['postimg'] . "'>" . self::linkAdd($p['body']) . "
                <form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
                <input type='submit' name='unlike' value='Unlike'>
                <span>" . $p['likes'] . " Likes</span>
                ";
                if ($userid == $loggedInUserId) {
                    $posts .= "<input type='submit' name='deletepost' value='Delete Post' />";
                }
                $posts .= "
                </form>
                <hr /> <br />";
            }
        }
        return $posts;
    }
    public static function createImgPost($postbody, $loggedInUserId, $profileUserId)
    {
        if (strlen($postbody) > 200) {
            die('Post must be 200 characters long or less!');
        }

        $topics = self::getTopics($postbody);

        if ($loggedInUserId == $profileUserId) {

            if (count(Notify::createNotify($postbody)) != 0) {
                foreach (Notify::createNotify($postbody) as $key => $n) {
                    $s = $loggedInUserId;
                    $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $key))[0]['id'];
                    if ($r != 0) {
                        DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type' => $n["type"], ':receiver' => $r, ':sender' => $s, ':extra' => $n["extra"]));
                    }
                }
            }

            DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', :topics)', array(':postbody' => $postbody, ':userid' => $profileUserId, ':topcs' => $topics));
            $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid' => $loggedInUserId))['0']['id'];
            return $postid;
        } else {
            die('Incorrect user!');
        }
    }
}
