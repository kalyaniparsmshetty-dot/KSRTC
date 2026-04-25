<?php
$pageTitle = "History | ksrtc";
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'] ?? '';
$roles = $_SESSION['roles'] ?? [];

function is_valid_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

if (in_array('ADMIN', $roles)) {
    if (!empty($_GET['start_date']) && !is_valid_date($_GET['start_date'])) {
        die("<div class='alert alert-warning'>Invalid Start Date</div>");
    }
    if (!empty($_GET['end_date']) && !is_valid_date($_GET['end_date'])) {
        die("<div class='alert alert-warning'>Invalid End Date</div>");
    }
    if (!empty($_GET['start_date']) && !empty($_GET['end_date']) && $_GET['start_date'] > $_GET['end_date']) {
        die("<div class='alert alert-warning'>Start Date cannot be after End Date</div>");
    }
}

try {

    if (in_array('ADMIN', $roles)) {

        $query = "
            SELECT tr.id, s1.stops AS from_stop, s2.stops AS to_stop,
                   tr.created_at, tr.status, tr.amount, u.email, tr.gender
            FROM ticket_requests tr
            JOIN stops s1 ON tr.from = s1.id
            JOIN stops s2 ON tr.to = s2.id
            JOIN users u ON tr.user_id = u.id
        ";

        $params = [];
        $conditions = [];

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $conditions[] = "DATE(tr.created_at) BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $_GET['start_date'];
            $params[':end_date'] = $_GET['end_date'];
        }

        if ($conditions) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY tr.created_at DESC";

        $stmt = $conn->prepare($query);
        $stmt->execute($params);

    } else {

        $stmt = $conn->prepare("
            SELECT tr.id, s1.stops AS from_stop, s2.stops AS to_stop,
                   tr.created_at, tr.status, tr.amount, tr.gender
            FROM ticket_requests tr
            JOIN stops s1 ON tr.from = s1.id
            JOIN stops s2 ON tr.to = s2.id
            WHERE tr.user_id = (SELECT id FROM users WHERE email = :username)
            ORDER BY tr.created_at DESC
        ");
        $stmt->execute([':username' => $username]);
    }

    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAmount = 0;
    $maleCount = 0;
    $femaleCount = 0;

    if (in_array('ADMIN', $roles)) {
        foreach ($tickets as $ticket) {
            if (isset($ticket['gender'])) {
                $gender = strtolower(trim($ticket['gender']));
                if ($gender === 'male') $maleCount++;
                if ($gender === 'female') $femaleCount++;
            }
            if (is_numeric($ticket['amount'])) {
                $totalAmount += $ticket['amount'];
            }
        }
    }

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>");
}
?>

<div class="container mt-5">
    <a href="index.php" class="btn mb-3 text-white" style="background-color:orange;">← Go Back</a>

    <h3><?= in_array('ADMIN', $roles) ? 'Ticket History' : 'Your Ticket History' ?></h3>

    <?php if (in_array('ADMIN', $roles)): ?>
        <div class="alert alert-info">
            <strong>Total Amount Collected:</strong> ₹<?= number_format($totalAmount, 2) ?>
        </div>
        <div class="alert alert-secondary">
            👨 Males : <strong><?= $maleCount ?></strong><br>
            👩 Females : <strong><?= $femaleCount ?></strong>
        </div>
    <?php endif; ?>

    <?php if (!empty($tickets)): ?>
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <?php if (in_array('ADMIN', $roles)): ?><th>User Email</th><?php endif; ?>
                    <th>From</th>
                    <th>To</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Gender</th>
                    <th>Amount</th>
                    <?php if (!in_array('ADMIN', $roles)): ?><th>Download</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($tickets as $index => $ticket): ?>
                <tr>
                    <td><?= $index + 1 ?></td>

                    <?php if (in_array('ADMIN', $roles)): ?>
                        <td><?= htmlspecialchars($ticket['email']) ?></td>
                    <?php endif; ?>

                    <td><?= htmlspecialchars($ticket['from_stop']) ?></td>
                    <td><?= htmlspecialchars($ticket['to_stop']) ?></td>
                    <td><?= htmlspecialchars($ticket['created_at']) ?></td>

                    <td>
                        <?php if ($ticket['status'] === 'paid'): ?>
                            <span class="badge bg-success">Paid</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($ticket['gender'] ?? 'Not Defined') ?></td>

                    <td>
                        <?= is_numeric($ticket['amount']) ? '₹' . number_format($ticket['amount'], 2) : '₹0.00' ?>
                    </td>

                    <?php if (!in_array('ADMIN', $roles)): ?>
                        <td>
                            <?php if ($ticket['status'] === 'paid'): ?>
                                <a href="download_ticket.php?ticket_id=<?= $ticket['id'] ?>" 
                                   class="btn btn-success btn-sm">
                                   Download
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Not Available</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>

                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No ticket records found.</div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>