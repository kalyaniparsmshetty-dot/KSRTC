<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['username']);
$pageTitle = isset($pageTitle) ? $pageTitle : "ksrtc.com";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="icon" type="img/x-icon" href="img/KSRTC-logo.png.ico">
    <link rel="stylesheet" href="css/app.css" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <script>
        window.addEventListener('load', function () {
            document.body.classList.add('loaded');
        });
    </script>

    <style>
        body.loaded {
            background-image: url('img/background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            margin: 0;
        }
    </style>

    <?php if (isset($customLoginCss)): ?>
        <link rel="stylesheet" href="<?= $customLoginCss ?>">
    <?php endif; ?>
</head>

<body>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init();
</script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="opacity:1 !important">
    <div class="container-fluid">
        <a href="index.php">
            <img src="img/KSRTC-logo.png" style="width:50px; margin-right:10px" alt="Logo">
        </a>

        <a class="navbar-brand" href="index.php">
            Karnataka State Road Transport Corporation
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item" style="margin-right:20px">
                    <a class="nav-link" href="index.php" style="color:orange">Home</a>
                </li>

                <li class="nav-item" style="margin-right:20px">
                    <a class="nav-link" href="aboutUs.php" style="color:orange">About Us</a>
                </li>

                <li class="nav-item" style="margin-right:20px">
                    <a class="nav-link" href="ticket_history.php" style="color:orange">Ticket History</a>
                </li>

                <?php if (isset($_SESSION['roles']) && in_array('USER', $_SESSION['roles']) && !in_array('ADMIN', $_SESSION['roles'])): ?>
                    <li class="nav-item" style="margin-right:20px;">
                        <a class="nav-link" href="userDashboard.php" style="color:orange;">Ticket</a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['roles']) && in_array('ADMIN', $_SESSION['roles'])): ?>
                    <li class="nav-item" style="margin-right:20px;">
                        <a class="nav-link" href="adminDashboard.php" style="color:orange;">Dashboard</a>
                    </li>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <?php
                    $username = $_SESSION['displayName'] ?? 'Guest';
                    $initial = strtoupper(substr($username, 0, 1));
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center"
                           href="#" data-bs-toggle="dropdown">
                            <div style="width:32px;height:32px;border-radius:50%;
                                background-color:#6c757d;color:orange;
                                display:flex;align-items:center;
                                justify-content:center;font-weight:bold;margin-right:8px;">
                                <?= $initial ?>
                            </div>
                            <?= htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile Settings</a></li>
                            <li><a class="dropdown-item" href="logout.php">Sign Out</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php" style="color:orange">Login</a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>