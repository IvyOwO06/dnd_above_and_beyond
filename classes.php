<?php
require 'inc/classesFunctions.php';
require 'inc/navFunctions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D&D Classes</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/classes.css">
    <script src="scripts/js/jsonSearch.js"></script>
</head>
<body>
    <?php displayHeader(); ?>
    
    <div class="search-section">
        <input type="text" class="live-search" placeholder="Search classes...">
        <?php displayClasses(); ?>
    </div>

    <!-- Modal Structure -->
    <div id="classModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">×</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <?php displayFooter(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('classModal');
            const modalBody = document.getElementById('modal-body');
            const closeBtn = document.querySelector('.modal-close');
            const classCards = document.querySelectorAll('.filter-item');

            // Open modal on card click
            classCards.forEach(card => {
                card.addEventListener('click', () => {
                    const classId = card.getAttribute('data-class-id');
                    const classDetails = document.getElementById(`class-details-${classId}`);
                    if (classDetails) {
                        modalBody.innerHTML = classDetails.innerHTML;
                        modal.style.display = 'flex';
                    } else {
                        modalBody.innerHTML = '<p>Error: Class details not found.</p>';
                        modal.style.display = 'flex';
                    }
                });
            });

            // Close modal with ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    modal.style.display = 'none';
                }
            });

            // Close modal
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
                modalBody.innerHTML = '';
            });

            // Close modal when clicking outside
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modalBody.innerHTML = '';
                }
            });
        });
    </script>
</body>
</html>