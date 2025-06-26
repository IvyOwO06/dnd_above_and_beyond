<?php
require 'inc/racesFunctions.php';
require 'inc/navFunctions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Races - D&D</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/races.css">
</head>
<body>
    <?php displayHeader(); ?>
    <div class="search-section">
        <input type="text" class="live-search" placeholder="Search races...">
        <?php displayRaces(); ?>
    </div>
    
    <!-- Modal Structure -->
    <div id="raceModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">×</span>
            <div id="modal-body"></div>
        </div>
    </div>
    
    <?php displayFooter(); ?>
    <script src="scripts/js/jsonSearch.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('raceModal');
            const modalBody = document.getElementById('modal-body');
            const closeModal = document.querySelector('.modal-close');
            const raceCards = document.querySelectorAll('.filter-item');

            // Open modal on race card click
            raceCards.forEach(card => {
                card.addEventListener('click', () => {
                    const raceData = JSON.parse(card.dataset.raceJson);
                    renderRaceData(raceData);
                    modal.style.display = 'flex';
                });
            });

            // Close modal
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Close modal with ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    modal.style.display = 'none';
                }
            });

            // Render race data in modal
            function renderRaceData(data) {
                let html = `<h1>${escapeHtml(data.name)}</h1>`;
                html += `<p><strong>Source:</strong> ${escapeHtml(data.source)}, Page ${data.page}</p>`;

                // Render race entries
                if (data.entries && Array.isArray(data.entries)) {
                    data.entries.forEach(entry => {
                        if (entry.name) {
                            html += `<h2>${escapeHtml(entry.name)}</h2>`;
                        }
                        if (entry.entries && Array.isArray(entry.entries)) {
                            html += renderEntries(entry.entries, 2);
                        }
                    });
                }

                // Render fluff
                if (data.fluff && Array.isArray(data.fluff)) {
                    html += '<h2>Lore</h2>';
                    html += renderEntries(data.fluff, 2);
                } else {
                    html += '<p><em>No fluff available for this version of the race.</em></p>';
                }

                modalBody.innerHTML = html;
            }

            // Recursively render entries
            function renderEntries(entries, depth) {
                let html = '';
                const nextDepth = Math.min(depth + 1, 6);
                entries.forEach(entry => {
                    if (typeof entry === 'string') {
                        html += `<p>${escapeHtml(stripJsonTags(entry))}</p>`;
                    } else if (typeof entry === 'object') {
                        if (entry.name) {
                            html += `<h${depth}>${escapeHtml(entry.name)}</h${depth}>`;
                        }
                        if (entry.entries && Array.isArray(entry.entries)) {
                            html += renderEntries(entry.entries, nextDepth);
                        }
                    }
                });
                return html;
            }

            // Escape HTML to prevent XSS
            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            // Strip JSON tags (simplified, adjust based on your stripJsonTags function)
            function stripJsonTags(str) {
                return str.replace(/{[^}]+}/g, '');
            }
        });
    </script>
</body>
</html>