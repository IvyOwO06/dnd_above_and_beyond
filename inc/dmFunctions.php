<?php

// Assuming dbConnect() is a function that establishes a database connection
function getNotesForCampaign($campaignId) {
    $db = dbConnect();
    $sql = "SELECT * FROM sessionnotes WHERE campaignId = ? ORDER BY noteCreatedAt DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    $notes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $db->close();
    return $notes;
}

function createNote($campaignId, $title, $content) {
    $db = dbConnect();
    $sql = "INSERT INTO sessionnotes (campaignId, noteTitle, noteContent) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iss", $campaignId, $title, $content);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function updateNote($noteId, $title, $content) {
    $db = dbConnect();
    $sql = "UPDATE sessionnotes SET noteTitle = ?, noteContent = ? WHERE noteId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $noteId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function deleteNote($noteId) {
    $db = dbConnect();
    $sql = "DELETE FROM sessionnotes WHERE noteId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $noteId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function getNote($noteId) {
    $db = dbConnect();
    $sql = "SELECT * FROM sessionnotes WHERE noteId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $noteId);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    return $note;
}


// ... existing functions (getNotesForCampaign, createNote, etc.) ...

function getSessionsForCampaign($campaignId) {
    $db = dbConnect();
    $sql = "SELECT * FROM sessions WHERE campaignId = ? ORDER BY sessionDate DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $db->close();
    return $sessions;
}

function createSession($campaignId, $sessionName, $sessionDate) {
    $db = dbConnect();
    $sql = "INSERT INTO sessions (campaignId, sessionName, sessionDate) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iss", $campaignId, $sessionName, $sessionDate);
    $success = $stmt->execute();
    $sessionId = $stmt->insert_id;
    $stmt->close();
    $db->close();
    return $success ? $sessionId : false;
}

function deleteSession($sessionId) {
    $db = dbConnect();
    // First, delete all initiative entries for this session
    $sql = "DELETE FROM initiativeorder WHERE sessionId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $sessionId);
    $stmt->execute();
    $stmt->close();

    // Then, delete the session
    $sql = "DELETE FROM sessions WHERE sessionId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $sessionId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function getInitiativesForSession($sessionId) {
    $db = dbConnect();
    $sql = "SELECT * FROM initiativeorder WHERE sessionId = ? ORDER BY initiative DESC, initiativeName ASC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $sessionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $initiatives = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $db->close();
    return $initiatives;
}

function createInitiative($sessionId, $initiativeName, $initiative, $isNPC) {
    $db = dbConnect();
    $sql = "INSERT INTO initiativeorder (sessionId, initiativeName, initiative, isNPC) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("isii", $sessionId, $initiativeName, $initiative, $isNPC);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function updateInitiative($initiativeId, $initiativeName, $initiative, $isNPC) {
    $db = dbConnect();
    $sql = "UPDATE initiativeorder SET initiativeName = ?, initiative = ?, isNPC = ? WHERE initiativeId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("siii", $initiativeName, $initiative, $isNPC, $initiativeId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function deleteInitiative($initiativeId) {
    $db = dbConnect();
    $sql = "DELETE FROM initiativeorder WHERE initiativeId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $initiativeId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function getInitiative($initiativeId) {
    $db = dbConnect();
    $sql = "SELECT * FROM initiativeorder WHERE initiativeId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $initiativeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $initiative = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    return $initiative;
}

function getSession($sessionId) {
    $db = dbConnect();
    $sql = "SELECT * FROM sessions WHERE sessionId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $sessionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    return $session;
}

function getQuestsForCampaign($campaignId) {
    $db = dbConnect();
    $sql = "SELECT * FROM quests WHERE campaignId = ? ORDER BY questStatus ASC, questTitle ASC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    $quests = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $db->close();
    return $quests;
}

function createQuest($campaignId, $questTitle, $questDescription, $questStatus) {
    $db = dbConnect();
    $sql = "INSERT INTO quests (campaignId, questTitle, questDescription, questStatus) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("isss", $campaignId, $questTitle, $questDescription, $questStatus);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function updateQuest($questId, $questTitle, $questDescription, $questStatus) {
    $db = dbConnect();
    $sql = "UPDATE quests SET questTitle = ?, questDescription = ?, questStatus = ? WHERE questId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssi", $questTitle, $questDescription, $questStatus, $questId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function deleteQuest($questId) {
    $db = dbConnect();
    $sql = "DELETE FROM quests WHERE questId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $questId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function getQuest($questId) {
    $db = dbConnect();
    $sql = "SELECT * FROM quests WHERE questId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $questId);
    $stmt->execute();
    $result = $stmt->get_result();
    $quest = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    return $quest;
}


// ... existing functions (getNotesForCampaign, getQuestsForCampaign, getSessionsForCampaign, etc.) ...
function getNPCsForCampaign($campaignId) {
    $db = dbConnect();
    $sql = "SELECT * FROM npcs WHERE campaignId = ? ORDER BY npcName ASC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    $npcs = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $db->close();
    return $npcs;
}

function createNPC($campaignId, $npcName, $npcRace, $npcClass, $npcImage, $npcDescription, $npcIsFriendly) {
    $db = dbConnect();
    $sql = "INSERT INTO npcs (campaignId, npcName, npcRace, npcClass, npcImage, npcDescription, npcIsFriendly) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("isssssi", $campaignId, $npcName, $npcRace, $npcClass, $npcImage, $npcDescription, $npcIsFriendly);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function updateNPC($npcId, $npcName, $npcRace, $npcClass, $npcImage, $npcDescription, $npcIsFriendly) {
    $db = dbConnect();
    $sql = "UPDATE npcs SET npcName = ?, npcRace = ?, npcClass = ?, npcImage = ?, npcDescription = ?, npcIsFriendly = ? WHERE npcId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssssii", $npcName, $npcRace, $npcClass, $npcImage, $npcDescription, $npcIsFriendly, $npcId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function deleteNPC($npcId) {
    $db = dbConnect();
    $sql = "DELETE FROM npcs WHERE npcId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $npcId);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function getNPC($npcId) {
    $db = dbConnect();
    $sql = "SELECT * FROM npcs WHERE npcId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $npcId);
    $stmt->execute();
    $result = $stmt->get_result();
    $npc = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    return $npc;
}