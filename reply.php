<?php  
include 'header.php';
include 'dbconfig.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Get email from DB
$stmt = $conn->prepare("SELECT email FROM contact_us LIMIT 1");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $row['email'] ?? '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $to = $_POST['email'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];

    $mail = new PHPMailer(true);

    try {
        // SMTP settings for Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'naveenkitageri@gmail.com';      // Your Gmail
        $mail->Password   = 'khfl qhdo wxvf hmax';           // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('naveenkitageri@gmail.com', 'KSRTC Admin');
        $mail->addAddress($to);  // Send to email from the form

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body);  // Convert newlines to <br>
        $mail->AltBody = $body;

        $mail->send();
        echo "<div class='alert alert-success text-center'>✅ Email sent successfully to $to</div>";
    } catch (Exception $e) {
        echo "<div class='alert alert-danger text-center'>❌ Message failed: {$mail->ErrorInfo}</div>";
    }
}
?>

<div class="container mt-5">
    <a href="view_messages.php" class="btn mb-3" style="background-color: orange;">← Go Back</a>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">TO</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">SUBJECT</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label">BODY</label>
                    <textarea class="form-control" id="body" name="body" rows="4" required></textarea>
                </div>  

                <div class="text-center">
                    <button type="submit" class="btn" style="background-color: orange;"><strong>Submit</strong></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
