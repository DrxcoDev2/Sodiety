<?php
// Start the session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <h1>Sodiety</h1>
    <div class="links-container">
        <a href="/lab/index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/lab/index/dashboard.php">Dashboard</a>
            <a href="/lab/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
        <?php else: ?>
            <a href="/lab/login/login.php">Login</a>
            <a href="/lab/signup/singup.php">Register</a>
        <?php endif; ?>
    </div>
</header>