<?php
$pageTitle = "User Dashboard | ksrtc.com";
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('USER', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$message = "";

/* Fetch stops */
$stmt = $conn->prepare("SELECT id, stops FROM stops ORDER BY id ASC");
$stmt->execute();
$stops = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $from = $_POST['FROM'] ?? null;
    $to = $_POST['TO'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $dob = $_POST['dob'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$from || !$to || !$gender || !$dob || $from == $to) {
        $message = "<div class='alert alert-danger'>Fill all fields properly.</div>";
    }
    elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $message = "<div class='alert alert-danger'>Upload Aadhaar card.</div>";
    }
    else {

        /* Calculate Age */
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;

        /* Calculate Distance */
        $distance = abs($to - $from);
        $baseFare = $distance * 10;

        $amount = 0;
        $status = 'pending';

        /* ===== TICKET LOGIC ===== */

        if (strtolower($gender) === 'female') {
            // Female & Girl free but ₹10 seat charge
            $amount = 10;
            $status = 'pending';
        } 
        else { // male / boy

            if ($age < 6) {
                // Below 6 free
                $amount = 0;
                $status = 'paid';
            }
            elseif ($age >= 6 && $age <= 12) {
                // Half ticket
                $amount = $baseFare / 2;
                $status = 'pending';
            }
            else {
                // Full ticket
                $amount = $baseFare;
                $status = 'pending';
            }
        }

        /* Upload Aadhaar */
        $uploadDir = "temp/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fileName = time() . "_" . uniqid() . "." . $extension;
        $targetFile = $uploadDir . $fileName;

        move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);

        /* ===== CORRECT INSERT (FIXED) ===== */
        $stmt = $conn->prepare("
            INSERT INTO ticket_requests 
            (user_id, `from`, `to`, gender, amount, status, path, result)
            VALUES 
            (:user_id, :from, :to, :gender, :amount, :status, :path, :result)
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':from' => $from,
            ':to' => $to,
            ':gender' => $gender,
            ':amount' => $amount,
            ':status' => $status,
            ':path' => $targetFile,
            ':result' => 'waiting'
        ]);

        $ticketId = $conn->lastInsertId();

        /* Redirect */
        if ($amount == 0) {
            header("Location: ticket_history.php");
        } else {
            header("Location: payment.php?ticket_id=" . $ticketId);
        }
        exit;
    }
}
?>

<div class="container mt-5">

    <h2 class="text-center mb-4">Book Your Ticket</h2>

    <?= $message ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Upload Aadhaar</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Type</label>
            <select name="gender" class="form-select" required>
                <option value="">Select</option>
                <option value="female">Female (Free + ₹10 seat)</option>
                <option value="female">Girl (Free + ₹10 seat)</option>
                <option value="male">Male</option>
                <option value="male">Boy</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">From</label>
            <select name="FROM" class="form-select" required>
                <option value="">Select Stop</option>
                <?php foreach ($stops as $stop): ?>
                    <option value="<?= $stop['id'] ?>">
                        <?= htmlspecialchars($stop['stops']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">To</label>
            <select name="TO" class="form-select" required>
                <option value="">Select Stop</option>
                <?php foreach ($stops as $stop): ?>
                    <option value="<?= htmlspecialchars($stop['id']) ?>">
                        <?= htmlspecialchars($stop['stops']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-warning w-100">
            Book Ticket
        </button>

    </form>
</div>

<?php include 'footer.php'; ?>