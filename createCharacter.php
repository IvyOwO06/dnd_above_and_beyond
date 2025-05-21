<?php
require 'inc/functions.php';

if (!isset($_SESSION['user'])) {
    die("⛔ You must be logged in to create a character.");
}

$conn = dbConnect();

// Step 1: Insert character
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
    $stmt->close();

    // Step 2: Get all skills
    $skillsQuery = $conn->query("SELECT skillId FROM skills");
    if ($skillsQuery && $skillsQuery->num_rows > 0) {

        // Step 3: Prepare insert for characterskills
        $stmtSkill = $conn->prepare("INSERT INTO characterskills (characterId, skillId, proficiency) VALUES (?, ?, 'none')");

        foreach ($skillsQuery as $skill) {
            $skillId = $skill['skillId'];
            $stmtSkill->bind_param("ii", $characterId, $skillId);
            $stmtSkill->execute();
        }

        $stmtSkill->close();
    }

    // Step 4: Redirect to builder
    $conn->close();
    header("Location: builder.php?characterId=" . $characterId);
    exit;
} else {
    echo "<p>❌ Failed to create character.</p>";
    $stmt->close();
    $conn->close();
}
?>
