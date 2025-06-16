<?php

include_once 'functions.php';

function getClassLevel($classId) {
    $class = getClassFromJson($classId);
    $level = $class['level'];
    return $level;
}