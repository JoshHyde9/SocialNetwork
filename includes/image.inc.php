<?php
class Image
{
    public static function uploadImage($formName, $query, $params)
    {
        $image = base64_encode(file_get_contents($_FILES[$formName]['tmp_name']));
        $options = array('http' => array(
            'method' => "POST",
            'header' => "Authorization: Bearer 14fb05c9e5c6b219d596370c5e4759af8c738a6d\n" .
            "Content-Type: application/x-www-form-urlencoded",
            "content" => $image,
        ));
        $context = stream_context_create($options);
        $imgurURL = "https://api.imgur.com/3/image";

        if ($_FILES[$formName]['size'] > 1048576) {
            die('Image is too big!, must be 10MB or less');
        }

        $res = file_get_contents($imgurURL, false, $context);
        $res = json_decode($res);

        $preParams = array($formName => $res->data->link);

        $params = $preParams + $params;

        DB::query($query, $params);
    }
}
