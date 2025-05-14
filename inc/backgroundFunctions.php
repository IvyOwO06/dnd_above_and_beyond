<?php
// Establish database connection
function getDBConnection() {
    return new PDO('mysql:host=localhost;dbname=dnm', 'root', '');
}


// Get available languages
function getLanguages() {
    $db = getDBConnection();
    $query = "SELECT * FROM languages";
    return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

// Get background traits
function getBackgroundTraits($backgroundId) {
    $db = getDBConnection();
    $query = "SELECT * FROM backgroundTraits WHERE backgroundId = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$backgroundId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get background choices (including language options)
function getBackgroundChoices($backgroundId) {
    $db = getDBConnection();
    $query = "SELECT * FROM backgroundChoices WHERE backgroundId = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$backgroundId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}