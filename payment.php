<?php
session_start();
ob_start();

$pageTitle = "Payment | ksrtc.com";
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('USER', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$ticketId = $_GET['ticket_id'] ?? null;
$username = $_SESSION['username'] ?? null;

if (!$ticketId || !$username) {
    die("Invalid access.");
}

/* ✅ Get User ID */
$stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute([':email' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$userId = $user['id'];

/* ✅ Fetch Ticket */
$stmt = $conn->prepare("SELECT * FROM ticket_requests WHERE id = :ticket_id");
$stmt->execute([':ticket_id' => $ticketId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Ticket not found.");
}

if ($ticket['user_id'] != $userId) {
    die("Unauthorized access.");
}

$amount = floatval($ticket['amount']);

/* =====================================================
   ✅ FREE TICKET (Female → amount = 0)
===================================================== */

if ($amount <= 0) {

    // Check if payment already exists
    $check = $conn->prepare("SELECT id FROM payments WHERE ticket_id = :ticket_id");
    $check->execute([':ticket_id' => $ticketId]);

    if (!$check->fetch()) {

        // Insert success payment
        $stmt = $conn->prepare("INSERT INTO payments 
            (ticket_id, user_id, amount, status, paid_at) 
            VALUES (:ticket_id, :user_id, :amount, 'success', NOW())");

        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':user_id' => $userId,
            ':amount' => 0
        ]);

        // VERY IMPORTANT → update ticket status
        $update = $conn->prepare("UPDATE ticket_requests 
                                  SET status='paid' 
                                  WHERE id=:ticket_id");

        $update->execute([':ticket_id' => $ticketId]);
    }

    header("Location: ticket_history.php?free=1");
    exit();
}

/* =====================================================
   ✅ PAID TICKET (Male)
===================================================== */

require 'vendor/autoload.php';
use Razorpay\Api\Api;

$api = new Api('rzp_test_ek75cF7ikf5j8R', 'fr4FYOf41WzH0rEXzGYICa5o');

/* Check existing pending payment */
$stmt = $conn->prepare("SELECT * FROM payments 
                        WHERE ticket_id = :ticket_id 
                        AND status = 'pending' 
                        ORDER BY id DESC LIMIT 1");

$stmt->execute([':ticket_id' => $ticketId]);
$existingPayment = $stmt->fetch(PDO::FETCH_ASSOC);

$amountPaise = $amount * 100;

if ($existingPayment && !empty($existingPayment['razorpay_order_id'])) {

    $orderId = $existingPayment['razorpay_order_id'];

} else {

    $order = $api->order->create([
        'receipt' => 'rcptid_' . $ticketId,
        'amount' => $amountPaise,
        'currency' => 'INR',
        'payment_capture' => 1
    ]);

    $orderId = $order['id'];

    // Insert pending payment
    $stmt = $conn->prepare("INSERT INTO payments 
        (ticket_id, user_id, amount, status, razorpay_order_id, created_at) 
        VALUES (:ticket_id, :user_id, :amount, 'pending', :order_id, NOW())");

    $stmt->execute([
        ':ticket_id' => $ticketId,
        ':user_id' => $userId,
        ':amount' => $amount,
        ':order_id' => $orderId
    ]);
}

ob_end_flush();
?>

<div class="container mt-5">
    <a href="index.php" class="btn mb-3 text-white" style="background-color: orange;">
        ← Go Back
    </a>

    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow p-4">
                <h3 class="mb-3 text-primary">KSRTC Ticket Payment</h3>
                <p class="mb-4 fs-5">
                    Please pay ₹<?= htmlspecialchars($amount) ?> to confirm your ticket.
                </p>
                <button id="rzp-button1" class="btn btn-success btn-lg px-5">
                    Pay Now
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const options = {
    key: "rzp_test_ek75cF7ikf5j8R",
    amount: "<?= $amountPaise ?>",
    currency: "INR",
    name: "KSRTC Booking",
    description: "Ticket Payment",
    order_id: "<?= $orderId ?>",
    handler: function (response) {

        fetch("payment_success.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id: response.razorpay_order_id,
                ticket_id: <?= json_encode($ticketId) ?>
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = "ticket_history.php?success=1";
            } else {
                alert("Payment verification failed.");
            }
        });
    },
    theme: { color: "#198754" }
};

const rzp = new Razorpay(options);

document.getElementById('rzp-button1').onclick = function (e) {
    rzp.open();
    e.preventDefault();
}
</script>

<?php include 'footer.php'; ?>