<?php
include 'header.php';
include 'dbconfig.php';

$token = $_GET['token'] ?? '';
$error = $success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()");
            $stmt->execute([':token' => $token]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reset) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
                $update->execute([
                    ':password' => $hashed,
                    ':email' => $reset['email']
                ]);

                // Clean up token
                $delete = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
                $delete->execute([':token' => $token]);

                $success = "Password reset successful. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Invalid or expired token.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5">
    <h3>Reset Password</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <?php if (!$success): ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
