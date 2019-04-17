<?php
require_once 'PHPMailer/PHPMailerAutoLoad.php';
class Mail
{
    public static function sendMail($subject, $body, $address)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '456';
        $mail->isHTML();
        $mail->Username = 'joshhyde546@gmail.com';
        $mail->Password = 'wgzvVT16cbX38g1';
        $mail->SetFrom = 'phillipbrown229@gmail.com';
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($address);

        $mail->Send();
    }
}
