<?php
require_once 'functions.php';
require_once 'builderFunctions.php';

function getCharacterSkillsJson($characterId)
{
    if (!$characterId || !is_numeric($characterId)) {
        return json_encode([]);
    }
    $skills = getCharacterSkills($characterId);
    return json_encode($skills);
}

function getSkillModifier($characterId, $skillId)
{
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