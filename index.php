<?php 
 include 'header.php'; 
?>
<div>
    <!-- Hero Section -->
    <header class="text-white text-center py-5">

        <div class="container">
            <h1 class="display-4" style="color :orange">Welcome</h1>
            <h2 class="display-4" style="color :black">Get Your Online Ticket</h2>
            <p class="lead">official Website of Karnataka State Road Transport Corporation</p>
            <a href="login.php" class="btn" style="background-color :orange"><strong>Book Now</strong></a>
        </div>
    </header>


    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <h2 class="text-center"><a href="aboutUs.php" style="color :black; text-decoration: none;">About Us</a></h2>
            <p class="lead text-center">Info that might want to know about Karnataka State Road Transport Corporation.</p>
        </div>
    </section>
</div>
 <div class="container mt-5">
    <div class="row">

<!-- Add this style block to your <style> section or CSS file -->
<style>
    .in-progress-icon {
        animation: rotate 2s linear infinite;
        color: #ffc107;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .card.in-progress-card {
        border-left: 5px solid #ffc107;
        animation: fadeIn 1s ease-in-out;
        transition: transform 0.3s ease;
        height: 100%; /* Ensure card fills column height */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card.in-progress-card:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .status-text {
        font-weight: 500;
        color: #856404;
    }

    .equal-height {
        display: flex;
        flex-direction: column;
    }
</style>


<!-- Bus Pass -->
<div class="col-md-6 mb-4 d-flex">
    <div class="card p-3 shadow-sm text-center in-progress-card equal-height w-100">
        <h5>Student Bus Pass</h5>
        <p>Access and review student bus pass information.</p>
        <p class="text-secondary">
            This feature will allow students to check the details and status of their existing bus passes.
            Applying for a new pass or requesting renewals is currently not available.
            This streamlined view is being designed to provide quick access to essential travel details.
        </p>
        <div class="text-muted mt-auto">
            <i class="bi bi-clock-history in-progress-icon" style="font-size: 2rem;"></i>
            <p class="mt-2 status-text">In Progress</p>
        </div>
    </div>
</div>

<!-- Senior Citizenship -->
<div class="col-md-6 mb-4 d-flex">
    <div class="card p-3 shadow-sm text-center in-progress-card equal-height w-100">
        <h5>Senior Citizenship</h5>
        <p>Handle senior citizen benefits and verifications.</p>
        <p class="text-secondary">
            A dedicated portal for senior citizens to register, update their details,
            and access exclusive services and benefits. Identity verification and
            eligibility assessments will be streamlined for ease of use.
        </p>
        <div class="text-muted mt-auto">
            <i class="bi bi-clock-history in-progress-icon" style="font-size: 2rem;"></i>
            <p class="mt-2 status-text">In Progress</p>
        </div>
    </div>
</div>



    </div>
</div>


<?php include 'footer.php'; ?>
