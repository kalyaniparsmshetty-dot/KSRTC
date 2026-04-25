<?php
include 'header.php';
include 'dbconfig.php';

try {
    $conn = new PDO($dsn, $user, $pass, $options);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete message if requested
    if (isset($_GET['delete_id'])) {
        $stmt = $conn->prepare("DELETE FROM contact_us WHERE id = :id");
        $stmt->execute(['id' => $_GET['delete_id']]);
        header("Location: view_contact.php?deleted=1");
        exit;
    }

    $stmt = $conn->query("SELECT * FROM contact_us ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $showSuccess = isset($_GET['deleted']);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="adminDashboard.php" class="btn mb-3" style="background-color :orange">← Go Back</a>
    <h2 class="text-center mb-4">📬 Contact Us Submissions</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>SL/No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Submitted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($contacts): ?>
                <?php $sl = 1; ?>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td class="text-center"><?= $sl++ ?></td>
                        <td><?= htmlspecialchars($contact['name']) ?></td>
                        <td><?= htmlspecialchars($contact['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($contact['message'])) ?></td>
                        <td><?= htmlspecialchars($contact['created_at']) ?></td>
                        <td class="text-center">
                            <a href="reply.php"
                               class="btn btn-sm btn-primary mb-1">Reply</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No messages found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
