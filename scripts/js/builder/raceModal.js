// Show the race modal with dynamic content
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

    modal.hidden = false;
    overlay.hidden = false;
}

// Close modal when clicking outside modal content (but still inside overlay)
document.getElementById('modal-overlay').addEventListener('click', function (event) {
    const modalContent = document.querySelector('#race-modal .modal-content');
    if (!modalContent.contains(event.target)) {
        closeRaceModal();
    }
});

// Close the race modal
function closeRaceModal() {
    document.getElementById('race-modal').hidden = true;
    document.getElementById('modal-overlay').hidden = true;
}

// Close modal when clicking the close button (×)
document.querySelector('#race-modal .close-button').addEventListener('click', closeRaceModal);

// Close modal when clicking the close button (×)
document.querySelector('#class-modal .close-button').addEventListener('click', closeRaceModal);

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeRaceModal();
    }
});