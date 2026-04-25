<?php
session_start();
include 'dbconfig.php';

require 'vendor/autoload.php';
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$razorpay_payment_id = $data['razorpay_payment_id'] ?? null;
$razorpay_order_id   = $data['razorpay_order_id'] ?? null;
$ticket_id           = $data['ticket_id'] ?? null;

if (!$razorpay_payment_id || !$razorpay_order_id || !$ticket_id) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit();
}

try {

    $api = new Api('rzp_test_ek75cF7ikf5j8R', 'fr4FYOf41WzH0rEXzGYICa5o');

    // Optional: Fetch payment from Razorpay for verification
    $payment = $api->payment->fetch($razorpay_payment_id);

    if ($payment->status != 'captured') {
        echo json_encode(['success' => false, 'message' => 'Payment not captured']);
        exit();
    }

    // Update payment record
    $stmt = $conn->prepare("
        UPDATE payments 
        SET status = 'success',
            razorpay_payment_id = :payment_id,
            paid_at = NOW()
        WHERE ticket_id = :ticket_id
        AND razorpay_order_id = :order_id
    ");

    $stmt->execute([
        ':payment_id' => $razorpay_payment_id,
        ':ticket_id' => $ticket_id,
        ':order_id' => $razorpay_order_id
    ]);

    // Update ticket status
    $stmt2 = $conn->prepare("
        UPDATE ticket_requests
        SET status = 'paid',
            updated_at = NOW()
        WHERE id = :ticket_id
    ");

    $stmt2->execute([
        ':ticket_id' => $ticket_id
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}