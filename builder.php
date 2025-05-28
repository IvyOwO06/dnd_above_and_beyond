<?php
require 'inc/builderFunctions.php';
require 'inc/classesFunctions.php';
require 'inc/racesFunctions.php';
require 'inc/navFunctions.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/overlay.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php
    displayHeader();

    handleCharacterCreation();
    homeTabBuilder($_GET['characterId']);

    displayFooter();
    ?>

</body>
<!-- builder tab functionality -->
<script>
    function showTabFromHash() {
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

    window.addEventListener('hashchange', showTabFromHash);

    function roll4d6DropLowest() {
        const rolls = Array.from({ length: 4 }, () => Math.floor(Math.random() * 6) + 1);
        rolls.sort((a, b) => a - b);
        return rolls[1] + rolls[2] + rolls[3]; // Drop the lowest
    }

    function roll4d6DropLowest() {
        const rolls = Array.from({ length: 4 }, () => Math.floor(Math.random() * 6) + 1);
        rolls.sort((a, b) => a - b);
        return rolls[1] + rolls[2] + rolls[3]; // Drop the lowest
    }

    function rollAbilities() {
        const scores = Array.from({ length: 6 }, () => roll4d6DropLowest());
        const fields = document.querySelectorAll('.ability-score');

        fields.forEach((field, index) => {
            field.value = scores[index];
            field.dispatchEvent(new Event('change'));
        });
    }

    function toggleInfo(type, id) {
        const info = document.getElementById(`${type}-info-${id}`);
        const arrow = document.getElementById(`${type}-arrow-${id}`);

        if (info.hidden) {
            info.hidden = false;
            arrow.textContent = '▼';
        } else {
            info.hidden = true;
            arrow.textContent = '▶';
        }
    }

    let selectedClassIndex = null;

    function showClassModal(index, name, description) {
        selectedClassIndex = index;

        document.getElementById('modal-class-info').innerHTML = `
        <h2>${name}</h2>
        <p>${description}</p>
      `;

        document.getElementById('class-modal').hidden = false;
        document.getElementById('modal-overlay').hidden = false;
    }

    function closeClassModal() {
        document.getElementById('class-modal').hidden = true;
        document.getElementById('modal-overlay').hidden = true;
        selectedClassIndex = null;
    }

    document.getElementById('modal-overlay').addEventListener('click', closeClassModal);
    document.querySelector('.close-button').addEventListener('click', closeClassModal);

    document.getElementById('confirm-selection').addEventListener('click', () => {
        if (selectedClassIndex !== null) {
            document.querySelector(`input[name="characterClass"][value="${selectedClassIndex}"]`).checked = true;
            closeClassModal();
        }
    });
</script>

</html>