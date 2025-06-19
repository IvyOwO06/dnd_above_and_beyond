// Ensure DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing modal handlers');

    // Tab navigation
    document.querySelectorAll('.tab-links a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetTab = link.getAttribute('href').substring(1);
            console.log(`Switching to tab: ${targetTab}`);
            // Hide all tabs and modals
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
            const overlay = document.querySelector('.overlay');
            if (overlay) overlay.classList.remove('active');
            // Show target tab
            const target = document.querySelector(`#${targetTab}`);
            if (target) target.classList.add('active');
            else console.error(`Target tab #${targetTab} not found`);
        });
    });

    // Show race modal
    function showRaceModal(index, name, info) {
        const modal = document.getElementById('race-modal');
        const overlay = document.querySelector('.overlay');
        const infoDiv = document.getElementById('modal-race-info');

        if (!modal || !overlay || !infoDiv) {
            console.error('Race modal elements missing:', { modal, overlay, infoDiv });
            return;
        }

        console.log(`Showing race modal for: ${name} (index: ${index})`);
        infoDiv.innerHTML = `
            <h2>${name}</h2>
            <p>${info}</p>
            <a href="races?raceId=${index}">Read more</a>
            <button id="confirm-race-selection" type="button" value="${index}">Select This Race</button>
        `;

        const confirmBtn = infoDiv.querySelector('#confirm-race-selection');
        if (confirmBtn) {
            confirmBtn.onclick = function () {
                console.log(`Selecting race: ${name} (index: ${index})`);
                const input = document.querySelector(`input.race-radio[value="${index}"]`);
                closeRaceModal();
            };
        } else {
            console.error('confirm-race-selection button not found');
        }

        modal.classList.add('active');
        overlay.classList.add('active');
    }

    // Close race modal
    function closeRaceModal() {
        const modal = document.getElementById('race-modal');
        const overlay = document.querySelector('.overlay');
        if (modal && overlay) {
            console.log('Closing race modal');
            modal.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    // Show class modal
    function showClassModal(index, name, info) {
        const modal = document.getElementById('class-modal');
        const overlay = document.querySelector('.overlay');
        const infoDiv = document.getElementById('modal-class-info');

        if (!modal || !overlay || !infoDiv) {
            console.error('Class modal elements missing:', { modal, overlay, infoDiv });
            return;
        }

        console.log(`Showing class modal for: ${name} (index: ${index})`);
        infoDiv.innerHTML = `
            <h2>${name}</h2>
            <p>${info}</p>
            <a href="classes.php?classId=${index}">Read more</a>
            <button id="confirm-class-selection" type="button" value="${index}">Select This Class</button>
        `;

        const confirmBtn = infoDiv.querySelector('#confirm-class-selection');
        if (confirmBtn) {
            confirmBtn.onclick = function () {
                console.log(`Selecting class: ${name} (index: ${index})`);
                const input = document.querySelector(`input.class-radio[value="${index}"]`);
                closeClassModal();
            };
        } else {
            console.error('confirm-class-selection button not found');
        }

        modal.classList.add('active');
        overlay.classList.add('active');
    }

    // Close class modal
    function closeClassModal() {
        const modal = document.getElementById('class-modal');
        const overlay = document.querySelector('.overlay');
        if (modal && overlay) {
            console.log('Closing class modal');
            modal.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    // Show feature modal
    function showFeatureModal(name, info, level) {
        const modal = document.getElementById('feature-modal');
        const overlay = document.querySelector('.overlay');
        const infoDiv = document.getElementById('modal-feature-info');

        if (!modal || !overlay || !infoDiv) {
            console.error('Feature modal elements missing:', { modal, overlay, infoDiv });
            return;
        }

        console.log(`Showing feature modal for: ${name} (level: ${level})`);
        infoDiv.innerHTML = `
            <h2>${name}</h2>
            <p><strong>Level:</strong> ${level}</p>
            <p>${info}</p>
        `;

        modal.classList.add('active');
        overlay.classList.add('active');
    }

    // Close feature modal
    function closeFeatureModal() {
        const modal = document.getElementById('feature-modal');
        const overlay = document.querySelector('.overlay');
        if (modal && overlay) {
            console.log('Closing feature modal');
            modal.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    // Modal button handlers
    const featureButtons = document.querySelectorAll('.show-feature-modal');
    console.log(`Found ${featureButtons.length} feature modal buttons`);
    featureButtons.forEach(button => {
        button.addEventListener('click', () => {
            const name = button.getAttribute('data-name');
            const info = button.getAttribute('data-info').replace(/\n/g, '<br>');
            const level = button.getAttribute('data-level');
            console.log(`Opening feature modal for: ${name} (level: ${level})`);
            showFeatureModal(name, info, level);
        });
    });

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
    const overlay = document.querySelector('.overlay');
    if (overlay) {
        overlay.addEventListener('click', (event) => {
            if (!event.target.closest('.modal-content')) {
                console.log('Closing modals via overlay click');
                closeRaceModal();
                closeClassModal();
                closeFeatureModal();
            }
        });
    } else {
        console.error('Overlay element (.overlay) not found');
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

    const featureCloseButton = document.querySelector('#feature-modal .close-button');
    if (featureCloseButton) {
        featureCloseButton.addEventListener('click', closeFeatureModal);
    } else {
        console.error('Feature modal close button not found');
    }

    // Escape key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            console.log('Closing modals via Escape key');
            closeRaceModal();
            closeClassModal();
            closeFeatureModal();
        }
    });

    // Live search functionality for features
    const searchInput = document.querySelector('.live-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.filter-item').forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                item.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    } else {
        console.error('Live search input (.live-search) not found');
    }
});