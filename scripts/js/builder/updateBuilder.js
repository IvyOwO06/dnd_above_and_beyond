// Get URL parameters
const params = new URLSearchParams(window.location.search);

// Get specific value
const characterId = params.get('characterId');

function updateField(field, value) {
$.ajax({
    url: 'updateBuilder',
    type: 'POST',
    data: {
    field: field,
    value: value,
    characterId: characterId,
    userId: userId

    },
    success: function(response) {
    console.log(`Updated ${field} to ${value}: ${response}`);
    },

    error: function() {
    alert(`Failed to update ${field}`);
    }
});
}

// Listen for input changes
$('#characterName').on('change', function() {
updateField('characterName', $(this).val());
});

$('#characterAge').on('change', function() {
updateField('characterAge', $(this).val());
});

$('#level').on('change', function() {
updateField('level', $(this).val());
});

$('#alignment').on('change', function () {
updateField('alignment', $(this).val());
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'confirm-race-selection') {
        const index = e.target.value;
        updateField('raceId', index);
        location.reload();
    }
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'confirm-class-selection') {
        const index = e.target.value;
        updateField('classId', index);
        location.reload();
    }
});

$('.ability-score').on('change', function () {
const field = $(this).data('field'); // gets "strength", "dexterity", etc.
const value = $(this).val();
updateField(field, value);
});