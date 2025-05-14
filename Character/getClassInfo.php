<?php
require 'inc/functions.php'; // or wherever your dbConnect function is


$pdo = dbConnect();

if (isset($_GET['classId'])) {
    $classId = intval($_GET['classId']);

    $stmt = $pdo->prepare("SELECT className, classInformation, classImage, primaryAblitity, hitPointDie, spellCastingAbility, spellCasting FROM classes WHERE classId = ?");
    $stmt->execute([$classId]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($class) {
        echo json_encode($class);
    } else {
        echo json_encode(["error" => "Class not found"]);
    }
} else {
    echo json_encode(["error" => "No classId provided"]);
}
