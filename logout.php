<?php
session_start();
session_destroy();
header('Location: /lab/login/login.php');
exit();
?>