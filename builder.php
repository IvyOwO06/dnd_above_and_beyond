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
    const rolls = Array.from({length: 4}, () => Math.floor(Math.random() * 6) + 1);
    rolls.sort((a, b) => a - b);
    return rolls[1] + rolls[2] + rolls[3]; // Drop the lowest
}

function roll4d6DropLowest() {
    const rolls = Array.from({length: 4}, () => Math.floor(Math.random() * 6) + 1);
    rolls.sort((a, b) => a - b);
    return rolls[1] + rolls[2] + rolls[3]; // Drop the lowest
}

function rollAbilities() {
    const scores = Array.from({length: 6}, () => roll4d6DropLowest());
    const fields = document.querySelectorAll('.ability-score');

    fields.forEach((field, index) => {
        field.value = scores[index];
    });
}
  </script>
      <!-- more info about classes and races -->
  <script>
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
  </script>
  </html>
