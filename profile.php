<?php
$pageTitle = "Edit Profile | ksrtc.com";
include 'header.php';
include 'dbconfig.php';

$userId = $_SESSION['username'] ?? null;

$error = $success = "";

// Ensure the user is logged in
if (!$userId) {
    header("Location: login.php"); // Redirect if not logged in
    exit;
}

// Fetch current user details
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    $error = "User not found.";
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);

    // Simple validation checks
    if (empty($first_name) || empty($last_name) || empty($phone)) {
        $error = "All fields are required.";
    } else {
        // Validate phone number format (basic check)
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            $error = "Phone number must be 10 digits.";
        } else {
            // Check phone uniqueness
            $check = $conn->prepare("SELECT id FROM users WHERE phone_number = ? AND email != ?");
            $check->execute([$phone, $userId]);
            if ($check->rowCount() > 0) {
                $error = "Phone number already in use.";
            } else {
                // Update the user profile
                $update = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, phone_number = ? WHERE email = ?");
                if ($update->execute([$first_name, $last_name, $phone, $userId])) {
                    $success = "Profile updated successfully.";
                    // Refresh user data
                    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $error = "Update failed. Try again.";
                }
            }
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center">Edit Your Profile</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <!-- Profile Edit Form -->
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label for="email" class="form-label">Email</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label for="first_name" class="form-label">First Name</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="first_name" id="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label for="last_name" class="form-label">Last Name</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="last_name" id="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label for="phone" class="form-label">Phone Number</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                        <a href="profile.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
