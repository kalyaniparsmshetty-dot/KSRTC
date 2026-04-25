<?php
$pageTitle = "Register | ksrtc.com";
include 'header.php';
include 'dbconfig.php';

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($phone) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email or phone exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR phone_number = ?");
        $stmt->execute([$email, $phone]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email or phone already registered.";
        } else {
            try {
                // Begin transaction
                $conn->beginTransaction();
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, phone_number, email, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$first_name, $last_name, $phone, $email, $hashedPassword]);

                // Get the newly inserted user's ID
                $userId = $conn->lastInsertId();

                // Assign the role (role_id = 1 for USER)
                $stmtRole = $conn->prepare("INSERT INTO users_roles (user_id, role_id) VALUES (?, ?)");
                $stmtRole->execute([$userId, 1]);

                // Commit transaction
                $conn->commit();

                $success = "Registration successful!";
            } catch (Exception $e) {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">Register</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" action="registration.php" id="registerForm">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password" required minlength="6">
                            <span class="input-group-text">
                                <i class="bi bi-eye-slash" id="togglePassword" style="cursor: pointer;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="6">
                            <span class="input-group-text">
                                <i class="bi bi-eye-slash" id="toggleConfirmPassword" style="cursor: pointer;"></i>
                            </span>
                        </div>
                        <div class="form-text text-danger d-none" id="passwordMismatch">Passwords do not match.</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn" style="background-color :orange;">Register</button>
                    </div>
                    <div class="text-center mt-3">
                        <a href="login.php" style="color :black;">Already have an account? Login here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for password match and show/hide -->
<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const pass = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        const errorMsg = document.getElementById('passwordMismatch');

        if (pass !== confirm) {
            e.preventDefault();
            errorMsg.classList.remove('d-none');
        } else {
            errorMsg.classList.add('d-none');
        }
    });

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    togglePassword.addEventListener('click', () => {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        togglePassword.classList.toggle('bi-eye');
        togglePassword.classList.toggle('bi-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', () => {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        toggleConfirmPassword.classList.toggle('bi-eye');
        toggleConfirmPassword.classList.toggle('bi-eye-slash');
    });
</script>

<?php include 'footer.php'; ?>