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
<body style="margin-left: 30px;">
    <!--<?php require '../components/header.php'; ?>-->
    <div style="
        text-align: center;
        justify-content: left;
        display: flex;
        margin-top: 20px;
        border-bottom: 1px solid #ccc;
    ">
        <div style="
            text-align: center;
            display: flex;
            align-items: center;
        ">
            <h1 style="font-size: 24px;">Dashboard</h1>
            <p style="margin-left: 10px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <a href="/lab/logout.php" style="
                margin-left: 10px;
                text-decoration: none;
                color: var(--primary-color);
            " class="link">Logout</a>
        </div>
    </div>
    <?php require '../components/friends-slide.php'; ?>
</body>
</html>
