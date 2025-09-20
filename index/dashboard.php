<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lab/login/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../styles/globals.css">
</head>
<body>
    <?php require '../components/header.php'; ?>
    <div class="container">
        <div class="form-container">
            <h1>Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
    </div>
</body>
</html>
