// Tab navigation
document.querySelectorAll('.tab-links a').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetTab = link.getAttribute('href').substring(1); // e.g., "race"
        // Hide all tabs and modals
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
        document.querySelector('#modal-overlay').classList.remove('active');
        // Show target tab
        document.querySelector(`#${targetTab}`).classList.add('active');
    });
});

// Show race modal
function showRaceModal(index, name, info) {
    const modal = document.getElementById('race-modal');
    const overlay = document.getElementById('modal-overlay');
    const infoDiv = document.getElementById('modal-race-info');
    const confirmBtn = document.getElementById('confirm-race-selection');

    infoDiv.innerHTML = `
        <h2>${name}</h2>
        <p>${info}</p>
        <a href="races.php?raceId=${index}">Read more</a>
    `;

    confirmBtn.onclick = function () {
        const input = document.querySelector(`input.race-radio[value="${index}"]`);
        if (input) {
            input.checked = true;
        }
        closeRaceModal();
    };

    modal.classList.add('active');
    overlay.classList.add('active');
}

// Close race modal
function closeRaceModal() {
    document.getElementById('race-modal').classList.remove('active');
    document.getElementById('modal-overlay').classList.remove('active');
}

// Show class modal
function showClassModal(index, name, info) {
    const modal = document.getElementById('class-modal');
    const overlay = document.getElementById('modal-overlay');
    const infoDiv = document.getElementById('modal-class-info');
    const confirmBtn = document.getElementById('confirm-class-selection');

    infoDiv.innerHTML = `
        <h2>${name}</h2>
        <p>${info}</p>
    `;

    confirmBtn.onclick = function () {
        const input = document.querySelector(`input.class-radio[value="${index}"]`);
        if (input) {
            input.checked = true;
        }
        closeClassModal();
    };

    modal.classList.add('active');
    overlay.classList.add('active');
}

// Close class modal
function closeClassModal() {
    document.getElementById('class-modal').classList.remove('active');
    document.getElementById('modal-overlay').classList.remove('active');
}

// Modal button handlers
document.querySelectorAll('.show-race-modal').forEach(button => {
    button.addEventListener('click', () => {
        const index = button.getAttribute('data-index');
        const name = button.getAttribute('data-name');
        const info = button.getAttribute('data-info');
        showRaceModal(index, name, info);
    });
});

document.querySelectorAll('.show-class-modal').forEach(button => {
    button.addEventListener('click', () => {
        const index = button.getAttribute('data-index');
        const name = button.getAttribute('data-name');
        const info = button.getAttribute('data-info');
        showClassModal(index, name, info);
    });
});

// Overlay click to close
document.getElementById('modal-overlay').addEventListener('click', (event) => {
    if (!event.target.closest('.modal-content')) {
        closeRaceModal();
        closeClassModal();
    }
});

// Close buttons
document.querySelector('#race-modal .close-button').addEventListener('click', closeRaceModal);
document.querySelector('#class-modal .close-button').addEventListener('click', closeClassModal);

// Escape key
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeRaceModal();
        closeClassModal();
    }
});