<?php
require_once 'inc/functions.php';
unset($_SESSION['user']);
echo '<script>alert("Logged out successfully!"); window.location.href = "index.php";</script>';
exit;
?>