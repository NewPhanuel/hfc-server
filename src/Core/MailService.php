<?php
declare(strict_types=1);

namespace DevPhanuel\Core;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private const SERVER_EMAIL = "noreply@hfc-server.com";
    private const SERVER_NAME = "hfc-server";
    private const VERIFICATION_MSG = "Your verification code is: ";

    public static function generateSixDigits()
    {
        return random_int(100000, 999999);
    }

    public static function sendCode(string $email, bool $isOTP = false): int|false
    {
        $code = self::generateSixDigits();
        $mail = new PHPMailer(true);
        $optMsg = "
        Hi,<br><br>
        We received a request to change the password for your account. Please use the following One-Time Password (OTP) to complete the process:<br><br>
        <h2>$code</h2><br><br>
        This OTP is valid for 10 minutes. If you did not request a password change, please ignore this email or contact our support team immediately.<br><br>
        Thank you,<br>
        The Happy Family Chapel Team
        ";
        $verificationMsg = "
        Hi,<br><br>
        Thank you for signing up with Happy Family Chapel. To complete your registration, please use the following verification code:<br><br>
        <h2>$code</h2><br><br>
        This code is valid for 10 minutes. If you did not sign up for this account, please ignore this email or contact our support team.<br><br>
        Thank you,<br>
        The Happy Family Chapel Team
        ";

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // STARTTLS;
            $mail->Port = (int) $_ENV['SMTP_PORT'];
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASS'];

            $mail->setFrom(self::SERVER_EMAIL, self::SERVER_NAME);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $isOTP ? 'Your OTP for Password Change Request' : 'Verify Your Email Address';
            $mail->Body = $isOTP ? $optMsg : $verificationMsg;
            $mail->send();

            return $code;
        } catch (Exception $e) {
            return false;
        }
    }
}