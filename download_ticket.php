<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
include 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['ticket_id'])) {
    exit("Ticket ID is required.");
}

$ticket_id = (int) $_GET['ticket_id'];

try {

    $stmt = $conn->prepare("
        SELECT * 
        FROM ticket_requests 
        WHERE id = :ticket_id AND user_id = :user_id
    ");
    
    $stmt->execute([
        ':ticket_id' => $ticket_id,
        ':user_id' => $user_id
    ]);

    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        exit("Ticket not found.");
    }

    if ($ticket['status'] !== 'paid') {
        exit("Payment not completed.");
    }

    /* ---------------- IMAGE CREATION ---------------- */

    $width = 900;
    $height = 550;

    $image = imagecreatetruecolor($width, $height);

    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray  = imagecolorallocate($image, 200, 200, 200);

    imagefill($image, 0, 0, $white);

    /* Border */
    imagerectangle($image, 20, 20, $width-20, $height-20, $black);

    /* ✅ STRONG PATH METHOD (Always Works in XAMPP) */
    $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/ksrtc-main/image/ksrtc-logo.png';

    if (file_exists($logoPath)) {
        $logo = imagecreatefrompng($logoPath);
        imagecopyresampled($image, $logo, 40, 40, 0, 0, 120, 120, imagesx($logo), imagesy($logo));
    }

    /* Header */
    imagestring($image, 5, 200, 70, "KARNATAKA STATE ROAD", $black);
    imagestring($image, 5, 200, 95, "TRANSPORT CORPORATION", $black);

    /* Divider */
    imageline($image, 40, 150, $width-40, 150, $gray);

    /* Receipt Title */
    imagestring($image, 5, 60, 170, "RECEIPT", $black);

    /* Ticket Details */
    $startY = 220;
    $gap = 40;

    imagestring($image, 4, 60, $startY, "Ticket ID : " . $ticket['id'], $black);
    imagestring($image, 4, 60, $startY+$gap, "From      : " . $ticket['from'], $black);
    imagestring($image, 4, 60, $startY+($gap*2), "To        : " . $ticket['to'], $black);
    imagestring($image, 4, 60, $startY+($gap*3), "Gender    : " . ucfirst($ticket['gender']), $black);
    imagestring($image, 4, 60, $startY+($gap*4), "Amount    : Rs " . $ticket['amount'], $black);
    imagestring($image, 4, 60, $startY+($gap*5), "Date      : " . $ticket['created_at'], $black);
    imagestring($image, 4, 60, $startY+($gap*6), "Status    : Completed", $black);

    /* Bottom Divider */
    imageline($image, 40, 460, $width-40, 460, $gray);

    /* Footer */
    imagestring($image, 3, 60, 480, "Please carry a valid ID during your journey.", $black);
    imagestring($image, 3, 60, 500, "24x7 Helpline: 1800-123-4567", $black);

    header("Content-Type: image/jpeg");
    header("Content-Disposition: attachment; filename=ticket_$ticket_id.jpg");

    imagejpeg($image, null, 100);
    imagedestroy($image);
    exit;

} catch (PDOException $e) {
    exit("Server error.");
}
?>