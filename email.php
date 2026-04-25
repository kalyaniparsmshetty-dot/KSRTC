<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'naveenkitageri@gmail.com';      // Your Gmail
    $mail->Password   = 'khfl qhdo wxvf hmax';         // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('naveenkitageri@gmail.com', 'Naveen');
    $mail->addAddress('naveenkitageri7@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Hi naveenkitageri7@gmail.com';
    $mail->Body    = '<b>This is a test email sent from localhost using Gmail SMTP.</b>';

    $mail->send();
    echo 'Message sent successfully';
} catch (Exception $e) {
    echo "Message failed: {$mail->ErrorInfo}";
}