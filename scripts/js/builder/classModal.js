function showclassModal(index, name, info) {
    const modal = document.getElementById('class-modal');
    const overlay = document.getElementById('modal-overlay');
    const infoDiv = document.getElementById('modal-class-info');
    const confirmBtn = document.getElementById('confirm-class-selection');

    infoDiv.innerHTML = `
        <h2>${name}</h2>
        <p>${info}</p>
        <a href="classes.php?classId=${index}">Read more</a>
        <button id="confirm-race-selection" type="button">Select This Race</button>
    `;

    confirmBtn.onclick = function () {
        const input = document.querySelector(`input.class-radio[value="${index}"]`);
        if (input) input.checked = true;
        closeclassModal();
    };

    modal.hidden = false;
    overlay.hidden = false;
}

function closeclassModal() {
    document.getElementById('class-modal').hidden = true;
    document.getElementById('modal-overlay').hidden = true;
}

document.querySelectorAll('.show-class-modal').forEach(button => {
    button.addEventListener('click', function () {
        const index = this.dataset.index;
        const name = this.dataset.name;
        const info = this.dataset.info.replace(/\n/g, '<br>');
        showclassModal(index, name, info);
    });
});

document.getElementById('modal-overlay').addEventListener('click', closeclassModal);
document.querySelector('#class-modal .close-button').addEventListener('click', closeclassModal);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeclassModal(); });