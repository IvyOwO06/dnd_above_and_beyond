let selectedClassIndex = null;

    function showClassModal(index, name, description) {
    selectedClassIndex = index;

    document.getElementById('modal-class-info').innerHTML = `
        <h2 id="modal-name">${name}</h2>
        <p id="modal-desc"> ${description}</p>
        

    `;/*add the id of the modale here*/

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
        // Get the radio button element
        const radioButton = document.querySelector(`input[name="characterClass"][value="${selectedClassIndex}"]`);
        
        // Check the radio button
        radioButton.checked = true;
        
        // Trigger the jQuery change event
        $(radioButton).trigger('change');
        
        closeClassModal();
    }
});