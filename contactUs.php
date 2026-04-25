<?php
$pageTitle = "Contact Us | ksrtc";
include 'header.php';
include 'dbconfig.php';

// Show login requirement if user is not logged in
if (!isset($_SESSION['user_id'])) {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Required</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .custom-popup {
                position: fixed;
                top: 20%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 20px 30px;
                border: 1px solid #ccc;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 9999;
                width: 500px;
                text-align: center;
            }
            .popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.4);
                z-index: 9998;
            }
        </style>
    </head>
    <body>
        <div class="popup-overlay"></div>
        <div class="custom-popup">
            <h5>Login Required</h5>
            <p>You must be logged in to view this page.</p>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <a href="login.php" class="btn btn-warning">Login</a>
                <button class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
            </div>
        </div>
    </body>
    </html>
    ';
    exit;
}

// Handle form submission and reCAPTCHA verification
$successMessage = $errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';

    $secretKey = '6LceuEcrAAAAAPsQvIto-iUWn1iQjbZ-QcKZRVTj'; // Replace with your actual secret key
    $remoteIp = $_SERVER['REMOTE_ADDR'];

    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse&remoteip=$remoteIp";
    $verifyResponse = file_get_contents($verifyURL);
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        $errorMessage = "❌ reCAPTCHA verification failed. Please try again.";
    } else {
        try{
            $stmt = $conn->prepare("INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)");
            $stmt -> execute([
                ":name"=>$name,
                ":email"=>$email,
                ":message"=>$message,
            ]);
            // Here you can process the form (e.g., send email, save to DB)
            $successMessage = "✅ Thank you, $name! Your message has been received.";
        }catch(PDOException $e){
            $errorMessage = "❌ Error saving your message. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<script>
            function validateRecaptcha() {
                var response = grecaptcha.getResponse();
                if (response.length === 0) {
                    alert("Please complete the reCAPTCHA.");
                    return false; // Block form submission
                }
                return true; // Allow form submission
            }
        </script>
<section class="py-5">
    <div class="container">
        <a href="index.php" class="btn mb-3" style="background-color :orange">← Go Back</a>
        <h2 class="text-center mb-5">We're Here to Assist You Anytime!</h2>

        <?php if ($successMessage): ?>
            <div class="alert alert-success text-center"><?= $successMessage ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
        <?php endif; ?>

        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h6 style="color: blue">How can we help you?</h6>
                <h5>Contact Us</h5>
                <p>We're here to help and answer any questions you<br>might have. We look forward to hearing from you!</p>
                <p><i class="bi bi-telephone-fill me-2"></i> Landline: <a href="tel:080-49596666" class="text" style="color: orange">080-49596666</a></p>
                <p><i class="bi bi-telephone-outbound-fill me-2"></i> Toll Free: <a href="tel:1800-123-4567" class="text" style="color: orange">1800-123-4567</a></p>
                <p><i class="bi bi-envelope-fill me-2"></i> Email: <a href="mailto:Customercare@ksrtc.in" class="text" style="color: orange">Customercare@ksrtc.in</a></p>
                <p><i class="bi bi-geo-alt-fill me-2"></i> Office Address: 123 Main Street, Bangalore, Karnataka</p>
                <h6 style="color: blue">Follow Us</h6>
                <ul class="list-unstyled d-flex">
                    <li class="me-3">
                        <a href="https://facebook.com" target="_blank"><i class="bi bi-facebook" style="color: orange; font-size: 1.5rem;"></i></a>
                    </li>
                    <li class="me-3">
                        <a href="https://twitter.com" target="_blank"><i class="bi bi-twitter" style="color: orange; font-size: 1.5rem;"></i></a>
                    </li>
                    <li class="me-3">
                        <a href="https://linkedin.com" target="_blank"><i class="bi bi-linkedin" style="color: orange; font-size: 1.5rem;"></i></a>
                    </li>
                    <li class="me-3">
                        <a href="https://instagram.com" target="_blank"><i class="bi bi-instagram" style="color: orange; font-size: 1.5rem;"></i></a>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 text-center">
                <img src="img/contact us.png" alt="Help Desk" style="height: 400px; width: 100%; object-fit: contain;">
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <h5 class="text-center mb-4">Send Us a Message</h5>
                    <form method="post" action="" onsubmit="return validateRecaptcha();">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="g-recaptcha mb-3" data-sitekey="6LceuEcrAAAAAN0D0J_VnxFIajJkHmpe1GvaDSLQ"></div>

                    <div class="text-center">
                        <button type="submit" class="btn" style="background-color: orange;"><strong>Submit</strong></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
