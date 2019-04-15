<?php
include 'includes/db.inc.php';
include 'includes/login.inc.php';
include 'includes/post.inc.php';
include 'includes/image.inc.php';

if (isset($_GET['topic'])) {
    if (DB::query("SELECT topics FROM posts WHERE FIND_IN_SET(':topic', topics)", array(':topic' => $_GET['topic']))) {
        $posts = DB::query("SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics", array(':topic' => $_GET('topic')));

        foreach ($posts as $post) {
            print_r($post);
        }
    }
}
