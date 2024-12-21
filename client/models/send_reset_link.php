<?php

include '../../db/config.php'; // Your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expires = time() + 3600;

        // Insert the reset token and expiry time into your password_resets table
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);

        $resetLink = "https://www.pegasustravelers.com/reset_password.php?token=$token";
        $userName = htmlspecialchars($user['name']); // Safely escape the user's name

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
              // Server settings
              include '../../smtp/config.php';



    // Recipients
    $mail->setFrom('mail@pegasustravelers.com', 'Pegasus Travelers');
  
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';

            // Start output buffering
            ob_start();
            // Include the email template
            include('../Email/password-reset-mail.php');
            // Get the contents of the buffer and clean the buffer
            $mailBody = ob_get_clean();

            // Replace placeholders with actual values
            $mailBody = str_replace('$userName', $userName, $mailBody);
            $mailBody = str_replace('$resetLink', $resetLink, $mailBody);
            $mailBody = str_replace("year", date('Y'), $mailBody);

            // Use the processed content as the email body
            $mail->Body = $mailBody;

            $mail->send();
            echo header('Location: ../pages/reset_link_sent_success.php');
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "If an account with that email exists, a password reset link has been sent.";
    }
}
?>