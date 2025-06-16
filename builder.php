<?php
require 'inc/builderFunctions.php';
require 'inc/classesFunctions.php';
require 'inc/racesFunctions.php';
require 'inc/navFunctions.php';
require 'inc/levelFunctions.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/overlay.css">
    <script src="scripts/js/jquery-3.7.1.min.js"></script>
</head>

<body>
    <?php
    displayHeader();

    homeTabBuilder($_GET['characterId']);

    displayFooter();
    ?>

</body>
<script>function showTabFromHash() {
    const hash = window.location.hash || '#general';
    const tabs = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    const activeTab = document.querySelector(hash);
    if (activeTab) {
        activeTab.classList.add('active');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    showTabFromHash();
    // Prevent scroll on hash change
    document.querySelectorAll('.tab-links a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            history.pushState(null, '', link.getAttribute('href'));
            showTabFromHash();
        });
    });
});

window.addEventListener('hashchange', showTabFromHash);</script>
<script src="scripts/js/jsonSearch.js"></script>
<script src="scripts/js/builder/updateBuilder.js"></script>
<script src="scripts/js/builder/rollAbilities.js"></script>
<script src="scripts/js/builder/modal.js"></script>
<script src="scripts/js/builder/level.js"></script>

</html>