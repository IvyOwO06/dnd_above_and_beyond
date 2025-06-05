<?php
session_start();
require_once 'inc/functions.php';
session_destroy();
echo '<script>alert("Logged out successfully!"); window.location.href = "index.php";</script>';
exit;
?>