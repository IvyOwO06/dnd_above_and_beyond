<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'inc/functions.php';

// Google OAuth Configuration
$google_client = new Google_Client();
$google_client->setClientId('1015906687321-pm2r694d6f6j1vcf74t0mgo654fj9li9.apps.googleusercontent.com');
$google_client->setClientSecret('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');

// Revoke Google token if it exists
if (isset($_SESSION['access_token'])) {
    $google_client->revokeToken($_SESSION['access_token']);
}

// Destroy session
session_destroy();
echo '<script>alert("Logged out successfully!"); window.location.href = "index.php";</script>';
exit;
?>