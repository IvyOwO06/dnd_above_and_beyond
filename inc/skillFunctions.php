<?php
require_once 'functions.php';
require_once 'builderFunctions.php';

function getCharacterSkillsJson($characterId) {
    if (!$characterId || !is_numeric($characterId)) {
        return json_encode([]);
    }
    $skills = getCharacterSkills($characterId);
    return json_encode($skills);
}

function getSkillModifier($characterId, $skillId) {
    if (!$characterId || !is_numeric($characterId) || !$skillId || !is_numeric($skillId)) {
        return json_encode(['modifier' => 0]);
    }

    $character = getCharacter($characterId);
    $skills = getCharacterSkills($characterId);
    $skill = null;

    foreach ($skills as $s) {
        if ($s['skillId'] == $skillId) {
            $skill = $s;
            break;
        }
    }

    if (!$character || !$skill) {
        return json_encode(['modifier' => 0]);
    }

    $modifier = calculateSkillModifier($character, $skill, $skill['proficiency'] ?? 'none');
    return json_encode(['modifier' => $modifier]);
}

function updateSkillProficiency($characterId, $skillId, $proficiency) {
    $response = ['success' => false, 'message' => ''];

    // Validate input
    if (
        !$characterId || !is_numeric($characterId) ||
        !$skillId || !is_numeric($skillId) ||
        !in_array($proficiency, ['none', 'proficient', 'expertise'])
    ) {
        $response['message'] = 'Invalid character ID, skill ID, or proficiency value.';
        return json_encode($response);
    }

    $conn = dbConnect();
    if (!$conn) {
        $response['message'] = 'Database connection failed.';
        return json_encode($response);
    }

    // Get current proficiency for this skill (if any)
    $stmt = $conn->prepare("SELECT proficiency FROM characterskills WHERE characterId = ? AND skillId = ?");
    $stmt->bind_param("ii", $characterId, $skillId);
    $stmt->execute();
    $result = $stmt->get_result();
    $current = $result->fetch_assoc();
    $stmt->close();

    $currentProficiency = $current['proficiency'] ?? 'none';

    // Count current proficiencies
    $stmt = $conn->prepare("SELECT
                                SUM(proficiency IN ('proficient', 'expertise')) AS profCount,
                                SUM(proficiency = 'expertise') AS expCount
                            FROM characterskills
                            WHERE characterId = ?");
    $stmt->bind_param("i", $characterId);
    $stmt->execute();
    $result = $stmt->get_result();
    $counts = $result->fetch_assoc();
    $stmt->close();

    $profCount = (int) $counts['profCount'];
    $expCount = (int) $counts['expCount'];

    // Adjust based on what we're changing
    if ($currentProficiency !== $proficiency) {
        // Remove the current value from the count
        if ($currentProficiency === 'proficient') {
            $profCount--;
        }
        if ($currentProficiency === 'expertise') {
            $expCount--;
        }

        // Add the new value to the count
        if ($proficiency === 'proficient') {
            $profCount++;
        }
        if ($proficiency === 'expertise') {
            $expCount++;
        }
    }

    // Enforce limits
    // if ($profCount >= 5 || $expCount >= 3) {
    //     $response['message'] = 'Proficiency limit exceeded for this character.';
    //     $conn->close();
    //     return json_encode($response);
    // }

    // Update or insert the skill proficiency
    $stmt = $conn->prepare("INSERT INTO characterskills (characterId, skillId, proficiency)
                            VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE proficiency = ?");
    if (!$stmt) {
        $response['message'] = 'Prepare failed: ' . $conn->error;
        $conn->close();
        return json_encode($response);
    }

    $stmt->bind_param("iiss", $characterId, $skillId, $proficiency, $proficiency);
    $success = $stmt->execute();

    if ($success) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Failed to update proficiency: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    return json_encode($response);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'updateSkillProficiency':
            echo updateSkillProficiency($input['characterId'], $input['skillId'], $input['proficiency']);
            break;
        default:
            // echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'getSkills':
            echo getCharacterSkillsJson($_GET['characterId']);
            break;
        case 'getSkillModifier':
            echo getSkillModifier($_GET['characterId'], $_GET['skillId']);
            break;
        default:
            // echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}