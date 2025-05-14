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
</head>

<body>
    <?php
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';

    displayHeader();

    handleCharacterCreation();
    homeTabBuilder();

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
