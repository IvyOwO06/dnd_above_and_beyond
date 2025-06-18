<?php
$user = $_GET['user'] ?? '';
require_once 'inc/functions.php';

dd($_SESSION);

if (!isset($_SESSION['pending_2fa']) || $_SESSION['pending_2fa']['username'] !== $user || $_SESSION['pending_2fa']['2fa_pending'] !== true) {
    header("Location: login?error=" . urlencode("Unauthorized access or 2FA not initiated."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = trim($_POST['code']);

    // Debug: Log input and stored codes
    // file_put_contents("debug.log", "Input: '$input_code', Stored: '{$_SESSION['pending_2fa']['2fa_code']}', Expiry: {$_SESSION['pending_2fa']['2fa_expiry']}\n", FILE_APPEND);

    if (!isset($_SESSION['pending_2fa']['2fa_code']) || !isset($_SESSION['pending_2fa']['2fa_expiry'])) {
        unset($_SESSION['pending_2fa']);
        $error = "2FA code not found or expired.";
        header("Location: verify2fa?user=" . urlencode($user) . "&error=" . urlencode($error));
        exit();
    }

    if (time() > $_SESSION['pending_2fa']['2fa_expiry']) {
        unset($_SESSION['pending_2fa']);
        $error = "2FA code has expired.";
        header("Location: verify2fa?user=" . urlencode($user) . "&error=" . urlencode($error));
        exit();
    }

    if ($input_code === $_SESSION['pending_2fa']['2fa_code']) {
        $_SESSION['user'] = [
            'username' => $_SESSION['pending_2fa']['username'],
            'id' => $_SESSION['pending_2fa']['id']
        ];
        unset($_SESSION['pending_2fa']); // Clear temporary 2FA data
        header("Location: index");
        exit();
    } else {
        $error = "Invalid 2FA code.";
        header("Location: verify2fa?user=" . urlencode($user) . "&error=" . urlencode($error));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify 2FA</title>
</head>
<body>
    <main>
        <form method="POST">
            <h2>Two-Factor Authentication</h2>
            <?php if (isset($_GET['error'])): ?>
                <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <label>Enter your 2FA code:</label>
            <input type="text" name="code" required>
            <button type="submit">Verify</button>
        </form>
    </main>
</body>
</html>