<?php
include 'dbconfig.php';
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_POST['email'] ?? '';
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($email)) {
        $error = "Email is required.";
    } else {
        try {
            // Check if email exists in users table
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error = "No user found with this email.";
            } else {
                // Generate token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Store token
                $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
                $stmt->execute([
                    ':email' => $email,
                    ':token' => $token,
                    ':expires_at' => $expires
                ]);

                // Prepare reset link
                $resetLink = "http://yourdomain.com/reset-password.php?token=" . urlencode($token);

                // Send email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'naveenkitageri@gmail.com'; // your email
                $mail->Password   = 'khfl qhdo wxvf hmax'; // app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('naveenkitageri@gmail.com', 'KSRTC Admin');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Reset Your Password';
                $mail->Body    = "
                    <p>Hello,</p>
                    <p>You requested a password reset. Click the link below to set a new password:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>This link will expire in 1 hour. If you didn’t request this, you can ignore this email.</p>
                ";

                $mail->send();
                $success = "A password reset link has been sent to your email.";
            }
        } catch (Exception $e) {
            $error = "Mailer Error: " . $mail->ErrorInfo;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!-- HTML form to request password reset -->
<div class="container mt-5">
    <h3>Forgot Password</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label>Email address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>
