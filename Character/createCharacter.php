<?php
require 'inc/functions.php';

if (!isset($_SESSION['user'])) {
    die("⛔ You must be logged in to create a character.");
}

$conn = dbConnect();

// Default values for a new placeholder character
$characterName = "New Character";
$characterAge = 0;
$classId = 0;
$raceId = 0;
$alignment = "";
$level = 1;
$userId = $_SESSION['user']['id'];

$stmt = $conn->prepare("INSERT INTO characters (characterName, characterAge, classId, raceId, alignment, level, userId) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siiisii", $characterName, $characterAge, $classId, $raceId, $alignment, $level, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $characterId = $stmt->insert_id;
    header("Location: builder.php?characterId=" . $characterId);
    exit;
} else {
    echo "<p>❌ Failed to create character.</p>";
}

$stmt->close();
$conn->close();
?>