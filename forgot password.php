<?php
include 'header.php';
include 'dbconfig.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

                $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
                $insert->execute([
                    ':email' => $email,
                    ':token' => $token,
                    ':expires' => $expires
                ]);

                // Send email (you must configure mail server)
                $resetLink = "https://yourdomain.com/reset_password.php?token=$token";
                $subject = "Password Reset Request";
                $message = "Click the following link to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.";
                $headers = "From: no-reply@yourdomain.com";

               

                $success = "A password reset link has been sent to email.";
            } else {
                $error = "No user found with that email.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5">
    <a href="index.php" class="btn mb-3" style="background-color :orange">← Go Back</a>
    <h3>Forgot Password</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>

<?php include 'footer.php'; ?>



