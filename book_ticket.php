<?php
session_start();
include 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$gender = $_POST['gender'] ?? '';
$from = $_POST['from'] ?? '';
$to = $_POST['to'] ?? '';

if (!$gender || !$from || !$to) {
    die("All fields required.");
}

/* Amount logic */
if (strtolower($gender) == 'female') {
    $amount = 0;   // Free ticket
} else {
    $amount = 50;  // Example price (you can change)
}

/* Insert ticket */
$stmt = $conn->prepare("
INSERT INTO ticket_requests 
(user_id, `from`, `to`, gender, amount, status, created_at)
VALUES 
(:user_id, :from, :to, :gender, :amount, 'pending', NOW())
");

$stmt->execute([
':user_id' => $userId,
':from' => $from,
':to' => $to,
':gender' => $gender,
':amount' => $amount
]);

$ticketId = $conn->lastInsertId();

/* Redirect to payment */
header("Location: payment.php?ticket_id=" . $ticketId);
exit;