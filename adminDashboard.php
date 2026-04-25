<?php
echo "PHP FILE STARTED";
$pageTitle = "Admin Dashboard | ksrtc.com";
include 'header.php';

if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Welcome to Admin Dashboard</h2>

    <div class="row">
        <div class="container mt-5">
            <div class="card p-3 shadow mx-auto" style="width :500px;margin :0 auto;align-items: center;">
                <h5>User Management</h5>
                <p>View, edit, or delete registered users.</p>
                <a href="ticket_history.php" class="btn w-50" style="background-color :orange;"><strong>Manage Users</strong></a>
            </div>
        </div>

        <!--<div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5>System Reports</h5>
                <p>Generate system usage reports and analytics.</p>
                <a href="reports.php" class="btn btn-secondary">View Reports</a>
            </div>
        </div>-->
    </div>
</div>

<?php include 'footer.php'; ?>
