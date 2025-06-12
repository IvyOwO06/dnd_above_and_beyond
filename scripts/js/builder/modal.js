// Ensure DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Tab navigation
    document.querySelectorAll('.tab-links a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetTab = link.getAttribute('href').substring(1); // e.g., "race"
            // Hide all tabs and modals
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
            const overlay = document.querySelector('#modal-overlay');
            if (overlay) overlay.classList.remove('active');
            // Show target tab
            const target = document.querySelector(`#${targetTab}`);
            if (target) target.classList.add('active');
        });
    });

    // Show race modal
    function showRaceModal(index, name, info) {
        const modal = document.getElementById('race-modal');
        const overlay = document.getElementById('modal-overlay');
        const infoDiv = document.getElementById('modal-race-info');

        if (!modal || !overlay || !infoDiv) {
            console.error('Race modal elements missing:', { modal, overlay, infoDiv });
            return;
        }

        // Set modal content
        infoDiv.innerHTML = `
            <h2>${name}</h2>
            <p>${info}</p>
            <a href="races?raceId=${index}">Read more</a>
            <button id="confirm-race-selection" type="button">Select This Race</button>
        `;

        // Bind event to the new confirm button
        const confirmBtn = infoDiv.querySelector('#confirm-race-selection');
        if (confirmBtn) {
            confirmBtn.onclick = function () {
                console.log(`Attempting to select race: ${name} (index: ${index})`);
                const input = document.querySelector(`input.race-radio[value="${index}"]`);
                if (input) {
                    input.checked = true;
                    console.log(`Selected race: ${name} (index: ${index})`);
                } else {
                    console.warn(`No race-radio input found for value "${index}"`);
                }
                closeRaceModal();
            };
        } else {
            console.error('confirm-race-selection button not found after setting innerHTML');
        }

        modal.classList.add('active');
        overlay.classList.add('active');
    }

    // Close race modal
    function closeRaceModal() {
        const modal = document.getElementById('race-modal');
        const overlay = document.getElementById('modal-overlay');
        if (modal && overlay) {
            modal.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    // Show class modal
    function showClassModal(index, name, info) {
        const modal = document.getElementById('class-modal');
        const overlay = document.getElementById('modal-overlay');
        const infoDiv = document.getElementById('modal-class-info');

        if (!modal || !overlay || !infoDiv) {
            console.error('Class modal elements missing:', { modal, overlay, infoDiv });
            return;
        }

        // Set modal content
        infoDiv.innerHTML = `
            <h2>${name}</h2>
            <p>${info}</p>
            <a href="classes.php?classId=${index}">Read more</a>
            <button id="confirm-class-selection" type="button">Select This Class</button>
        `;

        // Bind event to the new confirm button
        const confirmBtn = infoDiv.querySelector('#confirm-class-selection');
        if (confirmBtn) {
            confirmBtn.onclick = function () {
                console.log(`Attempting to select class: ${name} (index: ${index})`);
                const input = document.querySelector(`input.class-radio[value="${index}"]`);
                if (input) {
                    input.checked = true;
                    console.log(`Selected class: ${name} (index: ${index})`);
                } else {
                    console.warn(`No class-radio input found for value "${index}"`);
                }
                closeClassModal();
            };
        } else {
            console.error('confirm-class-selection button not found after setting innerHTML');
        }

        modal.classList.add('active');
        overlay.classList.add('active');
    }

    // Close class modal
    function closeClassModal() {
        const modal = document.getElementById('class-modal');
        const overlay = document.getElementById('modal-overlay');
        if (modal && overlay) {
            modal.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    // Modal button handlers
    document.querySelectorAll('.show-race-modal').forEach(button => {
        button.addEventListener('click', () => {
            const index = button.getAttribute('data-index');
            const name = button.getAttribute('data-name');
            const info = button.getAttribute('data-info').replace(/\n/g, '<br>');
            console.log(`Opening race modal for: ${name} (index: ${index})`);
            showRaceModal(index, name, info);
        });
    });

    document.querySelectorAll('.show-class-modal').forEach(button => {
        button.addEventListener('click', () => {
            const index = button.getAttribute('data-index');
            const name = button.getAttribute('data-name');
            const info = button.getAttribute('data-info').replace(/\n/g, '<br>');
            console.log(`Opening class modal for: ${name} (index: ${index})`);
            showClassModal(index, name, info);
        });
    });

    // Overlay click to close
    const overlay = document.getElementById('modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', (event) => {
            if (!event.target.closest('.modal-content')) {
                closeRaceModal();
                closeClassModal();
            }
        });
    }

    // Close buttons
    const raceCloseButton = document.querySelector('#race-modal .close-button');
    if (raceCloseButton) {
        raceCloseButton.addEventListener('click', closeRaceModal);
    }

    const classCloseButton = document.querySelector('#class-modal .close-button');
    if (classCloseButton) {
        classCloseButton.addEventListener('click', closeClassModal);
    }

    // Escape key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeRaceModal();
            closeClassModal();
        }
    });
});