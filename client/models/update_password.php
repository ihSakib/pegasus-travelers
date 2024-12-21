<?php
include '../../db/config.php'; // Your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT email, expires FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if ($reset && $reset['expires'] >= time()) {
        $email = $reset['email'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update the password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        // Delete the reset token
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            //Server settings
           include '../../smtp/config.php';

            $mail->setFrom('pro.iftekhar@gmail.com', 'Pegasus Travelers');
            $mail->addAddress($email);
            $mail->Subject = 'Password Updated Successfully';

            // HTML email body with Tailwind CSS
            $mail->isHTML(true);

            $mail->Body = file_get_contents('../Email/password-update-mail.php');

            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

        // Redirect to success page
        header('Location: ../pages/password_update_success.php');
        exit;
    } else {
        echo "Invalid or expired token.";
    }
}
