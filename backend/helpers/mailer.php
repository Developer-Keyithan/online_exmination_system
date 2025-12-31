<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once './vendor/autoload.php';

function sendMail($to, $subject, $message, $toName = '')
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = config('app.email-host');
        $mail->SMTPAuth   = true;
        $mail->Username   = config('app.platform-email');
        $mail->Password   = config('app.email-app-password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = config('app.email-port');

        $mail->setFrom(config('app.platform-email'), config('app.email-username'));
        $mail->addAddress($to, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}
