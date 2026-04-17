<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ MANUAL INCLUDE (correct path)
require __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

function sendOTP($email, $otp){

    $mail = new PHPMailer(true);

    try {

        // 🔥 SMTP CONFIG (DOMAIN MAIL)
        $mail->isSMTP();
        $mail->Host       = 'mail.faithearning.in';
        $mail->SMTPAuth   = true;

        $mail->Username   = 'noreply@faithearning.in';
        $mail->Password   = '#@Kumar8877';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // 🔥 IMPORTANT FIX (HOSTING SSL ISSUE FIX)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // 🔥 DEBUG (TEMP ON)
        $mail->SMTPDebug = 0; // 👉 test ke liye 2 kar sakte ho
        $mail->Debugoutput = 'html';

        // 🔥 EMAIL SETTINGS
        $mail->setFrom('noreply@faithearning.in', 'SellCart');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = '🔐 Your OTP Code - SellCart';

        $mail->Body = "
        <div style='font-family:Arial; padding:20px;'>
            <h2 style='color:#0f172a;'>🔐 OTP Verification</h2>
            <p>Hello,</p>
            <p>Your OTP is:</p>
            <h1 style='color:#10b981;'>$otp</h1>
            <p>This OTP will expire in 5 minutes.</p>
            <hr>
            <p style='font-size:12px;color:gray;'>SellCart Team</p>
        </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        // 🔥 DEBUG OUTPUT (VERY IMPORTANT)
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}